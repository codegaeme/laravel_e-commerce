<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;


        $role = Role::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());
        return view('admin.role.list', compact('role'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.role.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string'   => 'Tên sản phẩm không hợp lệ.',
            'name.max'      => 'Tên sản phẩm tối đa 255 ký tự.',
            'name.unique'   => 'Tên sản phẩm đã tồn tại, vui lòng chọn tên khác.',

            'description.string' => 'Mô tả không hợp lệ.',
        ]);


        $data = [
            'name' => $request->name,
            'description' => $request->description,


        ];
        $newCate = Role::create($data);
        return redirect()->route('admin.setRoles.index')->with('success', 'Thêm mới thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $category = Role::find($id);
        if ($category) {
            return view('admin.role.edit', compact('category'));
        } else {
            return redirect()->back()->with('error', 'Không tồn tại');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($id),
            ],
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string'   => 'Tên sản phẩm không hợp lệ.',
            'name.max'      => 'Tên sản phẩm tối đa 255 ký tự.',
            'name.unique'   => 'Tên sản phẩm đã tồn tại, vui lòng chọn tên khác.',
            'description.string' => 'Mô tả không hợp lệ.',
        ]);

        $category = Role::find($id);
        if ($category) {
            $data = [
                'name' => $request->name,
                'description' => $request->description,


            ];
            $category->update($data);
            return redirect()->route('admin.setRoles.index')->with('success', 'Sửa thành công');
        } else {
            return redirect()->back()->with('error', 'Không tồn tại');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('admin.setRoles.index')->with('success', 'Xóa thành công');
    }

    public function authen(Request $req)
    {
        $keyword = $req->keyword;
        $roles = Role::all();

        $user = User::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                        // ->orWhere('description', 'like', "%{$keyword}%");
                });
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($req->all());
        return view('admin.user.list', compact('user','roles'));
    }
    public function setRoles(Request $request){
       $user = User::findOrFail($request->user_id);
       $user->role_id = $request->role_id;
       $user ->save();
       return redirect()->route('admin.authen')->with('success','Thiết lập quyền thành công');
    }
}
