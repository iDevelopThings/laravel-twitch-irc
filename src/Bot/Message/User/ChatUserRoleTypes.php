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
    const VIEWER     = 0;
    const TURBO      = 1;
    const VIP        = 2;
    const SUBSCRIBER = 3;
    const MODERATOR  = 4;
    const STREAMER   = 5;
}
