<?php

namespace TwitchIrc\Bot\Message;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;
use TwitchIrc\Bot\Channel\Channel;
use TwitchIrc\Bot\Message\User\ChatUser;

class ChatMessage
{
    use InteractsWithIO;

    /**
     * The original message string from twitch irc
     *
     * @var string
     */
    protected string $originalMessage;
    /**
     * Username of the user who sent the message
     *
     * @var string
     */
    protected string $username;
    /**
     * Information about the user who sent the message
     *
     * @var ChatUser
     */
    protected ChatUser $user;
    /**
     * Channel that this message was sent in
     *
     * @var string
     */
    protected string $channel;
    /**
     * The content of the message
     *
     * @var string
     */
    protected string $message;
    /**
     * Is this a command?
     *
     * @var bool
     */
    protected bool $command;

    /**
     * The channel that this message was received on
     *
     * @var Channel
     */
    private Channel $channelHandler;

    /**
     * If the user is @mentioning someone in their message, we will store it here.
     *
     * @var array
     */
    private array $mentions = [];

    /**
     * ChatMessage constructor.
     *
     * @param Channel $channel
     */
    public function __construct(Channel $channel)
    {
        $this->output = new ConsoleOutput();
        $this->channelHandler = $channel;
    }

    /**
     * Parse the payload and return a message instance
     *
     * @param Channel $channel
     * @param string  $payload
     *
     * @return null|ChatMessage
     */
    public static function parse(Channel $channel, string $payload): ?ChatMessage
    {
        $incomingMessage = (new ChatMessage($channel));

        preg_match("/:(\S+)!\S+@\S+ PRIVMSG (#\S+) :(.*)/i", $payload, $match);

        $messageParts = explode(' ', $payload);

        if (str_starts_with($messageParts[0], '@badge-info')) {
            $badgeParts = explode(';', $messageParts[0]);

            $user = (new ChatUser())->parse($match[1], $badgeParts, $messageParts);

            $incomingMessage->setUser($user);
        }

        return $incomingMessage->setInformation([
            $match[1],
            $match[2],
            $match[3],
            $payload,
            Str::startsWith($match[3], '!'),
        ]);
    }

    /**
     * @param ChatUser $user
     *
     * @return ChatMessage
     */
    public function setUser(ChatUser $user): ChatMessage
    {
        $this->user = $user;

        return $this;
    }

    /**
     * We'll set all the info from an array and use destructuring
     * to set all of the classes values
     *
     * @param array $information
     *
     * @return $this
     */
    public function setInformation($information = []): ChatMessage
    {
        [$username, $channel, $message, $payload, $isCommand] = $information;

        $this->username = $username;
        $this->channel = $channel;
        $this->message = rtrim($message);
        $this->originalMessage = $payload;
        $this->command = $isCommand;

        $this->parseMentions();

        return $this;
    }

    /**
     * We'll check the message contents for @mentions and store the mentioned names if they exist
     */
    private function parseMentions()
    {
        preg_match_all("(@(?P<names>[a-zA-Z0-9-_]+))", $this->message, $matches);

        if (! isset($matches['names'])) {
            return;
        }

        if (empty($matches['names'])) {
            return;
        }

        $this->mentions = $matches['names'];
    }

    /**
     * If the channel name has the hashtag, we'll remove it first
     *
     * @return string
     */
    public function channelName(): string
    {
        return ltrim($this->channel, '#');
    }

    public function handle()
    {
        if ($this->isCommand()) {
            (new ChatCommand($this))->parseCommand();
        }
        $this->info('>>> Command: ' . ($this->command ? 'Yes' : 'No'));
        $this->info('>>> handled: ' . $this->username() . ': ' . $this->message());
    }

    /**
     * Was there an exclamation mark at the start of the message?
     *
     * @return bool
     */
    public function isCommand(): bool
    {
        return $this->command === true;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return ChatUser
     */
    public function user(): ChatUser
    {
        return $this->user;
    }

    /**
     * Did bits get sent with this message?
     *
     * @return bool
     */
    public function usedBits(): bool
    {
        $bits = $this->user->getBits();

        if ($bits === null) {
            return false;
        }

        if ($bits === 0) {
            return false;
        }

        return true;
    }

    /**
     * Access the channel that this message was sent from
     *
     * @return Channel
     */
    public function channelHandler(): Channel
    {
        return $this->channelHandler;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function mentions(): array
    {
        return $this->mentions;
    }
}
