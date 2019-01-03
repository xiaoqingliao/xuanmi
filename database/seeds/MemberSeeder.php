<?php

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0; $i<20; $i++) {
            $m = new Member();
            $m->openid = md5(uniqid() . time());
            $m->nickname = uniqid();
            $m->lat = '';
            $m->lng = '';
            $m->name = '';
            $m->save();
        }
    }
}
