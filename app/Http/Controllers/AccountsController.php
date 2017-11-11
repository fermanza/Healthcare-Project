<?php

namespace App\Http\Controllers;

use PDF;
use App\RSC;
use App\Region;
use JavaScript;
use App\Account;
use App\Division;
use App\Employee;
use App\Practice;
use App\StateAbbreviation;
use App\SystemAffiliation;
use DB;
use App\Scopes\AccountScope;
use App\Filters\AccountFilter;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AccountsController extends Controller
{

    private function validateUser() {
        $user = auth()->user();
        
        if (! $user || session('ignore-account-role-scope')) {
            return;
        }

        $builder = '';

        if ($user->hasRoleId(config('instances.roles.manager'))) {
            $builder = $this->check($user, config('instances.position_types.manager'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder = $this->check($user, config('instances.position_types.recruiter'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder = $this->check($user, config('instances.position_types.contract_coordinator'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder = $this->check($user, config('instances.position_types.director'), 'directorId');
        } else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder = $this->check($user, config('instances.position_types.dca'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other_view'))) {
            $builder = $this->check($user, config('instances.position_types.other'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other_edit'))) {
            $builder = $this->check($user, config('instances.position_types.other'), 'employeeId');
        }  else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder = $this->check($user, config('instances.position_types.credentialer'), 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.vp_of_operations'))) {
            $builder = $this->check($user, config('instances.position_types.vp_of_operations'), 'employeeId');
        }

        return $builder;
    }

    private function check($user, $role, $employeeType) {
        if (!$user->RSCs->isEmpty() && !$user->operatingUnits->isEmpty()) {

            $RSCs = $user->RSCs->map(function($RSC) {
                return $RSC->id;
            });

            $RSCs = implode (", ", $RSCs->toArray());


            $operatingUnits = $user->operatingUnits->map(function($operatingUnit) {
                return $operatingUnit->id;
            });

            $operatingUnits = implode (", ", $operatingUnits->toArray());

            $builder = "AND acc.RSCId IN ($RSCs) AND acc.operatingUnitId IN ($operatingUnits) AND acc.RSCId IS NOT NULL AND acc.operatingUnitId IS NOT NULL";
        } else if (!$user->RSCs->isEmpty() && $user->operatingUnits->isEmpty()) {
            $RSCs = $user->RSCs->map(function($RSC) {
                return $RSC->id;
            });

            $RSCs = $RSCs == null ? '' : implode (", ", $RSCs->toArray());

            $builder = "AND acc.RSCId IN ($RSCs) AND acc.RSCId IS NOT NULL";
        } else if ($user->RSCs->isEmpty() && !$user->operatingUnits->isEmpty()) {
            $operatingUnits = $user->operatingUnits->map(function($operatingUnit) {
                return $operatingUnit->id;
            });

            $operatingUnits = implode (", ", $operatingUnits->toArray());

            $builder = "AND acc.operatingUnitId IN ($operatingUnits) AND acc.operatingUnitId IS NOT NULL";
        } else {
            if($role == config('instances.position_types.recruiter')) {
                $builder = "AND (tae.accountId = acc.id AND tae.positionTypeId = $role and tae.isPrimary = 1 and tae.".$employeeType." = ".$user->employeeId.") OR (tae.accountId = acc.id AND tae.positionTypeId = $role and tae.isPrimary = 0 and tae.".$employeeType." = ".$user->employeeId.")";
            } elseif ($role == config('instances.position_types.director')) {
                $builder = "AND rsc.".$employeeType." = ".($user->employeeId ? $user->employeeId : '0')." AND rsc.".$employeeType." IS NOT NULL";
            } else {
                $builder = "AND tae.accountId = acc.id AND positionTypeId = $role  and tae.".$employeeType." = ".($user->employeeId ? $user->employeeId : '0')." AND ".$employeeType." IS NOT NULL";
            }
        }

        return $builder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, AccountFilter $filter)
    {

        $user_filter = $this->validateUser();

        $RSC_values = $request->RSCs;
        $RSC_values = $RSC_values == null ? '' : implode (", ", $RSC_values);
        $RSC_filter = $RSC_values == '' ? '' : 'AND acc.RSCId IN ('.$RSC_values.')';

        //  use tae with (Nolock) on production
        $accounts = Account::hydrate(DB::select(DB::raw("SELECT accounts.id
            , accounts.name
            , accounts.siteCode
            , accounts.city
            , accounts.state
            , accounts.startDate
            , accounts.endDate
            , accounts.parentSiteCode
            , accounts.RSC
            , accounts.operatingUnit
            , MAX(accounts.recruiter) as recruiter
            , MAX(accounts.manager) as manager
            , MAX(accounts.credentialer) as credentialer
        FROM (
        SELECT DISTINCT acc.id, acc.name, acc.siteCode, acc.city, acc.state, acc.startDate, acc.endDate, acc.parentSiteCode,
        rsc.name AS RSC, ou.name AS operatingUnit,
        CASE
            WHEN tae.positionTypeId = ".config('instances.position_types.recruiter')." AND isPrimary = 1 
                THEN fullName
        END AS recruiter,
        CASE
            WHEN tae.positionTypeId = ".config('instances.position_types.manager')."
                THEN fullName
        END AS manager,
        CASE
            WHEN tae.positionTypeId = ".config('instances.position_types.credentialer')."
                THEN fullName
        END AS credentialer
        FROM tAccount acc
        LEFT JOIN tRSC rsc on acc.RSCId = rsc.id
        LEFT JOIN tOperatingUnit ou on acc.operatingUnitId = ou.id
        LEFT JOIN (
           SELECT * FROM (
                SELECT accountId, employeeId, positionTypeId, isPrimary, fullName
                FROM vAccountToEmployee tae 
                 
            ) tae
        ) tae 
        ON acc.id = tae.accountId
        WHERE acc.active = '1' AND (acc.endDate IS NULL OR acc.endDate > '".Carbon::now()->format('Y-m-d')."')
        $user_filter
        $RSC_filter
        ) accounts 
        group by accounts.id
            , accounts.name
            , accounts.siteCode
            , accounts.city
            , accounts.state
            , accounts.startDate
            , accounts.endDate
            , accounts.parentSiteCode
            , accounts.RSC
            , accounts.operatingUnit")));

        $RSCs = RSC::where('active', true)->orderBy('name')->get();

        return view('admin.accounts.index', compact('accounts', 'RSCs'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function termed(Request $request, AccountFilter $filter)
    {
        $accounts = Account::select('id','name','siteCode','city','state','startDate','endDate','parentSiteCode','RSCId','operatingUnitId')
            ->withGlobalScope('role', new AccountScope)
            ->with([
                'rsc',
                'region',
                'recruiter.employee.person',
                'manager.employee.person',
            ])
            ->where('active', true)
            ->filter($filter)->termed(true)->get();

        $RSCs = RSC::where('active', true)->orderBy('name')->get();

        return view('admin.accounts.index', compact('accounts', 'RSCs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = new Account;
        $account->hasSMD = 1;
        
        $action = 'create';
        $view = 'admin.accounts.create';

        return $this->form($account, $action, $view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        $account = new Account;
        $request->save($account);

        Cache::forget('accounts'.$request->user()->id);

        flash(__('Account created.'));

        return redirect()->route('admin.accounts.edit', [$account]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return $account->load('division.group', 'practices');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        $action = 'edit';
        $view = 'admin.accounts.edit';

        return $this->form($account, $action, $view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AccountRequest  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, Account $account)
    {
        $request->save($account);

        Cache::forget('accounts'.$request->user()->id);

        flash(__('Account updated.'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Account $account)
    {
        $account->active = false;
        $account->save();

        Cache::forget('accounts'.$request->user()->id);

        flash(__('Account deleted.'));

        return back();
    }

    /**
     * Show the form for the specified resource.
     *
     * @param  \App\Account  $account
     * @param  string  $action
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function form($account, $action, $view)
    {
        $account->load('siteCodes', 'physiciansApps', 'practices', 'recruiter', 'recruiters', 'manager');
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $recruiters =  $employees->filter->hasPosition(config('instances.position_types.recruiter'));
        $managers =  $employees->filter->hasPosition(config('instances.position_types.manager'));
        $credentialers = $employees->filter->hasPosition(config('instances.position_types.credentialer'));
        $dcss = $employees->filter->hasPosition(config('instances.position_types.dcs'));
        $schedulers = $employees->filter->hasPosition(config('instances.position_types.scheduler'));
        $enrollments = $employees->filter->hasPosition(config('instances.position_types.enrollment'));
        $payrolls = $employees->filter->hasPosition(config('instances.position_types.payroll'));
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $states = StateAbbreviation::all();
        $oldAccount = old('siteCode') ? Account::where('siteCode', old('siteCode'))->first() : null;

        $params = compact('account', 'recruiters', 'managers', 'practices', 
            'divisions', 'RSCs', 'regions', 'action', 'states', 'credentialers',
            'dcss', 'schedulers', 'enrollments', 'payrolls', 'affiliations', 'oldAccount'
        );

        return view($view, $params);
    }

    /**
     * Upload an image to Storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function image(Request $request)
    {
        $this->validate($request, ['file' => 'required|image']);

        $path = $request->file('file')->store('account-images');

        return response()->json(['path' => '/storage/'.$path]);
    }

    /**
     * Streams the Account's internal plan.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function internalPlan(Account $account)
    {
        $pdf = PDF::loadView('pdfs.internal-plan', compact('account'));
        $fileName = str_slug($account->name).'-'.__('internal-plan.pdf');

        return $pdf->download($fileName);
    }

    /**
     * Merge Site Code of given Account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function merge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accounts' => 'required|array|exists:tAccount,id',
            'siteCode' => 'required|exists:tAccount,siteCode',
        ]);

        if ($validator->fails()) {
            flash(__('Account or Site Code does not exist.'), 'error');

            return back();
        }

        Account::whereIn('id', $request->accounts)->update([
            'mergedSiteCode' => $request->siteCode,
            'active' => false,
        ]);

        flash(__('Account Merged.'));

        Cache::forget('accounts'.$request->user()->id);

        return back();
    }

    /**
     * Set Parent Site Code to given Account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function parent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accounts' => 'required|array|exists:tAccount,id',
            'siteCode' => 'required|exists:tAccount,siteCode',
        ]);

        if ($validator->fails()) {
            flash(__('Account or Site Code does not exist.'), 'error');

            return back();
        }

        Account::whereIn('id', $request->accounts)->update([
            'parentSiteCode' => $request->siteCode,
        ]);

        flash(__('Parent Site Code has been set.'));

        Cache::forget('accounts'.$request->user()->id);

        return back();
    }

    /**
     * Set Parent Site Code to given Account.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function removeParent(Request $request, Account $account)
    {
        $account->parentSiteCode = null;
        $account->save();

        flash(__('Parent Site Code has been unset.'));

        Cache::forget('accounts'.$request->user()->id);

        return back();
    }

    /**
     * Toggle the global 'role' scope to current Session.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggleScope(Request $request)
    {
        $ignore = session('ignore-account-role-scope', false);

        session(['ignore-account-role-scope' => ! $ignore]);

        Cache::forget('accounts'.$request->user()->id);

        return back();
    }

    /**
     * Toggle the 'view child sites' to current Session.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggleChildren(Request $request)
    {
        $ignore = session('see-child-accounts', false);

        session(['see-child-accounts' => ! $ignore]);

        Cache::forget('accounts'.$request->user()->id);

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function findManager(Account $account)
    {
        return $account->manager;
    }
}
