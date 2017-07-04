<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = new Role;
        $action = 'create';
        $view = 'admin.roles.create';

        return $this->form($role, $action, $view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = new Role;
        $request->save($role);

        flash(__('Role created.'));

        return redirect()->route('admin.roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $action = 'edit';
        $view = 'admin.roles.edit';

        return $this->form($role, $action, $view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $request->save($role);

        flash(__('Role updated.'));

        return redirect()->route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        flash(__('Role deleted.'));

        return back();
    }

    /**
     * Show the form for the specified resource.
     *
     * @param  \App\Role  $role
     * @param  string  $action
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function form($role, $action, $view)
    {
        $role->load('permissions');
        $permissions = Permission::all();

        // refactor
        $lists = collect(config('acl'))->map(function ($names, $group) use (&$permissions) {
            return collect($names)->map(function ($display_name, $name) use (&$permissions) {
                $permissionKey = null;
                $permission = $permissions->first(function ($permission, $key) use ($name, &$permissionKey) {
                    $bool = $permission->name == $name;
                    if ($bool) {
                        $permissionKey = $key;
                    }
                    return $bool;
                });
                if ($permissionKey !== null) {
                    $permissions->pull($permissionKey);
                }
                return $permission;
            })->filter();
        });

        $params = compact('role', 'permissions', 'lists', 'action');

        return view($view, $params);
    }
}
