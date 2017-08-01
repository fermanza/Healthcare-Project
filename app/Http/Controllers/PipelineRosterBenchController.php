<?php

namespace App\Http\Controllers;

use App\Account;
use App\PipelineRosterBench;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PipelineRosterBenchController extends Controller
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
            'place' => [
                'required',
                Rule::in(config('pipeline.places')),
            ],
            'activity' => [
                'required',
                Rule::in(config('pipeline.activities')),
            ],
            'name' => 'required',
            'hours' => 'required|numeric|min:0',
            'interview' => 'nullable|date_format:"Y-m-d"',
            'contractOut' => 'nullable|date_format:"Y-m-d"',
            'contractIn' => 'nullable|date_format:"Y-m-d"',
            'firstShift' => 'nullable|date_format:"Y-m-d"',
            'notes' => 'nullable',
        ]);

        $rosterBench = new PipelineRosterBench;
        $rosterBench->pipelineId = $account->pipeline->id;
        $rosterBench->place = $request->place;
        $rosterBench->activity = $request->activity;
        $rosterBench->name = $request->name;
        $rosterBench->hours = $request->hours;
        $rosterBench->interview = $request->interview;
        $rosterBench->contractOut = $request->contractOut;
        $rosterBench->contractIn = $request->contractIn;
        $rosterBench->firstShift = $request->firstShift;
        $rosterBench->notes = $request->notes;
        $rosterBench->save();

        return $rosterBench->fresh();
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
     * @param  \App\PipelineRosterBench  $rosterBench
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, PipelineRosterBench $rosterBench)
    {
        $this->validate($request, [
            'place' => [
                'required',
                Rule::in(config('pipeline.places')),
            ],
        ]);

        $rosterBench->place = $request->place;
        $rosterBench->save();

        return $rosterBench;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @param  \App\PipelineRosterBench  $rosterBench
     * @return \Illuminate\Http\Response
     */
    public function resign(Request $request, Account $account, PipelineRosterBench $rosterBench)
    {
        $this->validate($request, [
            'type' => [
                'nullable',
                Rule::in(config('pipeline.recruiting_types')),
            ],
            'resigned' => 'required|date_format:"Y-m-d"',
            'resignedReason' => 'required',
        ]);

        $rosterBench->type = $request->type;
        $rosterBench->resigned = $request->resigned;
        $rosterBench->resignedReason = $request->resignedReason;
        $rosterBench->save();

        return $rosterBench;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @param  \App\PipelineRosterBench  $rosterBench
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account, PipelineRosterBench $rosterBench)
    {
        $rosterBench->delete();

        return $rosterBench;
    }
}
