<?php


namespace TwitchIrc\Bot\Command;


use TwitchIrc\Bot\Message\User\ChatUserRoleTypes;

/**
 * Trait HasCommandPermissions
 *
 * @package TwitchIrc\Bot\Command
 * @mixin BaseCommand|ChannelRewardCommand|ChannelStaticBotCommand
 */
trait HasCommandPermissions
{
	/**
	 * Check if the user has at-least one of the permissions listed on this command
	 *
	 * @return bool
	 */
	public function userHasPermissions(): bool
	{
		foreach ($this->chatUser()->permissions() as $permission) {
			if ($permission->in($this->permissions())) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Set an array of Roles that the user must have to use this command
	 *
	 * @return ChatUserRoleTypes[]
	 */
	public function permissions(): array
	{
		return [ChatUserRoleTypes::VIEWER()];
	}
}
