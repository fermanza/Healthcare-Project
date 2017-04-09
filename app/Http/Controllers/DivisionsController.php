<?php

namespace App\Http\Controllers;

use App\Group;
use App\Division;
use Illuminate\Http\Request;
use App\Http\Requests\DivisionRequest;

class DivisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $divisions = Division::with('group')->where('active', true)->get();

        return view('admin.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $division = new Division;
        $groups = Group::where('active', true)->orderBy('name')->get();
        $action = 'create';

        $params = compact('division', 'groups', 'action');

        return view('admin.divisions.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DivisionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DivisionRequest $request)
    {
        $division = new Division;
        $request->save($division);

        flash(__('Division created.'));

        return redirect()->route('admin.divisions.index');
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
     * @param  \App\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $division)
    {
        $groups = Group::where('active', true)->orderBy('name')->get();
        $action = 'edit';

        $params = compact('division', 'groups', 'action');

        return view('admin.divisions.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\DivisionRequest  $request
     * @param  \App\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function update(DivisionRequest $request, Division $division)
    {
        $request->save($division);

        flash(__('Division updated.'));

        return redirect()->route('admin.divisions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function destroy(Division $division)
    {
        $division->active = false;
        $division->save();

        flash(__('Division deleted.'));

        return back();
    }
}
