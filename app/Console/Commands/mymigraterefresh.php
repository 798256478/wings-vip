<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mymigraterefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mymigraterefresh {db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bulid database command migrate extension';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Confirm refresh the database:'.$GLOBALS['user_domain'].'? [y|n]')) {
            $this->call('migrate:reset', ['--force' => '']);
            $this->call('migrate', ['--force' => '','--path'=>'database/migrations']);
            $this->call('migrate', ['--force' => '','--path'=>'database/migrations/health']);
            $this->call('db:seed', ['--force' => '']);
        }
    }
}
