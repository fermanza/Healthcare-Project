<?php

namespace App\Http\Controllers;

use JavaScript;
use App\Account;
use App\Division;
use App\Employee;
use App\Position;
use App\Specialty;
use App\ContractLog;
use App\ContractNote;
use App\ContractType;
use App\ContractStatus;
use App\AccountEmployee;
use App\EmployementStatus;
use Illuminate\Http\Request;
use App\Filters\ContractLogsFilter;
use App\Http\Requests\ContractLogRequest;

class ContractLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Filters\ContractLogsFilter
     * @return \Illuminate\Http\Response
     */
    public function index(ContractLogsFilter $filter)
    {
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $practiceTypes = ['ED', 'IPS'];
        $positions = Position::orderBy('position')->get();
        $statuses = ContractStatus::orderBy('contractStatus')->get();
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $contractLogs = ContractLog::leftJoin('tContractStatus', 'tContractLogs.statusId', '=', 'tContractStatus.id')
            ->leftJoin('tPosition', 'tContractLogs.positionId', '=', 'tPosition.id')
            ->leftJoin('tPractice', 'tContractLogs.practiceId', '=', 'tPractice.id')
            ->leftJoin('tAccount', 'tContractLogs.accountId', '=', 'tAccount.id')
            ->leftJoin('tContractNote', 'tContractLogs.contractNoteId', '=', 'tContractNote.id')
            ->leftJoin('tDivision', 'tContractLogs.divisionId', '=', 'tDivision.id')
            ->leftJoin('tGroup', 'tDivision.groupId', '=', 'tGroup.id')
            ->select('tContractLogs.*')
            ->with([
                'status', 'position', 'practice', 'account', 'division.group', 'note',
            ])->where('tContractLogs.active', true)->filter($filter)->paginate();

        $params = compact(
            'contractLogs', 'divisions', 'practiceTypes',
            'positions', 'statuses', 'accounts'
        );

        return view('admin.contractLogs.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $contractLog = $request->has('id') ? ContractLog::findOrFail($request->id) : new ContractLog;
        $contractLog->load('account.division.group', 'account.practices');
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $statuses = ContractStatus::orderBy('contractStatus')->get();
        $specialties = Specialty::orderBy('specialty')->get();
        $employees =  Employee::where('active', true)
                                ->with('accountEmployees.positionType', 'person')
                                ->get()->sortBy->fullName();
        $recruiters = $employees->filter->hasPosition(config('instances.position_types.recruiter'));
        $managers = $employees->filter->hasPosition(config('instances.position_types.manager'));
        $coordinators = $employees;
        $types = ContractType::orderBy('contractType')->get();
        $notes = ContractNote::orderBy('contractNote')->get();
        $positions = Position::orderBy('position')->get();
        $action = 'create';

        JavaScript::put(compact('statuses'));

        $params = compact(
            'contractLog', 'accounts', 'statuses', 'specialties', 'recruiters',
            'managers', 'coordinators', 'types', 'notes', 'positions', 'action'
        );

        return view('admin.contractLogs.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ContractLogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractLogRequest $request)
    {
        $contractLog = new ContractLog;
        $request->save($contractLog);

        flash(__('ContractLog created.'));

        return redirect()->route('admin.contractLogs.index');
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
     * @param  \App\ContractLog  $contractLog
     * @return \Illuminate\Http\Response
     */
    public function edit(ContractLog $contractLog)
    {
        $contractLog->load('account.division.group', 'account.practices');
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $statuses = ContractStatus::orderBy('contractStatus')->get();
        $specialties = Specialty::orderBy('specialty')->get();
        $employees =  Employee::where('active', true)
                                ->with('accountEmployees.positionType', 'person')
                                ->get()->sortBy->fullName();
        $recruiters = $employees->filter->hasPosition(config('instances.position_types.recruiter'));
        $managers = $employees->filter->hasPosition(config('instances.position_types.manager'));
        $coordinators = $employees;
        $types = ContractType::orderBy('contractType')->get();
        $notes = ContractNote::orderBy('contractNote')->get();
        $positions = Position::orderBy('position')->get();
        $action = 'edit';

        JavaScript::put(compact('statuses'));

        $params = compact(
            'contractLog', 'accounts', 'statuses', 'specialties', 'recruiters',
            'managers', 'coordinators', 'types', 'notes', 'positions', 'action'
        );

        return view('admin.contractLogs.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ContractLogRequest  $request
     * @param  \App\ContractLog  $contractLog
     * @return \Illuminate\Http\Response
     */
    public function update(ContractLogRequest $request, ContractLog $contractLog)
    {
        $request->save($contractLog);

        flash(__('ContractLog updated.'));

        return redirect()->route('admin.contractLogs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContractLog  $contractLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractLog $contractLog)
    {
        $contractLog->active = false;
        $contractLog->save();

        flash(__('ContractLog deleted.'));

        return back();
    }
}
