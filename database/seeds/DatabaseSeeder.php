<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('MySqlSeeder');
        $this->call('MongoDBSeeder');
        $this->call('MongoDBHealthSeeder');
        $this->call('MySqlHealthSeeder');
        if($GLOBALS['user_domain']=='jintai'){
             $this->call('jintaiSeeder');
           
        }
        else {
             $this->call('defaultSeeder');
        }
           
    }
}
