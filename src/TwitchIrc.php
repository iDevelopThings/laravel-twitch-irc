<?php

namespace TwitchIrc;

use Illuminate\Support\Collection;
use TwitchIrc\Bot\BotUser\BotUserProviderContract;
use TwitchIrc\Bot\Channel\ChannelProviderContract;

class TwitchIrc implements TwitchIrcContract
{

    /**
     * @var BotUserProviderContract|null $botUser
     */
    private ?BotUserProviderContract $botUser = null;

    /**
     * @var ChannelProviderContract[] $channels
     */
    private array $channels = [];

    /**
     * @param BotUserProviderContract|null $botUser
     *
     * @return TwitchIrcContract
     */
    public function setBotUser(?BotUserProviderContract $botUser): TwitchIrcContract
    {
        $this->botUser = $botUser;

        return $this;
    }

    public function getBotUser(): BotUserProviderContract
    {
        return $this->botUser;
    }

    public function addChannel(ChannelProviderContract $channel): TwitchIrcContract
    {
        $this->channels[$channel->getUsername()] = $channel;

        return $this;
    }

    /**
     * @param ChannelProviderContract[] $channels
     *
     * @return TwitchIrcContract
     */
    public function setChannels(array $channels): TwitchIrcContract
    {
        $this->channels = tap(collect($channels), function (Collection $channels) {
            return $channels->mapWithKeys(function (ChannelProviderContract $channel) {
                return [$channel->getUsername(), $channel];
            });
        })->toArray();

        return $this;
    }

    /**
     * @param string $username
     *
     * @return ChannelProviderContract|null
     */
    public function getChannel(string $username): ChannelProviderContract|null
    {
        return $this->channels[$username] ?? null;
    }

    /**
     * @return ChannelProviderContract[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }
}
