<?php


namespace TwitchIrc\Bot\Connection;


use Amp\Http\Client\HttpException;
use Amp\Loop;
use Amp\Promise;
use Amp\Websocket\Client\Connection;
use Amp\Websocket\Client\ConnectionException;
use Amp\Websocket\ClosedException;
use TwitchIrc\Bot\BotUser\BotUserProviderContract;
use TwitchIrc\Bot\Channel\ChannelsHandler;
use TwitchIrc\Bot\Output;
use Exception;
use Illuminate\Support\Facades\Redis;
use TwitchIrc\TwitchIrc;
use TwitchIrc\TwitchIrcContract;
use function Amp\Websocket\Client\connect;

class BotConnection
{

    const RELOAD_SIGNAL_KEY = "twitch_bot_reload_signal";

    /**
     * The singleton instance.
     *
     * @var BotConnection|null $instance
     */
    public static ?BotConnection $instance = null;

    /**
     * The authorised "bot user provider" for the bot account, this
     * has our access_token, username etc
     *
     * @var BotUserProviderContract|null $botUserProvider
     */
    public ?BotUserProviderContract $botUserProvider = null;

    /**
     * The bots twitch login username
     *
     * @var string $nick
     */
    public string $nick = "";

    /**
     * The websocket server to connect to
     *
     * @var string $host
     */
    public string $host = 'wss://irc-ws.chat.twitch.tv';

    /**
     * The bots oauth token
     *
     * @var string $password
     */
    public string $password = "";

    /**
     * The socket connection with twitch, that the bot is using
     *
     * @var Connection|null $connection
     */
    public ?Connection $connection = null;

    /**
     * Current irc/connection stage
     * This will decide how we are currently processing and what we will do
     *
     * @var IrcStageTypes $ircStage
     */
    private IrcStageTypes $ircStage;

    private TwitchIrc $irc;

    public function __construct(TwitchIrcContract $irc)
    {
        $this->irc = $irc;
    }

    /**
     * Uses singleton pattern to instantiate the class.
     *
     * @return BotConnection
     */
    public static function getInstance(): BotConnection
    {
        if (self::$instance == null) {
            self::$instance = resolve(BotConnection::class);
        }

        return self::$instance;
    }

    /**
     * Send a message in a channel from outside the process
     *
     * @param $channelName
     * @param $message
     */
    public static function sendMessage($channelName, $message)
    {
        Redis::rPush('bot_messages', json_encode([$channelName, $message]));
    }

    /**
     * Send a trigger which will force things like a bot reload
     *
     * @param       $type
     * @param array $data
     */
    public static function sendBotTrigger($type, $data = [])
    {
        $triggerData = json_encode([
            "type" => $type,
            "data" => $data,
        ]);

        Redis::rPush('bot_triggers', $triggerData);
    }

    /**
     * Set the reload signal in the cache, so that the
     * script will detect it and force stop the bot process
     *
     * @throws Exception
     */
    public static function sendReloadSignal()
    {
        cache()->forever(self::RELOAD_SIGNAL_KEY, true);
        (new Output())->info('Successfully sent reload signal.');
    }

    /**
     * We have to connect to the socket externally from the loop.
     * I'm not sure why, but this is the only way
     *
     * We then pass the connection back into authorise() and it kinda voids the promises/auto resolves idk.
     *
     * @return Promise
     * @throws HttpException
     * @throws ConnectionException
     */
    public function connectToTwitch(): Promise
    {
        return connect($this->host);
    }

    /**
     * Get the UserProvider for the bot, set the oauth token
     * Then we send the regular bot request messages to twitch to initialise everything.
     *
     * @param $connection
     *
     * @throws ClosedException
     */
    public function authorise($connection)
    {
        $this->connection = $connection;

        $this->botUserProvider = $this->irc->getBotUser();;

        $this->nick     = $this->botUserProvider->getUsername();
        $this->password = 'oauth:' . $this->botUserProvider->getAccessToken();

        $this->send("CAP REQ :twitch.tv/tags twitch.tv/commands twitch.tv/membership");
        $this->send("PASS " . $this->password);
        $this->send("NICK " . $this->nick);

        ChannelsHandler::connectChannels($this->irc->getChannels());

        $this->runMessageQueue();
        $this->sendPings();
    }

    /**
     * Sends a socket message on the connection
     *
     * @param string $data
     *
     * @return Promise
     * @throws ClosedException
     */
    public function send(string $data): Promise
    {
        return $this->connection->send($data . " \r\n ");
    }

    /**
     * We will run a check over the message queue every x ms
     * If it contains any triggers/messages, queued from "outside"
     *
     * We'll process/send them
     */
    public function runMessageQueue(): string
    {
        return Loop::repeat($msInterval = 50, function () {
            $this->checkForReloadSignal();

            $this->messageQueue();
        });
    }

    /**
     * We will check the cache for a reload signal triggered by another script
     *
     * @throws Exception
     */
    private function checkForReloadSignal()
    {
        if (cache()->pull(self::RELOAD_SIGNAL_KEY)) {
            Loop::stop();
            (new Output())->info('Received reload signal... stopping bot process.');
        }
    }

    /**
     * Pop a message of the start of the list stored in redis.
     * Then send it on the relative channel
     *
     * @throws ClosedException
     */
    public function messageQueue()
    {
        $message = Redis::lPop('bot_messages');

        if ($message) {
            [$channel, $message] = json_decode($message, true);

            ChannelsHandler::channel($channel)->sendMessage($message);
        }
    }

    public function sendPings(): string
    {
        return Loop::repeat($msInterval = 5000, function () {
            $this->send('PING :tmi.twitch.tv');
        });
    }

    /**
     * Get the twitch socket connection
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * Handle the incoming IRC command from twitch.
     * Before we handle it, we need to try and understand which
     * channel the message is from and what it needs to do next.
     *
     * @param $payload
     */
    public function handleReceive($payload)
    {
        [$channel, $stage] = IrcParser::parseIrcCommand($payload);

        if ($stage === 'PING') {
            $this->send('PONG :tmi.twitch.tv');
        } elseif ($stage === 'NOTICE') {
            if (str_contains($payload, 'Login authentication failed')) {
                Loop::stop();

                (new Output())->warn('Login authorisation has failed. You need to re-authorise the channel with revent.');
            }
        } elseif ($channel !== $stage) {

            $channel = ltrim($channel, '#');

            try {
                $channel = ChannelsHandler::channel($channel);

                if ($channel) {
                    $channel->setStage($stage);
                    $channel->handle($payload);
                }
            } catch (Exception $exception) {
                (new Output())->error("Error: " . $exception->getMessage());
                (new Output())->error($exception->getTraceAsString());
            }
        }
    }
}
