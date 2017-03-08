<?php

namespace BwtTeam\LaravelConsole\Commands;

use Illuminate\Console\Command;

class ComposerUpdate extends Command
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update {--env= : Environment}'; //@todo: description "Рабочее окружение"

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description'; //@todo: description "Выполняет заданные команды только в определенном рабочем окружении"

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     *
     * @return void
     */
    public function __construct(\Illuminate\Config\Repository $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $env = $this->option('env') !== null ? $this->option('env') : $this->laravel->environment();
        $config = $this->config->get('console-commands.composer_update');

        if(array_key_exists($env, $config)) {
            foreach($config[$env] as $command) {
                $this->info("execute \"php artisan $command\""); //@todo: description "выполняется команда 'php artisan ...'"
                $this->call($command);
            }
        }
        $this->info('Command successfully completed!'); //@todo: description "Команда успешно завершена!"
    }
}
