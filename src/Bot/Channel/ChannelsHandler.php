<?php


namespace TwitchIrc\Bot\Channel;

use Amp\Websocket\ClosedException;
use TwitchIrc\Bot\Output;

/**
 * Class ChannelsHandler
 *
 * Channels handler will be the in-memory store/manager for each
 * twitch channel the bot is running in.
 *
 * When we connect to a channel, we will process everything from a {@see Channel}
 * We then store all channels/get them from this class.
 *
 * @package TwitchIrc\Bot\Channel
 */
class ChannelsHandler
{

    const RUNNING_ON_CHANNELS = 'twitchbot:connected-channels';

    /**
     * All channels that we are currently connected to with the bot
     *
     * @var Channel[] $channels
     */
    public static array $channels = [];

    /**
     * Prepare all twitch channels and send join requests
     *
     * @param ChannelProviderContract[] $channels
     *
     * @throws ClosedException
     */
    public static function connectChannels(array $channels)
    {
        foreach ($channels as $channel) {
            self::$channels[$channel->getUsername()] = new Channel($channel);
            self::$channels[$channel->getUsername()]->join();

            (new Output())->line('');
            (new Output())->line('Sent join command for channel: ' . $channel->getUsername());
            (new Output())->line('');
        }

        /**
         * Clear the running channels cache and store all usernames
         * of channels that have the bot running in their chat.
         */
        cache()->forget(self::RUNNING_ON_CHANNELS);
        cache()->forever(self::RUNNING_ON_CHANNELS, array_keys(self::$channels));
    }

    /**
     * Get a channel handler by username
     *
     * @param string $channel
     *
     * @return Channel|null
     */
    public static function channel(string $channel): ?Channel
    {
        $channel = ltrim($channel, '#');

        return self::$channels[$channel] ?? null;
    }

    /**
     * Checks if the cache contains x username. If it does, the bot is connected to their chat.
     *
     * @param $username
     *
     * @return bool
     */
    public static function isRunningOnChannel($username): bool
    {
        return in_array($username, cache()->get(self::RUNNING_ON_CHANNELS));
    }

}
