<?php

namespace TwitchIrc\Initiator;

use TwitchIrc\TwitchIrcContract;

interface TwitchIrcInitiatorContract
{
    public function __construct(TwitchIrcContract $irc);

    public function setup(): TwitchIrcContract;
}
