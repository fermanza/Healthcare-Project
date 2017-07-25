<?php

namespace App\Http\Controllers;

use App\Account;
use App\PipelineRecruiting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PipelineRecruitingController extends Controller
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
            'contract' => [
                'required',
                Rule::in(config('pipeline.contract_types')),
            ],
            'name' => 'required',
            'interview' => 'nullable|date_format:"Y-m-d"',
            'contractOut' => 'nullable|date_format:"Y-m-d"',
            'contractIn' => 'nullable|date_format:"Y-m-d"',
            'firstShift' => 'nullable|date_format:"Y-m-d"',
            'notes' => '',
        ]);

        $recruiting = new PipelineRecruiting;
        $recruiting->pipelineId = $account->pipeline->id;
        $recruiting->type = $request->type;
        $recruiting->contract = $request->contract;
        $recruiting->name = $request->name;
        $recruiting->interview = $request->interview;
        $recruiting->contractOut = $request->contractOut;
        $recruiting->contractIn = $request->contractIn;
        $recruiting->firstShift = $request->firstShift;
        $recruiting->notes = $request->notes;
        $recruiting->save();

        return $recruiting->fresh();
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
     * @param  \App\PipelineRecruiting  $recruiting
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, Account $account, PipelineRecruiting $recruiting)
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

        $recruiting->contract = $request->contract;
        $recruiting->interview = $request->interview;
        $recruiting->application = $request->application;
        $recruiting->contractOut = $request->contractOut;
        $recruiting->declined = $request->declined;
        $recruiting->declinedReason = $request->declinedReason;
        $recruiting->save();

        return $recruiting;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @param  \App\PipelineRecruiting  $recruiting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account, PipelineRecruiting $recruiting)
    {
        $recruiting->delete();

        return $recruiting;
    }
}
