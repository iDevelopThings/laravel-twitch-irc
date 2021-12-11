<?php

return [

    /**
     * You can replace this with your own implementation
     * This class allows you to prepare credentials/channels to connect to etc
     *
     * For example, if we need to connect to twitch channels from our database. We
     * could do that here and register the channels to connect to
     *
     * If our bot user credentials are stored in the database for example, we can also do that here.
     */
    'initiator' => \TwitchIrc\Initiator\TwitchIrcInitiator::class,

    'bot_user_provider' => \TwitchIrc\Bot\BotUser\BotUserProvider::class,

    'commands' => [
        /**
         * In your config/filesystems.php -> 'disks' array
         * Add:
         * 'twitch_bot' => [
         *      'driver'     => 'local',
         *      'root'       => app_path('Services/TwitchIrc/Commands'),
         *      'url'        => null,
         *      'visibility' => 'private',
         * ],
         *
         * This will allow the bot to use laravels storage provider to load your command classes dynamically.
         */
        'filesystem_disk' => 'commands',
        /**
         * This is the base namespace of your commands location
         * Make sure this ends with "\\". We'll check the filesystem_disk location and find all file names
         * recursively, we'll them format the name of the class to be for example:
         * Instead of "commands\HelloWorldCommand.php" "\\App\\Services\\TwitchIrc\\Commands\\commands\HelloWorldCommand::class"
         */
        'namespace'       => 'App\\Services\\TwitchIrc\\Commands\\',
    ],


    /**
     * If you have no custom logic for the main bot user which joins
     * channels irc then you can just add your credentials to the
     * .env file, and we'll load them from here.
     */
    'bot_credentials'   => [
        'username'      => env('TWITCH_BOT_USERNAME', null),
        'access_token'  => env('TWITCH_BOT_ACCESS_TOKEN', null),
        'refresh_token' => env('TWITCH_BOT_REFRESH_TOKEN', null),
    ],

    /**
     * The usernames of any channels you wish to join
     *
     * If you need to load this list from the database dynamically, rather than hard-coding here.
     * You can create a custom initiator, see the above "initiator" config.
     *
     * You can extend this {@see TwitchIrcInitiator} class and provide your own setup function.
     *
     * which for example, would load all channels from the db and then add them to the service.
     * You can see an example of doing this inside {@see TwitchIrcInitiator}
     */
    'channels'          => [
        //'some-channel-username-to-join',
    ],


];
