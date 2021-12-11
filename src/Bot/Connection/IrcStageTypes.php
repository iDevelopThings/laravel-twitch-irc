<?php

namespace TwitchIrc\Bot\Connection;

use BenSampo\Enum\Enum;
use TwitchIrc\Bot\Channel\Stages\JoinRoomStageHandler;
use TwitchIrc\Bot\Channel\Stages\MessageStageHandler;
use TwitchIrc\Bot\Channel\Stages\RoomStateStageHandler;

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
    public const IGNORE_STAGE = 'IGNORE_STAGE';
    public const PING_STAGE = 'PING';
    public const ACK_STAGE = 'ACK';
    public const ROOMSTATE_STAGE = 'ROOMSTATE';
    public const GLOBALUSERSTATE_STAGE = 'GLOBALUSERSTATE';
    public const NOTICE_STAGE = 'NOTICE';
    public const MESSAGE_STAGE = 'PRIVMSG';
    public const USERSTATE_STAGE = 'USERSTATE';
    public const PART_STAGE = 'PART';
    public const JOIN_STAGE = 'JOIN';

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
            'PRIVMSG' => [MessageStageHandler::class],
        ];

        return $stageHandlers[$stage->value] ?? null;
    }
}
