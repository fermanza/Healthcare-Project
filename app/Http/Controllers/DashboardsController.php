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
use Carbon\Carbon;
use JavaScript;

class DashboardsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SummaryFilter $filter, Request $request)
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

        $chartsData = $this->getChartsData($filter, $request->period);

        $params = compact('recruiters', 'managers', 'doos', 'SVPs', 'RMDs', 'states', 'cities', 'practices', 'regions', 'affiliations', 'sites', 'RSCs', 'groups');

        JavaScript::put($chartsData);

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

    private function getChartsData(SummaryFilter $filter, $period) {

        $period = !$period ? 'MTD' : $period;
        $currentQuarterStart = Carbon::today()->firstOfQuarter();
        $currentQuarterEnd = Carbon::today()->lastOfQuarter();

        $secondQuarterS = new \Carbon\Carbon('-3 months');
        $secondQuarterE = new \Carbon\Carbon('-3 months');
        $secondQuarterStart = $secondQuarterS->firstOfQuarter();
        $secondQuarterEnd = $secondQuarterE->lastOfQuarter();

        $thirdQuarterS = new \Carbon\Carbon('-6 months');
        $thirdQuarterE = new \Carbon\Carbon('-6 months');
        $thirdQuarterStart = $thirdQuarterS->firstOfQuarter();
        $thirdQuarterEnd = $thirdQuarterE->lastOfQuarter();

        $fourthQuarterS = new \Carbon\Carbon('-9 months');
        $fourthQuarterE = new \Carbon\Carbon('-9 months');
        $fourthQuarterStart = $fourthQuarterS->firstOfQuarter();
        $fourthQuarterEnd = $fourthQuarterE->lastOfQuarter();

        switch ($period) {
            case 'MTD':
                $accounts = AccountSummary::whereMonth('MonthEndDate', Carbon::now()->month)->whereYear('MonthEndDate', Carbon::now()->year)->get();
                $prevAccounts = AccountSummary::whereMonth('MonthEndDate', Carbon::now()->subMonth()->month)->whereYear('MonthEndDate', Carbon::now()->subMonth()->year)->get();
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::whereMonth('MonthEndDate', Carbon::now()->subMonth(2)->month)->whereYear('MonthEndDate', Carbon::now()->subMonth(2)->year)->get();
                $fourthPeriod = AccountSummary::whereMonth('MonthEndDate', Carbon::now()->subMonth(3)->month)->whereYear('MonthEndDate', Carbon::now()->subMonth(3)->year)->get();
                break;

            case 'QTD':
                $accounts = AccountSummary::whereDate('MonthEndDate', '>=', $currentQuarterStart)->whereDate('MonthEndDate', '<=', $currentQuarterEnd)->get();
                $prevAccounts = AccountSummary::whereDate('MonthEndDate', '>=', $secondQuarterStart)->whereDate('MonthEndDate', '<=', $secondQuarterEnd)->get();
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::whereDate('MonthEndDate', '>=', $thirdQuarterStart)->whereDate('MonthEndDate', '<=', $thirdQuarterEnd)->get();
                $fourthPeriod = AccountSummary::whereDate('MonthEndDate', '>=', $fourthQuarterStart)->whereDate('MonthEndDate', '<=', $fourthQuarterEnd)->get();
                break;

            case 'YTD':
                $accounts = AccountSummary::whereYear('MonthEndDate', Carbon::now()->year)->get();
                $prevAccounts = AccountSummary::whereYear('MonthEndDate', Carbon::now()->subYear()->year)->get();
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::whereYear('MonthEndDate', Carbon::now()->subYear(2)->year)->get();
                $fourthPeriod = AccountSummary::whereYear('MonthEndDate', Carbon::now()->subYear(3)->year)->get();
                break;
            
            default:
                break;
        }

        $monthsData = array($firstPeriod, $secondPeriod, $thirdPeriod, $fourthPeriod);
        
        $quarters = array(
            $currentQuarterStart,
            $secondQuarterStart,
            $thirdQuarterStart,
            $fourthQuarterStart
        );

        $completeStaffPhys = $accounts->sum('Complete Staff - Phys');
        $completeStaffAPP = $accounts->sum('Complete Staff - APP');
        $completeStaffTotal = $accounts->sum('Complete Staff - Total');

        $currentStaffPhys = $accounts->sum('Current Staff - Total');
        $currentStaffAPP = $accounts->sum('Current Staff - Total');
        $currentStaffTotal = $accounts->sum('Current Staff - Total');

        $openingsPhys = $accounts->sum('Current Openings - Phys');
        $openingsAPP = $accounts->sum('Current Openings - APP');
        $openingsTotal = $accounts->sum('Current Openings - Total');

        $currentApplications = $accounts->sum('MTD - Applications');
        $currentInterViews = $accounts->sum('MTD - Interviews');
        $currentContractsOut = $accounts->sum('MTD - Contracts Out');
        $currentContractsIn = $accounts->sum('MTD - Contracts In');
        $currentCredentialings = $accounts->sum('MTD - Signed Not Yet Started');

        $prevCompleteStaffPhys = $prevAccounts->sum('Complete Staff - Phys');
        $prevCompleteStaffAPP = $prevAccounts->sum('Complete Staff - APP');
        $prevCompleteStaffTotal = $prevAccounts->sum('Complete Staff - Total');

        $prevCurrentStaffPhys = $prevAccounts->sum('Current Staff - Total');
        $prevCurrentStaffAPP = $prevAccounts->sum('Current Staff - Total');
        $prevCurrentStaffTotal = $prevAccounts->sum('Current Staff - Total');

        $prevOpeningsPhys = $prevAccounts->sum('Current Openings - Phys');
        $prevOpeningsAPP = $prevAccounts->sum('Current Openings - APP');
        $prevOpeningsTotal = $prevAccounts->sum('Current Openings - Total');

        $prevApplications = $prevAccounts->sum('MTD - Applications');
        $prevInterViews = $prevAccounts->sum('MTD - Interviews');
        $prevContractsOut = $prevAccounts->sum('MTD - Contracts Out');
        $prevContractsIn = $prevAccounts->sum('MTD - Contracts In');
        $prevCredentialings = $prevAccounts->sum('MTD - Signed Not Yet Started');

        $percentRecruitedPhys = $completeStaffPhys == 0 ? 0 : round((($completeStaffPhys - $openingsPhys) / $completeStaffPhys) * 100, 2);
        $percentRecruitedAPP = $completeStaffAPP == 0 ? 0 : round((($completeStaffAPP - $openingsAPP) / $completeStaffAPP) * 100, 2);
        $percentRecruitedTotal = $completeStaffTotal == 0 ? 0 : round((($completeStaffTotal - $openingsTotal) / $completeStaffTotal) * 100, 2);
        $percentApplications = $prevApplications == 0 ? 0 : round((($currentApplications - $prevApplications) / $prevApplications) * 100, 2);
        $percentInterViews = $prevInterViews == 0 ? 0 : round((($currentInterViews - $prevInterViews) / $prevInterViews) * 100, 2);
        $percentContractsOut = $prevContractsOut == 0 ? 0 : round((($currentContractsOut - $prevContractsOut) / $prevContractsOut) * 100, 2);
        $percentContractsIn = $prevContractsIn == 0 ? 0 : round((($currentContractsIn - $prevContractsIn) / $prevContractsIn) * 100, 2);
        $percentCredentialings = $prevCredentialings == 0 ? 0 : round((($currentCredentialings - $prevCredentialings) / $prevCredentialings) * 100, 2);

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
            "PhysiciansRecruited" => $percentRecruitedPhys,
            "AppRecruited" => $percentRecruitedAPP,
            "totalPctRecruited" => $percentRecruitedTotal,
            "Applications" => $percentApplications,
            "Interviews" => $percentInterViews,
            "ContractsOut" => $percentContractsOut,
            "ContractsIn" => $percentContractsIn,
            "Credentialings" => $percentCredentialings
        ];

        $gauge = ["value" => $percentRecruitedTotal];

        $bars = [
            "contracts" => array(),
            "openings" => array()
        ];

        for($x = 0; $x < 4; $x++) {
            $tempContracts = array();
            $tempOpenings = array();


            switch ($period) {
                case 'MTD':
                    $tempContracts["name"] = Carbon::today()->subMonth($x)->format('F Y');
                    $tempOpenings["name"] = Carbon::today()->subMonth($x)->format('F Y');
                    break;

                case 'QTD':
                    $tempContracts["name"] = $quarters[$x]->format('Y').' Q'.$quarters[$x]->quarter;
                    $tempOpenings["name"] = $quarters[$x]->format('Y').' Q'.$quarters[$x]->quarter;
                    break;

                case 'YTD':
                    $tempContracts["name"] = Carbon::today()->subYear($x)->format('Y');
                    $tempOpenings["name"] = Carbon::today()->subYear($x)->format('Y');
                    break;
                
                default:
                    break;
            }

            $contracts = $monthsData[$x]->sum('MTD - Contracts In');
            $openings = $monthsData[$x]->sum('Current Openings - Total');

            $completeStaffTotal = $monthsData[$x]->sum('Complete Staff - Total');
            $openingsTotal = $monthsData[$x]->sum('Current Openings - Total');

            $line = $completeStaffTotal == 0 ? 0 : round((($completeStaffTotal - $openingsTotal) / $completeStaffTotal * 100), 2);

            $tempContracts["bar"] = $contracts;
            $tempOpenings["bar"] = $openings;

            $tempContracts["line1"] = $line;
            $tempOpenings["line1"] = $line;

            $bars["contracts"][] = $tempContracts;
            $bars["openings"][] = $tempOpenings;
        }
        
        $pipeline["data"] = [
            $currentApplications,
            $currentInterViews,
            $currentContractsOut,
            $currentContractsIn,
            $currentCredentialings
        ];

        $formatedResponse = array(
            'pipeline' => $pipeline, 
            'squares' => $squares, 
            'gauge' => $gauge,
            'bars' => $bars,
            'period' => $period
        );

        return $formatedResponse;
    }
}
