<?php

use Illuminate\Database\Seeder;
use App\Services\CardService;
use App\Models\User;
use App\Models\Card;

class MySqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['login_name' => '001', 'display_name' => 'Zhang', 'password' => '123', 'roles' => 'admin' ]);
        User::create(['login_name' => '002', 'display_name' => 'Wang', 'password' => '123', 'roles' => 'admin' ]);
        User::create(['login_name' => '003', 'display_name' => 'Li', 'password' => '123', 'roles' => 'admin' ]);
        User::create(['login_name' => '004', 'display_name' => 'Zhao', 'password' => '123', 'roles' => 'admin' ]);
        
        Card::create(['card_code' => $this->get_new_code(), 'openid' => $this->get_new_code(), 'mobile' => '13803854301', 'nickname' => 'User1', 'sex' => 0]);
        Card::create(['card_code' => $this->get_new_code(), 'openid' => $this->get_new_code(), 'mobile' => '13803854302', 'nickname' => 'User2', 'sex' => 0]);
        Card::create(['card_code' => $this->get_new_code(), 'openid' => $this->get_new_code(), 'mobile' => '13803854303', 'nickname' => 'User3', 'sex' => 0]);
        Card::create(['card_code' => $this->get_new_code(), 'openid' => $this->get_new_code(), 'mobile' => '13803854304', 'nickname' => 'User4', 'sex' => 0]);
        
    }
    
    public function get_new_code(){
        $day_int = (int)(date('z',time()));
        $day_str = sprintf("%03d", $day_int);
        if (strstr($day_str,'4')){
            $day_str = str_replace('4', '5', $day_str);
        }

        $number_int = mt_rand(0,99999);
        $number_str = sprintf("%05d", $number_int);
        if (strstr($number_str,'4')){
            $r_int = mt_rand(6,9);
            $r_str = (string)$r_int;
            $number_str = str_replace('4', $r_str, $number_str);
        }

        $code_str = $day_str . $number_str;

        // $this->db->where(['card_code' => $code_str]);
        // $c = $this->db->count_all_results('card');

        // if ($c > 0)
        //     return $this->get_new_code();
        // else
        return $code_str;
    }
}