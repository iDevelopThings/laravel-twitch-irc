<?php

namespace TwitchIrc\Bot\Channel\Stages;

use Exception;
use TwitchIrc\Bot\Message\ChatMessage;
use TwitchIrc\Bot\Output;

class MessageStageHandler extends IrcStageHandler
{
    /**
     * Handle the IRC Stage
     *
     * @param string $payload
     */
    public function handle(string $payload)
    {
        (new Output())->line($payload);


        try {
            $message = ChatMessage::parse($this->channel(), $payload);

            if ($message !== null) {
                $message->handle();
            }
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
