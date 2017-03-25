<?php

namespace BwtTeam\LaravelConsole\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class AppCleaning
 * @package BwtTeam\LaravelConsole\Commands
 *
 * @example php artisan app:cleaning db,files
 */
class AppCleaning extends Command
{
    const OPTION_ALL = 'all';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:cleaning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning application';

    /** @var \Illuminate\Config\Repository */
    protected $config;

    /**
     * @var array
     */
    protected $sections = [
        'files' => 'storage',
        'db' => 'database',
    ];

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
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
        $inputSections = explode(',', $this->argument('section'));
        $sections = $this->getSectionsByArgument($inputSections);
        if (count($sections) > 0 && $this->confirm('Do you really want to cleaning application?')) {
            foreach ($sections as $section) {
                call_user_func([$this, "clean" . ucfirst($section)]);
            }
        }

        return true;
    }

    /**
     * @param $argument
     *
     * @return array
     * @internal param $option
     *
     */
    protected function getSectionsByArgument($argument)
    {
        if ($argument == self::OPTION_ALL) {
            return $this->sections;
        } elseif (is_array($argument)) {
            return call_user_func_array('array_merge', array_map([$this, 'getSectionsByArgument'], $argument));
        } elseif (array_key_exists($argument, $this->sections)) {
            return [$argument => $this->sections[$argument]];
        }

        $this->error("Section with name \"$argument\" does not registered");
        return [];
    }

    /**
     * @return bool
     */
    protected function cleanDatabase()
    {
        $config = $this->config->get('console-commands.app_cleaning.db');
        \DB::statement("SET FOREIGN_KEY_CHECKS=0");

        $tables = \DB::select('SHOW TABLES');
        $excludeTables = array_merge([$this->config->get('database.migrations')], array_get($config, 'exclude', []));

        foreach ($tables as $table) {
            $table_array = get_object_vars($table);
            $tableName = $table_array[key($table_array)];
            if (in_array($tableName, $excludeTables)) {
                \DB::table($tableName)->truncate();
            }
        }

        \DB::statement("SET FOREIGN_KEY_CHECKS=1");
        $this->info("Database successfully cleared");

        return true;
    }

    /**
     * @return bool
     */
    protected function cleanStorage()
    {
        $config = $this->config->get('console-commands.app_cleaning.files');
        $disks = array_get($config, 'disks', []);
        $status = true;
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        foreach ($disks as $disk) {
            foreach ($disk->allDirectories() as $directory) {
                $status = $status && $disk->deleteDirectory($directory);
            }
            foreach ($disk->allFiles() as $file) {
                $status = $status && $disk->delete($file);
            }
        }

        if ($status) {
            $this->info("Files have been successfully removed");
        } else {
            $this->error("An error occurred while trying to delete files");
        }

        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['section', InputArgument::REQUIRED, 'Section name or "all"']
        ];
    }
}
