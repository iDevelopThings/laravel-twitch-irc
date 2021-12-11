<?php

namespace TwitchIrc\Bot\BotUser;

interface BotUserProviderContract
{
    /**
     * @param string|null $refresh_token
     *
     * @return BotUserProviderContract
     */
    public function refreshToken(?string $refresh_token): BotUserProviderContract;

    /**
     * @param string|null $access_token
     *
     * @return BotUserProviderContract
     */
    public function accessToken(?string $access_token): BotUserProviderContract;

    /**
     * @param string|null $username
     *
     * @return BotUserProviderContract
     */
    public function username(?string $username): BotUserProviderContract;

    /**
     * @return string|null
     */
    public function getUsername(): ?string;

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string;
}
