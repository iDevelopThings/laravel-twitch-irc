<?php


namespace TwitchIrc\Bot\Message\User;


use TwitchIrc\Bot\Channel\ChannelsHandler;
use TwitchIrc\Bot\Connection\IrcParser;

class ChatUser
{
	use HasPermissions;

	protected ?string $userId = null;

	protected ?string $username = null;

	protected ?string $displayName = null;

	protected bool $mod = false;

	protected ?bool $subscriber = false;

	protected bool $turbo = false;

	protected ?string $userType = null;

	protected ?array $badges = null;

	protected ?string $color = null;

	protected ?array $emotes = null;

	protected ?array $flags = null;

	protected ?string $id = null;

	protected ?string $roomId = null;

	protected ?string $timestamp = null;

	protected ?int $bits = null;
	/**
	 * @var mixed
	 */
	private ?string $channelName = null;

	public function parse($username, array $badgeParts, array $messageParts): ChatUser
	{
		/*ray($badgeParts);

		if ($user = UserHandlers::hasUser($messageParts[3], $username)) {
			return $user;
		}*/

		$badgeParts = array_map(function ($part) {
			$params = explode('=', $part);

			return [
				'key' => $params[0],
				'val' => trim($params[1]) === "" ? null : $params[1],
			];
		}, $badgeParts);

		$userParts = array_column($badgeParts, null, 'key');
		$userParts = array_column($badgeParts, 'val', 'key');

		$this->badges      = IrcParser::parseArrayPart($userParts['badges']);
		$this->bits        = $userParts['bits'] ?? null;
		$this->color       = $userParts['color'];
		$this->username    = $username;
		$this->displayName = $userParts['display-name'];
		$this->emotes      = IrcParser::parseArrayPart($userParts['emotes']);
		$this->flags       = IrcParser::parseArrayPart($userParts['flags']);
		$this->id          = $userParts['id'];
		$this->mod         = boolval($userParts['mod']);
		$this->roomId      = $userParts['room-id'];
		$this->channelName = $messageParts[3];
		$this->subscriber  = boolval($userParts['subscriber']);
		$this->timestamp   = $userParts['tmi-sent-ts'];
		$this->turbo       = boolval($userParts['turbo']);
		$this->userId      = $userParts['user-id'];
		$this->userType    = $userParts['user-type'];

		ChannelsHandler::channel($this->channelName)->users()->put($this);

		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getBadges(): ?array
	{
		return $this->badges;
	}

	/**
	 * @return string|null
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}

	/**
	 * @return string|null
	 */
	public function getDisplayName(): ?string
	{
		return $this->displayName;
	}

	/**
	 * @return string|null
	 */
	public function getUserType(): ?string
	{
		return $this->userType;
	}

	/**
	 * @return string|null
	 */
	public function getColor(): ?string
	{
		return $this->color;
	}

	/**
	 * @return array|null
	 */
	public function getEmotes(): ?array
	{
		return $this->emotes;
	}

	/**
	 * @return array|null
	 */
	public function getFlags(): ?array
	{
		return $this->flags;
	}

	/**
	 * @return string|null
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getRoomId(): ?string
	{
		return $this->roomId;
	}

	/**
	 * @return string|null
	 */
	public function getTimestamp(): ?string
	{
		return $this->timestamp;
	}

	/**
	 * @return int|null
	 */
	public function getBits(): ?int
	{
		return $this->bits;
	}

	/**
	 * @return string|null
	 */
	public function getUserId(): ?string
	{
		return $this->userId;
	}

	/**
	 * @return mixed
	 */
	public function getChannelName(): ?string
	{
		return $this->channelName;
	}

}
