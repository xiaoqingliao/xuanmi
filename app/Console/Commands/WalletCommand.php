<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\MemberWithdraw;
use App\Models\AppConstants;
use App\Models\SiteConfig;
use App\Services\alipay\Transfer;
use App\Services\alipay\TransferOrder;

/**
 * 提现自动发放
 * 将状态为审核通过和发放失败的记录自动发放
 */
class WalletCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提现自动发放，每小时执行一次。';

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
        $config = SiteConfig::where('code', 'site')->first();
        $alipay_appid = '';
        $alipay_privatekey = '';
        $alipay_publickey = '';
        if ($config != null) {
            $alipay_appid = $config->getParam('alipay_appid', '');
            $alipay_privatekey = $config->getParam('alipay_privatekey');
            $alipay_publickey = $config->getParam('alipay_publickey');
        }
        if ($alipay_appid == '' || $alipay_privatekey == '' || $alipay_publickey == '') return;
        $pay = new Transfer([
            'appid' => $alipay_appid,
            'privatekey' => $alipay_privatekey,
            'publickey' => $alipay_publickey,
        ]);

        $start = time();
        while(1) {
            $diff = time() - $start;
            if ($diff >= 3000) break;   //运行时间超过50分钟则退出

            //将失败时间超过1小时的提现，重新设为审核通过
            MemberWithdraw::where('status', AppConstants::FAILED)->where('updated_at', '<=', date('Y-m-d H:i:s', time() - 3600))->update(['status'=>AppConstants::ACCEPTED]);

            //每隔5秒处理1条审核通过提现记录
            $wallet = MemberWithdraw::where('status', AppConstants::ACCEPTED)->where('type', 'alipay')->with('member')->orderBy('id', 'asc')->first();
            if ($wallet == null) {
                sleep(5);
                continue;   //没有要处理的提现则退出
            }
            if ($wallet->member->alipay == '') {    //未设置支付宝
                sleep(5);
                $wallet->status = AppConstants::FAILED;
                $wallet->logs = '支付宝账户未设置';
                $wallet->save();
                continue;
            }
            $wallet->status = AppConstants::SENDING;
            $wallet->account = $wallet->member->alipay;
            $wallet->save();
            
            //发放到用户支付宝
            $order = new TransferOrder([
                'out_biz_no' => uniqid().time().rand(0,99),
                'payee_account' => $wallet->member->alipay,
                'amount' => $wallet->actual,
                'remark' => '会员提现',
            ]);
            $result = $pay->pay($order);
            if ($result != null && $result['code'] == '10000') {
                $wallet->status = AppConstants::SENDED;
                $wallet->logs = json_encode($result, JSON_UNESCAPED_UNICODE);
                $wallet->save();
            } else {
                $wallet->status = AppConstants::FAILED;
                $wallet->logs = json_encode($result, JSON_UNESCAPED_UNICODE);
                $wallet->save();
            }

            unset($wallet);
            unset($result);
            sleep(5);
        }
    }
}
