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
        
        $dates = AccountSummary::select('MonthEndDate')->get()->unique('MonthEndDate');

        $params = compact('accounts', 'employees', 'practices', 'divisions', 'RSCs', 'regions', 'dates', 'action');

        return view('admin.reports.index', $params);
    }

    public function exportToExcel(SummaryFilter $filter) {
        $dataToExport = $this->getSummaryData($filter);
        $headers = ["#", "Contract Name", "Service Line", "System Affiliation", "JV", "Operating Unit",
            "RSC", "Recruiter", "Secondary Recruiter", "Managers", "DOO", "SVP", "RMD", "City", "Location",
            "Start Date", "# of Months Account Open", "Phys", "APP", "Total", "Phys", "APP", "Total",
            "SMD", "AMD", "Phys", "APP", "Total", "% Recruited", "% Recruited - Phys", "% Recruited - APP",
            "Inc Comp", "FT Utilization - %", "Embassador Utilization - %", "Internal Locum Utilization - %",
            "External Locum Utilization - %", "Applications", "Interviews", "Contracts Out", "Contracts in",
            "Signed Not Yet Started", "Applications", "Interviews", "Pending Contracts", "Contracts In",
            "Signed Not Yet Started", "Inc Comp", "Attrition"
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
                        ($account->account && $account->account->division && $account->account->division->isJV) ? 'Yes' : 'No',
                        $account->{'Operating Unit'},
                        ($account->account && $account->account->rsc) ? $account->account->rsc->name : '',
                        $account->{'RSC Recruiter'},
                        $account->{'Secondary Recruiter'},
                        $account->Managers,
                        $account->DOO,
                        $account->SVP,
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
                        $account->{'Prev Month - Inc Comp'},
                        $account->{'Prev Month - FT Utilization - %'},
                        $account->{'Prev Month - Embassador Utilization - %'},
                        $account->{'Prev Month - Internal Locum Utilization - %'},
                        $account->{'Prev Month - External Locum Utilization - %'},
                        $account->{'MTD - Applications'},
                        $account->{'MTD - Interviews'},
                        $account->{'MTD - Contracts Out'},
                        $account->{'MTD - Contracts In'},
                        $account->{'MTD - Signed Not Yet Started'},
                        $account->{'YTD - Applications'},
                        $account->{'YTD - Interviews'},
                        $account->{'YTD - Pending Contracts'},
                        $account->{'YTD - Contracts In'},
                        $account->{'YTD - Signed Not Yet Started'},
                        $account->{'YTD - Inc Comp'},
                        $account->{'YTD - Attrition'},
                    ];

                    $sheet->row($rowNumber, $row);

                    if ($account->getMonthsSinceCreated() < 7) {
                        $sheet->cell('Q'.$rowNumber, function($cell) use ($account) {
                            $cell->setBackground('#1aaf54');
                            $cell->setFontColor('#ffffff');
                        });
                    }
                };

                $sheet->setFreeze('C3');
                $sheet->setAutoFilter('A2:AV2');
                $sheet->mergeCells('A1:Q1');
                $sheet->mergeCells('R1:T1');
                $sheet->mergeCells('U1:W1');
                $sheet->mergeCells('X1:AB1');
                $sheet->mergeCells('AC1:AE1');
                $sheet->mergeCells('AF1:AJ1');
                $sheet->mergeCells('AK1:AO1');
                $sheet->mergeCells('AP1:AV1');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('RECRUITING SUMMARY');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('R1', function($cell) {
                    $cell->setValue('COMPLETE STAFF');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#dce6f1');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('U1', function($cell) {
                    $cell->setValue('CURRENT STAFF');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#ebf1df');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('X1', function($cell) {
                    $cell->setValue('CURRENT OPENINGS');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#fffd38');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AC1', function($cell) {
                    $cell->setValue('PERCENT RECRUITED');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#e4dfec');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AD1', function($cell) {
                    $cell->setValue('PREV MONTH');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#c7eecf');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AK1', function($cell) {
                    $cell->setValue('MTD');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#fec7ce');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('AP1', function($cell) {
                    $cell->setValue('YTD');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#feeaa0');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cells('A2:AV2', function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A3:P'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('Q3:Q'.$rowNumber, function($cells) {
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('R3:AV'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->setColumnFormat(array(
                    'P3:P'.$rowNumber      => 'dd/mm/yy',
                    'Q3:AB'.$rowNumber     => '0.0',
                    'AC3:AE'.$rowNumber    => '0.0%',
                    'AF3:AF'.$rowNumber    => '"$"#,##0.00_-',
                    'AG3:AJ'.$rowNumber    => '0.0%',
                    'AU3:AU'.$rowNumber    => '"$"#,##0.00_-',
                    'AV3:AV'.$rowNumber     => '0.0',
                ));

                $sheet->setWidth(array(
                    'A'     => 5,
                    'B'     => 37,
                    'C'     => 12,
                    'D'     => 17,
                    'E'     => 6,
                    'F'     => 13,
                    'G'     => 7,
                    'H'     => 14,
                    'I'     => 17,
                    'J'     => 14,
                    'K'     => 10,
                    'L'     => 10,
                    'M'     => 9,
                    'N'     => 12,
                    'O'     => 11,
                    'P'     => 11,
                    'Q'     => 13,
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
                    'AJ'    => 12,
                    'AK'    => 12,
                    'AL'    => 12,
                    'AM'    => 12,
                    'AN'    => 12,
                    'AO'    => 12,
                    'AP'    => 12,
                    'AQ'    => 12,
                    'AR'    => 12,
                    'AS'    => 12,
                    'AT'    => 12,
                    'AU'    => 12,
                    'AV'    => 12
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

                $sheet->getStyle('A1:AV'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A1:Q'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('R1:T'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('U1:W'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('X1:AB'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AC1:AE'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AF1:AJ'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AK1:AO'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A2:AV2')->applyFromArray($headersStyle);

                $sheet->getStyle('Q2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AH2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AI2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AJ2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AO2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AT2')->getAlignment()->setWrapText(true);

                //$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
            });
        })->download('xlsx'); 
    }

    private function getSummaryData(SummaryFilter $filter) {
        return AccountSummary::with('account.rsc', 'account.division')
            ->filter($filter)->get()->unique('siteCode');
    }
}
