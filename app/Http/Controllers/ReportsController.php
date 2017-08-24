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
        $accounts = $this->getSummaryData($filter, 100);
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
        $dataToExport = $this->getSummaryData($filter, 5000);
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
                        $account->present()->excel('Complete Staff - Phys'),
                        $account->present()->excel('Complete Staff - APP'),
                        $account->present()->excel('Complete Staff - Total'),
                        $account->present()->excel('Current Staff - Phys'),
                        $account->present()->excel('Current Staff - APP'),
                        $account->present()->excel('Current Staff - Total'),
                        $account->present()->excel('Current Openings - SMD'),
                        $account->present()->excel('Current Openings - AMD'),
                        $account->present()->excel('Current Openings - Phys'),
                        $account->present()->excel('Current Openings - APP'),
                        $account->present()->excel('Current Openings - Total'),
                        $account->present()->excel('Percent Recruited - Total'),
                        $account->present()->excel('Percent Recruited - Phys'),
                        $account->present()->excel('Percent Recruited - APP'),
                        $account->present()->excel('Prev Month - Inc Comp'),
                        $account->present()->excel('Prev Month - FT Utilization - %'),
                        $account->present()->excel('Prev Month - Embassador Utilization - %'),
                        $account->present()->excel('Prev Month - Internal Locum Utilization - %'),
                        $account->present()->excel('Prev Month - External Locum Utilization - %'),
                        $account->present()->excel('MTD - Applications'),
                        $account->present()->excel('MTD - Interviews'),
                        $account->present()->excel('MTD - Contracts Out'),
                        $account->present()->excel('MTD - Contracts In'),
                        $account->present()->excel('MTD - Signed Not Yet Started'),
                        $account->present()->excel('YTD - Applications'),
                        $account->present()->excel('YTD - Interviews'),
                        $account->present()->excel('YTD - Pending Contracts'),
                        $account->present()->excel('YTD - Contracts In'),
                        $account->present()->excel('YTD - Signed Not Yet Started'),
                        $account->present()->excel('YTD - Inc Comp'),
                        $account->present()->excel('YTD - Attrition'),
                    ];

                    $sheet->row($rowNumber, $row);

                    if ($account->getMonthsSinceCreated() < 7) {
                        $sheet->cell('Q'.$rowNumber, function($cell) use ($account) {
                            $cell->setBackground('#1aaf54');
                            $cell->setFontColor('#ffffff');
                        });
                    }
                };

                $sheet->cell('R'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(R3:R'.$rowNumber.')');
                });

                $sheet->cell('S'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(S3:S'.$rowNumber.')');
                });

                $sheet->cell('T'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(T3:T'.$rowNumber.')');
                });

                $sheet->cell('U'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(U3:U'.$rowNumber.')');
                });

                $sheet->cell('V'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(V3:V'.$rowNumber.')');
                });

                $sheet->cell('W'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(W3:W'.$rowNumber.')');
                });

                $sheet->cell('X'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(X3:X'.$rowNumber.')');
                });

                $sheet->cell('Y'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(Y3:Y'.$rowNumber.')');
                });

                $sheet->cell('Z'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(Z3:Z'.$rowNumber.')');
                });

                $sheet->cell('AA'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AA3:AA'.$rowNumber.')');
                });

                $sheet->cell('AB'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AB3:AB'.$rowNumber.')');
                });

                $sheet->cell('AC'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=W'.($rowNumber+2).'/T'.($rowNumber+2));
                });

                $sheet->cell('AD'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=U'.($rowNumber+2).'/R'.($rowNumber+2));
                });

                $sheet->cell('AE'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=V'.($rowNumber+2).'/S'.($rowNumber+2));
                });

                $sheet->cell('AF'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AF3:AF'.$rowNumber.')');
                });

                $sheet->cell('AK'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AK3:AK'.$rowNumber.')');
                });

                $sheet->cell('AL'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AL3:AL'.$rowNumber.')');
                });

                $sheet->cell('AM'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AM3:AM'.$rowNumber.')');
                });

                $sheet->cell('AN'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AN3:AN'.$rowNumber.')');
                });

                $sheet->cell('AO'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AO3:AO'.$rowNumber.')');
                });

                $sheet->cell('AP'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AP3:AP'.$rowNumber.')');
                });

                $sheet->cell('AQ'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AQ3:AQ'.$rowNumber.')');
                });

                $sheet->cell('AR'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AR3:AR'.$rowNumber.')');
                });

                $sheet->cell('AS'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AS3:AS'.$rowNumber.')');
                });

                $sheet->cell('AT'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AT3:AT'.$rowNumber.')');
                });

                $sheet->cell('AU'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AU3:AU'.$rowNumber.')');
                });

                $sheet->cell('AV'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=SUM(AV3:AV'.$rowNumber.')');
                });

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
                    'AV3:AV'.$rowNumber    => '0.0',
                    'AC'.($rowNumber+2 )   => '0.0%',
                    'AD'.($rowNumber+2 )   => '0.0%',
                    'AE'.($rowNumber+2 )   => '0.0%',
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

    private function getSummaryData(SummaryFilter $filter, $pages) {
        return AccountSummary::with('account.rsc', 'account.division')
            ->filter($filter)->paginate($pages)->unique('siteCode');
    }
}
