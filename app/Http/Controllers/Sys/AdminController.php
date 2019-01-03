<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateFormRequest;
use App\Http\Requests\Admin\EditFormRequest;
use App\Models\Admin;

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->setCurrentMenu('sys:user');
        view()->share('permissions', config('auth.permissions'));
    }

    public function index()
    {
        $users = Admin::query()->where('id', '<>', 1)->orderBy('id', 'asc')->paginate(20);
        
        $values = [
            'users' => $users,
        ];

        return view('admin.index', $values);
    }

    public function create()
    {
        $user = new Admin();
        $values = [
            'user' => $user,
        ];

        return view('admin.create', $values);
    }

    public function store(CreateFormRequest $request)
    {
        $user = new Admin();
        $user->fill($request->all());
        $user->password = bcrypt(request('password', '123456'));
        //$user->extensions = [];
        $user->save();

        return redirect()->route('sys.admin.index');
    }

    public function edit($id)
    {
        $user = Admin::find($id);
        if ($user == null) {
            return back()->with('message', '用户不存在');
        }

        $values = [
            'user' => $user,
        ];
        return view('admin.edit', $values);
    }

    public function update(EditFormRequest $request, $id)
    {
        $user = Admin::find($id);
        if ($user == null) {
            return back()->with('message', '用户不存在');
        }
        $user->fill($request->all());
        if (empty($request->input('permissions'))) {
            $user->permissions = [];
        }
        if ($request->input('password') != '') {
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();

        return redirect()->route('sys.admin.index');
    }

    public function show($id)
    {
        $user = Admin::find($id);
        if ($user == null) {
            return back()->with('message', '用户不存在');
        }

        $values = [
            'user' => $user,
        ];

        return view('admin.show', $values);
    }

    public function disable($id)
    {
        $user = Admin::find($id);
        if ($user != null) {
            $user->disabled = !$user->disabled;
            $user->save();

            session()->flash('message', '修改成功');
        }

        return response()->json(['error'=>false]);
    }
}
