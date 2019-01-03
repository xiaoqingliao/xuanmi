<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * 会员到期检测
 * 每日0点运行一次，清理到期会员
 */
class MemberGroupCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
    }
}
