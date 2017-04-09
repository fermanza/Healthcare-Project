<?php

namespace App\Http\Controllers;

use App\PositionType;
use Illuminate\Http\Request;
use App\Http\Requests\PositionTypeRequest;

class PositionTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positionTypes = PositionType::where('active', true)->get();

        return view('admin.positionTypes.index', compact('positionTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $positionType = new PositionType;
        $action = 'create';

        $params = compact('positionType', 'action');

        return view('admin.positionTypes.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PositionTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionTypeRequest $request)
    {
        $positionType = new PositionType;
        $request->save($positionType);

        flash(__('PositionType created.'));

        return redirect()->route('admin.positionTypes.index');
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
     * @param  \App\PositionType  $positionType
     * @return \Illuminate\Http\Response
     */
    public function edit(PositionType $positionType)
    {
        $action = 'edit';

        $params = compact('positionType', 'action');

        return view('admin.positionTypes.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PositionTypeRequest  $request
     * @param  \App\PositionType  $positionType
     * @return \Illuminate\Http\Response
     */
    public function update(PositionTypeRequest $request, PositionType $positionType)
    {
        $request->save($positionType);

        flash(__('PositionType updated.'));

        return redirect()->route('admin.positionTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PositionType  $positionType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PositionType $positionType)
    {
        $positionType->active = false;
        $positionType->save();

        flash(__('PositionType deleted.'));

        return back();
    }
}
