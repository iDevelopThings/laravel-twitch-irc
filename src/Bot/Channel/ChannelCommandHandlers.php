<?php


namespace TwitchIrc\Bot\Channel;


use App\Services\TwitchIrc\Commands\HelloWorldCommand;
use Illuminate\Support\Facades\Storage;
use TwitchIrc\Bot\Command\BaseCommand;
use TwitchIrc\Bot\Output;
use Illuminate\Support\Str;

class ChannelCommandHandlers
{

    public array $rewards = [];
    /**
     * @var BaseCommand[]
     */
    protected array $commands = [];

    private array $excludedClasses = [

    ];

    /**
     * Return an array of unique commands
     * (excludes commands that are added as aliases)
     *
     * @return array
     */
    public function uniqueCommands(): array
    {
        return collect($this->commands)
            ->unique(fn($command) => $command->identifier())
            ->toArray();
    }

    /**
     * Reset and load the commands again
     *
     * @param $channelName
     */
    public function reload($channelName)
    {
        $this->commands = [];
        $this->load($channelName);
    }

    /**
     * Load all command types
     *
     * @param      $channelName
     */
    public function load($channelName)
    {
        $this->loadDefaultCommands();

        (new Output())->line('LOADED COMMANDS FOR: ' . $channelName);

        // if (!$logLoaded) {
        //     return;
        // }

        // $loadedCommands = [];
        // foreach ($this->commands as $identifier => $command) {
        //     $loadedCommands[] = [$identifier, get_class($command)];
        // }
    }

    /**
     * Load all default commands that have been specified in the codebase
     */
    public function loadDefaultCommands()
    {
        $commandClasses = Storage::disk('twitch-irc-commands')->allFiles();

        $fff            = resolve(HelloWorldCommand::class);
        $commandClasses = collect($commandClasses)
            ->map(fn($class) => $this->formatDefaultCommandClass($class))
            ->filter(function ($commandClass) {
                // $class = new ReflectionClass($commandClass);

                if (in_array($commandClass, $this->excludedClasses)) {
                    return false;
                }

                // if ($class->isSubclassOf(ChannelRewardCommand::class)) {
                // 	return false;
                // }
                // if ($class->isSubclassOf(ChannelStaticBotCommand::class)) {
                // 	return false;
                // }

                return true;
            })
            ->map(function ($commandClass) {
                $command = new $commandClass();

                return [
                    'identifier' => $command->identifier(),
                    'class'      => $command,
                    'aliases'    => $command->aliases(),
                ];
            });


        $this->loadCommandsArray($commandClasses);
    }

    private function formatDefaultCommandClass($class): string
    {
        $baseNamespace = config('twitch-irc.commands.namespace');

        return $baseNamespace . Str::replaceLast('.php', '', str_replace('/', '\\', $class));
    }

    /**
     * Load the commands
     *
     * @param $commands
     */
    private function loadCommandsArray($commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command['identifier'], $command['classReference'] ?? $command['class'], $command['aliases']);
        }
    }

    /**
     * Adds a command that is following a specific array structure
     * and add any aliases of this command also if defined.
     *
     * @param       $identifier
     * @param       $class
     * @param array $aliases
     */
    public function addCommand($identifier, $class, array $aliases = [])
    {
        if (is_string($class)) {
            $class = new $class();
        }

        $this->commands[$identifier] = $class;

        (new Output())->line('[COMMAND LOADER] Loaded command !' . $identifier);

        if (!empty($aliases)) {
            foreach ($aliases as $alias) {
                $this->commands[$alias] = $class;
                (new Output())->line('[COMMAND LOADER] Loaded command alias of !' . $identifier . ' as !' . $alias);
            }
        }
    }

    /**
     * Remove a command that is loaded & it's aliases
     *
     * @param $identifier
     */
    public function removeCommand($identifier)
    {
        $command = $this->getCommand($identifier);

        if ($command === null) {
            return;
        }

        foreach ($command->aliases() as $alias) {
            unset($this->commands[$alias]);
        }

        unset($this->commands[$identifier]);
    }

    public function getCommand(string $identifier): ?BaseCommand
    {
        return $this->commands[$identifier] ?? null;
    }

    /**
     * Is this command loaded already?
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCommand(string $identifier): bool
    {
        return isset($this->commands[$identifier]);
    }
}
