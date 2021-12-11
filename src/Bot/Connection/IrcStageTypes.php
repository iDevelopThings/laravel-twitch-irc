<?php

namespace TwitchIrc\Bot\Connection;

use TwitchIrc\Bot\Channel\Stages\JoinRoomStageHandler;
use TwitchIrc\Bot\Channel\Stages\MessageStageHandler;
use TwitchIrc\Bot\Channel\Stages\PingStageHandler;
use TwitchIrc\Bot\Channel\Stages\RoomStateStageHandler;
use BenSampo\Enum\Enum;

/**
 * @method static static ignoreStage()
 * @method static static connectingStage()
 * @method static static joiningStage()
 * @method static static namesListStage()
 * @method static static badgeInfo()
 * @method static static pingStage()
 * @method static static notice()
 * @method static static messageStage()
 * @method static static userState()
 * @method static static userLeaveStage()
 * @method static static userJoinStage()
 */
final class IrcStageTypes extends Enum
{
	const IGNORE_STAGE          = 'IGNORE_STAGE';
	const PING_STAGE            = 'PING';
	const ACK_STAGE             = 'ACK';
	const ROOMSTATE_STAGE       = 'ROOMSTATE';
	const GLOBALUSERSTATE_STAGE = 'GLOBALUSERSTATE';
	const NOTICE_STAGE          = 'NOTICE';
	const MESSAGE_STAGE         = 'PRIVMSG';
	const USERSTATE_STAGE       = 'USERSTATE';
	const PART_STAGE            = 'PART';
	const JOIN_STAGE            = 'JOIN';


	/**
	 * Converts our stage to a fully qualified class name
	 *
	 * @param $stage
	 *
	 * @return string[]|null
	 */
	public static function classForStage($stage): ?array
	{
		$stageHandlers = [
			'ROOMSTATE' => [
				JoinRoomStageHandler::class,
				RoomStateStageHandler::class,
			],
			'PRIVMSG'   => [MessageStageHandler::class],
		];

		return $stageHandlers[$stage->value] ?? null;
	}
}
