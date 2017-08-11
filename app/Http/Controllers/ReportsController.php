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

        $params = compact('accounts', 'employees', 'practices', 'divisions', 'RSCs', 'regions', 'action');

        return view('admin.reports.index', $params);
    }

    public function exportToExcel(SummaryFilter $filter) {
        $dataToExport = $this->getSummaryData($filter);
        $headers = ["#", "Contract Name", "Service Line", "System Affiliation", "JV", "Operating Unit",
            "RSC", "Recruiter", "Secondary Recruiter", "Managers", "DOO/SVP", "RMD", "City", "Location",
            "Start Date", "# of Months Account Open"
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
                        $account->name,
                        $account->practices->count() ? $account->practices->first()->name : '',
                        '',
                        ($account->division && $account->division->isJV) ? 'Yes' : 'No',
                        $account->region ? $account->region->name : '',
                        $account->rsc ? $account->rsc->name : '',
                        $account->recruiter ? $account->recruiter->fullName() : '',
                        $account->recruiters->count() ? $account->recruiters->map->fullName()->implode('; ') : '',
                        $account->manager ? $account->manager->fullName() : '',
                        $account->pipeline->svp,
                        $account->pipeline->rmd,
                        $account->city,
                        $account->state,
                        $account->startDate ? $account->startDate->format('d/m/y') : '',
                        $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated(),
                    ];

                    $sheet->row($rowNumber, $row);

                    if ($account->getMonthsSinceCreated() < 7) {
                        $sheet->cell('P'.$rowNumber, function($cell) use ($account) {
                            $cell->setBackground('#1aaf54');
                        });
                    }
                };

                $sheet->setFreeze('C3');
                $sheet->setAutoFilter('A2:P2');
                $sheet->mergeCells('A1:P1');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('WEST RSC RECRUITING SUMMARY');
                    $cell->setFontColor('#000000');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(8);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $x = "A1:P".$rowNumber;

                $sheet->cells("A1:P".$rowNumber, function($cells) {
                    $cells->setBorder('medium', 'medium', 'medium', 'medium');
                });

                $sheet->cells('A2:P2', function($cells) {
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

                $sheet->getStyle('A1:P'.$rowNumber)->applyFromArray($tableStyle);
                $sheet->getStyle('A2:P2')->applyFromArray($headersStyle);

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
