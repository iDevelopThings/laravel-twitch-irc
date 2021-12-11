<?php

namespace TwitchIrc\Commands;

use Illuminate\Console\Command;
use TwitchIrc\Bot\TwitchBot;

class TwitchIrcProcess extends Command
{
    public $signature = 'twitchbot {--stop}';

    public $description = 'Starts/tops the twitch bot';

    public function handle(): int
    {
        if ($this->option('stop')) {
            TwitchBot::stop();

            return self::SUCCESS;
        }

        TwitchBot::run();

        return self::SUCCESS;
    }
}
