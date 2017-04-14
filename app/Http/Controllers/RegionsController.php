<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;
use App\Http\Requests\RegionRequest;

class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::where('active', true)->get();

        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region = new Region;
        $action = 'create';

        $params = compact('region', 'action');

        return view('admin.regions.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RegionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {
        $region = new Region;
        $request->save($region);

        flash(__('Region created.'));

        return redirect()->route('admin.regions.index');
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
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        $action = 'edit';

        $params = compact('region', 'action');

        return view('admin.regions.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RegionRequest  $request
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function update(RegionRequest $request, Region $region)
    {
        $request->save($region);

        flash(__('Region updated.'));

        return redirect()->route('admin.regions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function destroy(Region $region)
    {
        $region->active = false;
        $region->save();

        flash(__('Region deleted.'));

        return back();
    }
}
