<?php

namespace App\Http\Controllers;

use App\RSC;
use App\Region;
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
use App\ProviderDesignation;
use App\Practice;
use App\Scopes\ContractLogScope;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Filters\ContractLogsFilter;
use App\Http\Requests\ContractLogRequest;
use Illuminate\Filesystem\Filesystem;

class ContractLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Filters\ContractLogsFilter
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ContractLogsFilter $filter)
    {
        $user = auth()->user();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        
        $c_coordinators =  $employees->filter->hasPosition(config('instances.position_types.contract_coordinator'));
        $c_managers = $employees->filter->hasPosition(config('instances.position_types.contract_manager'));
        $managers = $employees->filter->hasPosition(config('instances.position_types.manager'));
        $directors = $employees->filter->hasPosition(config('instances.position_types.director'));

        $coordinators = $c_coordinators->merge($c_managers)->merge($managers)->merge($directors)->sortBy(function($coordinator) {
            return $coordinator->fullName();
        });

        $practiceTypes = Practice::all();
        $positions = Position::orderBy('position')->get();
        $statuses = ContractStatus::orderBy('contractStatus')->get();
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $contractLogs = $this->getContractLogsData($filter, 100);

        $params = compact(
            'contractLogs', 'divisions', 'practiceTypes',
            'positions', 'statuses', 'accounts', 'regions',
            'RSCs', 'employees', 'coordinators'
        );

        \Session::put('contractLogFilters', $request->query());

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
        $action = 'create';
        $view = 'admin.contractLogs.create';

        return $this->form($contractLog, $action, $view);
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
    public function edit(Request $request, ContractLog $contractLog)
    {
        $action = 'edit';
        $view = 'admin.contractLogs.edit';

        return $this->form($contractLog, $action, $view);
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
        $contractLogFilters = \Session::get('contractLogFilters');

        $request->save($contractLog);

        flash(__('ContractLog updated.'));

        return redirect()->route('admin.contractLogs.index', $contractLogFilters);
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

    /**
     * Show the form for the specified resource.
     *
     * @param  \App\ContractLog  $contractLog
     * @param  string  $action
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function form($contractLog, $action, $view)
    {
        $contractLog->load('account.division.group', 'account.practices', 'accounts');
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $statuses = ContractStatus::orderBy('contractStatus')->get();
        $specialties = Specialty::orderBy('specialty')->get();
        $employees =  Employee::where('active', true)
            ->with('accountEmployees.positionType', 'person')
            ->get()->sortBy->fullName();
        $recruiters = $employees->filter->hasPosition(config('instances.position_types.recruiter'));
        $managers = $employees->filter->hasPosition(config('instances.position_types.manager'));
        $coordinators = $employees;
        $owners =  $employees->filter->hasPosition(config('instances.position_types.contract_coordinator'));
        $types = ContractType::orderBy('contractType')->get();
        $notes = ContractNote::orderBy('contractNote')->get();
        $positions = Position::orderBy('position')->get();
        $designations = ProviderDesignation::orderBy('name')->get();
        $additionalAccounts = $contractLog->accounts->diff(
            $contractLog->account ? [$contractLog->account] : []
        );
        $additionalRecruiters = $contractLog->recruiters->diff(
            $contractLog->recruiter ? [$contractLog->recruiter] : []
        );

        JavaScript::put(compact('statuses', 'specialties'));

        $params = compact(
            'contractLog', 'accounts', 'statuses', 'specialties', 'recruiters',
            'managers', 'coordinators', 'types', 'notes', 'positions',
            'designations', 'additionalAccounts', 'additionalRecruiters', 'action',
            'owners'
        );

        return view($view, $params);
    }

    /**
     * Toggle the global 'role' scope to current Session.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggleScope()
    {
        $ignore = session('ignore-contract-log-role-scope', false);

        session(['ignore-contract-log-role-scope' => ! $ignore]);

        return back();
    }

    public function exportToExcel(ContractLogsFilter $filter) {
        $dataToExport = $this->getContractLogsData($filter, 5000);
        
        $headers = ["Status", "Main Site Code", "Provider", "Specialty", "Account", "System", "Operating Unit", "RSC",
            "Contract Out Date", "Contract In Date", "# of Days (Contract Out to Contract In)",
            "Sent to Q/A Date", "Counter Sig Date", "Sent To Payroll Date", "# of Days (Contract Out to Payroll)",
            "Provider Start Date", "# of Hours", "Recruiter", "Recruiters", "Manager", "Contract Coordinator", "Contract",
            "(# of times) Revised/Resent", "Phys\MLP", "Value", "Comments"
        ];


        Excel::create('ContractLogs', function($excel) use ($dataToExport, $headers){
            $excel->sheet('ContractLogs', function($sheet) use ($dataToExport, $headers){
                
                $rowNumber = 1;

                $sheet->row($rowNumber, $headers);
                $sheet->row($rowNumber, function($row) {
                    $row->setBackground('#ffff38');
                });
                $sheet->setHeight($rowNumber, 40);

                foreach($dataToExport as $contractLog) {
                    $rowNumber++;
                    $sheet->setHeight($rowNumber, 40);
                    $contractStatus = $contractLog->status ? $contractLog->status->contractStatus : '';
                    $numOfHours = $contractLog->numOfHours;
                    $val = 1;
                    $additionalRecruiters = '';
                    $recruiters = $contractLog->recruiters->diff(
                        $contractLog->recruiter ? [$contractLog->recruiter] : []
                    );

                    foreach ($recruiters as $key => $recruiter) {
                        if (($key+1) == count($recruiters)) {
                            $additionalRecruiters .= $recruiter->fullName();
                        } else {
                            $additionalRecruiters .= $recruiter->fullName().', ';
                        }
                    }

                    $FTContractsOut = $contractLog->contractOutDate ? ($contractStatus == "Guaranteed Shifts" ? $val+0.5 : ($contractStatus == "PT to FT" ? ($numOfHours>=150 ? $val+0.5 : $val) : ($contractStatus == "New-Full Time" ? ($numOfHours>=150 ? $val+0.5 : $val) : 0))) : '';
                    $FTContractsIn = $contractLog->contractInDate == "Inactive" ? "Inactive" : (!$contractLog->contractInDate ? 0 : ($contractStatus == "Guaranteed Shifts" ? $val+0.5 : ($contractStatus == "PT to FT" ? ($numOfHours>=150 ? $val+0.5 : $val) : ($contractStatus == "New-Full Time" ? ($numOfHours>=150 ? $val+0.5 : $val) : 0))));

                    $row = [
                        $contractLog->status ? $contractLog->status->contractStatus : '',
                        $contractLog->account ? $contractLog->account->siteCode : '',
                        $contractLog->providerLastName.', '.$contractLog->providerFirstName.', '.($contractLog->designation ? $contractLog->designation->name : ''),
                        $contractLog->specialty ? $contractLog->specialty->specialty : '',
                        $contractLog->account ? $contractLog->account->name : '',
                        ($contractLog->account && $contractLog->account->systemAffiliation) ? $contractLog->account->systemAffiliation->name : '',
                        ($contractLog->account && $contractLog->account->region) ? $contractLog->account->region->name : '',
                        ($contractLog->account && $contractLog->account->rsc) ? $contractLog->account->rsc->name : '',
                        $contractLog->contractOutDate ? $contractLog->contractOutDate->format('d/m/Y') : '',
                        $contractLog->contractInDate ? $contractLog->contractInDate->format('d/m/Y') : '',
                        $contractLog->contractInDate ? $contractLog->contractInDate->diffInDays($contractLog->contractOutDate) : 'Contract Pending',
                        $contractLog->sentToQADate ? $contractLog->sentToQADate->format('d/m/Y'): '',
                        $contractLog->countersigDate ? $contractLog->countersigDate->format('d/m/Y') : '',
                        $contractLog->sentToPayrollDate ? $contractLog->sentToPayrollDate->format('d/m/Y') : '',
                        $contractLog->sentToPayrollDate ? $contractLog->sentToPayrollDate->diffInDays($contractLog->contractOutDate) : 'Payroll Pending',
                        $contractLog->projectedStartDate ? $contractLog->projectedStartDate->format('d/m/Y') : '',
                        $contractLog->numOfHours,
                        $contractLog->recruiter ? $contractLog->recruiter->fullName() : '',
                        $additionalRecruiters,
                        $contractLog->manager ? $contractLog->manager->fullName() : '',
                        $contractLog->coordinator ? $contractLog->coordinator->fullName() : '',
                        $contractLog->type ? $contractLog->type->contractType : '',
                        '',
                        $contractLog->position ? $contractLog->position->position : '',
                        $contractLog->value,
                        $contractLog->comments
                    ];

                    $sheet->row($rowNumber, $row);
                };

                $sheet->setFreeze('A2');
                $sheet->setAutoFilter('A1:Z1');

                $sheet->cell('A2:A'.$rowNumber, function($cell) {
                    $cell->setBackground('#f5964f');
                });

                $sheet->cells('A1:AJ1', function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri');
                    $cells->setFontSize(10);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('bottom');
                });

                $sheet->cells('A2:AJ'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri');
                    $cells->setFontSize(10);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('bottom');
                });

                $sheet->setWidth(array(
                    'A'     => 18,
                    'B'     => 9,
                    'C'     => 14,
                    'D'     => 11,
                    'E'     => 22,
                    'F'     => 8,
                    'G'     => 9,
                    'H'     => 10,
                    'I'     => 12,
                    'J'     => 15,
                    'K'     => 10,
                    'L'     => 8,
                    'M'     => 11,
                    'N'     => 9,
                    'O'     => 9,
                    'P'     => 8,
                    'Q'     => 11,
                    'R'     => 8,
                    'S'     => 16,
                    'T'     => 12,
                    'U'     => 20,
                    'V'     => 8,
                    'W'     => 17,
                    'X'     => 10,
                    'Y'     => 10,
                    'Z'     => 50
                ));

                $sheet->setColumnFormat(array(
                    'I2:J'.$rowNumber      => 'mm-dd-yy',
                    'L2:N'.$rowNumber      => 'mm-dd-yy',
                    'P2:P'.$rowNumber      => 'mm-dd-yy',
                ));

                $tableStyle = array(
                    'borders' => array(
                        'inside' => array(
                            'style' => 'thin',
                            'color' => array('rgb' => '000000'),
                        ),
                    ),
                );

                $sheet->getStyle('A1:Z'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A1:Z1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('Z2:Z'.$rowNumber)->getAlignment()->setWrapText(true);
            });
        })->download('xlsx'); 
    }

    private function getContractLogsData(ContractLogsFilter $filter, $results) {
        return ContractLog::withGlobalScope('role', new ContractLogScope)->leftJoin('tContractStatus', 'tContractLogs.statusId', '=', 'tContractStatus.id')
            ->leftJoin('tPosition', 'tContractLogs.positionId', '=', 'tPosition.id')
            ->leftJoin('tPractice', 'tContractLogs.practiceId', '=', 'tPractice.id')
            ->leftJoin('tAccount', 'tContractLogs.accountId', '=', 'tAccount.id')
            ->leftJoin('tContractNote', 'tContractLogs.contractNoteId', '=', 'tContractNote.id')
            ->leftJoin('tDivision', 'tContractLogs.divisionId', '=', 'tDivision.id')
            ->leftJoin('tGroup', 'tDivision.groupId', '=', 'tGroup.id')
            ->leftJoin('tProviderDesignation', 'tContractLogs.providerDesignationId', '=', 'tProviderDesignation.id')
            ->select('tContractLogs.*')
            ->with('status', 'position', 'practice', 'division.group', 'note', 'account', 'designation',
                'specialty', 'recruiter', 'manager', 'coordinator', 'type', 'status', 'account.region', 'account.rsc')
            ->where('tContractLogs.active', true)->filter($filter)->paginate($results);
    }

    public function exportAll() {
        $email = \Auth::user()->email;

        \Artisan::queue('export-contract-logs', [
            'email' => $email
        ]);

        flash(__('An email will be sent to your email within the next 10 minutes.'));

        return back();
    }

    public function downloadZip(Request $request) {
        $timestamp = $request->timestamp;
        $fileSystem = new Filesystem;

        $file = public_path('contract_logs_'.$timestamp.'.zip');

        if($fileSystem->exists($file)) {
            return response()->download($file)->deleteFileAfterSend(true);
        } else {
            flash(__("That file has already been downloaded and it's not on the server anymore."));

            return redirect()->route('admin.contractLogs.index');
        }
    }
}
