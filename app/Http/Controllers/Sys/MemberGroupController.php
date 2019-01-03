<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\MemberGroupRequest;
use App\Models\MemberGroup;
use App\Models\AdminLog;

class MemberGroupController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('member:group');
    }

    public function index()
    {
        $groups = MemberGroup::query()->orderBy('orderindex', 'asc')->orderBy('id', 'asc')->get();

        $values = [
            'groups' => $groups,
        ];
        AdminLog::addLog($this->user->id, 'list', 'member_groups', null, '查看代理级别');
        return view('membergroup.index', $values);
    }

    public function create()
    {
        $group = new MemberGroup();
        $values = [
            'group' => $group,
            'groups' => [],
        ];
        return view('membergroup.form', $values);
    }

    public function store(MemberGroupRequest $request)
    {
        $group = new MemberGroup();
        $group->fill($request->all());
        $group->same = intval(request('same'));
        $group->save();

        return redirect()->route('sys.membergroup.index')->with('message', '添加成功');
    }

    public function edit($id)
    {
        $group = MemberGroup::find($id);
        if ($group == null) {
            return back()->with('message', '代理级别不存在');
        }
        $groups = MemberGroup::query()->orderBy('orderindex', 'asc')->orderBy('id', 'asc')->get();
        $values = [
            'group' => $group,
            'groups' => $groups,
        ];
        return view('membergroup.form', $values);
    }

    public function update(MemberGroupRequest $request, $id)
    {
        $group = MemberGroup::find($id);
        if ($group == null) {
            return back()->with('message', '代理级别不存在');
        }
        $group->fill($request->all());
        $group->save();

        AdminLog::addLog($this->user->id, 'update', 'member_groups', $group, '修改代理级别');
        return redirect()->route('sys.membergroup.index')->with('message', '修改成功');
    }
}
