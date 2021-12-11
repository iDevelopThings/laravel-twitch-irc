<?php

namespace TwitchIrc\Bot\Message\User;

/**
 * Trait HasPermissions
 *
 * @package TwitchIrc\Bot\Message\User
 * @mixin ChatUser
 */
trait HasPermissions
{
    /**
     * Is this user the streamer?
     *
     * @return bool
     */
    public function isStreamer(): bool
    {
        return $this->hasPermission(ChatUserRoleTypes::STREAMER());
    }

    /**
     * Check if the user has a specific type from {@see ChatUserRoleTypes}
     *
     * @param ChatUserRoleTypes $permission
     *
     * @return bool
     */
    public function hasPermission(ChatUserRoleTypes $permission): bool
    {
        return $permission->in($this->permissions());
    }

    /**
     * @return ChatUserRoleTypes[]
     */
    public function permissions(): array
    {
        $permissions = [ChatUserRoleTypes::VIEWER()];

        if ($this->badges === null) {
            return $permissions;
        }

        foreach ($this->badges as $badge) {
            if (str_contains($badge, 'turbo')) {
                $permissions[] = ChatUserRoleTypes::TURBO();

                continue;
            }
            if (str_contains($badge, 'vip')) {
                $permissions[] = ChatUserRoleTypes::VIP();

                continue;
            }
            if (str_contains($badge, 'subscriber')) {
                $permissions[] = ChatUserRoleTypes::SUBSCRIBER();

                continue;
            }
            if (str_contains($badge, 'moderator')) {
                $permissions[] = ChatUserRoleTypes::MODERATOR();

                continue;
            }
            if (str_contains($badge, 'broadcaster')) {
                $permissions[] = ChatUserRoleTypes::STREAMER();

                continue;
            }
        }

        return $permissions;
    }

    /**
     * Is the message sender the streamer or a mod?
     *
     * @return bool
     */
    public function isModOrStreamer(): bool
    {
        return ($this->hasPermission(ChatUserRoleTypes::STREAMER()) || $this->hasPermission(ChatUserRoleTypes::MODERATOR()));
    }
}
