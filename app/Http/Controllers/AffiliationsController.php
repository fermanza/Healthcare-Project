<?php

namespace App\Http\Controllers;

use App\SystemAffiliation;
use Illuminate\Http\Request;
use App\Http\Requests\AffiliationRequest;
use Illuminate\Support\Facades\Storage;

class AffiliationsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $affiliations = SystemAffiliation::all();

        return view('admin.affiliations.index', compact('affiliations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $affiliation = new SystemAffiliation;
        $action = 'create';

        $params = compact('affiliation', 'action');

        return view('admin.affiliations.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AffiliationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AffiliationRequest $request)
    {
        $affiliation = new SystemAffiliation;
        $request->save($affiliation);

        flash(__('Affiliation created.'));

        return redirect()->route('admin.affiliations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SystemAffiliation $affiliation
     * @return \Illuminate\Http\Response
     */
    public function show(SystemAffiliation $affiliation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SystemAffiliation $affiliation
     * @return \Illuminate\Http\Response
     */
    public function edit(SystemAffiliation $affiliation)
    {
        $action = 'edit';
        $params = compact('affiliation', 'action');

        return view('admin.affiliations.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AffiliationRequest  $request
     * @param  \App\SystemAffiliation $affiliation
     * @return \Illuminate\Http\Response
     */
    public function update(AffiliationRequest $request, SystemAffiliation $affiliation)
    {
        $request->save($affiliation);

        flash(__('Affiliation updated.'));

        return redirect()->route('admin.affiliations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SystemAffiliation $affiliation
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemAffiliation $affiliation)
    {
        $affiliation->delete();

        flash(__('Affiliation deleted.'));

        return back();
    }
}
