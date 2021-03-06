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
use App\vFactInterview;
use App\vContractLog;
use App\Filters\SummaryFilter;
use App\Scopes\AccountSummaryScope;
use Carbon\Carbon;
use DB;
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
        $SVPs = Pipeline::distinct()->select('SVP')->whereNotNull('SVP')->orderBy('SVP')->get();
        $RMDs = Pipeline::distinct()->select('RMD')->whereNotNull('RMD')->orderBy('RMD')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $states = StateAbbreviation::all();
        $cities = Account::distinct()->select('city')->where('active', true)->whereNotNull('city')->orderBy('city')->get();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::distinct()->select('name')->get();
        $sites = Account::where('active', true)->orderBy('name')->get();
        $groups = Group::where('active', true)->get()->sortBy('name');

        $chartsData = $this->getChartsData($filter, $request->period, $request->new);

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

    private function getChartsData(SummaryFilter $filter, $period, $newFilter) {
        Carbon::useMonthsOverflow(false);

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
                $accounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->month)->whereYear('MonthEndDate', Carbon::today()->year)->get();
                $prevAccounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->subMonth()->month)->whereYear('MonthEndDate', Carbon::today()->subMonth()->year)->get();
                $currentMonth = $accounts;
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->subMonth(2)->month)->whereYear('MonthEndDate', Carbon::today()->subMonth(2)->year)->get();
                $fourthPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->subMonth(3)->month)->whereYear('MonthEndDate', Carbon::today()->subMonth(3)->year)->get();
                break;

            case 'QTD':
                $accounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereDate('MonthEndDate', '>=', $currentQuarterStart)->whereDate('MonthEndDate', '<=', $currentQuarterEnd)->get();
                $prevAccounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereDate('MonthEndDate', '>=', $secondQuarterStart)->whereDate('MonthEndDate', '<=', $secondQuarterEnd)->get();
                $currentMonth = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->month)->whereYear('MonthEndDate', Carbon::today()->year)->get();
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereDate('MonthEndDate', '>=', $thirdQuarterStart)->whereDate('MonthEndDate', '<=', $thirdQuarterEnd)->get();
                $fourthPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereDate('MonthEndDate', '>=', $fourthQuarterStart)->whereDate('MonthEndDate', '<=', $fourthQuarterEnd)->get();
                break;

            case 'YTD':
                $accounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereYear('MonthEndDate', Carbon::today()->year)->get();
                $prevAccounts = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereYear('MonthEndDate', Carbon::today()->subYear()->year)->get();
                $currentMonth = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereMonth('MonthEndDate', Carbon::today()->month)->whereYear('MonthEndDate', Carbon::today()->year)->get();
                $firstPeriod = $accounts;
                $secondPeriod = $prevAccounts;
                $thirdPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereYear('MonthEndDate', Carbon::today()->subYear(2)->year)->get();
                $fourthPeriod = AccountSummary::withGlobalScope('role', new AccountSummaryScope)->select('Complete Staff - Phys', 'Complete Staff - APP', 'Complete Staff - Total', 'Current Openings - Phys', 'Current Openings - APP', 'Current Openings - Total', 'MTD - Applications', 'MTD - Interviews', 'MTD - Contracts Out', 'MTD - Contracts In', 'MTD - Signed Not Yet Started', 'Start Date')->filter($filter)->whereYear('MonthEndDate', Carbon::today()->subYear(3)->year)->get();
                break;
            
            default:
                break;
        }

        if ($newFilter == "1") {
            $accounts = $accounts->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $prevAccounts = $prevAccounts->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $currentMonth = $currentMonth->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $firstPeriod = $firstPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $secondPeriod = $secondPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $thirdPeriod = $thirdPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });

            $fourthPeriod = $fourthPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() <= 7;
            });
        } elseif ($newFilter == "2") {
            $accounts = $accounts->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $prevAccounts = $prevAccounts->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $currentMonth = $currentMonth->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $firstPeriod = $firstPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $secondPeriod = $secondPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $thirdPeriod = $thirdPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });

            $fourthPeriod = $fourthPeriod->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });
        }

        $prevMonthStart = Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d');
        $prevMonthDate = Carbon::today()->subMonth()->format('Y-m-d');
        $currentMonthStart = Carbon::today()->startOfMonth()->format('Y-m-d');
        $currentMonthDate = Carbon::today()->format('Y-m-d');

        $interviews = vFactInterview::withGlobalScope('role', new AccountSummaryScope)->filter($filter)->new($newFilter)->selectRaw('interviewDate, sum(InterviewCount) as InterviewCount')
        ->where(function($query) use ($prevMonthDate, $prevMonthStart, $currentMonthDate, $currentMonthStart) {
            $query->where(function($q) use ($prevMonthDate, $prevMonthStart) {
                $q->where('DateOfInterview', '>=', $prevMonthStart)
                    ->where('DateOfInterview', '<=', $prevMonthDate);
            })
            ->orWhere(function($q) use ($currentMonthDate, $currentMonthStart) {
                $q->where('DateOfInterview', '>=', $currentMonthStart)
                    ->where('DateOfInterview', '<=', $currentMonthDate);
            });
        })
        ->groupBy('interviewDate')->get();

        $applications = vFactInterview::withGlobalScope('role', new AccountSummaryScope)->filter($filter)->new($newFilter)->selectRaw('applicationDate, sum(applicationCount) as applicationCount')
        ->where(function($query) use ($prevMonthDate, $prevMonthStart, $currentMonthDate, $currentMonthStart) {
            $query->where(function($q) use ($prevMonthDate, $prevMonthStart) {
                $q->where('CreatedOn', '>=', $prevMonthStart)
                    ->where('CreatedOn', '<=', $prevMonthDate);
            })
            ->orWhere(function($q) use ($currentMonthDate, $currentMonthStart) {
                $q->where('CreatedOn', '>=', $currentMonthStart)
                    ->where('CreatedOn', '<=', $currentMonthDate);
            });
        })
        ->groupBy('applicationDate')->get();

        $contractsIn = vContractLog::withGlobalScope('role', new AccountSummaryScope)->filter($filter)->new($newFilter)->selectRaw('dateadd(month, datediff(month, 0, contractInDate), 0) as contractIn, sum(value) as contractsInCount')
        ->where(function($query) use ($prevMonthDate, $prevMonthStart, $currentMonthDate, $currentMonthStart) {
            $query->where(function($a) use ($prevMonthDate, $prevMonthStart) {
                $a->where('contractInDate', '>=', $prevMonthStart)
                    ->where('contractInDate', '<=', $prevMonthDate);
            })
            ->orWhere(function($q) use ($currentMonthDate, $currentMonthStart) {
                $q->where('contractInDate', '>=', $currentMonthStart)
                    ->where('contractInDate', '<=', $currentMonthDate);
            });
        })
        ->groupBy(DB::raw('dateadd(month, datediff(month, 0, contractInDate), 0)'))->get();

        $contractsOut = vContractLog::withGlobalScope('role', new AccountSummaryScope)->filter($filter)->new($newFilter)->selectRaw('dateadd(month, datediff(month, 0, contractOutDate), 0) as contractOut, sum(value) as contractsOutCount')
         ->where(function($query) use ($prevMonthDate, $prevMonthStart, $currentMonthDate, $currentMonthStart) {
            $query->where(function($a) use ($prevMonthDate, $prevMonthStart) {
                $a->where('contractOutDate', '>=', $prevMonthStart)
                    ->where('contractOutDate', '<=', $prevMonthDate);
            })
            ->orWhere(function($q) use ($currentMonthDate, $currentMonthStart) {
                $q->where('contractOutDate', '>=', $currentMonthStart)
                    ->where('contractOutDate', '<=', $currentMonthDate);
            });
        })
        ->groupBy(DB::raw('dateadd(month, datediff(month, 0, contractOutDate), 0)'))->get();

        $monthsData = array($firstPeriod, $secondPeriod, $thirdPeriod, $fourthPeriod);
        
        $quarters = array(
            $currentQuarterStart,
            $secondQuarterStart,
            $thirdQuarterStart,
            $fourthQuarterStart
        );

        $totalAccounts = $currentMonth->count();

        $completeStaffPhys = $currentMonth->sum('Complete Staff - Phys');
        $completeStaffAPP = $currentMonth->sum('Complete Staff - APP');
        $completeStaffTotal = $currentMonth->sum('Complete Staff - Total');

        $currentOpeningsPhys = $currentMonth->sum('Current Openings - Phys');
        $currentOpeningsAPP = $currentMonth->sum('Current Openings - APP');
        $currentOpeningsTotal = $currentMonth->sum('Current Openings - Total');

        $currentApplications = $accounts->sum('MTD - Applications');
        $currentInterViews = $accounts->sum('MTD - Interviews');
        $currentContractsOut = $accounts->sum('MTD - Contracts Out');
        $currentContractsIn = $accounts->sum('MTD - Contracts In');
        $currentCredentialings = $accounts->sum('MTD - Signed Not Yet Started');

        $prevCompleteStaffPhys = $prevAccounts->sum('Complete Staff - Phys');
        $prevCompleteStaffAPP = $prevAccounts->sum('Complete Staff - APP');
        $prevCompleteStaffTotal = $prevAccounts->sum('Complete Staff - Total');

        $prevCurrentStaffPhys = $prevAccounts->sum('Current Staff - Phys');
        $prevCurrentStaffAPP = $prevAccounts->sum('Current Staff - APP');
        $prevCurrentStaffTotal = $prevAccounts->sum('Current Staff - Total');

        $prevOpeningsTotal = $prevAccounts->sum('Current Openings - Total');


        //// TOP SQUARES INFO /////
        $percentRecruitedPhys = $completeStaffPhys == 0 ? 0 : round((($completeStaffPhys - $currentOpeningsPhys) / $completeStaffPhys) * 100, 2);
        $percentRecruitedAPP = $completeStaffAPP == 0 ? 0 : round((($completeStaffAPP - $currentOpeningsAPP) / $completeStaffAPP) * 100, 2);
        $percentRecruitedTotal = $completeStaffTotal == 0 ? 0 : round((($completeStaffTotal - $currentOpeningsTotal) / $completeStaffTotal) * 100, 2);


        $percentOpenings = $prevOpeningsTotal == 0 ? 0 : round((($currentOpeningsTotal - $prevOpeningsTotal) / $prevOpeningsTotal) * 100, 2);


        $realCurrentApplications = isset($applications[1]) ? $applications[1]->applicationCount : 0;
        $prevApplications = isset($applications[0]) ? $applications[0]->applicationCount : 0;

        $realCurrentInterviews = isset($interviews[1]) ? $interviews[1]->InterviewCount : 0;
        $prevInterviews = isset($interviews[0]) ? $interviews[0]->InterviewCount : 0;

        $realCurrentContractsIn = isset($contractsIn[1]) ? $contractsIn[1]->contractsInCount : 0;
        $prevContractsIn = isset($contractsIn[0]) ? $contractsIn[0]->contractsInCount : 0;

        $realCurrentContractsOut = isset($contractsOut[1]) ? $contractsOut[1]->contractsOutCount : 0;
        $prevContractsOut = isset($contractsOut[0]) ? $contractsOut[0]->contractsOutCount : 0;

        //// BOTTOM SQUARES INFO /////
        $percentApplications = $prevApplications == 0 ? 0 : round((($realCurrentApplications - $prevApplications) / $prevApplications) * 100, 2);
        $percentInterViews = $prevInterviews == 0 ? 0 : round((($realCurrentInterviews - $prevInterviews) / $prevInterviews) * 100, 2);
        $percentContractsOut = $prevContractsOut == 0 ? 0 : round((($realCurrentContractsOut - $prevContractsOut) / $prevContractsOut) * 100, 2);
        $percentContractsIn = $prevContractsIn == 0 ? 0 : round((($realCurrentContractsIn - $prevContractsIn) / $prevContractsIn) * 100, 2);

        $pipeline = [
            "data" => [], 
            "titles" => [
                "Accounts",
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
            "Openings" => $percentOpenings
        ];

        $gauge = ["value" => $percentRecruitedTotal];

        $bars = [
            "contracts" => array(),
            "openings" => array()
        ];

        for($x = 3; $x >= 0; $x--) {
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

            $contractsIn = $monthsData[$x]->sum('MTD - Contracts In');
            $contractsOut = $monthsData[$x]->sum('MTD - Contracts Out');
            $openings = $monthsData[$x]->sum('Current Openings - Total');

            $completeStaffTotal = $monthsData[$x]->sum('Complete Staff - Total');
            $openingsTotal = $monthsData[$x]->sum('Current Openings - Total');


            $lineContractsIn = $completeStaffTotal == 0 ? 0 : round((($completeStaffTotal - $openingsTotal) / $completeStaffTotal * 100), 2);
            $lineContractsInVsOut = $contractsOut == 0 ? 0 : round((($contractsIn / $contractsOut) * 100), 2);

            $tempContracts["bar"] = $contractsIn;
            $tempOpenings["bar"] = $openings;

            $tempContracts["line1"] = $lineContractsIn;
            $tempOpenings["line1"] = $lineContractsInVsOut > 100 ? 100 : $lineContractsInVsOut;

            $bars["contracts"][] = $tempContracts;
            $bars["openings"][] = $tempOpenings;
        }
        
        $pipeline["data"] = [
            $totalAccounts,
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
