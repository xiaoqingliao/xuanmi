<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\SiteConfig;
use App\Models\AdminLog;

/**
 * 店铺参数设置
 */
class SiteConfigController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        
    }

    public function config()
    {
        $category = request('category');
        $categories = [
            'site' => '店铺参数设置',
            'noticeparam' => '财务分成回复消息设置',
            'copyright' => '服务协议设置',
        ];
        if (isset($categories[$category]) == false) {
            $category = 'site';
        }
        $item = SiteConfig::where('code', $category)->first();
        if ($item == null) {
            $site = new SiteConfig();
            $site->title = $categories[$category];
            $site->code = $category;
            $site->params = [];
            $site->save();

            $item = $site;
        }
        
        $values = [
            'setting' => $item,
            'category' => $category,
        ];
        $this->setCurrentMenu('sys:param:' . $category);
        return view('site.' . $category, $values);
    }

    public function postConfig()
    {
        $category = request('category');
        $item = SiteConfig::where('code', $category)->first();
        if ($item == null) {
            return back()->with('message', '保存失败，请稍后重试');
        }
        
        $setting = $item->settings;
        $data = request()->all();
        foreach($data as $key=>$val) {
            if (in_array($key, ['_method', '_token'])) continue;
            $setting[$key] = $val;
        }
        $item->params = $setting;
        $item->save();

        AdminLog::addLog($this->user->id, 'update', 'configs', $item, '更新参数');
        return redirect()->route('sys.site.config', ['category'=>$category])->with('message', '修改成功');
    }

    /**
     * 通知内容模板设置
     */
    public function notice()
    {
        $item = SiteConfig::where('code', 'notice')->first();
        if ($item == null) {
            $site = new SiteConfig();
            $site->title = '通知模板设置';
            $site->code = 'notice';
            $site->params = [];
            $site->save();

            $item = $site;
        }
        
        $types = [
            MessageType::NEWORDER => ['name'=>'新订单', 'desc'=>''],
            MessageType::PAY => ['name'=>'支付成功', 'desc'=>''],
            MessageType::MODIFY => ['name'=>'修改价格', 'desc'=>''],
            MessageType::SHIPPING => ['name'=>'确认发货', 'desc'=>''],
            MessageType::RECEIVED => ['name'=>'确认收货', 'desc'=>''],
            MessageType::CANCEL => ['name'=>'用户取消订单', 'desc'=>''],
            MessageType::SYSCANCEL => ['name'=>'系统取消订单', 'desc'=>''],
            MessageType::SYSRESTORE => ['name'=>'系统恢复订单', 'desc'=>''],
            MessageType::EARNING => ['name'=>'分销收益提成', 'desc'=>''],
            MessageType::WALLET => ['name'=>'提现申请', 'desc'=>''],
            MessageType::WALLET_ACCEPT => ['name'=>'提现放款审核通过', 'desc'=>''],
            MessageType::WALLET_REJECT => ['name'=>'提现申请审核拒绝', 'desc'=>''],
            MessageType::REFUND => ['name'=>'退款已返还', 'desc'=>''],
        ];
        
        $values = [
            'setting' => $item,
            'types' => $types,
        ];
        
        return view('site.notice', $values);
    }
    
    public function postNotice()
    {
        $item = SiteConfig::where('code', 'notice')->first();
        if ($item == null) {
            return back()->with('message', '保存失败，请稍后重试');
        }
        
        $data = request()->all();
        foreach($data as $key=>$val) {
            if (in_array($key, ['_method', '_token'])) continue;
            $setting[$key] = $val;
        }
        $item->params = $setting;
        $item->save();

        AdminLog::addLog($this->user->id, 'update', 'notice', $item, '更新通知模板');
        return redirect()->route('sys.site.notice')->with('message', '修改成功');
    }
}
