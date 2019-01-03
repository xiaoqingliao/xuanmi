<?php

use Illuminate\Database\Seeder;

use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new Admin();
        $user->name = '系统管理员';
        $user->username = 'root';
        $user->password = bcrypt('123456');
        $user->disabled = false;
        $user->extensions = [];
        $user->permissions = [];
        $user->save();
    }
}
