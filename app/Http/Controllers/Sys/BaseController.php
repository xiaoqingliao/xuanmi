<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Category;

class BaseController extends Controller
{
    protected $user = null;
    protected $isMobile = false;
    public function __construct() {
        parent::__construct();
        $this->middleware(function($request, $next) {
            $this->user = auth('sys')->user();
            
            if ($this->user && $this->user->disabled) {
                auth('sys')->logout();
                return redirect('/sys/login');
            }
            view()->share('page_user', $this->user);
            return $next($request);
        });
        
        //$page_categories = Category::getList(Category::TYPE_ARTICLE);
        //$this->setPermissions($page_categories);
        view()->share('page_title', '炫秘小程序平台管理');
        view()->share('page_user', $this->user);
        view()->share('page_start', microtime(true));
        //view()->share('page_categories', $page_categories);
        $this->setCurrentMenu('');
    }
    
    protected function setCurrentMenu($menu)
    {
        view()->share('page_menu', $menu);
    }

    protected function setPermissions($categories)
    {
        $permissions = config('auth.permissions');
        foreach($categories as $_category) {
            $permissions['content']['items']['article_' . $_category->id] = $_category->title;
        }
        config(['auth.permissions'=>$permissions]);
    }

    /**
     * 检测浏览器是否为手机
     */
    private function detectBrowser()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (strpos($agent, 'iphone')) {
            $this->isMobile = true;
        } else if (strpos($agent, 'android')) {
            $this->isMobile = true;
        }
    }
}
