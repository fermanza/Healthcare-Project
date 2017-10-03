<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountSummary;
use App\RSC;
use App\Region;
use App\Division;
use App\Employee;
use App\Practice;
use App\Scopes\AccountSummaryScope;
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
    public function summary(SummaryFilter $filter, Request $request)
    {

        $queryString = $request->query();

        if(count($queryString) == 0) {
            $maxDate = AccountSummary::max('MonthEndDate');

            $maxDate = Carbon::parse($maxDate)->format('m-Y');

            return redirect()->route('admin.reports.summary.index', ['monthEndDate' => $maxDate]);
        }

        $accounts = $this->getSummaryData($filter, 500);
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
        set_time_limit(600);

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

            $accountIds = $dataToExport->map(function($account) { return $account->accountId; });
            $accountIds = array_values($accountIds->toArray());

            if(count($accountIds) > 100) {
                $accountIds = array_slice($accountIds, 0, 100);
            }

            $accounts = Account::whereIn('id', $accountIds)->get();

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
                        $account->present()->excel('Prev - Inc Comp'),
                        $account->present()->excel('Prev - FT Util - %'),
                        $account->present()->excel('Prev - Embassador Util - %'),
                        $account->present()->excel('Prev - Int Locum Util - %'),
                        $account->present()->excel('Prev - Ext Locum Util - %'),
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

                $sheet->cell('AF1', function($cell) {
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
            });
        })->download('xlsx'); 
    }

    public function exportToExcelDetailed(SummaryFilter $filter) {
        set_time_limit(600);

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


        Excel::create('Summary Report Detailed', function($excel) use ($dataToExport, $headers){

            $accountIds = $dataToExport->map(function($account) { return $account->accountId; });
            $accountIds = array_values($accountIds->toArray());

            if(count($accountIds) > 100) {
                $accountIds = array_slice($accountIds, 0, 100);
            }

            $accounts = Account::whereIn('id', $accountIds)->get();

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
                        $account->present()->excel('Prev - Inc Comp'),
                        $account->present()->excel('Prev - FT Util - %'),
                        $account->present()->excel('Prev - Embassador Util - %'),
                        $account->present()->excel('Prev - Int Locum Util - %'),
                        $account->present()->excel('Prev - Ext Locum Util - %'),
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

                $sheet->cell('AF1', function($cell) {
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
            });

            foreach ($accounts as $account) {
                $account->load([
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
                ]);

                $activeRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->reject(function($rosterBench){
                    return $rosterBench->signedNotStarted;
                })->sortBy('name');

                $activeRosterPhysicians = $activeRosterPhysicians->values();

                $benchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->reject(function($rosterBench){
                    return $rosterBench->signedNotStarted;
                })->sortBy('name');

                $benchPhysicians = $benchPhysicians->values();

                $activeRosterAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->reject(function($rosterBench){
                    return $rosterBench->signedNotStarted;
                })->sortBy('name');

                $activeRosterAPPs = $activeRosterAPPs->values();

                $benchAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->reject(function($rosterBench){
                    return $rosterBench->signedNotStarted;
                })->sortBy('name');

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

                $accountPrevMonthIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

                $accountYTDIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

                $sheetName = $account->siteCode;

                $excel->sheet($sheetName, function($sheet) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $credentialers, $recruitings, $accountPrevMonthIncComp, $accountYTDIncComp){
                    
                    $rosterBenchRow = $this->createRosterBenchTable($sheet, $account, $activeRosterPhysicians, $activeRosterAPPs);

                    $this->createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp);

                    $benchTable = $this->createBenchTable($sheet, $account, $rosterBenchRow, $benchPhysicians, $benchAPPs);

                    $recruitingTable = $this->createRecruitingTable($sheet, $account, $benchTable[1], $recruitings);

                    $credentialingTable = $this->createCredentialingTable($sheet, $account, $recruitingTable, $credentialers);

                    $requirementsTable = $this->createRequirementsTable($sheet, $account, $credentialingTable);

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
                        'I16:I17' => '"$"#,##0.00_-',
                    ));

                    $heights = array();

                    for($x = $recruitingTable[0]; $x <= ($credentialingTable[1]); $x++) {
                            $heights[$x] = 25;
                    }

                    $sheet->setHeight($heights);
                    $sheet->setHeight(array($rosterBenchRow => 3));

                    $sheet->getStyle('A1:I2')->applyFromArray($tableStyle);
                    $sheet->getStyle('H4:I13')->applyFromArray($tableStyle);
                    $sheet->getStyle('H14:I17')->applyFromArray($tableStyle);
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
                    $sheet->setBorder("H18:I".($recruitingTable[0]-1), 'none');
                    $sheet->setBorder("G3:G".($recruitingTable[0]-1), 'none');
                });
            }
        })->download('xlsx'); 
    }

    private function getSummaryData(SummaryFilter $filter, $pages) {
        return AccountSummary::withGlobalScope('role', new AccountSummaryScope)->with('account')->filter($filter)->paginate($pages);
    }

    private function createRecruitingTable($sheet, $account, $benchTableStartData, $recruitings) {
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
            $cell->setValue('MD\APP');
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

    private function createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp) {
        $sheet->cell('H4', function($cell) use ($account) {
            $cell->setBackground('#b5c7e6');
            $cell->setValue('Team Members');
            $cell->setFontFamily('Calibri (Body)');
            $cell->setFontSize(14);
            $cell->setAlignment('center');
            $cell->setValignment('center');
        });

        $sheet->cells('H5:H13', function($cells) {
            $cells->setBackground('#fff1ce');
        });

        $sheet->cells('H14:I17', function($cells) {
            $cells->setBackground('#b5c7e6');
        });

        $sheet->cells('H5:I17', function($cells) {
            $cells->setFontFamily('Calibri (Body)');
            $cells->setFontSize(11);
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });

        $sheet->cell('H5', function($cell) use ($account) {
            $cell->setValue('SVP');
        });
        $sheet->cell('H6', function($cell) use ($account) {
            $cell->setValue('RMD');
        });
        $sheet->cell('H7', function($cell) use ($account) {
            $cell->setValue('DOO');
        });
        $sheet->cell('H8', function($cell) use ($account) {
            $cell->setValue('DCS');
        });
        $sheet->cell('H9', function($cell) use ($account) {
            $cell->setValue('Recruiter');
        });
        $sheet->cell('H10', function($cell) use ($account) {
            $cell->setValue('Credentialer');
        });
        $sheet->cell('H11', function($cell) use ($account) {
            $cell->setValue('Scheduler');
        });
        $sheet->cell('H12', function($cell) use ($account) {
            $cell->setValue('Enrollment');
        });
        $sheet->cell('H13', function($cell) use ($account) {
            $cell->setValue('Payroll');
        });
        $sheet->cell('H14', function($cell) use ($account) {
            $cell->setValue('Physician Opens');
        });
        $sheet->cell('H15', function($cell) use ($account) {
            $cell->setValue('APP Opens');
        });
        $sheet->cell('H16', function($cell) use ($account) {
            $cell->setValue('Prev Month - Inc Comp');
        });
        $sheet->cell('H17', function($cell) use ($account) {
            $cell->setValue('YTD - Inc Comp');
        });

        $sheet->cell('I5', function($cell) use ($account) {
            $cell->setValue($account->pipeline->svp);
        });
        $sheet->cell('I6', function($cell) use ($account) {
            $cell->setValue($account->pipeline->rmd);
        });
        $sheet->cell('I7', function($cell) use ($account) {
            $cell->setValue($account->pipeline->dca);
        });
        $sheet->cell('I8', function($cell) use ($account) {
            $cell->setValue($account->dcs ? $account->dcs->fullName() : '');
        });
        $sheet->cell('I9', function($cell) use ($account) {
            $cell->setValue($account->recruiter ? $account->recruiter->fullName() : '');
        });
        $sheet->cell('I10', function($cell) use ($account) {
            $cell->setValue($account->credentialer ? $account->credentialer->fullName() : '');
        });
        $sheet->cell('I11', function($cell) use ($account) {
            $cell->setValue($account->scheduler ? $account->scheduler->fullName() : '');
        });
        $sheet->cell('I12', function($cell) use ($account) {
            $cell->setValue($account->enrollment ? $account->enrollment->fullName() : '');
        });
        $sheet->cell('I13', function($cell) use ($account) {
            $cell->setValue($account->payroll ? $account->payroll->fullName() : '');
        });
        $sheet->cell('I14', function($cell) use ($account) {
            $cell->setValue($account->pipeline->staffPhysicianFTEOpenings);
        });
        $sheet->cell('I15', function($cell) use ($account) {
            $cell->setValue($account->pipeline->staffAppsFTEOpenings);
        });
        $sheet->cell('I16', function($cell) use ($accountPrevMonthIncComp) {
            $cell->setValue($accountPrevMonthIncComp->{'Prev Month - Inc Comp'});
        });
        $sheet->cell('I17', function($cell) use ($accountYTDIncComp) {
            $cell->setValue($accountYTDIncComp->{'YTD - Inc Comp'});
        });
    }

    private function createRosterBenchTable($sheet, $account, $activeRosterPhysicians, $activeRosterAPPs) {
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
            $cell->setValue('FT Roster MD ('.count($activeRosterPhysicians).')');
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

        $physicianOpenings = $account->pipeline->staffPhysicianFTEOpenings;
        $appOpenings = $account->pipeline->staffAppsFTEOpenings;

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
            $countUntil = count($activeRosterPhysicians) < 13 ? 13 : count($activeRosterPhysicians);

            for ($i = 0; $i < $countUntil; $i++) { 
                $row = [
                    $rosterBenchCount,
                    isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"] : '',
                    isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                    $rosterBenchCount,
                    isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                    isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                ];

                $sheet->row($rosterBenchRow, $row);

                $rosterBenchRow++;
                $rosterBenchCount++;
            }
        } else {
            $countUntil = count($activeRosterAPPs) < 13 ? 13 : count($activeRosterAPPs);

            for ($i = 0; $i < $countUntil; $i++) { 
                $row = [
                    $rosterBenchCount,
                    isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"] : '',
                    isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                    $rosterBenchCount,
                    isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                    isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                ];

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

    private function createBenchTable($sheet, $account, $rosterBenchRow, $benchPhysicians, $benchAPPs) {
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
                    'MD/PRN',
                    $benchPhysicians[$i]->name,
                    $benchPhysicians[$i]->firstShift ? Carbon::parse($benchPhysicians[$i]->firstShift)->format('m-d-Y') : '',
                    isset($benchAPPs[$i]) ? 'APP/4' : '',
                    isset($benchAPPs[$i]) ? $benchAPPs[$i]->name : '',
                    isset($benchAPPs[$i]) ? ($benchAPPs[$i]->firstShift ? Carbon::parse($benchAPPs[$i]->firstShift)->format('m-d-Y') : '') : ''
                ];

                $sheet->row($benchTableStartData, $row);

                $benchTableStartData++;
            }
        } else {
            for ($i = 0; $i < count($benchAPPs); $i++) { 
                $row = [
                    isset($benchPhysicians[$i]) ? 'MD/PRN' : '',
                    isset($benchPhysicians[$i]) ? $benchPhysicians[$i]->name : '',
                    isset($benchPhysicians[$i]) ? ($benchPhysicians[$i]->firstShift ? Carbon::parse($benchPhysicians[$i]->firstShift)->format('m-d-Y') : '') : '',
                    'APP/4',
                    $benchAPPs[$i]->name,
                    $benchAPPs[$i]->firstShift ? Carbon::parse($benchAPPs[$i]->firstShift)->format('m-d-Y') : ''
                ];

                $sheet->row($benchTableStartData, $row);

                $benchTableStartData++;
            }
        }

        return array($benchTableStart, $benchTableStartData);
    }

    private function createCredentialingTable($sheet, $account, $recruitingTable, $credentialers) {
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
            $cell->setValue('MD\APP');
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
                $credentialer->activity ? ($credentialer->activity == 'physician' ? 'MD' : 'APP') : '',
                $credentialer->contractIn ? $credentialer->contractIn->format('m-d-Y') : '',
                $credentialer->fileToCredentialing ? $credentialer->fileToCredentialing->format('m-d-Y') : '',
                $credentialer->appToHospital ? $credentialer->appToHospital->format('m-d-Y') : '',
                '',
                $credentialer->privilegeGoal ? $credentialer->privilegeGoal->format('m-d-Y') : '',
                $credentialer->notes
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

    private function createRequirementsTable($sheet, $account, $credentialingTable) {
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
            $cell->setValue('Application');
        });

        $sheet->cell('A'.($requirementsTableStart+4), function($cell) use ($account) {
            $cell->setValue('Meetings');
        });

        $sheet->cell('A'.($requirementsTableStart+5), function($cell) use ($account) {
            $cell->setValue('Other');
        });

        $sheet->cell('B'.($requirementsTableStart+1), function($cell) use ($account) {
            $cell->setValue($account->requirements);
        });

        $sheet->cell('B'.($requirementsTableStart+2), function($cell) use ($account) {
            $cell->setValue($account->fees);
        });

        $sheet->cell('B'.($requirementsTableStart+3), function($cell) use ($account) {
            $cell->setValue($account->applications);
        });

        $sheet->cell('B'.($requirementsTableStart+4), function($cell) use ($account) {
            $cell->setValue($account->meetings);
        });

        $sheet->cell('B'.($requirementsTableStart+5), function($cell) use ($account) {
            $cell->setValue($account->other);
        });

        return array($requirementsTableStart);
    }
}
