<?php

namespace App\Http\Controllers;

use App\Account;
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

    public function exportToExcel() {
        Excel::create('Summary Report', function($excel) use ($dataToExport){
            $excel->sheet('Summary', function($sheet) use ($dataToExport){
                $headers = ["#", "Contract Name", "Service Line", "System Affiliation", "JV", "Operating Unit",
                    "RSC", "Recruiter", "Secondary Recruiter", "Managers", "DOO/SVP", "RMD", "City", "Location",
                    "Start Date", "# of Months Account Open"
                ];

                $rowNumber = 1;

                $sheet->row($rowNumber, $headers);
                $sheet->row($rowNumber, function($row) {
                    $row->setBackground('#d9d9d9');
                });
                $sheet->setHeight($rowNumber, 40);

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
                        $account->startDate ? $account->startDate->format('Y-m-d') : '',
                        $account->getMonthsSinceCreated(),
                    ];

                    $sheet->row($rowNumber, $row);

                    if ($account->getMonthsSinceCreated() < 7) {
                        $sheet->cell('P'.$rowNumber, function($cell) use ($account) {
                            $cell->setBackground('#1aaf54');
                        });
                    }
                };

                $sheet->setFreeze('C1');
                $sheet->setAutoFilter();
                
                $sheet->cells('A1:P1', function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri');
                    $cells->setFontSize(11);
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A2:P'.$rowNumber, function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri');
                    $cells->setFontSize(11);
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBorder('solid', 'none', 'none', 'solid');
                });

                $styleArray = array(
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

                $sheet->getStyle('A1:P'.$rowNumber)->applyFromArray($styleArray);

            });
        })->download('xlsx'); 
    }

    private function getSummaryData(SummaryFilter $filter) {
        return Account::leftJoin('tAccountToEmployee as tRecruiter', function($join) {
                $join->on('tRecruiter.accountId', '=', 'tAccount.id')
                ->on('tRecruiter.positionTypeId', '=', DB::raw(config('instances.position_types.recruiter')));
            }) 
            ->leftJoin('tAccountToEmployee as tManager', function($join) {
                $join->on('tManager.accountId', '=', 'tAccount.id')
                ->on('tManager.positionTypeId', '=', DB::raw(config('instances.position_types.manager')));
            }) 
            ->leftJoin('tAccountToPractice', 'tAccount.id', '=', 'tAccountToPractice.accountId')
            ->leftJoin('tDivision', 'tAccount.divisionId', '=', 'tDivision.id')
            ->leftJoin('tGroup', 'tDivision.groupId', '=', 'tGroup.id')
            ->select('tAccount.*')
            ->with('recruiter.employee.person', 'recruiters.employee.person', 'manager.employee.person', 'division.group', 'region', 'rsc', 'pipeline', 'practices')
            ->where('tAccount.active', true)->filter($filter)->groupBy('tAccount.id')->get();
    }
}
