<?php

namespace TwitchIrc\Bot;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

class Output
{
    use InteractsWithIO;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    public static function output(): Output
    {
        return (new self());
    }
}
