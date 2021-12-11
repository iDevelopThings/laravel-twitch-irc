<?php

namespace TwitchIrc\Bot\Message\User;

use BenSampo\Enum\Enum;

/**
 * @method static static VIEWER()
 * @method static static TURBO()
 * @method static static VIP()
 * @method static static SUBSCRIBER()
 * @method static static MODERATOR()
 * @method static static STREAMER()
 */
final class ChatUserRoleTypes extends Enum
{
    public const VIEWER = 0;
    public const TURBO = 1;
    public const VIP = 2;
    public const SUBSCRIBER = 3;
    public const MODERATOR = 4;
    public const STREAMER = 5;
}
