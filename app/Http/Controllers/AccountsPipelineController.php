<?php

namespace App\Http\Controllers;

use JavaScript;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountsPipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function index(Account $account)
    {
        $account->load([
            'pipeline' => function ($query) {
                $query->with([
                    'rostersBenchs', 'recruitings', 'locums',
                ]);
            },
            'recruiter.employee' => function ($query) {
                $query->with('person', 'manager.person');
            },
            'division.group.region',
            'practices',
        ]);
        $pipeline = $account->pipeline;
        $region = $account->region;
        $practice = $account->practices->count() ? $account->practices->first() : null;
        $practiceTimes = config('pipeline.practice_times');
        $recruitingTypes = config('pipeline.recruiting_types');
        $contractTypes = config('pipeline.contract_types');
        $accounts = Account::where('active', true)->orderBy('name')->get();

        if ($practice && $practice->isIPS() && $pipeline->practiceTime == 'hours') {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 180 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 180 : $pipeline->fullTimeHoursApps;
        } else {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 120 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 120 : $pipeline->fullTimeHoursApps;
        }

        $params = compact(
            'account', 'pipeline', 'region', 'practice', 'practiceTimes',
            'recruitingTypes', 'contractTypes', 'accounts'
        );

        JavaScript::put($params);

        return view('admin.accounts.pipeline.index', $params);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $this->validate($request, [
            'medicalDirector' => '',
            'rmd' => '',
            'svp' => '',
            'dca' => '',
            'practiceTime' => [
                'nullable',
                Rule::in(config('pipeline.practice_times')),
            ],
            'staffPhysicianHaves' => 'numeric',
            'staffAppsHaves' => 'numeric',
            'staffPhysicianNeeds' => 'numeric',
            'staffAppsNeeds' => 'numeric',
            'staffPhysicianOpenings' => 'numeric',
            'staffAppsOpenings' => 'numeric',
            'fullTimeHoursPhys' => 'numeric',
            'fullTimeHoursApps' => 'numeric',
            'staffPhysicianFTEHaves' => 'nullable|numeric',
            'staffPhysicianFTENeeds' => 'nullable|numeric',
            'staffPhysicianFTEOpenings' => 'nullable|numeric',
            'staffAppsFTEHaves' => 'nullable|numeric',
            'staffAppsFTENeeds' => 'nullable|numeric',
            'staffAppsFTEOpenings' => 'nullable|numeric',
        ]);

        $pipeline = $account->pipeline;
        $pipeline->medicalDirector = $request->medicalDirector;
        $pipeline->rmd = $request->rmd;
        $pipeline->svp = $request->svp;
        $pipeline->dca = $request->dca;
        $pipeline->practiceTime = $request->practiceTime;
        $pipeline->staffPhysicianHaves = $request->staffPhysicianHaves;
        $pipeline->staffAppsHaves = $request->staffAppsHaves;
        $pipeline->staffPhysicianNeeds = $request->staffPhysicianNeeds;
        $pipeline->staffAppsNeeds = $request->staffAppsNeeds;
        $pipeline->staffPhysicianOpenings = $request->staffPhysicianOpenings;
        $pipeline->staffAppsOpenings = $request->staffAppsOpenings;
        $pipeline->fullTimeHoursPhys = $request->fullTimeHoursPhys;
        $pipeline->fullTimeHoursApps = $request->fullTimeHoursApps;
        $pipeline->staffPhysicianFTEHaves = $request->staffPhysicianFTEHaves;
        $pipeline->staffPhysicianFTENeeds = $request->staffPhysicianFTENeeds;
        $pipeline->staffPhysicianFTEOpenings = $request->staffPhysicianFTEOpenings;
        $pipeline->staffAppsFTEHaves = $request->staffAppsFTEHaves;
        $pipeline->staffAppsFTENeeds = $request->staffAppsFTENeeds;
        $pipeline->staffAppsFTEOpenings = $request->staffAppsFTEOpenings;
        $pipeline->save();

        flash(__('Pipeline Updated.'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
