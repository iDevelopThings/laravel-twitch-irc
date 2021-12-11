<?php

namespace TwitchIrc\Bot\Command;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Trait HasCommandCooldown
 *
 * @package TwitchIrc\Bot\Command
 * @mixin BaseCommand
 */
trait HasCommandCooldown
{
    /**
     * Add the cache entry for managing command cooldown
     *
     * @throws Exception
     */
    public function startCooldown()
    {
        $finishAt = now()->addSeconds($this->cooldownSeconds());

        cache()->put(
            $this->cooldownCacheKey(),
            $finishAt,
            $finishAt
        );
    }

    /**
     * After using this command, how long should the command be on cooldown for?
     *
     * @return int
     */
    public function cooldownSeconds(): int
    {
        return 0;
    }

    /**
     * The key for the cooldown cache
     *
     * @return string
     */
    public function cooldownCacheKey(): string
    {
        return $this->identifier() . ':' . Str::camel($this->channel()->username) . ':cooldown';
    }

    /**
     * Get the time remaining on this commands cooldown
     *
     * @return string
     */
    public function cooldownTimeRemaining(): string
    {
        if (! $this->isOnCooldown()) {
            return 'none';
        }

        $time = Carbon::parse(cache()->get($this->cooldownCacheKey()));

        return $time->diffForHumans(['syntax' => CarbonInterface::DIFF_ABSOLUTE]);
    }

    /**
     * Check if the command is on cooldown
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isOnCooldown(): bool
    {
        if ($this->chatUser()->isModOrStreamer()) {
            return false;
        }

        return cache()->has($this->cooldownCacheKey());
    }
}
