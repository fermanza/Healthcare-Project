<?php

namespace App\Http\Controllers;

use App\Account;
use App\PipelineLocum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PipelineLocumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Account $account)
    {
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(config('pipeline.recruiting_types')),
            ],
            'name' => 'required',
            'agency' => 'required',
            'potentialStart' => 'nullable|date_format:"Y-m-d"',
            'credentialingNotes' => '',
            'shiftsOffered' => 'nullable|integer|min:0',
            'startDate' => 'nullable|date_format:"Y-m-d"',
            'comments' => '',
        ]);

        $locum = new PipelineLocum;
        $locum->pipelineId = $account->pipeline->id;
        $locum->type = $request->type;
        $locum->name = $request->name;
        $locum->agency = $request->agency;
        $locum->potentialStart = $request->potentialStart;
        $locum->credentialingNotes = $request->credentialingNotes;
        $locum->shiftsOffered = $request->shiftsOffered;
        $locum->startDate = $request->startDate;
        $locum->comments = $request->comments;
        $locum->save();

        return $locum->fresh();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @param  \App\PipelineLocum  $locum
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, Account $account, PipelineLocum $locum)
    {
        $this->validate($request, [
            'contract' => [
                'nullable',
                Rule::in(config('pipeline.contract_types')),
            ],
            'interview' => 'nullable|date_format:"Y-m-d"',
            'application' => 'nullable|date_format:"Y-m-d"',
            'contractOut' => 'nullable|date_format:"Y-m-d"',
            'declined' => 'required|date_format:"Y-m-d"',
            'declinedReason' => 'required',
        ]);

        $locum->contract = $request->contract;
        $locum->interview = $request->interview;
        $locum->application = $request->application;
        $locum->contractOut = $request->contractOut;
        $locum->declined = $request->declined;
        $locum->declinedReason = $request->declinedReason;
        $locum->save();

        return $locum;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @param  \App\PipelineLocum  $locum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account, PipelineLocum $locum)
    {
        $locum->delete();

        return $locum;
    }
}
