<?php

namespace TwitchIrc\Bot\Message;

use Amp\Promise;
use Amp\Websocket\ClosedException;
use Illuminate\Support\Str;

class ChatCommand
{
    /**
     * !shoutout, without the exclamation mark
     *
     * @var string
     */
    protected string $identifier = "";

    /**
     * Command arguments, for example !shoutout username, the args would be ['username']
     *
     * @var array
     */
    protected array $args = [];

    /**
     * @var ChatMessage
     */
    private ChatMessage $message;

    /**
     * IncomingCommand constructor.
     *
     * @param ChatMessage $message
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * We will attempt to parse the message and match it to a command
     * If we can't match it to a command, we'll look for a similar command
     * and ask the message sender if x command was the one that they wanted.
     */
    public function parseCommand()
    {
        $splitMessage = preg_split('/[\s]+/', $this->message->message());
        ;

        $this->identifier = strtolower(Str::substr($splitMessage[0], 1));
        unset($splitMessage[0]);

        $this->args = $splitMessage;

        $command = $this->message->channelHandler()
            ->commandHandler()
            ->getCommand($this->identifier);

        if (! $command) {
            return;
        }

        $command->setCommand($this);

        if ($command->isOnCooldown()) {
            $this->reply("This command is on cooldown for " . $command->cooldownTimeRemaining());

            return;
        }

        if (! $command->userHasPermissions()) {
            $this->reply("No permissions.");

            return;
        }

        $command->handle();
        $command->afterHandle();

        //ray("Processed command '{$this->identifier}'")->green();


        // $similarCommand = $this->message
        // 	->channelHandler()
        // 	->commandHandler()
        // 	->getSimilarCommand($this->identifier);
        //
        // if ($similarCommand === null) {
        // 	return;
        // }
        //
        // $this->reply("Command wasn't found... did you mean to use !{$similarCommand}?");
    }

    /**
     * Reply to the message sender.
     *
     * @param $message
     *
     * @return Promise
     * @throws ClosedException
     */
    public function reply($message)
    {
        $prefixMessage = "@{$this->chatMessage()->username()}";

        if (! empty($this->chatMessage()->mentions())) {
            $prefixMessage = implode(' ', array_map(fn ($name) => '@' . $name, $this->chatMessage()->mentions()));
        }

        return $this->chatMessage()
            ->channelHandler()
            ->sendMessage("{$prefixMessage} {$message}");
    }

    /**
     * @return ChatMessage
     */
    public function chatMessage(): ChatMessage
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return array_values($this->args);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
