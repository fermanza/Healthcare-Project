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

Artisan::command('export-contract-logs {email} {--queue}', function ($email) {

    $timestamp = \Carbon\Carbon::now()->timestamp;

    $contractLogs = App\ContractLog::withGlobalScope('role', new App\Scopes\ContractLogScope)
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
    ->where('tContractLogs.active', true)->get();

    $headers = ["Status", "Main Site Code", "Provider", "Specialty", "Account", "System", "Operating Unit", "RSC",
        "Contract Out Date", "Contract In Date", "# of Days (Contract Out to Contract In)",
        "Sent to Q/A Date", "Counter Sig Date", "Sent To Payroll Date", "# of Days (Contract Out to Payroll)",
        "Provider Start Date", "# of Hours", "Recruiter", "Recruiters", "Manager", "Contract Coordinator", "Contract",
        "(# of times) Revised/Resent", "Phys\MLP", "Value", "Reason", "Comments"
    ];  

    Maatwebsite\Excel\Facades\Excel::create('Contract_Logs_'.$timestamp, function($excel) use ($contractLogs, $headers){
        $excel->sheet('Contract Logs', function($sheet) use ($contractLogs, $headers){
            
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
                $additionalRecruiters = '';
                $recruiters = $contractLog->recruiters->diff(
                    $contractLog->recruiter ? [$contractLog->recruiter] : []
                );

                $startDate = $contractLog->projectedStartDate ? $contractLog->projectedStartDate->format('m/d/Y') : '';

                if($startDate != '' && $startDate == \Carbon\Carbon::now()->format('m/d/Y')) {
                    $sheet->cell('A'.$rowNumber.':Z'.$rowNumber, function($cell) {
                        $cell->setFontColor('#ff0000');
                        $cell->setFontWeight('bold');
                    });
                }

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
                    $contractLog->contractOutDate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->contractOutDate) : '',
                    $contractLog->inactive ? 'Inactive' : ($contractLog->contractInDate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->contractInDate) : ''),
                    $contractLog->contractInDate ? $contractLog->contractInDate->diffInDays($contractLog->contractOutDate) : 'Contract Pending',
                    $contractLog->sentToQADate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->sentToQADate): '',
                    $contractLog->countersigDate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->countersigDate) : '',
                    $contractLog->sentToPayrollDate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->sentToPayrollDate) : '',
                    $contractLog->sentToPayrollDate ? $contractLog->sentToPayrollDate->diffInDays($contractLog->contractOutDate) : 'Payroll Pending',
                    $contractLog->projectedStartDate ? \PHPExcel_Shared_Date::PHPToExcel($contractLog->projectedStartDate) : '',
                    $contractLog->numOfHours,
                    $contractLog->recruiter ? $contractLog->recruiter->fullName() : '',
                    $additionalRecruiters,
                    $contractLog->manager ? $contractLog->manager->fullName() : '',
                    $contractLog->coordinator ? $contractLog->coordinator->fullName() : '',
                    $contractLog->type ? $contractLog->type->contractType : '',
                    '',
                    $contractLog->position ? $contractLog->position->position : '',
                    $contractLog->value,
                    $contractLog->note ? $contractLog->note->contractNote : '',
                    $contractLog->comments
                ];

                $sheet->row($rowNumber, $row);
            };

            $sheet->setFreeze('A2');
            $sheet->setAutoFilter('A1:AA1');

            $sheet->cells('A2:A'.$rowNumber, function($cells) {
                $cells->setBackground('#f5964f');
            });

            $sheet->cells('A1:AJ1', function($cells) {
                $cells->setFontFamily('Calibri');
                $cells->setFontSize(10);
                $cells->setFontWeight('bold');
                $cells->setAlignment('center');
                $cells->setValignment('bottom');
            });

            $sheet->cells('A2:AJ'.$rowNumber, function($cells) {
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
                'Z'     => 40,
                'AA'    => 50,
            ));

            $sheet->setColumnFormat(array(
                'I2:J'.$rowNumber      => 'mm/dd/yy',
                'L2:N'.$rowNumber      => 'mm/dd/yy',
                'P2:P'.$rowNumber      => 'mm/dd/yy',
            ));

            $tableStyle = array(
                'borders' => array(
                    'inside' => array(
                        'style' => 'thin',
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $sheet->getStyle('A1:AA'.$rowNumber)->applyFromArray($tableStyle);
            $sheet->getStyle('A1:AA1')->getAlignment()->setWrapText(true);
            $sheet->getStyle('Z2:Z'.$rowNumber)->getAlignment()->setWrapText(true);
            $sheet->getStyle('AA2:AA'.$rowNumber)->getAlignment()->setWrapText(true);
        });
    })->store('xlsx', public_path('contract_logs'), true);

    $user = new App\User;
    $user->email = $email;

    $user->notify(new App\Notifications\ContractLogNotification($timestamp));
    
})->describe('Download an excel file with all contract logs');

