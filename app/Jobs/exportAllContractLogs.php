<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\ContractLog;
use App\Scopes\ContractLogScope;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Filesystem\Filesystem;

class exportAllContractLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = 1;

        ContractLog::withGlobalScope('role', new ContractLogScope)
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
        ->where('tContractLogs.active', true)->chunk(2500, function($contractLogs) use (&$count) {
            $headers = ["Status", "Main Site Code", "Provider", "Specialty", "Account", "System", "Operating Unit", "RSC",
                "Contract Out Date", "Contract In Date", "# of Days (Contract Out to Contract In)",
                "Sent to Q/A Date", "Counter Sig Date", "Sent To Payroll Date", "# of Days (Contract Out to Payroll)",
                "Provider Start Date", "# of Hours", "Recruiter", "Manager", "Contract Coordinator", "Contract",
                "(# of times) Revised/Resent", "Comments"
            ];

            Excel::create('Contract_Logs_'.$count, function($excel) use ($contractLogs, $headers, &$count){
                $excel->sheet('Summary', function($sheet) use ($contractLogs, $headers, &$count){
                    
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
            })->store('xlsx', public_path('contract_logs'), true);

            $count++;
        });

        $zipper = new \Chumper\Zipper\Zipper;

        $files = glob(public_path('contract_logs/*'));
        $zipper->make(public_path('contract_logs.zip'))->add($files)->close();

        $file = new Filesystem;
        $file->deleteDirectory(public_path('contract_logs'));

        \Mail::raw('', function ($message) {
            $message->from('envision@app.com', 'Envision');

            $message->to($this->email)->subject('Contract Logs');

            $message->attach(public_path('contract_logs.zip'));
        });

        if (\Mail::failures()) {
            // return response showing failed emails
        } else {
            $file->delete(public_path('contractLogs.zip'));
        }
    }
}
