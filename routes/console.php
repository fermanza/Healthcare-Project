<?php

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('set-new-routes', function () {
    $aclNames = collect(config('acl'))->collapse();
    App\Permission::unguard();

    collect(Route::getRoutes())
        ->filter(function ($route) {
            return in_array('acl', $route->middleware());
        })->each(function ($route) use ($aclNames) {
            App\Permission::firstOrCreate([
                'name' => $route->getName(),
            ], [
                'display_name' => array_get($aclNames, $route->getName()),
            ]);
        });

    App\Permission::reguard();
})->describe('Register any new route to the Permissions table');

    Artisan::command('export-contract-logs', function () {

    $count = 1;

    App\ContractLog::withGlobalScope('role', new App\Scopes\ContractLogScope)
    ->leftJoin('tContractStatus', 'tContractLogs.statusId', '=', 'tContractStatus.id')
    ->leftJoin('tPosition', 'tContractLogs.positionId', '=', 'tPosition.id')
    ->leftJoin('tPractice', 'tContractLogs.practiceId', '=', 'tPractice.id')
    ->leftJoin('tAccount', 'tContractLogs.accountId', '=', 'tAccount.id')
    ->leftJoin('tContractNote', 'tContractLogs.contractNoteId', '=', 'tContractNote.id')
    ->leftJoin('tDivision', 'tContractLogs.divisionId', '=', 'tDivision.id')
    ->leftJoin('tGroup', 'tDivision.groupId', '=', 'tGroup.id')
    ->leftJoin('tProviderDesignation', 'tContractLogs.providerDesignationId', '=', 'tProviderDesignation.id')
    ->select('tContractLogs.*')
    ->with('status', 'position', 'practice', 'division.group', 'note', 'account', 'designation',
        'specialty', 'recruiter', 'manager', 'coordinator', 'type', 'status')
    ->where('tContractLogs.active', true)->chunk(2500, function($contractLogs) {
        $headers = ["Status", "Main Site Code", "Provider", "Specialty", "Account", "System", "Operating Unit", "RSC",
            "Contract Out Date", "Contract In Date", "# of Days (Contract Out to Contract In)",
            "Sent to Q/A Date", "Counter Sig Date", "Sent To Payroll Date", "# of Days (Contract Out to Payroll)",
            "Provider Start Date", "# of Hours", "Recruiter", "Manager", "Contract Coordinator", "Contract",
            "(# of times) Revised/Resent", "Comments"
        ];

        Maatwebsite\Excel\Facades\Excel::create('Contract Logs - '.$count, function($excel) use ($contractLogs, $headers){
            $excel->sheet('Summary', function($sheet) use ($contractLogs, $headers){
                
                $rowNumber = 1;

                $sheet->row($rowNumber, $headers);
                $sheet->row($rowNumber, function($row) {
                    $row->setBackground('#ffff38');
                });
                $sheet->setHeight($rowNumber, 40);

                foreach($contractLogs as $contractLog) {
                    $rowNumber++;
                    $sheet->setHeight($rowNumber, 40);
                    $contractStatus = $contractLog->status ? $contractLog->status->contractStatus : '';
                    $numOfHours = $contractLog->numOfHours;
                    $val = 1;

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
                        $contractLog->manager ? $contractLog->manager->fullName() : '',
                        $contractLog->coordinator ? $contractLog->coordinator->fullName() : '',
                        $contractLog->type ? $contractLog->type->contractType : '',
                        '',
                        $contractLog->comments
                    ];

                    $sheet->row($rowNumber, $row);
                };

                $sheet->setFreeze('A2');
                $sheet->setAutoFilter('A1:W1');

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
                    'U'     => 8,
                    'V'     => 17,
                    'W'     => 50
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

                $sheet->getStyle('A1:W'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A1:W1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('W2:W'.$rowNumber)->getAlignment()->setWrapText(true);
            });
        })->store('xlsx', public_path('contractLogs'), true);

        $count++;
    });
    
})->describe('Download an excel file with all contract logs');

Artisan::command('sync-current-contractlogs-to-accounts', function () {
    App\ContractLog::all()->each(function ($contractLog) {
        $contractLog->accounts()->syncWithoutDetaching([$contractLog->accountId]);
    });
})->describe('Sync the existing Contract Logs with their initial Account to pivot');

Artisan::command('sync-current-contractlogs-to-employees', function () {
    App\ContractLog::all()->each(function ($contractLog) {
        $contractLog->recruiters()->syncWithoutDetaching([$contractLog->recruiterId]);
    });
})->describe('Sync the existing Contract Logs with their initial Recruiter to pivot');

Artisan::command('create-initial-pipeline-to-accounts', function () {
    App\Account::with('pipeline')->get()->each(function ($account) {
        if (! $account->pipeline) {
            $pipeline = new App\Pipeline;
            $pipeline->accountId = $account->id;
            $pipeline->save();
        }
    });
})->describe('Creates an initial Pipeline to existing Accounts');
