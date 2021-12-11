<?php

namespace TwitchIrc\Bot\BotUser;

class BotUserProvider implements BotUserProviderContract
{
    private ?string $username = null;

    private ?string $access_token = null;

    private ?string $refresh_token = null;

    /**
     * @param string|null $refresh_token
     *
     * @return BotUserProviderContract
     */
    public function refreshToken(?string $refresh_token): BotUserProviderContract
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * @param string|null $access_token
     *
     * @return BotUserProviderContract
     */
    public function accessToken(?string $access_token): BotUserProviderContract
    {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * @param string|null $username
     *
     * @return BotUserProviderContract
     */
    public function username(?string $username): BotUserProviderContract
    {
        $this->username = $username;

        return $this;
    }

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
    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }
}
