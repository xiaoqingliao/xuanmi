<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MemberGroup;
use App\Models\Member;
use App\Models\Order;
use App\Repo\OrderRepo;
/**
 * 新用户注册分成
 */
class RegMoneyDivideCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:reg';

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
     * 用户注册成功支付后，自己的上二级有分销收入
     * @return mixed
     */
    public function handle()
    {
        /*$member = new Member();
        $member->openid = uniqid();
        $member->username = '13455667788';
        $member->phone = '13455667788';
        $member->nickname = '34234';
        $member->name = '233423';
        $member->group_id = 1;
        $member->parent_id = 2;
        $member->parent_path = '1,2';
        $member->company = '';
        $member->duty = '';
        $member->phone = '';
        $member->wechat = '';
        $member->summary = '';
        $member->userinfo = [];
        $member->extensions = [];
        $member->logged = 1;
        $member->proxy_first_time = $member->freshTimestamp()->timestamp;
        $member->proxy_start_time = $member->proxy_first_time;
        $member->proxy_end_time = $member->freshTimestamp()->addYear()->timestamp;
        $member->probation = false;
        $member->save();*/

        /*$member = Member::find(3);
        $order = new Order();
        $order->member_id = $member->id;
        $order->merchant_id = 0;
        $order->sn = Order::buildSn();
        $order->type = Order::TYPE_REG;
        $order->title = '会员注册';
        $order->price = config('site.reg_price', 198);
        $order->balance = 0;
        $order->online_balance = $order->price;
        $order->status = Order::STATUS_NEW;
        $order->content = [];
        $order->name = $member->name;
        $order->phone = $member->phone;
        $order->rebate = [];
        $order->pay_time = 0;
        $order->cancel_time = 0;
        $order->pay_type = '';
        $order->out_trade_no = '';
        $order->remark = '';
        $order->member_remark = '';
        $order->extensions = [];
        $order->save();

        $repo = new OrderRepo();
        $repo->payed($order, '3423423424');*/
    }
}
