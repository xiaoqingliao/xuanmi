<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\AdminLog;
use App\Models\Category;
use App\Models\Article;

class ArticleController extends BaseController
{
    private $current_typeid = 0;
    private $current_title = '';
    public function __construct()
    {
        parent::__construct();
        
        $typeid = intval(request('type'));
        $types = Category::getArticleTypes();
        if (isset($types[$typeid]) == false) {
            $type_ids = array_keys($types);
            if (empty($type_ids)) {
                abort(404);
            }
            $typeid = $type_ids[0];
        }

        $categories = Category::getList($typeid);
        
        $this->current_typeid = $typeid;
        $this->current_title = $types[$typeid];
        view()->share('title', $this->current_title);
        view()->share('typeid', $this->current_typeid);
        view()->share('categories', $categories);
    }
    
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = 20;

        $filters = [
            'key' => request('key'),
            'status' => intval(request('status')),
        ];
        $cursor = Article::where('type', $this->current_typeid);
        if ($filters['key'] != '') {
            $cursor->where('title', 'like', '%'. $filters['key'] .'%');
        }
        if ($filters['status'] > 0) {
            $cursor->where('status', $filters['status']);
        }
        $articles = $cursor->with('member')->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);

        $values = [
            'articles' => $articles,
            'filters' => $filters,
            'start' => ($page - 1) * $pagesize,
        ];
        AdminLog::addLog($this->user->id, 'list', 'articles', null, '查看'. $this->current_title .'列表');
        return view('article.index', $values);
    }

    public function destroy($id)
    {
        $article = Article::find($id);
        if ($article != null) {
            $article->delete();

            AdminLog::addLog($this->user->id, 'delete', 'articles', $article);
            session()->flash('message', '删除成功');
        }
        return response()->json(['error'=>false]);
    }
}
