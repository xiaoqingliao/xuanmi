<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Http\Controllers\Sys;

use App\Models\AdminLog;
use App\Models\Category;

/**
 * 分类栏目管理
 */
class CategoryController extends BaseController
{
    private $current_type_title = '';
    private $current_type = 0;
    public function __construct()
    {
        parent::__construct();

        $types = Category::getTypes();
        $this->current_type = intval(request('type'));
        if (isset($types[$this->current_type]) == false) {
            $this->current_type = Category::TYPE_MEETING;
        }
        $this->current_type_title = $types[$this->current_type];

        view()->share('type_title', $this->current_type_title);
        view()->share('current_type', $this->current_type);
        $this->setCurrentMenu(Category::getCode($this->current_type) . ':category');
    }

    public function index()
    {
        $categories = Category::where('type', $this->current_type)->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
        
        $values = [
            'categories' => $categories,
        ];
        AdminLog::addLog($this->user->id, 'list', 'categories', null, '查看'. $this->current_type_title .'类型列表');
        return view('category.index', $values);
    }

    public function store()
    {
        $data = [
            'title' => request('title'),
            'cover' => request('cover', ''),
            'fields' => request('fields', []),
            'extensions' => request('extensions', []),
        ];
        $category = new Category();
        $category->title = $data['title'];
        $category->cover = $data['cover'];
        $category->type = $this->current_type;
        $category->fields = $data['fields'];
        $category->extensions = $data['extensions'];

        if ($category->title == '') {
            return response()->json(['error'=>true, 'message'=>'未填写类型名称']);
        }
        $category->save();
        AdminLog::addLog($this->user->id, 'insert', 'categories', $category);
        session()->flash('message', '添加成功');
        return response()->json(['error'=>false]);
    }

    public function update($id)
    {
        $data = [
            'title' => request('title'),
            'cover' => request('cover', ''),
            'fields' => request('fields', []),
            'extensions' => request('extensions', []),
        ];
        $category = Category::find($id);
        if ($category == null) {
            return response()->json(['error'=>true, 'message'=>'类型不存在或已删除']);
        }

        $category->title = $data['title'];
        $category->cover = $data['cover'];
        $category->type = $this->current_type;
        $category->fields = $data['fields'];
        $category->extensions = $data['extensions'];

        if ($category->title == '') {
            return response()->json(['error'=>true, 'message'=>'未填写类型名称']);
        }
        $category->save();
        
        AdminLog::addLog($this->user->id, 'update', 'categories', $category);
        session()->flash('message', '修改成功');
        return response()->json(['error'=>false]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category != null) {
            $category->delete();
            
            AdminLog::addLog($this->user->id, 'delete', 'categories', $category);
            session()->flash('message', '删除成功');
        }
        
        return response()->json(['error'=>false]);
    }
}
