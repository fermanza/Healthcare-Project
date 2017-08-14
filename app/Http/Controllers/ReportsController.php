<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountSummary;
use App\RSC;
use App\Region;
use App\Division;
use App\Employee;
use App\Practice;
use App\Filters\SummaryFilter;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(SummaryFilter $filter)
    {
        $accounts = $this->getSummaryData($filter);
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $dates = $accounts->map(function($account){ 
            return $account->{'MonthEndDate'}->format('m-Y'); 
        })->unique();

        $params = compact('accounts', 'employees', 'practices', 'divisions', 'RSCs', 'regions', 'dates', 'action');

        return view('admin.reports.index', $params);
    }

    public function exportToExcel(SummaryFilter $filter) {
        $dataToExport = $this->getSummaryData($filter);
        $headers = ["#", "Contract Name", "Service Line", "System Affiliation", "JV", "Operating Unit",
            "RSC", "Recruiter", "Secondary Recruiter", "Managers", "DOO/SVP", "RMD", "City", "Location",
            "Start Date", "# of Months Account Open", "Phys", "APP", "Total", "Phys", "APP", "Total",
            "SMD", "AMD", "Phys", "APP", "Total", "% Recruited", "% Recruited - Phys", "% Recruited - APP",
            "Physician Daily Hours", "APP Daily Hours", "Total Physician Shifts", "Phys Increase Comp - $",
            "INC per Phys Shift - $", "Fulltime/Cross Cred Utilization - %", "Phys Embassador Utilization - %",
            "Phys Qualitas/Tiva Utilization - %", "Phys External Locum Utilization - %", "Director INC - $",
            "Fulltime INC - $", "Cross Credential INC - $", "Embassador INC - $", "Internal Locum INC - $",
            "External Locum INC - $", "Director Shifts", "Fulltime Shifts", "Cross Credential Shifts",
            "Embassador Shifts", "Internal Locum Shifts", "External Locum Shifts", "Total APP Shifts",
            "APP Increase Comp - $", "INC per Shift - $", "APP Qualitas/Tiva Utilization - %",
            "APP External Locum Utilization - %", "Fulltime INC - $", "Cross Credential INC - $",
            "Embassador INC - $", "Internal Locum INC - $", "External Locum INC - $", "Fulltime Shifts",
            "Cross Credential Shifts", "Internal Locum Shifts", "External Locum Shifts"
        ];


        Excel::create('Summary Report', function($excel) use ($dataToExport, $headers){
            $excel->sheet('Summary', function($sheet) use ($dataToExport, $headers){
                
                $rowNumber = 2;

                $sheet->row($rowNumber, $headers);
                $sheet->row($rowNumber, function($row) {
                    $row->setBackground('#d9d9d9');
                });
                $sheet->setHeight($rowNumber, 25);

                foreach($dataToExport as $account) {
                    $rowNumber++;

                    $row = [
                        $account->siteCode,
                        $account->{'Hospital Name'},
                        $account->Practice,
                        $account->{'System Affiliation'},
                        ($account->division && $account->division->isJV) ? 'Yes' : 'No',
                        $account->{'Operating Unit'},
                        $account->rsc ? $account->rsc->name : '',
                        $account->{'RSC Recruiter'},
                        $account->{'Secondary Recruiter'},
                        $account->Managers,
                        $account->{'DOO/SVP'},
                        $account->RMD,
                        $account->City,
                        $account->Location,
                        $account->{'Start Date'} ? $account->{'Start Date'}->format('d/m/y') : '',
                        $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated(),
                        $account->{'Complete Staff - Phys'},
                        $account->{'Complete Staff - APP'},
                        $account->{'Complete Staff - Total'},
                        $account->{'Current Staff - Phys'},
                        $account->{'Current Staff - APP'},
                        $account->{'Current Staff - Total'},
                        $account->{'Current Openings - SMD'},
                        $account->{'Current Openings - AMD'},
                        $account->{'Current Openings - Phys'},
                        $account->{'Current Openings - APP'},
                        $account->{'Current Openings - Total'},
                        $account->{'Percent Recruited - Total'},
                        $account->{'Percent Recruited - Phys'},
                        $account->{'Percent Recruited - APP'},
                        $account->{'Hours - Phys'},
                        $account->{'Hours - APP'},
                        $account->{'Total Shifts - Phys'},
                        $account->{'Increased Comp - Phys'},
                        $account->{'Increased Comp Per Shift - Phys'},
                        $account->{'Fulltime/Cross Cred Utlization - Phys'},
                        $account->{'Embassador Utilization - Phys'},
                        $account->{'Qualitas/Tiva Utilization - Phys'},
                        $account->{'External Locum Utilization - Phys'},
                        $account->{'Director Increased Comp - Phys'},
                        $account->{'Full Time Increased Comp - Phys'},
                        $account->{'Cross Credential Increased Comp - Phys'},
                        $account->{'Embassador Increased Comp - Phys'},
                        $account->{'Internal Locum Increased Comp - Phys'},
                        $account->{'External Locum Increased Comp - Phys'},
                        $account->{'Director Shifts - Phys'},
                        $account->{'Fulltime Shifts - Phys'},
                        $account->{'Cross Credential Shifts - Phys'},
                        $account->{'Embassador Shifts - Phys'},
                        $account->{'Internal Locum Shifts - Phys'},
                        $account->{'External Locum Shifts - Phys'},
                        $account->{'Total Shifts - APP'},
                        $account->{'Increased Comp - APP'},
                        $account->{'Increased Comp Per Shift - APP'},
                        $account->{'Qualitas/Tiva Utilization - APP'},
                        $account->{'External Locum Utilization - APP'},
                        $account->{'Full Time Increased Comp - APP'},
                        $account->{'Cross Credential Increased Comp - APP'},
                        $account->{'Embassador Increased Comp - APP'},
                        $account->{'Internal Locum Increased Comp - APP'},
                        $account->{'External Locum Increased Comp - APP'},
                        $account->{'Fulltime Shifts - APP'},
                        $account->{'Cross Credential Shifts - APP'},
                        $account->{'Internal Locum Shifts - APP'},
                        $account->{'External Locum Shifts - APP'}
                    ];

                    $sheet->row($rowNumber, $row);

                    if ($account->getMonthsSinceCreated() < 7) {
                        $sheet->cell('P'.$rowNumber, function($cell) use ($account) {
                            $cell->setBackground('#1aaf54');
                            $cell->setFontColor('#ffffff');
                        });
                    }
                };

                $sheet->setFreeze('C3');
                $sheet->setAutoFilter('A2:P2');
                $sheet->mergeCells('A1:P1');
                $sheet->mergeCells('Q1:S1');
                $sheet->mergeCells('T1:V1');
                $sheet->mergeCells('W1:AA1');
                $sheet->mergeCells('AB1:AD1');
                $sheet->mergeCells('AE1:AF1');
                $sheet->mergeCells('AG1:AY1');
                $sheet->mergeCells('AZ1:BM1');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('WEST RSC RECRUITING SUMMARY');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('Q1', function($cell) {
                    $cell->setValue('COMPLETE STAFF');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#dce6f1');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('T1', function($cell) {
                    $cell->setValue('CURRENT STAFF');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#ebf1df');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('W1', function($cell) {
                    $cell->setValue('CURRENT OPENINGS');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#fffd38');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AB1', function($cell) {
                    $cell->setValue('PERCENT RECRUITED');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#e4dfec');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AE1', function($cell) {
                    $cell->setValue('DAILY STAFFING HOURS');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AG1', function($cell) {
                    $cell->setValue('PHYSICIAN INCREASE COMP & SHIFTS BY RESOURCE TYPE');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#dbeef3');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AZ1', function($cell) {
                    $cell->setValue('APP INCREASE COMP & SHIFTS BY RESOURCE TYPE');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#fce9da');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cells('A2:BM2', function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A3:O'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('P3:P'.$rowNumber, function($cells) {
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('Q3:BM'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->setColumnFormat(array(
                    'O3:O'.$rowNumber      => 'dd/mm/yy',
                    'P3:AA'.$rowNumber     => '0.0',
                    'AB3:AD'.$rowNumber    => '0.0%',
                    'AE3:AG'.$rowNumber    => '0.0',
                    'AH3:AI'.$rowNumber    => '"$"#,##0.00_-',
                    'AJ3:AM'.$rowNumber    => '0.0%',
                    'AN3:AS'.$rowNumber    => '"$"#,##0.00_-',
                    'AZ1:AZ'.$rowNumber    => '0.0',
                    'BA3:BB'.$rowNumber    => '"$"#,##0.00_-',
                    'BC3:BD'.$rowNumber    => '0.0%',
                    'BE3:BI'.$rowNumber    => '"$"#,##0.00_-',
                ));

                $sheet->setWidth(array(
                    'A'     => 5,
                    'B'     => 37,
                    'C'     => 12,
                    'D'     => 17,
                    'E'     => 6,
                    'F'     => 13,
                    'G'     => 7,
                    'H'     => 11,
                    'I'     => 17,
                    'J'     => 10,
                    'K'     => 10,
                    'L'     => 10,
                    'M'     => 9,
                    'N'     => 10,
                    'O'     => 11,
                    'P'     => 13,
                    'Q'     => 7,
                    'R'     => 7,
                    'S'     => 7,
                    'T'     => 7,
                    'U'     => 7,
                    'V'     => 7,
                    'W'     => 7,
                    'X'     => 7,
                    'Y'     => 7,
                    'Z'     => 7,
                    'AA'    => 7,
                    'AB'    => 12,
                    'AC'    => 15,
                    'AD'    => 15,
                    'AE'    => 12,
                    'AF'    => 12,
                    'AG'    => 13,
                    'AH'    => 13,
                    'AI'    => 12,
                    'AJ'    => 17,
                    'AK'    => 15,
                    'AL'    => 15,
                    'AM'    => 17,
                    'AN'    => 14,
                    'AO'    => 14,
                    'AP'    => 19,
                    'AQ'    => 16,
                    'AR'    => 18,
                    'AS'    => 18,
                    'AT'    => 13,
                    'AU'    => 13,
                    'AV'    => 18,
                    'AW'    => 15,
                    'AX'    => 17,
                    'AY'    => 17,
                    'AZ'    => 14,
                    'BA'    => 18,
                    'BB'    => 14,
                    'BC'    => 15,
                    'BD'    => 17,
                    'BE'    => 14,
                    'BF'    => 19,
                    'BG'    => 16,
                    'BH'    => 18,
                    'BI'    => 18,
                    'BJ'    => 13,
                    'BK'    => 18,
                    'BL'    => 17,
                    'BM'    => 17
                ));

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

                $sheet->getStyle('A1:BM'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A1:P'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('Q1:S'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('T1:V'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('W1:AA'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AB1:AD'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AE1:AF'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AG1:AM'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A2:BM2')->applyFromArray($headersStyle);

                $sheet->getStyle('P2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AH2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AJ2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AK2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AL2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AM2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('BC2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('BD2')->getAlignment()->setWrapText(true);

                //$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
            });
        })->download('xlsx'); 
    }

    private function getSummaryData(SummaryFilter $filter) {
        // return Account::leftJoin('tAccountToEmployee as tRecruiter', function($join) {
        //         $join->on('tRecruiter.accountId', '=', 'tAccount.id')
        //         ->on('tRecruiter.positionTypeId', '=', DB::raw(config('instances.position_types.recruiter')));
        //     }) 
        //     ->leftJoin('tAccountToEmployee as tManager', function($join) {
        //         $join->on('tManager.accountId', '=', 'tAccount.id')
        //         ->on('tManager.positionTypeId', '=', DB::raw(config('instances.position_types.manager')));
        //     }) 
        //     ->leftJoin('tAccountToPractice', 'tAccount.id', '=', 'tAccountToPractice.accountId')
        //     ->leftJoin('tDivision', 'tAccount.divisionId', '=', 'tDivision.id')
        //     ->leftJoin('tGroup', 'tDivision.groupId', '=', 'tGroup.id')
        //     ->select('tAccount.*')
        //     ->with('recruiter.employee.person', 'recruiters.employee.person', 'manager.employee.person', 'division.group', 'region', 'rsc', 'pipeline', 'practices')
        //     ->where('tAccount.active', true)->filter($filter)->get()->unique();

        return AccountSummary::leftJoin('tAccount', 'vAccountSummary.siteCode', 'tAccount.siteCode')
            ->select('vAccountSummary.*', 'tAccount.divisionId', 'tAccount.RSCId')
            ->filter($filter)->get()->unique('siteCode');
    }
}
