<?php

namespace App\Http\Controllers;

use App\Practice;
use Illuminate\Http\Request;
use App\Http\Requests\PracticeRequest;

class PracticesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $practices = Practice::where('active', true)->get();

        return view('admin.practices.index', compact('practices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $practice = new Practice;
        $action = 'create';

        $params = compact('practice', 'action');

        return view('admin.practices.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PracticeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PracticeRequest $request)
    {
        $practice = new Practice;
        $request->save($practice);

        flash(__('Service Line created.'));

        return redirect()->route('admin.practices.index');
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
     * @param  \App\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function edit(Practice $practice)
    {
        $action = 'edit';

        $params = compact('practice', 'action');

        return view('admin.practices.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PracticeRequest  $request
     * @param  \App\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function update(PracticeRequest $request, Practice $practice)
    {
        $request->save($practice);

        flash(__('Service Line updated.'));

        return redirect()->route('admin.practices.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Practice  $practice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Practice $practice)
    {
        $practice->active = false;
        $practice->save();

        flash(__('Service Line deleted.'));

        return back();
    }
}
