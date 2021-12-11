<?php

namespace TwitchIrc\Bot\Connection;

class IrcParser
{
    /**
     * If the value is null, we'll return it
     * If the value has a comma, it means there is > 1 item.(Single items dont have a comma.)
     * So we'll split by comma and return all badges.
     * Otherwise, there is only one item, so we'll return the item in an array.
     *
     * @param $part
     *
     * @return array|false|string[]|null
     */
    public static function parseArrayPart($part)
    {
        if ($part === null) {
            return null;
        }

        if (str_contains($part, ',')) {
            return explode(',', $part);
        }

        return [$part];
    }

    /**
     * When we receive the IRC message from twitch, we'll try to parse
     * what type of "IRC Command" it is
     *
     * @param $payload
     *
     */
    public static function parseIrcCommand($payload)
    {
        $possibleIrcCommands = [
            'PING' => null,
            'ACK' => null,
            'GLOBALUSERSTATE' => 3,
            'PRIVMSG' => 3,
            'ROOMSTATE' => 3,
            'USERSTATE' => 3,
            'JOIN' => 2,
            'PART' => 2,
        ];

        $lines = preg_split("/\r\n|\n|\r/", $payload);

        $channel = null;
        $stage = null;

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $parts = explode(' ', $line);

            foreach ($parts as $index => $part) {
                foreach ($possibleIrcCommands as $command => $channelNameIndex) {
                    if ($command === $part) {
                        $stage = $part;
                    }

                    /**
                     * If the "irc command" doesn't contain a channel name, the channelNameIndex
                     * var will be null. We won't process it in this case.
                     */
                    if ($channelNameIndex === null) {
                        continue;
                    }

                    if ($index === $channelNameIndex) {
                        $channel = $part;
                    }
                }
            }
        }
        if ($channel && $stage) {
            return [$channel, $stage];
        }
    }
}
