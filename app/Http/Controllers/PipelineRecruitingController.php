<?php

namespace App\Http\Controllers;

use App\Account;
use App\PipelineRecruiting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\PipelineRosterBench;
use Carbon\Carbon;

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
            'interview' => 'nullable|date_format:"m/d/Y"',
            'contractOut' => 'nullable|date_format:"m/d/Y"',
            'contractIn' => 'nullable|date_format:"m/d/Y"',
            'firstShift' => 'nullable|date_format:"m/d/Y"',
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
        $recruiting->noc = $request->noc;
        $recruiting->notes = $request->notes;
        $recruiting->lastUpdated = Carbon::now();
        $recruiting->lastUpdatedBy = \Auth::id();
        $recruiting->providerId = $request->providerId;
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
     * @param  \App\Account  $account
     * @param  \App\PipelineRecruiting  $recruiting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, PipelineRecruiting $recruiting)
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
            'interview' => 'nullable|date_format:"m/d/Y"',
            'contractOut' => 'nullable|date_format:"m/d/Y"',
            'contractIn' => 'nullable|date_format:"m/d/Y"',
            'firstShift' => 'nullable|date_format:"m/d/Y"',
            'notes' => '',
        ]);

        $recruiting->pipelineId = $account->pipeline->id;
        $recruiting->type = $request->type;
        $recruiting->contract = $request->contract;
        $recruiting->name = $request->name;
        $recruiting->interview = $request->interview;
        $recruiting->contractOut = $request->contractOut;
        $recruiting->contractIn = $request->contractIn;
        $recruiting->firstShift = $request->firstShift;
        $recruiting->notes = $request->notes;
        $recruiting->noc = $request->noc;
        $recruiting->providerId = $request->providerId;
        $recruiting->stopLight = $request->stopLight;
        $recruiting->fileToCredentialing = $request->fileToCredentialing;
        $recruiting->privilegeGoal = $request->privilegeGoal;
        $recruiting->appToHospital = $request->appToHospital;
        $recruiting->stage = $request->stage;
        $recruiting->enrollmentStatus = $request->enrollmentStatus;
        $recruiting->enrollmentNotes = $request->enrollmentNotes;
        $recruiting->credentialingNotes = $request->credentialingNotes;
        $recruiting->lastUpdated = Carbon::now();
        $recruiting->lastUpdatedBy = \Auth::id();
        $recruiting->save();

        return $recruiting->fresh();
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
            'interview' => 'nullable|date_format:"m/d/Y"',
            'application' => 'nullable|date_format:"m/d/Y"',
            'contractOut' => 'nullable|date_format:"m/d/Y"',
            'declined' => 'required|date_format:"m/d/Y"',
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

    /**
     * Switch specified recruiting to rosterbench.
     *
     * @param \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @param  \App\PipelineRecruiting  $recruiting
     * @return \Illuminate\Http\Response
     */
    public function switch(Request $request, Account $account, PipelineRecruiting $recruiting)
    {
        $rosterBench = new PipelineRosterBench;
        $rosterBench->pipelineId = $request->pipelineId;
        $rosterBench->place = $request->place;
        $rosterBench->type = $request->type;
        $rosterBench->activity = $request->activity;
        $rosterBench->hours = 0;
        $rosterBench->name = $request->name;
        $rosterBench->contract = $request->contract;
        $rosterBench->interview = $request->interview;
        $rosterBench->contractOut = $request->contractOut;
        $rosterBench->contractIn = $request->contractIn;
        $rosterBench->firstShift = $request->firstShift;
        $rosterBench->notes = $request->notes;
        $rosterBench->noc = $request->noc;

        if ($rosterBench->save()) {
            $recruiting->delete();

            return $rosterBench;
        }
    }


    /**
     * Make the specified recruiting a credentialing.
     *
     * @param \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @param  \App\PipelineRecruiting  $recruiting
     * @return \Illuminate\Http\Response
     */
    public function makeCred(Request $request, Account $account, PipelineRecruiting $recruiting)
    {
        $recruiting->isCredentialing = 1;
        $recruiting->save();

        return $recruiting;
    }
}
