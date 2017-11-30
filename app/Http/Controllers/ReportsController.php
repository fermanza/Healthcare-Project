<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountSummary;
use App\RSC;
use App\Region;
use App\Division;
use App\Employee;
use App\Practice;
use App\SystemAffiliation;
use App\StateAbbreviation;
use App\Group;
use App\Pipeline;
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
        $newFilter = $request->new;

        if(count($queryString) == 0) {
            $maxDate = AccountSummary::max('MonthEndDate');

            $maxDate = Carbon::parse($maxDate)->format('m-Y');

            return redirect()->route('admin.reports.summary.index', ['monthEndDate' => $maxDate]);
        }

        $accounts = $this->getSummaryData($filter, 500);
        $sites = Account::where('active', true)->orderBy('name')->get();
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $doos = $employees->filter->hasPosition(config('instances.position_types.doo'));
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::all();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $states = StateAbbreviation::all();
        $groups = Group::where('active', true)->get()->sortBy('name');
        $SVPs = Pipeline::distinct('SVP')->select('SVP')->orderBy('SVP')->get();
        $RMDs = Pipeline::distinct('RMD')->select('RMD')->orderBy('RMD')->get();
        $cities = Account::distinct('city')->select('city')->where('active', true)->orderBy('city')->get();
        
        $dates = AccountSummary::distinct('MonthEndDate')->select('MonthEndDate')->orderBy('MonthEndDate')->get();

        if ($newFilter == "1") {
            $accounts = $accounts->filter(function($account) {
                return $account->getMonthsSinceCreated() < 7;
            });
        } elseif ($newFilter == "2") {
            $accounts = $accounts->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });
        }

        $params = compact('accounts', 'employees', 'doos', 'practices', 'divisions', 'RSCs', 'regions', 'dates', 'affiliations', 'states', 'groups', 'action', 'sites', 'cities', 'SVPs', 'RMDs');

        return view('admin.reports.summary.index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function usage(SummaryFilter $filter, Request $request)
    {

        $queryString = $request->query();

        if(count($queryString) == 0) {
            $maxDate = AccountSummary::max('MonthEndDate');

            $maxDate = Carbon::parse($maxDate)->format('m-Y');

            return redirect()->route('admin.reports.usage.index', ['monthEndDate' => $maxDate]);
        }

        $accounts = $this->getUsageData($filter, 500);
        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::all();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $states = StateAbbreviation::all();
        
        $dates = AccountSummary::select('MonthEndDate')->get()->unique('MonthEndDate');

        $params = compact('accounts', 'employees', 'practices', 'divisions', 'RSCs', 'regions', 'dates', 'affiliations', 'states', 'action');

        return view('admin.reports.usage.index', $params);
    }

    /**
     * Toggle the global 'role' scope to current Session.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggleScopeSummary(Request $request)
    {
        $ignore = session('ignore-summary-role-scope', false);

        session(['ignore-summary-role-scope' => ! $ignore]);

        return back();
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
                        $account->{'Start Date'} ? \PHPExcel_Shared_Date::PHPToExcel($account->{'Start Date'}) : '',
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
                    $cell->setValue('=(T'.($rowNumber+2).'-AB'.($rowNumber+2).')/T'.($rowNumber+2));
                });

                $sheet->cell('AD'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=(R'.($rowNumber+2).'-Z'.($rowNumber+2).'-Y'.($rowNumber+2).'-X'.($rowNumber+2).')/R'.($rowNumber+2));
                });

                $sheet->cell('AE'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=(S'.($rowNumber+2).'-AA'.($rowNumber+2).')/S'.($rowNumber+2));
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
                    'P3:P'.$rowNumber      => 'mm/dd/yy',
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

    public function usageToExcel(SummaryFilter $filter) {
        $dataToExport = $this->getSummaryData($filter, 5000);
        $headers = ["#", "Contract Name", "Service Line", "System Affiliation", "JV", "Operating Unit",
            "RSC", "Recruiter", "Secondary Recruiter", "Managers", "Last Updated By", "Last Updated Time"
        ];


        Excel::create('Recruitment Update Report', function($excel) use ($dataToExport, $headers){

            $accountIds = $dataToExport->map(function($account) { return $account->accountId; });
            $accountIds = array_values($accountIds->toArray());

            if(count($accountIds) > 100) {
                $accountIds = array_slice($accountIds, 0, 100);
            }

            $accounts = Account::whereIn('id', $accountIds)->get();

            $excel->sheet('Usage Report', function($sheet) use ($dataToExport, $headers){
                
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
                        $account->account && $account->account->pipeline && (is_object($account->account->pipeline->lastUpdate())) ? (is_object($account->account->pipeline->lastUpdate()->updatedBy) ? $account->account->pipeline->lastUpdate()->updatedBy->name : '') : '',
                        $account->account && $account->account->pipeline && $account->account->pipeline->lastUpdate() ? ($account->account->pipeline->lastUpdate()->lastUpdated ? Carbon::parse($account->account->pipeline->lastUpdate()->lastUpdated)->format('m/d/Y H:i:s') : '') : ''
                    ];

                    $sheet->row($rowNumber, $row);
                };

                
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('K1:L1');

                // $sheet->setAutoFilter('A2:L2');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('RECRUITING SUMMARY');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cell('K1', function($cell) {
                    $cell->setValue('USAGE');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->cells('A2:L2', function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A3:L'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setFontSize(8);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

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
                    'K'     => 15,
                    'L'     => 15,
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

                $sheet->getStyle('A1:L'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A2:L2')->applyFromArray($headersStyle);
            });
        })->download('xlsx'); 
    }

    public function exportToExcelDetailed(Request $request, SummaryFilter $filter) {
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

        $newFilter = $request->new;

        if ($newFilter == "1") {
            $dataToExport = $dataToExport->filter(function($account) {
                return $account->getMonthsSinceCreated() < 7;
            });
        } elseif ($newFilter == "2") {
            $dataToExport = $dataToExport->filter(function($account) {
                return $account->getMonthsSinceCreated() > 7;
            });
        }

        $affiliation = $request->affiliations ? $request->affiliations[0] : '';
        $RSC = $request->RSCs ? $request->RSCs[0] : '';
        $operatingUnit = $request->regions ? $request->regions[0] : '';
        $serviceLine = $request->practices ? $request->practices[0] : '';

        if ($RSC != '') {
            $RSCInfo = RSC::find($RSC);
            $RSC = $RSCInfo->name;
        }

        if (substr($serviceLine, 0, 2) === 'ED') {
            $serviceLine = 'ED';
        } else if (substr($serviceLine, 0, 2) === 'HM') {
            $serviceLine = 'HM';
        }

        $fileName = '';

        if ($affiliation != '') {
            $fileName .= $affiliation.' - ';
        }

        if ($RSC != '') {
            $fileName .= $RSC.' - ';
        }

        if ($operatingUnit != '') {
            $fileName .= $operatingUnit.' - ';
        }

        if ($serviceLine != '') {
            $fileName .= $serviceLine;
        }

        if ($fileName == '') {
            $fileName = 'Summary Report Detailed';
        }

        $fileName = trim($fileName, ' -');

        $tableStyle = array(
            'borders' => array(
                'outline' => array(
                    'style' => 'thin',
                    'color' => array('rgb' => '000000'),
                ),
                'inside' => array(
                    'style' => 'thin',
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $headerStyle = array(
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

        $topBorder = array(
            'borders' => array(
                'top' => array(
                    'style' => 'medium',
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $bottomBorder = array(
            'borders' => array(
                'bottom' => array(
                    'style' => 'medium',
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $accountIds = $dataToExport->map(function($account) { return $account->accountId; })->unique();
        $accountIds = array_values($accountIds->toArray());

        if(count($accountIds) > 150) {
            flash(__('Please try to avoid exporting above the limit which currently is at 150.'));

            return back();
        }

        Excel::create($fileName, function($excel) use ($dataToExport, $accountIds, $headers, $tableStyle, $headerStyle, $topBorder, $bottomBorder){

            $accounts = Account::whereIn('id', $accountIds)->with([
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

            $excel->sheet('Summary', function($sheet) use ($dataToExport, $headers, $tableStyle, $headerStyle){
                
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
                        $account->{'Start Date'} ? \PHPExcel_Shared_Date::PHPToExcel($account->{'Start Date'}) : '',
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
                    $cell->setValue('=(T'.($rowNumber+2).'-AB'.($rowNumber+2).')/T'.($rowNumber+2));
                });

                $sheet->cell('AD'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=(R'.($rowNumber+2).'-Z'.($rowNumber+2).'-Y'.($rowNumber+2).'-X'.($rowNumber+2).')/R'.($rowNumber+2));
                });

                $sheet->cell('AE'.($rowNumber+2), function($cell) use($rowNumber) {
                    $cell->setValue('=(S'.($rowNumber+2).'-AA'.($rowNumber+2).')/S'.($rowNumber+2));
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
                    'P3:P'.$rowNumber      => 'mm/dd/yy',
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

                $sheet->getStyle('A1:AV'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A1:Q'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('R1:T'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('U1:W'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('X1:AB'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AC1:AE'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AF1:AJ'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('AK1:AO'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A2:AV2')->applyFromArray($headerStyle);

                $sheet->getStyle('Q2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AH2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AI2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AJ2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AO2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('AT2')->getAlignment()->setWrapText(true);

                $sheet->getColumnDimension('AF')->setVisible(false);
                $sheet->getColumnDimension('AG')->setVisible(false);
                $sheet->getColumnDimension('AH')->setVisible(false);
                $sheet->getColumnDimension('AI')->setVisible(false);
                $sheet->getColumnDimension('AJ')->setVisible(false);
                $sheet->getColumnDimension('AK')->setVisible(false);
                $sheet->getColumnDimension('AL')->setVisible(false);
                $sheet->getColumnDimension('AM')->setVisible(false);
                $sheet->getColumnDimension('AN')->setVisible(false);
                $sheet->getColumnDimension('AO')->setVisible(false);
                $sheet->getColumnDimension('AP')->setVisible(false);
                $sheet->getColumnDimension('AQ')->setVisible(false);
                $sheet->getColumnDimension('AR')->setVisible(false);
                $sheet->getColumnDimension('AS')->setVisible(false);
                $sheet->getColumnDimension('AT')->setVisible(false);
                $sheet->getColumnDimension('AU')->setVisible(false);
                $sheet->getColumnDimension('AV')->setVisible(false);
            });

            foreach ($accounts as $account) {

                if ($account->pipeline->practiceTime == "hours") {
                    if ($account->pipeline->staffPhysicianFTENeeds == 0) {
                        $percentRecruitedPhys = 0;
                    } else {
                        $percentRecruitedPhys = ($account->pipeline->staffPhysicianFTEHaves / $account->pipeline->staffPhysicianFTENeeds) * 100;
                    }
                    
                    if ($account->pipeline->staffAppsFTENeeds == 0) {
                        $percentRecruitedApp = 0;
                    } else {
                        $percentRecruitedApp = ($account->pipeline->staffAppsFTEHaves / $account->pipeline->staffAppsFTENeeds) * 100;
                    }

                    $percentRecruitedPhysReport = $percentRecruitedPhys > 100 ? 100 : $percentRecruitedPhys;
                    $percentRecruitedAppReport = $percentRecruitedApp > 100 ? 100 : $percentRecruitedApp;
                } else {
                    if ($account->pipeline->staffPhysicianNeeds == 0) {
                        $percentRecruitedPhys = 0;
                    } else {
                        $percentRecruitedPhys = ($account->pipeline->staffPhysicianFTEHaves / $account->pipeline->staffPhysicianNeeds) * 100;
                    }

                    if ($account->pipeline->staffAppsNeeds == 0) {
                        $percentRecruitedApp = 0;
                    } else {
                        $percentRecruitedApp = ($account->pipeline->staffAppsFTEHaves / $account->pipeline->staffAppsNeeds) * 100;
                    }

                    $percentRecruitedPhysReport = $percentRecruitedPhys > 100 ? 100 : $percentRecruitedPhys;
                    $percentRecruitedAppReport = $percentRecruitedApp > 100 ? 100 : $percentRecruitedApp;
                }

                $activeRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortByDesc(function($rosterBench){
                    return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
                });

                $SMD = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->isSMD;
                });

                $AMD = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->isAMD;
                });

                $SMDOpen = $account->hasSMD ? ($SMD->isEmpty() ? 1 : 0) : 0;

                $AMDOpen = $account->hasAMD ? ($AMD->isEmpty() ? 1 : 0) : 0;

                $benchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortByDesc(function($rosterBench){
                    return sprintf('%-12s%s', $rosterBench->isChief, $rosterBench->name);
                });

                $activeRosterAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'roster';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortBy('name');

                $benchAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->place == 'bench';
                })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
                ->sortBy('name');

                $recruitings = $account->pipeline->recruitings->reject(function($recruiting) { 
                    return !is_null($recruiting->declined); 
                })->sortBy('name');

                $locums = $account->pipeline->locums->reject(function($locum) { 
                    return !is_null($locum->declined); 
                })->sortBy('name');

                $declines = $account->pipeline->recruitings->concat($account->pipeline->locums)
                ->filter(function($locum) { 
                    return !is_null($locum->declined); 
                })->sortByDesc('declined');

                $resigneds = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return !is_null($rosterBench->resigned);
                })->sortBy('name');

                $credentialersPhys = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'physician' && $rosterBench->signedNotStarted;
                })->reject(function($rosterBench){
                    return $rosterBench->resigned;
                })->sortBy('name');

                $credentialersAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                    return $rosterBench->activity == 'app' && $rosterBench->signedNotStarted;
                })->reject(function($rosterBench){
                    return $rosterBench->resigned;
                })->sortBy('name');


                $sheetName = (strlen($account->name) > 31) ? substr($account->name,0,28).'...' : $account->name;
                $sheetName = str_replace("/", "_", $sheetName);
                $sheetName = str_replace("?", "_", $sheetName);

                $excel->sheet($sheetName, function($sheet) use ($account, $percentRecruitedPhys, $percentRecruitedApp, $percentRecruitedPhysReport, $percentRecruitedAppReport, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $recruitings, $locums, $declines, $resigneds, $credentialersPhys, $credentialersAPP, $SMDOpen, $AMDOpen, $tableStyle, $headerStyle, $topBorder, $bottomBorder){

                    $accountInfo = $account->name.', '.$account->siteCode.' '.$account->address.' '.($account->recruiter ? $account->recruiter->fullName() : '').', '.($account->manager ? $account->manager->fullName() : '');

                    $sheet->mergeCells('A1:K3');
                    $sheet->mergeCells('A23:K23');
                    $sheet->mergeCells('A24:K24');
                    $sheet->mergeCells('A10:K10');
                    $sheet->mergeCells('C14:E14');
                    $sheet->mergeCells('G14:I14');
                    $sheet->mergeCells('C15:D15');
                    $sheet->mergeCells('G15:H15');

                    $sheet->cell('A1', function($cell) use ($accountInfo) {
                        $cell->setValue($accountInfo);
                    });

                    $sheet->cell('B5', function($cell) {
                        $cell->setValue('Medical Director');
                    });

                    $sheet->cell('C5', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->medicalDirector);
                    });

                    $sheet->cell('B6', function($cell) {
                        $cell->setValue('SVP');
                    });

                    $sheet->cell('C6', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->svp);
                    });

                    $sheet->cell('B7', function($cell) {
                        $cell->setValue('Service Line');
                    });

                    $sheet->cell('C7', function($cell) use ($account) {
                        $cell->setValue($account->practices->count() ? $account->practices->first()->name : '');
                    });

                    $sheet->cell('E5', function($cell) {
                        $cell->setValue('RMD');
                    });

                    $sheet->cell('F5', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->rmd);
                    });

                    $sheet->cell('E6', function($cell) {
                        $cell->setValue('DOO');
                    });

                    $sheet->cell('F6', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->dca);
                    });

                    $sheet->cell('E7', function($cell) {
                        $cell->setValue('Service Line Time');
                    });

                    $sheet->cell('F7', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->practiceTime);
                    });

                    $sheet->cell('H5', function($cell) {
                        $cell->setValue('RSC');
                    });

                    $sheet->cell('I5', function($cell) use ($account) {
                        $cell->setValue($account->rsc ? $account->rsc->name : '');
                    });

                    $sheet->cell('H6', function($cell) {
                        $cell->setValue('Operating Unit');
                    });

                    $sheet->cell('I6', function($cell) use ($account) {
                        $cell->setValue($account->region ? $account->region->name : '');
                    });

                    $sheet->cell('A10', function($cell) {
                        $cell->setValue('Complete Staffing and Current Openings');
                        $cell->setBackground('#FFFF00');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('C12', function($cell) {
                        $cell->setValue('SMD');
                    });

                    $sheet->cell('D12', function($cell) use ($SMDOpen) {
                        $cell->setValue($SMDOpen);
                        $cell->setBackground('#FFFF00');
                    });

                    $sheet->cell('E12', function($cell) {
                        $cell->setValue('AMD');
                    });

                    $sheet->cell('F12', function($cell) use ($AMDOpen) {
                        $cell->setValue($AMDOpen);
                        $cell->setBackground('#FFFF00');
                    });

                    $sheet->cell('G12', function($cell) {
                        $cell->setValue('PHYS');
                    });

                    $sheet->cell('H12', function($cell) use ($account, $SMDOpen, $AMDOpen) {
                        if ($account->pipeline->practiceTime == "hours") {
                            $phys = $this->roundnum($account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves, 0.5) - $SMDOpen;

                            $phys = $phys < 0 ? 0 : $phys;
                        } else {
                            $phys = $this->roundnum($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianHaves, 0.5) - $SMDOpen;

                            $phys = $phys < 0 ? 0 : $phys;
                        }

                        $cell->setValue($phys);

                        $cell->setBackground('#FFFF00');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('I12', function($cell) {
                        $cell->setValue('APP');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('J12', function($cell) use ($account) {
                        if ($account->pipeline->practiceTime == "hours") {
                            $cell->setValue($account->pipeline->staffAppsFTEOpenings);
                        } else {
                            $cell->setValue($account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves);
                        }
                        
                        $cell->setBackground('#FFFF00');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cells('C14:G14', function($cells) {
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setFontSize(11);
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $sheet->cell('C14', function($cell) {
                        $cell->setValue('Physician');
                    });

                    $sheet->cell('G14', function($cell) {
                        $cell->setValue('APPs');
                    });

                    $sheet->cell('C15', function($cell) {
                        $cell->setValue('Full Time Hours');
                    });
                    $sheet->cell('G15', function($cell) {
                        $cell->setValue('Full Time Hours');
                    });

                    $sheet->cell('E15', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->fullTimeHoursPhys);
                    });
                    $sheet->cell('I15', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->fullTimeHoursApps);
                    });

                    if ($account->pipeline->practiceTime == "hours") {
                        $sheet->cell('D16', function($cell) {
                            $cell->setValue('Hours');
                        });
                        $sheet->cell('E16', function($cell) {
                            $cell->setValue('FTEs');
                        });

                        $sheet->cell('H16', function($cell) {
                            $cell->setValue('Hours');
                        });
                        $sheet->cell('I16', function($cell) {
                            $cell->setValue('FTEs');
                        });
                    } else {
                        $sheet->cell('D16', function($cell) {
                            $cell->setValue('FTEs');
                        });

                        $sheet->cell('H16', function($cell) {
                            $cell->setValue('FTEs');
                        });
                    }

                    $sheet->cell('C17', function($cell) {
                        $cell->setValue('Haves');
                    });
                    $sheet->cell('C18', function($cell) {
                        $cell->setValue('Needs');
                    });
                    $sheet->cell('C19', function($cell) {
                        $cell->setValue('Openings');
                    });
                    $sheet->cell('C20', function($cell) {
                        $cell->setValue('Percent Recruited Actual');
                    });
                    $sheet->cell('C21', function($cell) {
                        $cell->setValue('Percent Recruited Reported');
                    });

                    if ($account->pipeline->practiceTime == "hours") {
                        $sheet->cell('D17', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffPhysicianHaves);
                        });
                        $sheet->cell('D18', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffPhysicianNeeds);
                        });
                        $sheet->cell('D19', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianHaves);
                        });
                        $sheet->cell('D20', function($cell) use ($percentRecruitedPhys) {
                            $cell->setValue(number_format($percentRecruitedPhys, 1).'%');
                        });
                        $sheet->cell('D21', function($cell) use ($percentRecruitedPhysReport) {
                            $cell->setValue(number_format($percentRecruitedPhysReport, 1).'%');
                        });

                        $sheet->cell('E17', function($cell) use ($account) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue(round($account->pipeline->staffPhysicianHaves / $account->pipeline->fullTimeHoursPhys, 1));
                            }
                        });
                        $sheet->cell('E18', function($cell) use ($account) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue(round($account->pipeline->staffPhysicianNeeds / $account->pipeline->fullTimeHoursPhys, 1));
                            }
                        });
                        $sheet->cell('E19', function($cell) use ($account) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue($this->roundnum(($account->pipeline->staffPhysicianNeeds / $account->pipeline->fullTimeHoursPhys) - ($account->pipeline->staffPhysicianHaves / $account->pipeline->fullTimeHoursPhys), 0.5));
                            }
                        });
                    } else {
                        $sheet->cell('D17', function($cell) use ($account, $activeRosterPhysicians) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue($account->pipeline->staffPhysicianHaves);
                            }
                        });
                        $sheet->cell('D18', function($cell) use ($account) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue($account->pipeline->staffPhysicianNeeds);
                            }
                        });
                        $sheet->cell('D19', function($cell) use ($account) {
                            if ($account->pipeline->fullTimeHoursPhys == 0) {
                                $cell->setValue(0);
                            } else {
                                $cell->setValue($this->roundnum($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianHaves, 0.5));
                            }
                        });
                        $sheet->cell('D20', function($cell) use ($percentRecruitedPhys) {
                            $cell->setValue(number_format($percentRecruitedPhys, 1).'%');
                        });
                        $sheet->cell('D21', function($cell) use ($percentRecruitedPhysReport) {
                            $cell->setValue(number_format($percentRecruitedPhysReport, 1).'%');
                        });
                    }

                    $sheet->cell('G17', function($cell) {
                        $cell->setValue('Haves');
                    });
                    $sheet->cell('G18', function($cell) {
                        $cell->setValue('Needs');
                    });
                    $sheet->cell('G19', function($cell) {
                        $cell->setValue('Openings');
                    });
                    $sheet->cell('G20', function($cell) {
                        $cell->setValue('Percent Recruited Actual');
                    });
                    $sheet->cell('G21', function($cell) {
                        $cell->setValue('Percent Recruited Reported');
                    });

                    if ($account->pipeline->practiceTime == "hours") {
                        $sheet->cell('H17', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsHaves);
                        });
                        $sheet->cell('H18', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsNeeds);
                        });
                        $sheet->cell('H19', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsOpenings);
                        });
                        $sheet->cell('H20', function($cell) use ($percentRecruitedApp) {
                            $cell->setValue(number_format($percentRecruitedApp, 1).'%');
                        });
                        $sheet->cell('H21', function($cell) use ($percentRecruitedAppReport) {
                            $cell->setValue(number_format($percentRecruitedAppReport, 1).'%');
                        });

                        $sheet->cell('I17', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsFTEHaves);
                        });
                        $sheet->cell('I18', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsFTENeeds);
                        });
                        $sheet->cell('I19', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsFTEOpenings);
                        });
                    } else {
                        $sheet->cell('H17', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsFTEHaves);
                        });
                        $sheet->cell('H18', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsNeeds);
                        });
                        $sheet->cell('H19', function($cell) use ($account) {
                            $cell->setValue($account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves);
                        });
                        $sheet->cell('H20', function($cell) use ($percentRecruitedApp) {
                            $cell->setValue(number_format($percentRecruitedApp, 1).'%');
                        });
                        $sheet->cell('H21', function($cell) use ($percentRecruitedAppReport) {
                            $cell->setValue(number_format($percentRecruitedAppReport, 1).'%');
                        });
                    }

                    $sheet->cell('A23', function($cell) use ($accountInfo) {
                        $cell->setValue('Current Roster');
                        $cell->setBackground('#1eb1ed');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('A24', function($cell) use ($accountInfo) {
                        $cell->setValue('Physician');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cells('A25:I25', function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $sheet->cell('J25', function($cell) use ($accountInfo) {
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('K25', function($cell) use ($accountInfo) {
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $rosterPhysicianFields = [
                        'SMD',
                        'AMD',
                        'Name',
                        'Hours',
                        'FT/PTG/EMB',
                        'Interview',
                        'Contract Out',
                        'Contract In',
                        'First Shift',
                        'Last contact Date & Next Steps',
                        'Signed Not Started'
                    ];


                    $rosterAppsFields = [
                        'Chief',
                        '',
                        'Name',
                        'Hours',
                        'FT/PTG/EMB',
                        'Interview',
                        'Contract Out',
                        'Contract In',
                        'First Shift',
                        'Last contact Date & Next Steps',
                        'Signed Not Started'
                    ];

                    $benchFields = [
                        'Name',
                        '',
                        '',
                        'Hours',
                        'PRN/Locum',
                        'Interview',
                        'Contract Out',
                        'Contract In',
                        'First Shift',
                        'Last contact Date & Next Steps',
                        'Signed Not Started'
                    ];

                    $recruitingFiedls = [
                        'PHYS/APP',
                        '',
                        'Name',
                        '',
                        'FT/PT/EMB',
                        'Interview',
                        'Contract Out',
                        'Contract In',
                        'First Shift',
                        'Last contact Date & Next Steps',
                        ''
                    ];

                    $locumFiedls = [
                        'PHYS/APP',
                        '',
                        'Name',
                        'Agency',
                        'Potential Start',
                        'Credentialing Notes',
                        '',
                        'Shifts',
                        'Start Date',
                        'Comments',
                        ''
                    ];

                    $declinedFields = [
                        'Name',
                        '',
                        '',
                        'FT/PT/EMB',
                        'Interview',
                        'Application',
                        '',
                        'Contract Out',
                        'Declined',
                        'Reason',
                        ''
                    ];

                    $resignedFields = [
                        'PHYS/APP',
                        '',
                        '',
                        'Name',
                        '',
                        '',
                        '',
                        'Resigned',
                        '',
                        'Reason',
                        ''
                    ];

                    $credentialingFields = [
                        'Name',
                        '',
                        'Hours',
                        'FT/PT/EMB',
                        'File To Credentialing',
                        'APP To Hospital',
                        'Stage',
                        'Privilege Goal',
                        'Enrollment Status',
                        'Notes',
                        ''
                    ];


                    $sheet->row(25, $rosterPhysicianFields);

                    $currentRosterPhysicianStart = 26;

                    foreach ($activeRosterPhysicians as $rosterPhysician) {
                        $row = [
                            $rosterPhysician->isSMD ? 'Yes' : '',
                            $rosterPhysician->isAMD ? 'Yes' : '',
                            $rosterPhysician->name,
                            $rosterPhysician->hours,
                            strtoupper($rosterPhysician->contract),
                            $rosterPhysician->interview ? $rosterPhysician->interview->format('m/d/Y') : '',
                            $rosterPhysician->contractOut ? $rosterPhysician->contractOut->format('m/d/Y') : '',
                            $rosterPhysician->contractIn ? $rosterPhysician->contractIn->format('m/d/Y') : '',
                            $rosterPhysician->firstShift ? $rosterPhysician->firstShift->format('m/d/Y') : '',
                            $rosterPhysician->notes,
                            $rosterPhysician->signedNotStarted ? 'Yes' : ''
                        ];

                        $sheet->row($currentRosterPhysicianStart, $row);

                        $currentRosterPhysicianStart++;
                    }

                    $currentRosterAppStart = $currentRosterPhysicianStart+2;

                    $sheet->mergeCells('A'.$currentRosterAppStart.':K'.$currentRosterAppStart);

                    $sheet->cell('A'.$currentRosterAppStart, function($cell) use ($accountInfo) {
                        $cell->setValue('APPs');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($currentRosterAppStart+1).':B'.($currentRosterAppStart+1));
                    $sheet->row($currentRosterAppStart+1, $rosterAppsFields);

                    $sheet->cells('A'.($currentRosterAppStart+1).':K'.($currentRosterAppStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $currentRosterAppStartTable = $currentRosterAppStart+2;

                    foreach ($activeRosterAPPs as $rosterAPP) {
                        $sheet->mergeCells('A'.$currentRosterAppStartTable.':B'.$currentRosterAppStartTable);

                        $row = [
                            $rosterAPP->isChief ? 'Yes' : '',
                            '',
                            $rosterAPP->name,
                            $rosterAPP->hours,
                            strtoupper($rosterAPP->contract),
                            $rosterAPP->interview ? $rosterAPP->interview->format('m/d/Y') : '',
                            $rosterAPP->contractOut ? $rosterAPP->contractOut->format('m/d/Y') : '',
                            $rosterAPP->contractIn ? $rosterAPP->contractIn->format('m/d/Y') : '',
                            $rosterAPP->firstShift ? $rosterAPP->firstShift->format('m/d/Y') : '',
                            $rosterAPP->notes,
                            $rosterAPP->signedNotStarted ? 'Yes' : ''
                        ];

                        $sheet->row($currentRosterAppStartTable, $row);

                        $currentRosterAppStartTable++;
                    }

                    $currentBenchPhysicianStart = $currentRosterAppStartTable+2;

                    $sheet->mergeCells('A'.$currentBenchPhysicianStart.':K'.$currentBenchPhysicianStart);

                    $sheet->cell('A'.$currentBenchPhysicianStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Current Bench');
                        $cell->setBackground('#1eb1ed');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($currentBenchPhysicianStart+1).':K'.($currentBenchPhysicianStart+1));

                    $sheet->cell('A'.($currentBenchPhysicianStart+1), function($cell) use ($accountInfo) {
                        $cell->setValue('Physician');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($currentBenchPhysicianStart+2).':C'.($currentBenchPhysicianStart+2));
                    $sheet->row($currentBenchPhysicianStart+2, $benchFields);

                    $sheet->cells('A'.($currentBenchPhysicianStart+2).':K'.($currentBenchPhysicianStart+2), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $currentBenchPhysicianStartTable = $currentBenchPhysicianStart+3;

                    foreach ($benchPhysicians as $benchPhysician) {
                        $sheet->mergeCells('A'.$currentBenchPhysicianStartTable.':C'.$currentBenchPhysicianStartTable);

                        $row = [
                            $benchPhysician->name,
                            '',
                            '',
                            $benchPhysician->hours,
                            strtoupper($benchPhysician->contract),
                            $benchPhysician->interview ? $benchPhysician->interview->format('m/d/Y') : '',
                            $benchPhysician->contractOut ? $benchPhysician->contractOut->format('m/d/Y') : '',
                            $benchPhysician->contractIn ? $benchPhysician->contractIn->format('m/d/Y') : '',
                            $benchPhysician->firstShift ? $benchPhysician->firstShift->format('m/d/Y') : '',
                            $benchPhysician->notes,
                            $benchPhysician->signedNotStarted ? 'Yes' : ''
                        ];

                        $sheet->row($currentBenchPhysicianStartTable, $row);

                        $currentBenchPhysicianStartTable++;
                    }

                    $currentBenchAPPStart = $currentBenchPhysicianStartTable+2;

                    $sheet->mergeCells('A'.$currentBenchAPPStart.':K'.$currentBenchAPPStart);

                    $sheet->cell('A'.$currentBenchAPPStart, function($cell) use ($accountInfo) {
                        $cell->setValue('APPs');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($currentBenchAPPStart+1).':C'.($currentBenchAPPStart+1));
                    $sheet->row($currentBenchAPPStart+1, $benchFields);

                    $sheet->cells('A'.($currentBenchAPPStart+1).':K'.($currentBenchAPPStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $currentBenchAPPStartTable = $currentBenchAPPStart+2;

                    foreach ($benchAPPs as $benchAPP) {
                        $sheet->mergeCells('A'.$currentBenchAPPStartTable.':C'.$currentBenchAPPStartTable);

                        $row = [
                            $benchAPP->name,
                            '',
                            '',
                            $benchAPP->hours,
                            strtoupper($benchAPP->contract),
                            $benchAPP->interview ? $benchAPP->interview->format('m/d/Y') : '',
                            $benchAPP->contractOut ? $benchAPP->contractOut->format('m/d/Y') : '',
                            $benchAPP->contractIn ? $benchAPP->contractIn->format('m/d/Y') : '',
                            $benchAPP->firstShift ? $benchAPP->firstShift->format('m/d/Y') : '',
                            $benchAPP->notes,
                            $benchAPP->signedNotStarted ? 'Yes' : ''
                        ];

                        $sheet->row($currentBenchAPPStartTable, $row);

                        $currentBenchAPPStartTable++;
                    }

                    $recruitingPipelineStart = $currentBenchAPPStartTable+2;

                    $sheet->mergeCells('A'.$recruitingPipelineStart.':K'.$recruitingPipelineStart);

                    $sheet->cell('A'.$recruitingPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Recruiting Pipeline');
                        $cell->setBackground('#00a65a');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($recruitingPipelineStart+1).':B'.($recruitingPipelineStart+1));
                    $sheet->mergeCells('C'.($recruitingPipelineStart+1).':D'.($recruitingPipelineStart+1));
                    $sheet->mergeCells('J'.($recruitingPipelineStart+1).':K'.($recruitingPipelineStart+1));

                    $sheet->row($recruitingPipelineStart+1, $recruitingFiedls);

                    $sheet->cells('A'.($recruitingPipelineStart+1).':K'.($recruitingPipelineStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $recruitingPipelineStartTable = $recruitingPipelineStart+2;

                    foreach ($recruitings as $recruiting) {
                        $sheet->mergeCells('A'.$recruitingPipelineStartTable.':B'.$recruitingPipelineStartTable);
                        $sheet->mergeCells('C'.$recruitingPipelineStartTable.':D'.$recruitingPipelineStartTable);
                        $sheet->mergeCells('J'.$recruitingPipelineStartTable.':K'.$recruitingPipelineStartTable);

                        $row = [
                            $recruiting->type,
                            '',
                            $recruiting->name,
                            '',
                            strtoupper($recruiting->contract),
                            $recruiting->interview ? $recruiting->interview->format('m/d/Y') : '',
                            $recruiting->contractOut ? $recruiting->contractOut->format('m/d/Y') : '',
                            $recruiting->contractIn ? $recruiting->contractIn->format('m/d/Y') : '',
                            $recruiting->firstShift ? $recruiting->firstShift->format('m/d/Y') : '',
                            $recruiting->notes,
                            ''
                        ];

                        $sheet->row($recruitingPipelineStartTable, $row);

                        $recruitingPipelineStartTable++;
                    }

                    $locumsPipelineStart = $recruitingPipelineStartTable+2;

                    $sheet->mergeCells('A'.$locumsPipelineStart.':K'.$locumsPipelineStart);

                    $sheet->cell('A'.$locumsPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Locums Pipeline');
                        $cell->setBackground('#00a65a');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($locumsPipelineStart+1).':B'.($locumsPipelineStart+1));
                    $sheet->mergeCells('F'.($locumsPipelineStart+1).':G'.($locumsPipelineStart+1));
                    $sheet->mergeCells('J'.($locumsPipelineStart+1).':K'.($locumsPipelineStart+1));

                    $sheet->row($locumsPipelineStart+1, $locumFiedls);

                    $sheet->cells('A'.($locumsPipelineStart+1).':K'.($locumsPipelineStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $locumsPipelineStartTable = $locumsPipelineStart+2;

                    foreach ($locums as $locum) {
                        $sheet->mergeCells('A'.$locumsPipelineStartTable.':B'.$locumsPipelineStartTable);
                        $sheet->mergeCells('F'.$locumsPipelineStartTable.':G'.$locumsPipelineStartTable);
                        $sheet->mergeCells('J'.$locumsPipelineStartTable.':K'.$locumsPipelineStartTable);

                        $row = [
                            $locum->type,
                            '',
                            $locum->name,
                            $locum->agency,
                            $locum->potentialStart ? $locum->potentialStart->format('m/d/Y') : '',
                            $locum->credentialingNotes,
                            '',
                            $locum->shiftsOffered,
                            $locum->startDate ? $locum->startDate->format('m/d/Y') : '',
                            $locum->comments,
                            ''
                        ];

                        $sheet->row($locumsPipelineStartTable, $row);

                        $locumsPipelineStartTable++;
                    }

                    $declinedPipelineStart = $locumsPipelineStartTable+2;

                    $sheet->mergeCells('A'.$declinedPipelineStart.':K'.$declinedPipelineStart);

                    $sheet->cell('A'.$declinedPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Declined List');
                        $cell->setBackground('#f39c12');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($declinedPipelineStart+1).':C'.($declinedPipelineStart+1));
                    $sheet->mergeCells('F'.($declinedPipelineStart+1).':G'.($declinedPipelineStart+1));
                    $sheet->mergeCells('J'.($declinedPipelineStart+1).':K'.($declinedPipelineStart+1));

                    $sheet->row($declinedPipelineStart+1, $declinedFields);

                    $sheet->cells('A'.($declinedPipelineStart+1).':K'.($declinedPipelineStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $declinedPipelineStartTable = $declinedPipelineStart+2;

                    foreach ($declines as $decline) {
                        $sheet->mergeCells('A'.$declinedPipelineStartTable.':C'.$declinedPipelineStartTable);
                        $sheet->mergeCells('F'.$declinedPipelineStartTable.':G'.$declinedPipelineStartTable);
                        $sheet->mergeCells('J'.$declinedPipelineStartTable.':K'.$declinedPipelineStartTable);

                        $row = [
                            $decline->name,
                            '',
                            '',
                            strtoupper($decline->contract),
                            $decline->interview ? $decline->interview->format('m/d/Y') : '',
                            $decline->application ? $decline->application->format('m/d/Y') : '',
                            '',
                            $decline->contractOut ? $decline->contractOut->format('m/d/Y') : '',
                            $decline->declined ? $decline->declined->format('m/d/Y') : '',
                            $decline->declinedReason,
                            ''
                        ];

                        $sheet->row($declinedPipelineStartTable, $row);

                        $declinedPipelineStartTable++;
                    }

                    $resignedPipelineStart = $declinedPipelineStartTable+2;

                    $sheet->mergeCells('A'.$resignedPipelineStart.':K'.$resignedPipelineStart);

                    $sheet->cell('A'.$resignedPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Resigned List');
                        $cell->setBackground('#f39c12');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($resignedPipelineStart+1).':C'.($resignedPipelineStart+1));
                    $sheet->mergeCells('D'.($resignedPipelineStart+1).':G'.($resignedPipelineStart+1));
                    $sheet->mergeCells('H'.($resignedPipelineStart+1).':I'.($resignedPipelineStart+1));
                    $sheet->mergeCells('J'.($resignedPipelineStart+1).':K'.($resignedPipelineStart+1));

                    $sheet->row($resignedPipelineStart+1, $resignedFields);

                    $sheet->cells('A'.($resignedPipelineStart+1).':K'.($resignedPipelineStart+1), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $resignedPipelineStartTable = $resignedPipelineStart+2;

                    foreach ($resigneds as $resigned) {
                        $sheet->mergeCells('A'.$resignedPipelineStartTable.':C'.$resignedPipelineStartTable);
                        $sheet->mergeCells('D'.$resignedPipelineStartTable.':G'.$resignedPipelineStartTable);
                        $sheet->mergeCells('H'.$resignedPipelineStartTable.':I'.$resignedPipelineStartTable);
                        $sheet->mergeCells('J'.$resignedPipelineStartTable.':K'.$resignedPipelineStartTable);

                        $row = [
                            strtoupper($resigned->type),
                            '',
                            '',
                            $resigned->name,
                            '',
                            '',
                            '',
                            $resigned->resigned ? $resigned->resigned->format('m/d/Y') : '',
                            '',
                            $resigned->resignedReason,
                            ''
                        ];

                        $sheet->row($resignedPipelineStartTable, $row);

                        $resignedPipelineStartTable++;
                    }

                    $credentialingPipelineStart = $resignedPipelineStartTable+2;

                    $sheet->mergeCells('A'.$credentialingPipelineStart.':K'.$credentialingPipelineStart);

                    $sheet->cell('A'.$credentialingPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Credentialing Pipeline');
                        $cell->setBackground('#1eb1ed');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($credentialingPipelineStart+1).':K'.($credentialingPipelineStart+1));

                    $sheet->cell('A'.($credentialingPipelineStart+1), function($cell) use ($accountInfo) {
                        $cell->setValue('Physician');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($credentialingPipelineStart+2).':B'.($credentialingPipelineStart+2));
                    $sheet->mergeCells('J'.($credentialingPipelineStart+2).':K'.($credentialingPipelineStart+2));

                    $sheet->row($credentialingPipelineStart+2, $credentialingFields);

                    $sheet->cells('A'.($credentialingPipelineStart+2).':K'.($credentialingPipelineStart+2), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $credentialingPipelinePhysicianStart = $credentialingPipelineStart+3;

                    foreach ($credentialersPhys as $credentialer) {
                        $sheet->mergeCells('A'.$credentialingPipelinePhysicianStart.':B'.$credentialingPipelinePhysicianStart);
                        $sheet->mergeCells('J'.$credentialingPipelinePhysicianStart.':K'.$credentialingPipelinePhysicianStart);

                        $row = [
                            $credentialer->name,
                            '',
                            $credentialer->hours,
                            strtoupper($credentialer->contract),
                            $credentialer->fileToCredentialing ? $credentialer->fileToCredentialing->format('m/d/Y') : '',
                            $credentialer->appToHospital ? $credentialer->appToHospital->format('m/d/Y') : '',
                            $credentialer->stage,
                            $credentialer->privilegeGoal ? $credentialer->privilegeGoal->format('m/d/Y') : '',
                            $credentialer->enrollmentStatus,
                            $credentialer->credentialingNotes,
                            ''
                        ];

                        $sheet->row($credentialingPipelinePhysicianStart, $row);

                        $credentialingPipelinePhysicianStart++;
                    }

                    $sheet->mergeCells('A'.($credentialingPipelinePhysicianStart+2).':K'.($credentialingPipelinePhysicianStart+2));

                    $sheet->cell('A'.($credentialingPipelinePhysicianStart+2), function($cell) use ($accountInfo) {
                        $cell->setValue('APPs');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->mergeCells('A'.($credentialingPipelinePhysicianStart+3).':B'.($credentialingPipelinePhysicianStart+3));
                    $sheet->mergeCells('J'.($credentialingPipelinePhysicianStart+3).':K'.($credentialingPipelinePhysicianStart+3));

                    $sheet->row($credentialingPipelinePhysicianStart+3, $credentialingFields);

                    $sheet->cells('A'.($credentialingPipelinePhysicianStart+3).':K'.($credentialingPipelinePhysicianStart+3), function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                    });

                    $credentialingPipelineAPPStart = $credentialingPipelinePhysicianStart+4;

                    foreach ($credentialersAPP as $credentialer) {
                        $sheet->mergeCells('A'.$credentialingPipelineAPPStart.':B'.$credentialingPipelineAPPStart);
                        $sheet->mergeCells('J'.$credentialingPipelineAPPStart.':K'.$credentialingPipelineAPPStart);

                        $row = [
                            $credentialer->name,
                            '',
                            $credentialer->hours,
                            strtoupper($credentialer->contract),
                            $credentialer->fileToCredentialing ? $credentialer->fileToCredentialing->format('m/d/Y') : '',
                            $credentialer->appToHospital ? $credentialer->appToHospital->format('m/d/Y') : '',
                            $credentialer->stage,
                            $credentialer->privilegeGoal ? $credentialer->privilegeGoal->format('m/d/Y') : '',
                            $credentialer->enrollmentStatus,
                            $credentialer->credentialingNotes,
                            ''
                        ];

                        $sheet->row($credentialingPipelineAPPStart, $row);

                        $credentialingPipelineAPPStart++;
                    }

                    $sheet->cells('A1:K'.$credentialingPipelineAPPStart, function($cells) {
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setFontSize(9);
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $sheet->cell('A1', function($cell) {
                        $cell->setBackground('#d0cece');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cells('B5:B7', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('E5:E7', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('H5:H6', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('C17:C21', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('G17:G21', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('A10:K16', function($cells) {
                        $cells->setFontSize(11);
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('A23:K25', function($cells) {
                        $cells->setFontSize(11);
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cell('J25', function($cell){
                        $cell->setAlignment('left');
                    });

                    $sheet->cells('A'.$currentRosterAppStart.':K'.($currentRosterAppStart+1), function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cell('J'.($currentRosterAppStart+1), function($cell) {
                        $cell->setAlignment('left');
                    });

                    $sheet->cells('A'.$currentBenchPhysicianStart.':K'.($currentBenchPhysicianStart+2), function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cell('J'.($currentBenchPhysicianStart+2), function($cell) {
                        $cell->setAlignment('left');
                    });

                    $sheet->cells('A'.$currentBenchAPPStart.':K'.($currentBenchAPPStart+1), function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cell('J'.($currentBenchAPPStart+1), function($cell) {
                        $cell->setAlignment('left');
                    });

                    $sheet->cells('A'.$recruitingPipelineStart.':K'.$recruitingPipelineStart, function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($recruitingPipelineStart+1).':K'.($recruitingPipelineStart+1), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.$locumsPipelineStart.':K'.$locumsPipelineStart, function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($locumsPipelineStart+1).':K'.($locumsPipelineStart+1), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.$declinedPipelineStart.':K'.$declinedPipelineStart, function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($declinedPipelineStart+1).':K'.($declinedPipelineStart+1), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.$resignedPipelineStart.':K'.$resignedPipelineStart, function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($resignedPipelineStart+1).':K'.($resignedPipelineStart+1), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.$credentialingPipelineStart.':K'.($credentialingPipelineStart+1), function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($credentialingPipelineStart+2).':K'.($credentialingPipelineStart+2), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($credentialingPipelinePhysicianStart+2).':K'.($credentialingPipelinePhysicianStart+2), function($cells) {
                        $cells->setFontSize(11);
                    });

                    $sheet->cells('A'.($credentialingPipelinePhysicianStart+3).':K'.($credentialingPipelinePhysicianStart+3), function($cells) {
                        $cells->setAlignment('left');
                        $cells->setFontSize(11);
                    });

                    $sheet->setWidth(array(
                        'A'     => 9,
                        'B'     => 15,
                        'C'     => 22,
                        'D'     => 15,
                        'E'     => 18,
                        'F'     => 18,
                        'G'     => 22,
                        'H'     => 14,
                        'I'     => 18,
                        'J'     => 30,
                        'K'     => 19
                    ));

                    $heights = array(
                        4   => 35
                    );

                    $sheet->setHeight($heights);

                    $sheet->getStyle('A1:K1')->applyFromArray($topBorder);
                    $sheet->getStyle('A'.($credentialingPipelineAPPStart+1).':K'.($credentialingPipelineAPPStart+1))->applyFromArray($bottomBorder);

                    $sheet->getStyle('A24:K'.$currentRosterPhysicianStart)->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$currentRosterAppStart.':K'.$currentRosterAppStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($currentBenchPhysicianStart+1).':K'.$currentBenchPhysicianStartTable)->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$currentBenchAPPStart.':K'.$currentBenchAPPStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($recruitingPipelineStart+1).':K'.$recruitingPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($locumsPipelineStart+1).':K'.$locumsPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($declinedPipelineStart+1).':K'.$declinedPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($resignedPipelineStart+1).':K'.$resignedPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($credentialingPipelineStart+1).':K'.$credentialingPipelinePhysicianStart)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($credentialingPipelinePhysicianStart+2).':K'.$credentialingPipelineAPPStart)->applyFromArray($tableStyle);

                    $sheet->getStyle('B5:C7')->applyFromArray($tableStyle);
                    $sheet->getStyle('E5:F7')->applyFromArray($tableStyle);
                    $sheet->getStyle('H5:I6')->applyFromArray($tableStyle);

                    $sheet->getStyle('C14:E15')->applyFromArray($tableStyle);
                    $sheet->getStyle('D16:E16')->applyFromArray($tableStyle);
                    $sheet->getStyle('C17:E21')->applyFromArray($tableStyle);

                    $sheet->getStyle('G14:I15')->applyFromArray($tableStyle);
                    $sheet->getStyle('H16:I16')->applyFromArray($tableStyle);
                    $sheet->getStyle('G17:I21')->applyFromArray($tableStyle);

                    $sheet->getStyle('C12:J12')->applyFromArray($headerStyle);
                });
            }
        })->download('xlsx'); 
    }

    private function getSummaryData(SummaryFilter $filter, $pages) {
        return AccountSummary::withGlobalScope('role', new AccountSummaryScope)
        ->with('account.rsc')->filter($filter)->paginate($pages);
    }

    private function getUsageData(SummaryFilter $filter, $pages) {
        return AccountSummary::withGlobalScope('role', new AccountSummaryScope)
        ->with(['account.pipeline' => function($query) {
            $query->with('rostersBenchs', 'recruitings', 'locums');
        }])->filter($filter)->paginate($pages);
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

    private function roundnum($num, $nearest){ 
        return round($num / $nearest) * $nearest;
    }
}
