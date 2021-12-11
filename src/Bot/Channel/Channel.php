<?php

namespace TwitchIrc\Bot\Channel;

use Amp\Promise;
use Amp\Websocket\ClosedException;
use TwitchIrc\Bot\Channel\Stages\IrcStageHandler;
use TwitchIrc\Bot\Channel\Stages\JoinRoomStageHandler;
use TwitchIrc\Bot\Connection\BotConnection;
use TwitchIrc\Bot\Connection\IrcStageTypes;

class Channel
{
    /**
     * The username of the channel we are connected to
     *
     * @var string
     */
    public string $username;

    /**
     * Is this channel connected & joined?
     *
     * @var bool
     */
    public bool $connected = false;

    /**
     * This channels current stage
     *
     * @var IrcStageTypes
     */
    public IrcStageTypes $stage;

    /**
     * Stores the commands for this channel and allows us to load, get etc
     *
     * @var ChannelCommandHandlers
     */
    private ChannelCommandHandlers $commandHandler;

    /**
     * Stores the users who are messaging in this channel
     *
     * @var ChannelUsers
     */
    private ChannelUsers $users;

    public function __construct(ChannelProviderContract $channel)
    {
        $this->username = $channel->getUsername();
        $this->commandHandler = new ChannelCommandHandlers();
        $this->users = new ChannelUsers();
    }

    /**
     * Sends a message on this channel
     *
     * @param string $message
     *
     * @return Promise
     * @throws ClosedException
     */
    public function sendMessage(string $message): Promise
    {
        return BotConnection::getInstance()->send("PRIVMSG #{$this->username} :{$message}");
    }

    /**
     * Makes the bot join the channel
     *
     * @throws ClosedException
     */
    public function join(): Promise
    {
        $this->stage = IrcStageTypes::fromValue('ACK');

        return BotConnection::getInstance()->send("JOIN #{$this->username}");
    }

    /**
     * Set the stage we are currently about to process
     *
     * @param $stage
     */
    public function setStage($stage)
    {
        $this->stage = IrcStageTypes::coerce($stage);
    }

    /**
     * When the bot has successfully joined the channel, we will
     * need to load any additional data for it to function
     *
     * We will call this method from {@see JoinRoomStageHandler}
     */
    public function whenJoinedSuccessfully()
    {
        $this->connected = true;

        $this->loadCommands();
    }

    /**
     * Once the bot has loaded, we will load the commands
     * We will need to call this function later on if the streamer
     * updates their commands, this is why it's defined like this.
     */
    public function loadCommands()
    {
        $this->commandHandler()->load($this->username);
    }

    /**
     * @return ChannelCommandHandlers
     */
    public function commandHandler(): ChannelCommandHandlers
    {
        return $this->commandHandler;
    }

    /**
     * When we receive some data on the socket connection, it will
     * be parsed and sent the correct channel. This method will handle
     * the data sent after it was parsed and knows where it needs to go.
     *
     * @param string $payload
     */
    public function handle(string $payload)
    {
        $stageClasses = IrcStageTypes::classForStage($this->stage);

        if (! $stageClasses) {
            return;
        }

        foreach ($stageClasses as $stageClass) {
            /**
             * @var IrcStageHandler $handler
             */
            $handler = new $stageClass($this);
            $handler->handle($payload);
        }
    }

    /**
     * @return ChannelUsers
     */
    public function users(): ChannelUsers
    {
        return $this->users;
    }
}
