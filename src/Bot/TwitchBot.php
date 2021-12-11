<?php


namespace TwitchIrc\Bot;


use Amp\Loop;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use Illuminate\Support\Facades\Log;
use TwitchIrc\Bot\Connection\BotConnection;
use TwitchIrc\Initiator\TwitchIrcInitiator;
use TwitchIrc\Initiator\TwitchIrcInitiatorContract;

class TwitchBot
{
    /**
     * We will run the loop and begin connecting to Twitch IRC
     */
    public static function run()
    {
        /**
         * @var TwitchIrcInitiatorContract $initiator
         */
        resolve(config('twitch-irc.initiator'))->setup();

        Loop::run(function () {
            try {
                $bot = BotConnection::getInstance();

                $connection = yield $bot->connectToTwitch();

                $bot->authorise($connection);

                while ($message = yield $bot->connection()->receive()) {
                    /** @var Message $message */
                    $payload = yield $message->buffer();

                    (new Output())->line(">>> " . $payload);

                    $bot->handleReceive($payload);
                }
            } catch (ClosedException $exception) {
                Log::info('Twitch bot errored... ' . $exception->getMessage());
                self::restart();
            }
        });
    }

    public static function restart()
    {
        self::stop();
        self::run();
    }

    /**
     * We will force close the bot process using this method
     */
    public static function stop()
    {
        //BotConnection::sendReloadSignal();
        Loop::stop();
    }


}
