<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mymigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mymigrate {db}';

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
        $this->info('DB:'.$GLOBALS['user_domain']);
        $this->call('migrate', ['--force']);
        $this->call('migrate', ['--force','--path'=>'database/migrations/health']);
    }
}
