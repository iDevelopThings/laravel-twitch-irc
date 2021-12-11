<?php


namespace TwitchIrc\Bot\Channel\Stages;


use Amp\Websocket\Client\Connection;
use TwitchIrc\Bot\Channel\Channel;
use TwitchIrc\Bot\Connection\BotConnection;

abstract class IrcStageHandler
{

	/**
	 * The channel we are currently handing this stage for
	 *
	 * @var Channel $channel
	 */
	public Channel $channel;

	public function __construct(Channel $channel)
	{
		$this->channel = $channel;
	}

	/*public function processStage(string $payload): Promise
	{
		$deferred = new Deferred;

		$deferred->resolve($this->handle($payload));

		return $deferred->promise();
	}*/

	/**
	 * Handle the IRC Stage
	 *
	 * @param string $payload
	 */
	public abstract function handle(string $payload);

	/**
	 * @return Channel
	 */
	public function channel(): Channel
	{
		return $this->channel;
	}

	/**
	 * @return Connection
	 */
	public function connection(): Connection
	{
		return BotConnection::getInstance()->connection();
	}

}
