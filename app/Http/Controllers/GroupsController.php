<?php

namespace App\Http\Controllers;

use App\Group;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::with('region')->where('active', true)->get();

        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $group = new Group;
        $regions = Region::where('active', true)->orderBy('name')->get();
        $action = 'create';

        $params = compact('group', 'regions', 'action');

        return view('admin.groups.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\GroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $group = new Group;
        $request->save($group);

        flash(__('Group created.'));

        return redirect()->route('admin.groups.index');
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
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $regions = Region::where('active', true)->orderBy('name')->get();
        $action = 'edit';

        $params = compact('group', 'regions', 'action');

        return view('admin.groups.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\GroupRequest  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, Group $group)
    {
        $request->save($group);

        flash(__('Group updated.'));

        return redirect()->route('admin.groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->active = false;
        $group->save();

        flash(__('Group deleted.'));

        return back();
    }
}
