<?php

namespace TwitchIrc\Bot\Channel;

class ChannelProvider implements ChannelProviderContract
{
    private ?string $username = null;

    private ?string $id = null;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $username
     *
     * @return ChannelProviderContract
     */
    public function username(?string $username): ChannelProviderContract
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string|null $id
     *
     * @return ChannelProviderContract
     */
    public function id(?string $id): ChannelProviderContract
    {
        $this->id = $id;

        return $this;
    }
}
