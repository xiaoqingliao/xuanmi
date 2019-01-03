<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AppConstants;
use App\Models\CompanyFinanceLog;
use App\Models\CompanyFinanceStat;
use App\Models\MemberProxyApply;
use App\Models\MemberAccountLog;
use App\Models\MemberWithdraw;
use App\Models\Member;
use App\Models\Order;

class HomeController extends BaseController
{
    public function home()
    {
        $finance_stat = new CompanyFinanceStat();

        $week_dates =  [];
        for($i=6; $i>=0; $i--) {
            $week_dates[] = date('Y-m-d', strtotime('-'. $i .' day'));
        }

        $week_proxy = [];
        $week_members = [];
        $week_logins = [];
        $week_renews = [];
        foreach($week_dates as $date)
        {
            $_start = $date . ' 00:00:00';
            $_end = $date . ' 23:59:59';
            $week_proxy[] = MemberProxyApply::where('status', AppConstants::ACCEPTED)->where('updated_at', '>=', $_start)->where('updated_at', '<=', $_end)->count();
            $week_members[] = Order::where('type', Order::TYPE_REG)->whereIn('status', [Order::STATUS_PAYED, Order::STATUS_FINISHED])->where('created_at', '>=', $_start)->where('created_at', '<=', $_end)->count();
            $week_logins[] = Member::where('group_id', '>', 0)->where('updated_at', '>=', $_start)->where('updated_at', '<=', $_end)->count();
            $week_renews[] = Order::where('type', Order::TYPE_RENEW)->whereIn('status', [Order::STATUS_PAYED, Order::STATUS_FINISHED])->where('created_at', '>=', $_start)->where('created_at', '<=', $_end)->count();
        }

        $today_reg = $week_members[6];
        $today_renew = $week_renews[6];
        $today_proxy = $week_proxy[6];
        $seven_proxy = array_sum($week_proxy);
        $seven_reg = array_sum($week_members);
        $seven_renew = array_sum($week_renews);

        $proxy_count = MemberProxyApply::where('status', AppConstants::PENDING)->count();
        $withdraw_count = MemberWithdraw::where('status', AppConstants::PENDING)->count();

        $proxy_list = MemberProxyApply::where('status', AppConstants::PENDING)->with('member', 'group')->orderBy('id', 'desc')->take(10)->get();
        $withdraw_list = MemberWithdraw::where('status', AppConstants::PENDING)->with('member')->orderBy('id', 'desc')->take(10)->get();
        $reg_list = Order::where('type', Order::TYPE_REG)->whereIn('status', [Order::STATUS_PAYED, Order::STATUS_FINISHED])->with('member')->orderBy('id', 'desc')->take(10)->get();

        $values = [
            'finance_stat' => $finance_stat,
            'proxy_count' => $proxy_count,
            'withdraw_count' => $withdraw_count,
            'proxy_list' => $proxy_list,
            'withdraw_list' => $withdraw_list,
            'reg_list' => $reg_list,
            'today_reg' => $today_reg,
            'today_renew' => $today_renew,
            'today_proxy' => $today_proxy,
            'seven_proxy' => $seven_proxy,
            'seven_reg' => $seven_reg,
            'seven_renew' => $seven_renew,
            'chart_options' => [
                'dates' => $week_dates,
                'members' => $week_members,
                'proxy' => $week_proxy,
                'logins' => $week_logins,
                'renews' => $week_renews,
            ],
        ];

        return view('home.home', $values);
    }

    public function login()
    {
        $redirect = request('redirect');
        $values = [
            'redirect' => $redirect,
            'failed' => '',
        ];
        return view('home.login', $values);
    }

    public function postLogin()
    {
        $redirect = request('redirect');
        $username = request('username');
        $password = request('password');

        if (auth('sys')->attempt(['username'=>$username, 'password'=>$password, 'disabled'=>false], false)) {
            if ($redirect == '' || $redirect == '/sys/login') {
                $redirect = '/sys';
            }
            return redirect($redirect);
        }

        $values = [
            'redirect' => $redirect,
            'failed' => '用户名或密码错误',
        ];
        return view('home.login', $values);
    }

    public function password()
    {
        return view('home.password');
    }

    public function postPassword()
    {
        $pwd = request('pwd');
        $old_pwd = request('old_pwd');
        $user = auth('sys')->user();

        if (\Hash::check($old_pwd, $user->password) == false)
        {
            return back()->with('message', '旧密码错误');
        }

        $user->password = bcrypt($pwd);
        $user->save();

        return back()->with('message', '修改成功');
    }

    public function logout()
    {
        auth('sys')->logout();
        return redirect('/sys/login');
    }
}
