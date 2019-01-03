<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;
use App\Models\Industry;

class IndustryController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('sys:industry');
    }
    
    public function index()
    {
        $industries = Industry::where('hide', false)->orderBy('parent_id', 'asc')->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();

        return view('industry.index', ['industries'=>$industries, 'idx'=>0]);
    }

    public function store()
    {
        $title = request('title');
        $parentid = intval(request('parent_id'));
        if ($title == '') {
            return response()->json(['error'=>true, 'message'=>'名称不能为空']);
        }

        $parent = Industry::find($parentid);

        $industry = new Industry();
        $industry->title = $title;
        $industry->parent_id = $parent != null ? $parent->id : 0;
        $industry->hide = false;
        $industry->orderindex = 0;

        $industry->save();

        session()->flash('message', '添加成功');
        return response()->json(['error'=>false]);
    }

    public function update($id)
    {
        $industry = Industry::find($id);
        if ($industry == null) {
            return response()->json(['error'=>true, 'message'=>'行业不存在']);
        }
        
        $title = request('title');
        if ($title == '') {
            return response()->json(['error'=>true, 'message'=>'名称不能为空']);
        }
        
        $industry->title = $title;
        $industry->save();

        session()->flash('message', '修改成功');
        return response()->json(['error'=>false]);
    }

    public function destroy($id)
    {
        $industry = Industry::find($id);
        if ($industry != null) {
            $industry->hide = true;
            $industry->save();

            //隐藏所有下级行业分类
            Industry::where('parent_id', $industry->id)->update(['hide'=>1]);
            session()->flash('message', '删除成功');
        }
        return response()->json(['error'=>false]);
    }
}
