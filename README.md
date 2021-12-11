# laravel-twitch-irc

[![Latest Version on Packagist](https://img.shields.io/packagist/v/idevelopthings/laravel-twitch-irc.svg?style=flat-square)](https://packagist.org/packages/idevelopthings/laravel-twitch-irc)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/idevelopthings/laravel-twitch-irc/run-tests?label=tests)](https://github.com/idevelopthings/laravel-twitch-irc/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/idevelopthings/laravel-twitch-irc/Check%20&%20fix%20styling?label=code%20style)](https://github.com/idevelopthings/laravel-twitch-irc/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/idevelopthings/laravel-twitch-irc.svg?style=flat-square)](https://packagist.org/packages/idevelopthings/laravel-twitch-irc)

Twitch IRC Bot for laravel applications. This ships with the ability to create custom commands for your bot, with aliases, cooldowns, ability to connecto to multiple channels, etc.

## Installation

You can install the package via composer:

```bash
composer require idevelopthings/laravel-twitch-irc
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="twitch-irc-config"
```

## Usage

Publish the config file first, add to your .env: 

```shell
TWITCH_BOT_USERNAME="your bots username"
TWITCH_BOT_ACCESS_TOKEN="your bots token"
```
You can obtain a token here: https://twitchapps.com/tmi/
When using the token, don't include the "oauth:" prefix.

Now we can create a command, the default location is app/Services/TwitchIrc/Commands, you can change this path in the config file

```php
<?php
namespace App\Services\TwitchIrc\Commands;

use TwitchIrc\Bot\Command\BaseCommand;

class HelloWorldCommand extends BaseCommand
{
    public function identifier(): string {
        return "hello";
    }

    public function aliases(): array {
        return [
            'hi',
            'hw',
            'helloworld',
        ];
    }

    public function description(): string {
        return "Responds with hello world.";
    }

    public function handle(): void {
        $this->reply('Hello world.');
    }
}
```

Now you can run "php artisan twitchbot" to start the bot.
We can also use "php artisan twitchbot --stop" to stop the bot, which is useful for CI setups.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sam Parton](https://github.com/iDevelopThings)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
