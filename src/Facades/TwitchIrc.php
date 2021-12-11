<?php

namespace TwitchIrc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TwitchIrc\TwitchIrc
 */
class TwitchIrc extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'twitch-irc';
    }
}
