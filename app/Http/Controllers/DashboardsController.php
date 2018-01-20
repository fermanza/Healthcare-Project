<?php

namespace App\Http\Controllers;

use App\Dashboard;
use App\User;
use App\AccountSummary;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Http\Requests\DashboardRequest;
use Illuminate\Support\Facades\Storage;
use App\Account;
use App\RSC;
use App\Region;
use App\Division;
use App\Employee;
use App\Practice;
use App\SystemAffiliation;
use App\StateAbbreviation;
use App\Group;
use App\Pipeline;
use App\Filters\SummaryFilter;

class DashboardsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SummaryFilter $filter)
    {
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();

        $recruiters = $employees->filter->hasPosition(config('instances.position_types.recruiter'));
        $managers = $employees->filter->hasPosition(config('instances.position_types.manager'));
        $doos = $employees->filter->hasPosition(config('instances.position_types.doo'));
        $SVPs = Pipeline::distinct('SVP')->select('SVP')->whereNotNull('SVP')->orderBy('SVP')->get();
        $RMDs = Pipeline::distinct('RMD')->select('RMD')->whereNotNull('RMD')->orderBy('RMD')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $states = StateAbbreviation::all();
        $cities = Account::distinct('city')->select('city')->where('active', true)->orderBy('city')->get();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::all();
        $sites = Account::where('active', true)->orderBy('name')->get();
        $groups = Group::where('active', true)->get()->sortBy('name');

        $chartsData = $this->getChartsData($filter);

        $params = compact('recruiters', 'managers', 'doos', 'SVPs', 'RMDs', 'states', 'cities', 'practices', 'regions', 'affiliations', 'sites', 'RSCs', 'groups');

        return view('admin.dashboards.charts', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dashboard = new Dashboard;
        $users = User::orderBy('name')->get();
        $action = 'create';

        $params = compact('dashboard', 'users', 'action');

        return view('admin.dashboards.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DashboardRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DashboardRequest $request)
    {
        $dashboard = new Dashboard;
        $request->save($dashboard);

        flash(__('Dashboard created.'));

        return redirect()->route('admin.dashboards.edit', [$dashboard]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dashboard $dashboard
     * @return \Illuminate\Http\Response
     */
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dashboard $dashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(Dashboard $dashboard)
    {
        $action = 'edit';
        $users = User::orderBy('name')->get();
        $params = compact('dashboard', 'users', 'action');

        return view('admin.dashboards.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dashboard $dashboard
     * @return \Illuminate\Http\Response
     */
    public function update(DashboardRequest $request, Dashboard $dashboard)
    {
        $request->save($dashboard);

        flash(__('Dashboard updated.'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dashboard $dashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dashboard $dashboard)
    {
        $dashboard->delete();

        flash(__('Dashboard deleted.'));

        return back();
    }

    private function getChartsData(SummaryFilter $filter) {
        $accounts = AccountSummary::with('account.rsc')->filter($filter)->get();

        $pipeline = [
            "data" => [], 
            "titles" => [
                "Applications",
                "Interviews",
                "Contracts Out",
                "Contracts In",
                "Credentialing"
            ]
        ];

        $squares = [
            "PhysiciansRecruited" => 0,
            "AppRecruited" => 0,
            "totalPctRecruited" => 0,
            "QTDApplications" => 0,
            "QTDInterviews" => 0,
            "QTDContractsOut" => 0,
            "QTDContractsIn" => 0,
            "QTDCredentialing" => 0
        ];

        $gauge = ["value" => 0];

        $bars = [];

        

        //dd($accounts);
    }
}
