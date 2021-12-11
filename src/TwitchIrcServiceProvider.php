<?php

namespace TwitchIrc;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TwitchIrc\Commands\TwitchIrcProcess;
use TwitchIrc\Initiator\TwitchIrcInitiator;
use TwitchIrc\Initiator\TwitchIrcInitiatorContract;

class TwitchIrcServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('twitch-irc')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(TwitchIrcProcess::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton(TwitchIrcContract::class, TwitchIrc::class);
        $this->app->singleton(TwitchIrcInitiatorContract::class, TwitchIrcInitiator::class);
    }

    public function packageRegistered()
    {
        if (! config()->has('filesystems.disks.twitch-irc-commands')) {
            config()->set('filesystems.disks.twitch-irc-commands', [
                'driver' => 'local',
                'root' => app_path('Services/TwitchIrc/Commands'),
                'url' => null,
                'visibility' => 'private',
            ]);


            $config = config()->get('filesystems.disks');
        }
    }
}
