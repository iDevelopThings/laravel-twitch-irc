<?php

namespace TwitchIrc\Initiator;

use TwitchIrc\Bot\BotUser\BotUserProvider;
use TwitchIrc\Bot\Channel\ChannelProvider;
use TwitchIrc\TwitchIrcContract;

class TwitchIrcInitiator implements TwitchIrcInitiatorContract
{
    public TwitchIrcContract $irc;

    protected array $config = [];

    public function __construct(TwitchIrcContract $irc)
    {
        $this->config = config('twitch-irc');
        $this->irc = $irc;
    }

    public function setup(): TwitchIrcContract
    {
        $botUser = (new BotUserProvider())
            ->username($this->config['bot_credentials']['username'])
            ->accessToken($this->config['bot_credentials']['access_token'])
            ->refreshToken($this->config['bot_credentials']['refresh_token']);

        $this->irc->setBotUser($botUser);

        $this->irc->setChannels(
            array_map(function ($channelUsername) {
                return (new ChannelProvider())->username($channelUsername);
            }, $this->config['channels'])
        );

        return $this->irc;
    }
}
