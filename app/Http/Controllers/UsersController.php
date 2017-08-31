<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Employee;
use App\RSC;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles', 'employee.person')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User;
        $action = 'create';
        $view = 'admin.users.create';

        return $this->form($user, $action, $view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User;
        $request->save($user);

        flash(__('User created.'));

        return redirect()->route('admin.users.index');
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $action = 'edit';
        $view = 'admin.users.edit';

        return $this->form($user, $action, $view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $request->save($user);

        flash(__('User updated.'));

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        flash(__('User deleted.'));

        return back();
    }

    /**
     * Show the form for the specified resource.
     *
     * @param  \App\User  $user
     * @param  string  $action
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function form($user, $action, $view)
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get();
        $employees = Employee::with('person')
            ->where('active', true)
            ->get()->sortBy->fullName();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();

        $params = compact('user', 'roles', 'employees', 'RSCs', 'regions', 'action');

        return view($view, $params);
    }
}
