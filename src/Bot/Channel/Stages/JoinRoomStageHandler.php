<?php


namespace TwitchIrc\Bot\Channel\Stages;


class JoinRoomStageHandler extends IrcStageHandler
{

	/**
	 * Handle the IRC Stage
	 *
	 * @param string $payload
	 */
	public function handle(string $payload)
	{
		$this->channel()->whenJoinedSuccessfully();
	}
}
