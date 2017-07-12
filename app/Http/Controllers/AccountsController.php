<?php

namespace App\Http\Controllers;

use PDF;
use App\RSC;
use JavaScript;
use App\Account;
use App\Division;
use App\Employee;
use App\Practice;
use App\Scopes\AccountScope;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use Illuminate\Support\Facades\Validator;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::where('active', true)->get();

        return view('admin.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = new Account;
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

        flash(__('Account updated.'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        $account->active = false;
        $account->save();

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
        $account->load('siteCodes', 'physiciansApps', 'practices', 'recruiter', 'manager');
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();

        $params = compact('account', 'employees', 'practices', 'divisions', 'RSCs', 'action');

        JavaScript::put([
            'account' => [
                'physiciansNeeded' => $account->physiciansNeeded,
                'appsNeeded' => $account->appsNeeded,
                'physicianHoursPerMonth' => $account->physicianHoursPerMonth,
                'appHoursPerMonth' => $account->appHoursPerMonth,
            ],
        ]);

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

        return back();
    }

    /**
     * Set Parent Site Code to given Account.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function removeParent(Account $account)
    {
        $account->parentSiteCode = null;
        $account->save();

        flash(__('Parent Site Code has been unset.'));

        return back();
    }
}
