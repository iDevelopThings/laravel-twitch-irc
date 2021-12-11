<?php

namespace TwitchIrc\Bot\Channel;

interface ChannelProviderContract
{
    /**
     * @return string|null
     */
    public function getUsername(): ?string;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @param string|null $username
     *
     * @return ChannelProviderContract
     */
    public function username(?string $username): ChannelProviderContract;

    /**
     * @param string|null $id
     *
     * @return ChannelProviderContract
     */
    public function id(?string $id): ChannelProviderContract;
}