Artisan::command('export-accounts-pdf {email} {ids} {--queue}', function ($email, $ids) {
    $timestamp = \Carbon\Carbon::now()->timestamp;

    function createRecruitingTable($sheet, $account, $benchTableStartData, $recruitings) {
        $recruitingTableStart = $benchTableStartData+2;

        $sheet->mergeCells('A'.$recruitingTableStart.':F'.$recruitingTableStart);
        $sheet->mergeCells('G'.$recruitingTableStart.':H'.$recruitingTableStart);
        $sheet->mergeCells('E'.($recruitingTableStart+1).':I'.($recruitingTableStart+1));

        $sheet->cell('A'.$recruitingTableStart, function($cell) use ($account) {
            $cell->setValue('Recruiting Pipeline');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('G'.$recruitingTableStart, function($cell) use ($account) {
            $cell->setValue('Candidates');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('I'.$recruitingTableStart, function($cell) use ($account) {
            $cell->setBackground('#c1e7c9');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('A'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setValue('FT/PT');
        });

        $sheet->cell('B'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Provider');
        });

        $sheet->cell('C'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setValue('PHYS\APP');
        });

        $sheet->cell('D'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Stage');
        });

        $sheet->cell('E'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Notes');
        });

        $sheet->cell('A'.($recruitingTableStart+1).':E'.($recruitingTableStart+1), function($cell) use ($account) {
            $cell->setBackground('#fff1ce');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $recruitingTableDataStart = $recruitingTableStart+2;

        foreach ($recruitings as $recruiting) {
            $sheet->mergeCells('E'.$recruitingTableDataStart.':I'.$recruitingTableDataStart);

            $row = [
                strtoupper($recruiting->contract),
                $recruiting->name,
                strtoupper($recruiting->type),
                '',
                $recruiting->notes
            ];

            $sheet->row($recruitingTableDataStart, $row);

            $recruitingTableDataStart++;
        }

        $sheet->cell('E'.($recruitingTableStart+2).':E'.($recruitingTableDataStart), function($cell) use ($account) {
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(8);
            $cell->setAlignment('left');
            $cell->setValignment('center');
        });

        return array($recruitingTableStart, $recruitingTableDataStart);
    }

    function roundnum($num, $nearest){ 
        return round($num / $nearest) * $nearest; 
    } 

    function createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp) {
        $SMD = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->isSMD;
        });

        $SMD = $SMD->sortBy(function($s) {
            return strtolower($s->name);
        });

        $sheet->cell('H4', function($cell) use ($account) {
            $cell->setBackground('#b5c7e6');
            $cell->setValue('Team Members');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cells('H5:H14', function($cells) {
            $cells->setBackground('#fff1ce');
        });

        $sheet->cells('H15:I18', function($cells) {
            $cells->setBackground('#b5c7e6');
        });

        $sheet->cells('H5:I18', function($cells) {
            $cells->setFontFamily('Calibri (Body)');
            $cells->setFontSize(11);
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });

        $sheet->cell('H5', function($cell) use ($account) {
            $cell->setValue('SMD');
        });
        $sheet->cell('H6', function($cell) use ($account) {
            $cell->setValue('SVP');
        });
        $sheet->cell('H7', function($cell) use ($account) {
            $cell->setValue('RMD');
        });
        $sheet->cell('H8', function($cell) use ($account) {
            $cell->setValue('DOO');
        });
        $sheet->cell('H9', function($cell) use ($account) {
            $cell->setValue('DCS');
        });
        $sheet->cell('H10', function($cell) use ($account) {
            $cell->setValue('Recruiter');
        });
        $sheet->cell('H11', function($cell) use ($account) {
            $cell->setValue('Credentialer');
        });
        $sheet->cell('H12', function($cell) use ($account) {
            $cell->setValue('Scheduler');
        });
        $sheet->cell('H13', function($cell) use ($account) {
            $cell->setValue('Enrollment');
        });
        $sheet->cell('H14', function($cell) use ($account) {
            $cell->setValue('Payroll');
        });
        $sheet->cell('H15', function($cell) use ($account) {
            $cell->setValue('Physician Opens');
        });
        $sheet->cell('H16', function($cell) use ($account) {
            $cell->setValue('APP Opens');
        });
        $sheet->cell('H17', function($cell) use ($account) {
            $cell->setValue('Prev Month - Inc Comp');
        });
        $sheet->cell('H18', function($cell) use ($account) {
            $cell->setValue('YTD - Inc Comp');
        });

        $sheet->cell('I5', function($cell) use ($SMD) {
            if ($SMD->isEmpty()) {
                $cell->setValue('OPEN');
                $cell->setBackground('#FFFF00');
                $cell->setFontWeight('bold');
            } else {
                $cell->setValue($SMD->first()->name);
            }
        });
        $sheet->cell('I6', function($cell) use ($account) {
            $cell->setValue($account->pipeline->svp);
        });
        $sheet->cell('I7', function($cell) use ($account) {
            $cell->setValue($account->pipeline->rmd);
        });
        $sheet->cell('I8', function($cell) use ($account) {
            $cell->setValue($account->pipeline->dca);
        });
        $sheet->cell('I9', function($cell) use ($account) {
            $cell->setValue($account->dcs ? $account->dcs->fullName() : '');
        });
        $sheet->cell('I10', function($cell) use ($account) {
            $cell->setValue($account->recruiter ? $account->recruiter->fullName() : '');
        });
        $sheet->cell('I11', function($cell) use ($account) {
            $cell->setValue($account->credentialer ? $account->credentialer->fullName() : '');
        });
        $sheet->cell('I12', function($cell) use ($account) {
            $cell->setValue($account->scheduler ? $account->scheduler->fullName() : '');
        });
        $sheet->cell('I13', function($cell) use ($account) {
            $cell->setValue($account->enrollment ? $account->enrollment->fullName() : '');
        });
        $sheet->cell('I14', function($cell) use ($account) {
            $cell->setValue($account->payroll ? $account->payroll->fullName() : '');
        });
        $sheet->cell('I15', function($cell) use ($account) {
            if ($account->pipeline->practiceTime == 'hours') {
                $result = $account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves;
            } else {
                $result = $account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves;
            }

            $cell->setValue(roundnum($result, 0.5));
        });
        $sheet->cell('I16', function($cell) use ($account) {
            if ($account->pipeline->practiceTime == 'hours') {
                $result = $account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves;
            } else {
                $result = $account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves;
            }

            $cell->setValue(roundnum($result, 0.5));
        });
        $sheet->cell('I17', function($cell) use ($accountPrevMonthIncComp) {
            if(is_object($accountPrevMonthIncComp)) {
                $cell->setValue($accountPrevMonthIncComp->{'Prev - Inc Comp'});
            } else {
                $cell->setValue('');
            }
        });
        $sheet->cell('I18', function($cell) use ($accountYTDIncComp) {
            if(is_object($accountYTDIncComp)) {
                $cell->setValue($accountYTDIncComp->{'YTD - Inc Comp'});
            } else {
                $cell->setValue('');
            }
        });
    }

    function createRosterBenchTable($sheet, $account, $activeRosterPhysicians, $activeRosterAPPs) {
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('D4:E4');
        $sheet->mergeCells('H4:I4');

        $sheet->cell('A1', function($cell) use ($account) {
            $cell->setValue($account->name);
            $cell->setFontColor('#FFFFFF');
            $cell->setBackground('#325694');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(16);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cells('A2:I2', function($cells) {
            $cells->setFontColor('#000000');
            $cells->setBackground('#b5c7e6');
            $cells->setFontFamily('Calibri (Body)');
            $cells->setFontSize(13);
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });

        $sheet->cell('A2', function($cell) use ($account) {
            $cell->setValue($account->googleAddress);
        });

        $sheet->cell('F2', function($cell) use ($account) {
            $cell->setValue('IC');
        });

        $sheet->cell('H2', function($cell) use ($account) {
            $cell->setValue($account->siteCode);
        });

        $sheet->cell('I2', function($cell) use ($account) {
            $cell->setValue('RTI Site Code');
        });

        $sheet->cell('A4', function($cell) use ($account, $activeRosterPhysicians) {
            $cell->setValue('FT Roster PHYS ('.count($activeRosterPhysicians).')');
        });

        $sheet->cell('C4', function($cell) use ($account) {
            $cell->setValue('Start Date');
        });

        $sheet->cell('D4', function($cell) use ($account, $activeRosterAPPs) {
            $cell->setValue('FT Roster APP ('.count($activeRosterAPPs).')');
        });

        $sheet->cell('F4', function($cell) use ($account) {
            $cell->setValue('Start Date');
        });

        $rosterBenchRow = 5;
        $rosterBenchCount = 1;

        if ($account->pipeline->practiceTime == 'hours') {
            $physicianOpenings = $this->roundnum($account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves, 0.5);
            $appOpenings = $this->roundnum($account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves, 0.5);
        } else {
            $physicianOpenings = $this->roundnum($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves, 0.5);
            $appOpenings = $this->roundnum($account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves, 0.5);
        }

        $physicianNegative = $physicianOpenings < 0 ? true : false;
        $appNegative = $appOpenings < 0 ? true : false;

        $physicianDecimal = $physicianOpenings - floor($physicianOpenings);
        $appDecimal = $appOpenings - floor($appOpenings);

        $normalizedPhyOpenings = $physicianNegative ? (ceil($physicianOpenings * -1)) : ceil($physicianOpenings);
        $normalizedAppOpenings = $appNegative ? (ceil($appOpenings * -1)) : ceil($appOpenings);

        $activeRosterPhysicians = $activeRosterPhysicians->toArray();
        $activeRosterAPPs = $activeRosterAPPs->toArray();

        for ($x = 1; $x <= (int) $normalizedPhyOpenings; $x++) {
            $tempArray = array();

            if ($x == $normalizedPhyOpenings) {
                $tempArray["name"] = $physicianDecimal == 0.5 ? ($physicianNegative ? "Open: -0.5" : "Open: 0.5") : ($physicianNegative ? "Open: -1.0" : "Open: 1.0");
                $tempArray["firstShift"] = '';
            } else {
                $tempArray["name"] = $physicianNegative ? "Open: -1.0" : "Open: 1.0";
                $tempArray["firstShift"] = '';
            }

            array_push($activeRosterPhysicians, $tempArray);   
        }

        for ($x = 1; $x <= (int) $normalizedAppOpenings; $x++) {
            $tempArray = array();

            if ($x == $normalizedAppOpenings) {
                $tempArray["name"] = $appDecimal == 0.5 ? ($appNegative ? "Open: -0.5" : "Open: 0.5") : ($appNegative ? "Open: -1.0" : "Open: 1.0");
                $tempArray["firstShift"] = '';
            } else {
                $tempArray["name"] = $appNegative ? "Open: -1.0" : "Open: 1.0";
                $tempArray["firstShift"] = '';
            }

            array_push($activeRosterAPPs, $tempArray);   
        }

        if(count($activeRosterPhysicians) >= count($activeRosterAPPs)) {
            $countUntil = count($activeRosterPhysicians) < 14 ? 14 : count($activeRosterPhysicians);

            for ($i = 0; $i < $countUntil; $i++) { 
                if ($account->pipeline->practiceTime == 'hours') {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '').(isset($activeRosterPhysicians[$i]["hours"]) ? " (".$activeRosterPhysicians[$i]["hours"].")" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"].(isset($activeRosterAPPs[$i]["hours"]) ? " (".$activeRosterAPPs[$i]["hours"].")" : '') : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                } else {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                }

                $sheet->row($rosterBenchRow, $row);

                $rosterBenchRow++;
                $rosterBenchCount++;
            }
        } else {
            $countUntil = count($activeRosterAPPs) < 14 ? 14 : count($activeRosterAPPs);

            for ($i = 0; $i < $countUntil; $i++) {
                if ($account->pipeline->practiceTime == 'hours') {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '').(isset($activeRosterPhysicians[$i]["hours"]) ? " (".$activeRosterPhysicians[$i]["hours"].")" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"].(isset($activeRosterAPPs[$i]["hours"]) ? " (".$activeRosterAPPs[$i]["hours"].")" : '') : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                } else {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? \Carbon\Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                }

                $sheet->row($rosterBenchRow, $row);

                $rosterBenchRow++;
                $rosterBenchCount++;
            }
        }

        $sheet->mergeCells('A'.$rosterBenchRow.':F'.$rosterBenchRow);

        $sheet->row(($rosterBenchRow+1), array(
            'Open/Proactive',
            '',
            '',
            'Open/Proactive',
            '',
            ''
        ));

        return $rosterBenchRow;
    }

    function createBenchTable($sheet, $account, $rosterBenchRow, $benchPhysicians, $benchAPPs) {
        $benchTableStart = $rosterBenchRow+3;

        $sheet->mergeCells('A'.$benchTableStart.':C'.$benchTableStart);
        $sheet->mergeCells('D'.$benchTableStart.':F'.$benchTableStart);

        $sheet->cell('A'.$benchTableStart, function($cell) use ($account) {
            $cell->setValue('PT/Locums MD');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('D'.$benchTableStart, function($cell) use ($account) {
            $cell->setValue('PT/Locums APP');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->row(($benchTableStart+1), array(
            'Type/Shifts', 'Name', 'Start/Source',
            'Type/Shifts', 'Name', 'Start/Source'
        ));

        $benchTableStartData = $benchTableStart+2;

        if(count($benchPhysicians) >= count($benchAPPs)) {
            for ($i = 0; $i < count($benchPhysicians); $i++) { 
                $row = [
                    'PHYS/PRN',
                    $benchPhysicians[$i]->name,
                    $benchPhysicians[$i]->firstShift ? \Carbon\Carbon::parse($benchPhysicians[$i]->firstShift)->format('m-d-Y') : '',
                    isset($benchAPPs[$i]) ? 'APP/4' : '',
                    isset($benchAPPs[$i]) ? $benchAPPs[$i]->name : '',
                    isset($benchAPPs[$i]) ? ($benchAPPs[$i]->firstShift ? \Carbon\Carbon::parse($benchAPPs[$i]->firstShift)->format('m-d-Y') : '') : ''
                ];

                $sheet->row($benchTableStartData, $row);

                $benchTableStartData++;
            }
        } else {
            for ($i = 0; $i < count($benchAPPs); $i++) { 
                $row = [
                    isset($benchPhysicians[$i]) ? 'PHYS/PRN' : '',
                    isset($benchPhysicians[$i]) ? $benchPhysicians[$i]->name : '',
                    isset($benchPhysicians[$i]) ? ($benchPhysicians[$i]->firstShift ? \Carbon\Carbon::parse($benchPhysicians[$i]->firstShift)->format('m-d-Y') : '') : '',
                    'APP/4',
                    $benchAPPs[$i]->name,
                    $benchAPPs[$i]->firstShift ? \Carbon\Carbon::parse($benchAPPs[$i]->firstShift)->format('m-d-Y') : ''
                ];

                $sheet->row($benchTableStartData, $row);

                $benchTableStartData++;
            }
        }

        return array($benchTableStart, $benchTableStartData);
    }

    function createCredentialingTable($sheet, $account, $recruitingTable, $credentialers) {
        $credentialingTableStart = $recruitingTable[1]+2;

        $sheet->mergeCells('A'.$credentialingTableStart.':F'.$credentialingTableStart);
        $sheet->mergeCells('G'.$credentialingTableStart.':H'.$credentialingTableStart);
        $sheet->mergeCells('F'.($credentialingTableStart+1).':G'.($credentialingTableStart+1));

        $sheet->cell('A'.$credentialingTableStart, function($cell) use ($account) {
            $cell->setValue('Credentialing Pipeline');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('G'.$credentialingTableStart, function($cell) use ($account) {
            $cell->setValue('Candidates');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('I'.$credentialingTableStart, function($cell) use ($account) {
            $cell->setBackground('#c1e7c9');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('A'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('FT/PT/Locums');
        });

        $sheet->cell('B'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Provider');
        });

        $sheet->cell('C'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('PHYS\APP');
        });

        $sheet->cell('D'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Contract Received');
        });

        $sheet->cell('E'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('File to Credentialing');
        });

        $sheet->cell('F'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('APP to Hospital');
        });

        $sheet->cell('H'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Privilege Goal');
        });

        $sheet->cell('I'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setValue('Notes');
        });

        $sheet->cell('A'.($credentialingTableStart+1).':I'.($credentialingTableStart+1), function($cell) use ($account) {
            $cell->setBackground('#fff1ce');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(11);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $credentialingTableDataStart = $credentialingTableStart+2;

        foreach ($credentialers as $credentialer) {
            $sheet->mergeCells('F'.$credentialingTableDataStart.':G'.$credentialingTableDataStart);

            $row = [
                strtoupper($credentialer->contract),
                $credentialer->name,
                $credentialer->activity ? ($credentialer->activity == 'physician' ? 'PHYS' : 'APP') : '',
                $credentialer->contractIn ? $credentialer->contractIn->format('m-d-Y') : '',
                $credentialer->fileToCredentialing ? $credentialer->fileToCredentialing->format('m-d-Y') : '',
                $credentialer->appToHospital ? $credentialer->appToHospital->format('m-d-Y') : '',
                '',
                $credentialer->privilegeGoal ? $credentialer->privilegeGoal->format('m-d-Y') : '',
                $credentialer->credentialingNotes
            ];

            $sheet->row($credentialingTableDataStart, $row);

            $credentialingTableDataStart++;
        }

        $sheet->cell('I'.($credentialingTableStart+2).':I'.($credentialingTableDataStart), function($cell) use ($account) {
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(8);
            $cell->setAlignment('left');
            $cell->setValignment('center');
        });

        return array($credentialingTableStart, $credentialingTableDataStart);
    }

    function createRequirementsTable($sheet, $account, $credentialingTable) {
        $requirementsTableStart = $credentialingTable[1]+2;

        $sheet->mergeCells('A'.$requirementsTableStart.':I'.$requirementsTableStart);
        $sheet->mergeCells('B'.($requirementsTableStart+1).':I'.($requirementsTableStart+1));
        $sheet->mergeCells('B'.($requirementsTableStart+2).':I'.($requirementsTableStart+2));
        $sheet->mergeCells('B'.($requirementsTableStart+3).':I'.($requirementsTableStart+3));
        $sheet->mergeCells('B'.($requirementsTableStart+4).':I'.($requirementsTableStart+4));
        $sheet->mergeCells('B'.($requirementsTableStart+5).':I'.($requirementsTableStart+5));

        $sheet->cell('A'.$requirementsTableStart, function($cell) use ($account) {
            $cell->setValue('Credentialing Account Requirements');
            $cell->setBackground('#b5c7e6');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cell('A'.($requirementsTableStart+1), function($cell) use ($account) {
            $cell->setValue('Requirements');
        });

        $sheet->cell('A'.($requirementsTableStart+2), function($cell) use ($account) {
            $cell->setValue('Fees');
        });

        $sheet->cell('A'.($requirementsTableStart+3), function($cell) use ($account) {
            $cell->setValue('Meetings');
        });

        $sheet->cell('A'.($requirementsTableStart+4), function($cell) use ($account) {
            $cell->setValue('Other');
        });

        $sheet->cell('B'.($requirementsTableStart+1), function($cell) use ($account) {
            $cell->setValue($account->requirements);
        });

        $sheet->cell('B'.($requirementsTableStart+2), function($cell) use ($account) {
            $cell->setValue($account->fees);
        });

        $sheet->cell('B'.($requirementsTableStart+3), function($cell) use ($account) {
            $cell->setValue($account->meetings);
        });

        $sheet->cell('B'.($requirementsTableStart+4), function($cell) use ($account) {
            $cell->setValue($account->other);
        });

        return array($requirementsTableStart);
    }

    /// Process Start

    $ids = count($ids) > 500 ? array_slice($ids, 0, 500) : $ids;

    $accounts = \App\Account::whereIn('id', $ids)->with([
        'pipeline' => function ($query) {
            $query->with([
                'rostersBenchs', 'recruitings', 'locums',
            ]);
        },
        'recruiter.employee' => function ($query) {
            $query->with('person', 'manager.person');
        },
        'division.group.region',
        'practices',
    ])->orderBy('name')->get();

    $timestamp = \Carbon\Carbon::now()->timestamp;

    if ($accounts) {
        $fileInfo = Excel::create('Accounts_Batch_Print_'.$timestamp, function($excel) use ($accounts){

            foreach ($accounts as $account) {
                $activeRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortByDesc(function($rosterBench){
                    return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->name);
                });
                $activeRosterPhysicians = $activeRosterPhysicians->values();
                $benchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortBy('name');
                $benchPhysicians = $benchPhysicians->values();
                $activeRosterAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortBy('name');
                $activeRosterAPPs = $activeRosterAPPs->values();
                $benchAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortBy('name');
                $benchAPPs = $benchAPPs->values();
                $credentialers = $account->pipeline->rostersBenchs
                ->reject(function($rosterBench) { 
                    return !is_null($rosterBench->resigned); 
                })
                ->reject(function($rosterBench){
                    return !$rosterBench->signedNotStarted;
                })->sortBy('name');
                $recruitings = $account->pipeline->recruitings
                ->reject(function($rosterBench) { 
                    return !is_null($rosterBench->declined); 
                })
                ->sortBy('name');
                $accountPrevMonthIncComp = \App\AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();
                $accountYTDIncComp = \App\AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

                $sheetName = (strlen($account->name) > 31) ? substr($account->name,0,28).'...' : $account->name;
                $sheetName = str_replace("/", "_", $sheetName);
                $sheetName = str_replace("?", "_", $sheetName);

                $excel->sheet($sheetName, function($sheet) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $credentialers, $recruitings, $accountPrevMonthIncComp, $accountYTDIncComp){
                    
                    $rosterBenchRow = createRosterBenchTable($sheet, $account, $activeRosterPhysicians, $activeRosterAPPs);
                    ///////// Team Members //////////
                    createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp);
                    ///////// Team Members //////////
                    /////// Bench Table ////////
                    $benchTable = createBenchTable($sheet, $account, $rosterBenchRow, $benchPhysicians, $benchAPPs);
                    /////// Bench Table ////////
                    /////// Recruiting Table /////////
                    $recruitingTable = createRecruitingTable($sheet, $account, $benchTable[1], $recruitings);
                    /////// Recruiting Table /////////
                    ////// Credentialing Table ////////
                    $credentialingTable = createCredentialingTable($sheet, $account, $recruitingTable, $credentialers);
                    ////// Credentialing Recruiting Table ////////
                    ////// Requirements Table ////////
                    $requirementsTable = createRequirementsTable($sheet, $account, $credentialingTable);
                    ////// Requirements Table ////////
                    $sheet->cells('A4:F4', function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setBackground('#b5c7e6');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A4:F'.($rosterBenchRow+1), function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A'.($benchTable[0]+1).':F'.($benchTable[1]), function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A'.($recruitingTable[0]+1).':D'.($recruitingTable[1]), function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A'.($credentialingTable[0]+1).':H'.($credentialingTable[1]), function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A'.($requirementsTable[0]+1).':F'.($requirementsTable[0]+5), function($cells) {
                        $cells->setFontColor('#000000');
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cell('A4', function($cells) {
                        $cells->setFontSize(14);
                    });
                    $sheet->cell('D4', function($cells) {
                        $cells->setFontSize(14);
                    });
                    $sheet->cell('C4', function($cells) {
                        $cells->setFontSize(11);
                    });
                    $sheet->cell('F4', function($cells) {
                        $cells->setFontSize(11);
                    });
                    $tableStyle = array(
                        'borders' => array(
                            'outline' => array(
                                'style' => 'medium',
                                'color' => array('rgb' => '000000'),
                            ),
                            'inside' => array(
                                'style' => 'thin',
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    );
                    $headersStyle = array(
                        'borders' => array(
                            'outline' => array(
                                'style' => 'medium',
                                'color' => array('rgb' => '000000'),
                            ),
                            'inside' => array(
                                'style' => 'medium',
                                'color' => array('rgb' => '000000'),
                            ),
                        ),
                    );
                    $sheet->setAutoSize(true);
                    $sheet->setWidth(array(
                        'A'     => 12,
                        'C'     => 10,
                        'D'     => 12,
                        'F'     => 10,
                        'G'     => 1,
                        'H'     => 18,
                        'I'     => 18,
                    ));
                    $sheet->setColumnFormat(array(
                        'I17:I18' => '"$"#,##0.00_-',
                    ));
                    $heights = array();
                    for($x = $recruitingTable[0]; $x <= ($credentialingTable[1]); $x++) {
                            $heights[$x] = 25;
                    }
                    $sheet->setHeight($heights);
                    $sheet->setHeight(array($rosterBenchRow => 3));
                    $sheet->getStyle('A1:I2')->applyFromArray($tableStyle);
                    $sheet->getStyle('H4:I14')->applyFromArray($tableStyle);
                    $sheet->getStyle('H15:I18')->applyFromArray($tableStyle);
                    $sheet->getStyle('A4:F'.($rosterBenchRow+1))->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$benchTable[0].':F'.($benchTable[1]))->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$recruitingTable[0].':I'.$recruitingTable[1])->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$credentialingTable[0].':I'.$credentialingTable[1])->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$requirementsTable[0].':I'.($requirementsTable[0]+5))->applyFromArray($tableStyle);
                    $sheet->getStyle('D'.($credentialingTable[0]+1))->getAlignment()->setWrapText(true);
                    $sheet->getStyle('E'.($credentialingTable[0]+1))->getAlignment()->setWrapText(true);
                    $sheet->getStyle('F'.($credentialingTable[0]+1))->getAlignment()->setWrapText(true);
                    $sheet->getStyle('E'.($recruitingTable[0]+2).':I'.$recruitingTable[1])->getAlignment()->setWrapText(true);
                    $sheet->getStyle('I'.($credentialingTable[0]+2).':I'.$credentialingTable[1])->getAlignment()->setWrapText(true);
                    $sheet->setBorder("A3:I3", 'none');
                    $sheet->setBorder("A".($benchTable[0]-1).":I".($benchTable[0]-1), 'none');
                    $sheet->setBorder("A".($recruitingTable[0]-1).":I".($recruitingTable[0]-1), 'none');
                    $sheet->setBorder("A".($recruitingTable[0]-2).":I".($recruitingTable[0]-2), 'none');
                    $sheet->setBorder("A".($credentialingTable[0]-1).":I".($credentialingTable[0]-1), 'none');
                    $sheet->setBorder("A".($requirementsTable[0]-1).":I".($requirementsTable[0]-1), 'none');
                    $sheet->setBorder("H19:I".($recruitingTable[0]-1), 'none');
                    $sheet->setBorder("G3:G".($recruitingTable[0]-1), 'none');
                });
            }
        })->store('pdf', public_path('exports'), true);
    }

    /// Process End

    $user = new App\User;
    $user->email = $email;

    $user->notify(new App\Notifications\AccountsNotification($timestamp));
})->describe('Download a zip file containing pdf files regarding accounts information.');

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
