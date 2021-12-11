<?php


namespace TwitchIrc\Bot\Channel;


use TwitchIrc\Bot\Message\User\ChatUser;

class ChannelUsers
{
	/**
	 * All users who have sent a message in the channel
	 *
	 * @var ChatUser[]
	 */
	protected array $users = [];

	public function has($username): bool
	{
		return $this->get(strtolower($username)) !== null;
	}

	public function get($username): ?ChatUser
	{
		return $this->users[strtolower($username)] ?? null;
	}

	public function put(ChatUser $user)
	{
		$this->users[strtolower($user->getUsername())] = $user;
	}

}
