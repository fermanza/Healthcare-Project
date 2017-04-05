<?php

namespace App\Http\Controllers;

use App\Account;
use App\Division;
use App\Employee;
use App\Practice;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::all();

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

        $params = compact('account', 'employees', 'practices', 'divisions');

        return view('admin.accounts.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $account = new Account;
        $account->name = $request->name;
        $account->site_code = $request->site_code;
        $account->recruiter_id = $request->recruiter_id;
        $account->manager_id = $request->manager_id;
        $account->practice_id = $request->practice_id;
        $account->division_id = $request->division_id;
        $account->google_address = $request->google_address;
        $account->street = $request->street;
        $account->number = $request->number;
        $account->city = $request->city;
        $account->state = $request->state;
        $account->zip_code = $request->zip_code;
        $account->country = $request->country;
        $account->start_date = $request->start_date ? $request->start_date.':00': null;
        $account->physicians_needed = $request->physicians_needed;
        $account->apps_needed = $request->apps_needed;
        $account->physician_hours_per_month = $request->physician_hours_per_month;
        $account->app_hours_per_month = $request->app_hours_per_month;
        $account->press_release = $request->press_release ?: false;
        $account->press_release_date = $request->press_release_date;
        $account->management_change_mailers = $request->management_change_mailers ?: false;
        $account->recruiting_mailers = $request->recruiting_mailers ?: false;
        $account->email_blast = $request->email_blast ?: false;
        $account->purl_campaign = $request->purl_campaign ?: false;
        $account->marketing_slick = $request->marketing_slick ?: false;
        $account->collaboration_recruiting_team = $request->collaboration_recruiting_team ?: false;
        $account->collaboration_recruiting_team_names = $request->collaboration_recruiting_team_names;
        $account->compensation_grid = $request->compensation_grid ?: false;
        $account->compensation_grid_bonuses = $request->compensation_grid_bonuses;
        $account->recruiting_incentives = $request->recruiting_incentives ?: false;
        $account->recruiting_incentives_description = $request->recruiting_incentives_description;
        $account->locum_companies_notified = $request->locum_companies_notified ?: false;
        $account->search_firms_notified = $request->search_firms_notified ?: false;
        $account->departments_coordinated = $request->departments_coordinated ?: false;
        $account->save();

        return back();
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
