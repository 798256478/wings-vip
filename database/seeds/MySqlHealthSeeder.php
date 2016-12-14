<?php

use Illuminate\Database\Seeder;
use App\Models\Health\Progress;
use App\Models\Health\GoodCodeProgress;
use App\Models\Health\Experiment;
use App\Models\User;
class MySqlHealthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['login_name' => '000000', 'display_name' => 'admin', 'password' => '111111', 'roles' => 'admin' ]);
        User::create(['login_name' => '130822', 'display_name' => 'cashier', 'password' => '111111', 'roles' => 'cashier' ]);
        Progress::create(['name'=>'样品接收']);
        Progress::create(['name'=>'样品质检']);
        Progress::create(['name'=>'样品DNA处理']);
        Progress::create(['name'=>'样本检测']);
        Progress::create(['name'=>'数据分析']);
        Progress::create(['name'=>'检测报告']);
        Progress::create(['name'=>'废弃','isshow'=>0]);
        Progress::create(['name'=>'风险评估','isshow'=>0]);

    }

}
