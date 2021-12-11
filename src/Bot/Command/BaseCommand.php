<?php

namespace TwitchIrc\Bot\Command;

use Amp\Promise;
use TwitchIrc\Bot\Channel\Channel;
use TwitchIrc\Bot\Message\ChatCommand;
use TwitchIrc\Bot\Message\ChatMessage;
use TwitchIrc\Bot\Message\User\ChatUser;

abstract class BaseCommand
{
    use HasCommandPermissions;
    use HasCommandCooldown;

    /**
     * @var ChatCommand|null
     */
    private ?ChatCommand $chatCommand = null;

    /**
     * Handle the command processing.
     * Define any logic here, respond with a message etc.
     */
    abstract public function handle(): void;

    /**
     * Some kind of description about how to use the command.
     *
     * @return string
     */
    abstract public function description(): string;

    /**
     * Define any additional aliases that you can use to call this command with.
     *
     * @return array
     */
    public function aliases(): array
    {
        return [];
    }

    /**
     * When we receive a message containing a {@see ChatCommand} and we've parsed it
     * and initiated it. We will initiate the command handler class and set the command.
     *
     * @param ChatCommand $command
     *
     * @return BaseCommand
     */
    public function setCommand(ChatCommand $command): BaseCommand
    {
        $this->chatCommand = $command;

        return $this;
    }

    /**
     * Get the chat user who triggered the command handler
     *
     * @return ChatUser
     */
    public function chatUser(): ChatUser
    {
        return $this->chatMessage()->user();
    }

    /**
     * Get the message that triggered the handler
     *
     * @return ChatMessage
     */
    public function chatMessage(): ChatMessage
    {
        return $this->chatCommand->chatMessage();
    }

    /**
     * Reply to the user who triggered this command
     *
     * @param $message
     *
     * @return Promise
     */
    public function reply($message): Promise
    {
        return $this->chatCommand->reply($message);
    }

    /**
     * There are times when we need to use the message contents
     * without the identifier. In this case, we don't want to modify
     * the message contents stored on the chatMessage()
     *
     * @return string
     */
    public function messageWithoutIdentifier(): string
    {
        return ltrim(str_replace('!' . $this->command()->getIdentifier(), '', $this->chatMessage()->message()));
    }

    /**
     * Get the chat command that was used to trigger the handler
     *
     * @return ChatCommand
     */
    public function command(): ChatCommand
    {
        return $this->chatCommand;
    }

    /**
     * Some logic to be run after the handle method has been run
     */
    public function afterHandle()
    {
        if ($this->cooldownSeconds() > 0) {
            $this->startCooldown();
        }
    }

    /**
     * The "trigger" for the command, for example;
     * If we wanted the user to trigger it with !ping, it should return "ping"
     *
     * @return string
     */
    abstract public function identifier(): string;

    /**
     * The channel instance that this handler was triggered on
     *
     * @return Channel
     */
    public function channel(): Channel
    {
        return $this->chatMessage()->channelHandler();
    }
}
