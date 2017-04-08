<?php

namespace App\Http\Controllers;

use PDF;
use App\Account;
use App\Division;
use App\Employee;
use App\Practice;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;

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
        $employees = Employee::with('person')->get()->sortBy->fullName();
        $practices = Practice::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $action = 'create';

        $params = compact('account', 'employees', 'practices', 'divisions', 'action');

        return view('admin.accounts.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        $employees = Employee::with('person')->get()->sortBy->fullName();
        $practices = Practice::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $action = 'edit';

        $params = compact('account', 'employees', 'practices', 'divisions', 'action');

        return view('admin.accounts.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, Account $account)
    {
        $request->save($account);

        flash(__('Account updated.'));

        return redirect()->route('admin.accounts.edit', [$account]);
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
     * Upload an image to Storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function image(Request $request)
    {
        $this->validate($request, ['file' => 'required|image']);

        $file = $request->file('file');

        if (! $file->isValid()) {
            return response()->json(['message' => __('Error with uploaded file. Try again.')], 409);
        }

        $path = $file->store('account-images');

        return response()->json(['path' => '/'.$path]);
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

        return $pdf->download('internal-plan.pdf');
    }
}
