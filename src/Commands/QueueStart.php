<?php

namespace BwtTeam\LaravelConsole\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class QueueStart extends Command
{
    const OPTION_ALL = 'all';

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:queue:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Queues';

    /**
     * @var array
     */
    protected $queues = [
        'default',
        'price-checking',
        'notification',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\Illuminate\Config\Repository $config)
    {
        $this->config = $config;
        $this->init();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queues = $this->getQueuesByOption($this->option('queue'));
        foreach ($queues as $queue) {
            $this->startQueueWorker($queue);
        }

        return true;
    }

    /**
     *  Initialize the configuration.
     */
    protected function init() {
        $config = $this->config->get('console-commands.queue_start');

        $this->queues = array_key_exists('queues', $config) ? $config['queues'] : [];
    }

    /**
     * @param $option
     * @return array
     */
    protected function getQueuesByOption($option)
    {
        if ($option == self::OPTION_ALL) {
            return $this->queues;
        } else {
            return [$option];
        }
    }


    /**
     * @param string $queue
     * @return bool
     */
    protected function startQueueWorker($queue)
    {
        $cmd = sprintf("php %s queue:work --queue=%s", base_path('artisan'), $queue);

        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd . " > nul", "r"));
        } else {
            exec("nohup " . $cmd . " > /dev/null 2>&1 &");
        }
        $this->info("Queue worker for \"$queue\" started");

        return true;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['queue', null, InputOption::VALUE_OPTIONAL, 'Queue name or "all"', self::OPTION_ALL]
        ];
    }
}
