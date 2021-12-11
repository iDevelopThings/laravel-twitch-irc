<?php

namespace TwitchIrc;

use TwitchIrc\Bot\BotUser\BotUserProviderContract;
use TwitchIrc\Bot\Channel\ChannelProviderContract;

interface TwitchIrcContract
{
    /**
     * @param BotUserProviderContract|null $botUser
     *
     * @return TwitchIrcContract
     */
    public function setBotUser(?BotUserProviderContract $botUser): TwitchIrcContract;

    public function addChannel(ChannelProviderContract $channel): TwitchIrcContract;

    /**
     * @param ChannelProviderContract[] $channels
     *
     * @return TwitchIrcContract
     */
    public function setChannels(array $channels): TwitchIrcContract;

    /**
     * @param string $username
     *
     * @return ChannelProviderContract|null
     */
    public function getChannel(string $username): ChannelProviderContract|null;
}
