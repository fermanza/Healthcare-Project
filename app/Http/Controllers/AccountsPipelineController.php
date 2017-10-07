<?php

namespace App\Http\Controllers;

use JavaScript;
use App\Account;
use App\AccountSummary;
use App\RSC;
use App\Region;
use App\Division;
use App\Employee;
use App\Practice;
use App\SystemAffiliation;
use Illuminate\Filesystem\Filesystem;
use App\Filters\AccountFilter;
use App\Scopes\AccountScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\IOFactory;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AccountsPipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function index(Account $account)
    {
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
            'practices', 'summary',
        ]);
        $pipeline = $account->pipeline;
        $summary = $account->summary;
        $region = $account->region;
        $practice = $account->practices->count() ? $account->practices->first() : null;
        $practiceTimes = config('pipeline.practice_times');
        $recruitingTypes = config('pipeline.recruiting_types');
        $contractTypes = config('pipeline.contract_types');
        $benchContractTypes = config('pipeline.bench_contract_types');
        $accounts = Account::where('active', true)->orderBy('name')->get();

        if ($practice && $practice->isIPS() && $pipeline->practiceTime == 'hours') {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 180 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 180 : $pipeline->fullTimeHoursApps;
        } else {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 120 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 120 : $pipeline->fullTimeHoursApps;
        }

        $percentRecruitedPhys = 0;
        $percentRecruitedApp = 0;
        $percentRecruitedPhysReport = 0;
        $percentRecruitedAppReport = 0;
        
        if ($summary) {
            if($summary->{'Complete Staff - Phys'} && $summary->{'Complete Staff - Phys'} > 0) {
                $percentRecruitedPhys = ($summary->{'Current Staff - Phys'} / $summary->{'Complete Staff - Phys'}) * 100;
            }
            if($summary->{'Complete Staff - APP'} && $summary->{'Complete Staff - APP'} > 0) {
                $percentRecruitedApp = ($summary->{'Current Staff - APP'} / $summary->{'Complete Staff - APP'}) * 100;
            }
            $percentRecruitedPhysReport = $percentRecruitedPhys > 100 ? 100 : $percentRecruitedPhys;
            $percentRecruitedAppReport = $percentRecruitedApp > 100 ? 100 : $percentRecruitedApp;
        }

        $params = compact(
            'account', 'pipeline', 'region', 'practice', 'practiceTimes',
            'recruitingTypes', 'contractTypes', 'benchContractTypes', 'accounts', 'percentRecruitedPhys',
            'percentRecruitedApp', 'percentRecruitedPhysReport', 'percentRecruitedAppReport'
        );

        JavaScript::put($params);

        return view('admin.accounts.pipeline.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $this->validate($request, [
            'medicalDirector' => '',
            'rmd' => '',
            'svp' => '',
            'dca' => '',
            'practiceTime' => [
                'nullable',
                Rule::in(config('pipeline.practice_times')),
            ],
            'staffPhysicianHaves' => 'numeric',
            'staffAppsHaves' => 'numeric',
            'staffPhysicianNeeds' => 'numeric',
            'staffAppsNeeds' => 'numeric',
            'staffPhysicianOpenings' => 'numeric',
            'staffAppsOpenings' => 'numeric',
            'fullTimeHoursPhys' => 'numeric',
            'fullTimeHoursApps' => 'numeric',
            'staffPhysicianFTEHaves' => 'nullable|numeric',
            'staffPhysicianFTENeeds' => 'nullable|numeric',
            'staffPhysicianFTEOpenings' => 'nullable|numeric',
            'staffAppsFTEHaves' => 'nullable|numeric',
            'staffAppsFTENeeds' => 'nullable|numeric',
            'staffAppsFTEOpenings' => 'nullable|numeric',
        ]);

        $pipeline = $account->pipeline;
        $pipeline->medicalDirector = $request->medicalDirector;
        $pipeline->rmd = $request->rmd;
        $pipeline->svp = $request->svp;
        $pipeline->dca = $request->dca;
        $pipeline->practiceTime = $request->practiceTime;
        $pipeline->staffPhysicianHaves = $request->staffPhysicianHaves;
        $pipeline->staffAppsHaves = $request->staffAppsHaves;
        $pipeline->staffPhysicianNeeds = $request->staffPhysicianNeeds;
        $pipeline->staffAppsNeeds = $request->staffAppsNeeds;
        $pipeline->staffPhysicianOpenings = $request->staffPhysicianOpenings;
        $pipeline->staffAppsOpenings = $request->staffAppsOpenings;
        $pipeline->fullTimeHoursPhys = $request->fullTimeHoursPhys;
        $pipeline->fullTimeHoursApps = $request->fullTimeHoursApps;
        $pipeline->staffPhysicianFTEHaves = $request->staffPhysicianFTEHaves;
        $pipeline->staffPhysicianFTENeeds = $request->staffPhysicianFTENeeds;
        $pipeline->staffPhysicianFTEOpenings = $request->staffPhysicianFTEOpenings;
        $pipeline->staffAppsFTEHaves = $request->staffAppsFTEHaves;
        $pipeline->staffAppsFTENeeds = $request->staffAppsFTENeeds;
        $pipeline->staffAppsFTEOpenings = $request->staffAppsFTEOpenings;
        $pipeline->lastUpdated = Carbon::now();
        $pipeline->lastUpdatedBy = \Auth::id();
        $pipeline->save();

        if($request->expectsJson()) {
            return $pipeline;
        }

        flash(__('Pipeline Updated.'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Export to word file.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function exportWord(Account $account) {
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

        $lists = $this->getWordLists($account);

        $currentRosterPhysiciansList = $lists['currentRosterPhysiciansList'];
        $currentBenchPhysiciansList = $lists['currentBenchPhysiciansList'];
        $locumsMDList = $lists['locumsMDList'];
        $currentRosterAPPList = $lists['currentRosterAPPList'];
        $currentBenchAPPList = $lists['currentBenchAPPList'];
        $locumsAPPList = $lists['locumsAPPList'];
        $futureRostersList = $lists['futureRostersList'];
        $futureLocumsList = $lists['futureLocumsList'];
        $recruitingsList = $lists['recruitingsList'];
        $credentialingsList = $lists['credentialingsList'];

        $address = $account->googleAddress.' | '.$account->city.', '.$account->state.' '.$account->zipCode;

        // Creating the new document...
        $word = new \PhpOffice\PhpWord\PhpWord();
        $documentName = $account->name.', '.$account->siteCode.'.docx';

        $boldFontStyle = array('name' => 'Cambria(Body)', 'size' => 8, 'bold' => true);
        $boldUnderlinedFontStyle = array('name' => 'Cambria(Body)', 'size' => 8, 'bold' => true, 'underline' => 'single');
        $normalFontStyle = array('name' => 'Cambria(Body)', 'size' => 8);
        $paragraphCenterStyle = array('align' => 'center');
        $footerStyle = array('name' => 'ArialMT', 'size' => 8);

        $section = $word->addSection();

        $section->addImage(
            'swoosh.png',
            array(
                'width'            => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(21.59),
                'height'           => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(5),
                'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE
            )
        );

        $section->addImage(
            'envision.png',
            array(
                'width' => 160,
                'height' => 40
            )
        );

        $section->addText(
            '<w:br/>'.$account->name.' '.($account->practices->count() ? $account->practices->first()->name : '').'<w:br/>'.
            $account->city.','.$account->state.'<w:br/>'.
            Carbon::today()->format('F d, Y').'<w:br/>',
            $boldFontStyle,
            $paragraphCenterStyle
        );

        // Define table style arrays
        $styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80);
        // Define cell style arrays
        $styleCell = array('valign'=>'top');
        // Add table style
        $word->addTableStyle('myOwnTableStyle', $styleTable);
        // Add table
        $table = $section->addTable('myOwnTableStyle');
        
        // Add row
        $table->addRow(100);
        // Add cells
        $table->addCell(5690, $styleCell)->addText('FT Physicians', $boldFontStyle);
        $table->addCell(5690, $styleCell)->addText('PT Physicians', $boldFontStyle);
        $table->addCell(5690, $styleCell)->addText('Locums', $boldFontStyle);

        // Add row
        $table->addRow();
        // Add cells
        $table->addCell(5690, $styleCell)->addText($currentRosterPhysiciansList, $normalFontStyle);
        $table->addCell(5690, $styleCell)->addText($currentBenchPhysiciansList, $normalFontStyle);
        $table->addCell(5690, $styleCell)->addText($locumsMDList, $normalFontStyle);

        // Add row
        $table->addRow(100);
        // Add cells
        $table->addCell(2000, $styleCell)->addText('FT APP', $boldFontStyle);
        $table->addCell(2000, $styleCell)->addText('PT APP', $boldFontStyle);
        $table->addCell(2000, $styleCell)->addText('Locums APP', $boldFontStyle);

        // Add row
        $table->addRow();
        // Add cells
        $table->addCell(2000, $styleCell)->addText($currentRosterAPPList, $normalFontStyle);
        $table->addCell(2000, $styleCell)->addText($currentBenchAPPList, $normalFontStyle);
        $table->addCell(2000, $styleCell)->addText($locumsAPPList, $normalFontStyle);

        $section->addText('<w:br/>FTE Physicians required: '.$account->pipeline->staffPhysicianFTENeeds.'<w:br/>'.
            'Current need: '.$account->pipeline->staffPhysicianFTEOpenings.'<w:br/>',
            $normalFontStyle
        );

        $section->addText('FTE Apps required: '.$account->pipeline->staffAppsFTENeeds.'<w:br/>'.
            'Current need: '.$account->pipeline->staffAppsFTEOpenings.'<w:br/>',
            $normalFontStyle
        );

        $footer = $section->addFooter();
        $footer->addText($address, $normalFontStyle, array('align' => 'right'));

        if($futureRostersList != '') {
            $section->addText('Providers Hired who have not started', $boldUnderlinedFontStyle);
            $section->addText($futureRostersList, $normalFontStyle);
        }

        if($futureLocumsList != '') {
            $section->addText('Locums who have not started', $boldUnderlinedFontStyle);
            $section->addText($futureLocumsList, $normalFontStyle);
        }

        if($recruitingsList) {
            $section->addText('Pipeline/candidate update', $boldUnderlinedFontStyle);
            $section->addText($recruitingsList, $normalFontStyle);
        }

        if($credentialingsList) {
            $section->addText('Credentialing', $boldUnderlinedFontStyle);
            $section->addText($credentialingsList, $normalFontStyle);
        }

        // Saving the document...
        $objWriter = IOFactory::createWriter($word, 'Word2007');
        $objWriter->save($documentName);

        return response()->download($documentName)->deleteFileAfterSend(true);
    }

    /**
     * Export to excel file.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Account $account) {
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
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->name);
        });

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

        $sheetName = $account->name.', '.$account->siteCode.' - Ops Review';

        Excel::create($sheetName, function($excel) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $credentialers, $recruitings, $accountPrevMonthIncComp, $accountYTDIncComp){
            $excel->sheet('Summary', function($sheet) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $credentialers, $recruitings, $accountPrevMonthIncComp, $accountYTDIncComp){
                
                $rosterBenchRow = $this->createRosterBenchTable($sheet, $account, $activeRosterPhysicians, $activeRosterAPPs);

                ///////// Team Members //////////
                $this->createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp);
                ///////// Team Members //////////


                /////// Bench Table ////////
                $benchTable = $this->createBenchTable($sheet, $account, $rosterBenchRow, $benchPhysicians, $benchAPPs);
                /////// Bench Table ////////

                /////// Recruiting Table /////////
                $recruitingTable = $this->createRecruitingTable($sheet, $account, $benchTable[1], $recruitings);
                /////// Recruiting Table /////////

                ////// Credentialing Table ////////
                $credentialingTable = $this->createCredentialingTable($sheet, $account, $recruitingTable, $credentialers);
                ////// Credentialing Recruiting Table ////////

                ////// Requirements Table ////////
                $requirementsTable = $this->createRequirementsTable($sheet, $account, $credentialingTable);
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
            });
        })->download('xlsx'); 
    }

    public function getWordLists($account) {
        $today = Carbon::today();

        ////// Elements for lists /////////
        $currentRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'roster' && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->reject(function($rosterBench){
            return $rosterBench->signedNotStarted;
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
        });

        $currentRosterPhysicians = $currentRosterPhysicians->values();

        $currentBenchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->reject(function($rosterBench){
            return $rosterBench->signedNotStarted;
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
        });

        $currentBenchPhysicians = $currentBenchPhysicians->values();

        $locumsMD = $account->pipeline->locums->filter(function($locum) {
            return $locum->type == 'phys';
        })->reject(function($locum){
            return $locum->declined;
        })->sortBy('name');

        $locumsMD = $locumsMD->values();

        $currentRosterAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'roster' && $rosterBench->activity == 'app';
        })->reject(function($rosterBench){
            return $rosterBench->signedNotStarted;
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortBy('name');

        $currentRosterAPP = $currentRosterAPP->values();

        $currentBenchAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'app';
        })->reject(function($rosterBench){
            return $rosterBench->signedNotStarted;
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortBy('name');

        $currentBenchAPP = $currentBenchAPP->values();

        $locumsAPP = $account->pipeline->locums->filter(function($locum) {
            return $locum->type == 'app';
        })->reject(function($locum){
            return $locum->declined;
        })->sortBy('name');

        $locumsAPP = $locumsAPP->values();

        $futureRosters = $account->pipeline->rostersBenchs->filter(function($rosterBench) use ($today) {
            if($rosterBench->firstShift) {
                $firstShift = Carbon::parse($rosterBench->firstShift);

                return $firstShift->gte($today);
            }
        });

        $futureRosters = $futureRosters->values();

        $futureLocums = $account->pipeline->locums->filter(function($locum) use ($today) {
            if($locum->startDate) {
                $startDate = Carbon::parse($locum->startDate);

                return $startDate->gte($today);
            }
        });

        $futureLocums = $futureLocums->values();

        $credentialings = $account->pipeline->rostersBenchs->filter(function($locum) {
            return $locum->signedNotStarted;
        })->reject(function($locum){
            return $locum->declined;
        })->sortBy('name');

        $credentialings = $credentialings->values();
        /////// End of Elements for lists /////////


        /////// Lists ///////////
        $currentRosterPhysiciansList = '';
        foreach ($currentRosterPhysicians as $key => $rosterPhysician) {
            $currentRosterPhysiciansList.= $rosterPhysician->name.' '.
            ($rosterPhysician->isAMD && $rosterPhysician->isSMD ? 'AMD, SMD ' : ($rosterPhysician->isAMD ? 'AMD ' : ($rosterPhysician->isSMD ? 'SMD ' : ''))).'('.$rosterPhysician->hours.')';

            if($key != (count($currentRosterPhysicians)-1)) {
                $currentRosterPhysiciansList.= '<w:br/>';
            }
        }

        $currentBenchPhysiciansList = '';
        foreach ($currentBenchPhysicians as $key => $benchPhysician) {
            $currentBenchPhysiciansList.= $benchPhysician->name.' '.
            ($benchPhysician->isAMD && $benchPhysician->isSMD ? 'AMD, SMD ' : ($benchPhysician->isAMD ? 'AMD ' : ($benchPhysician->isSMD ? 'SMD ' : ''))).'('.$benchPhysician->hours.')';

            if($key != (count($currentBenchPhysicians)-1)) {
                $currentBenchPhysiciansList.= '<w:br/>';
            }
        }

        $locumsMDList = '';
        foreach ($locumsMD as $key => $locumMD) {
            $locumsMDList.= $locumMD->name.' MD '.'('.$locumMD->agency.')';

            if($key != (count($locumsMD)-1)) {
                $locumsMDList.= '<w:br/>';
            }
        }

        $currentRosterAPPList = '';
        foreach ($currentRosterAPP as $key => $rosterAPP) {
            $currentRosterAPPList.= $rosterAPP->name.' '.
            ($rosterAPP->isAMD && $rosterAPP->isSMD ? 'AMD, SMD ' : ($rosterAPP->isAMD ? 'AMD ' : ($rosterAPP->isSMD ? 'SMD ' : ''))).'('.$rosterAPP->hours.')';

            if($key != (count($currentRosterAPP)-1)) {
                $currentRosterAPPList.= '<w:br/>';
            }
        }

        $currentBenchAPPList = '';
        foreach ($currentBenchAPP as $key => $benchAPP) {
            $currentBenchAPPList.= $benchAPP->name.' '.
            ($benchAPP->isAMD && $benchAPP->isSMD ? 'AMD, SMD ' : ($benchAPP->isAMD ? 'AMD ' : ($benchAPP->isSMD ? 'SMD ' : ''))).'('.$benchAPP->hours.')';

            if($key != (count($currentBenchAPP)-1)) {
                $currentBenchAPPList.= '<w:br/>';
            }
        }

        $locumsAPPList = '';
        foreach ($locumsAPP as $key => $locumAPP) {
            $locumsAPPList.= $locumAPP->name.' MD '.'('.$locumAPP->agency.')';

            if($key != (count($locumsAPP)-1)) {
                $locumsAPPList.= '<w:br/>';
            }
        }

        $futureRostersList = '';
        foreach ($futureRosters as $key => $futureRoster) {
            $futureRostersList .= $futureRoster->name.' - '.htmlspecialchars($futureRoster->notes);

            $futureRostersList.= '<w:br/>';
        }

        $futureLocumsList = '';
        foreach ($futureLocums as $key => $futureLocum) {
            $futureLocumsList .= $futureLocum->name.' - '.htmlspecialchars($futureLocum->credentialingNotes);

            $futureLocumsList.= '<w:br/>';
        }

        $recruitingsList = '';
        foreach ($account->pipeline->recruitings as $key => $recruiting) {
            $recruitingsList .= $recruiting->name.' - '.htmlspecialchars($recruiting->notes);

            $recruitingsList.= '<w:br/>';
        }

        $credentialingsList = '';
        foreach ($credentialings as $key => $credentialing) {
            $credentialingsList .= $credentialing->name.' - '.htmlspecialchars($credentialing->notes);

            $credentialingsList.= '<w:br/>';
        }
        /////// End of Lists ///////////

        return array(
            'currentRosterPhysiciansList' => $currentRosterPhysiciansList,
            'currentBenchPhysiciansList' => $currentBenchPhysiciansList,
            'locumsMDList' => $locumsMDList,
            'currentRosterAPPList' => $currentRosterAPPList,
            'currentBenchAPPList' => $currentBenchAPPList,
            'locumsAPPList' => $locumsAPPList,
            'futureRostersList' => $futureRostersList,
            'futureLocumsList' => $futureLocumsList,
            'recruitingsList' => $recruitingsList,
            'credentialingsList' => $credentialingsList
        );
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
            if ($account->pipeline->practiceTime == 'hours') {
                $cell->setValue($account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves);
            } else {
                $cell->setValue($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves);
            }
        });
        $sheet->cell('I15', function($cell) use ($account) {
            if ($account->pipeline->practiceTime == 'hours') {
                $cell->setValue($account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves);
            } else {
                $cell->setValue($account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves);
            }
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
            $physicianOpenings = $account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves;
            $appOpenings = $account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves;
        } else {
            $physicianOpenings = $account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves;
            $appOpenings = $account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves;
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
            $countUntil = count($activeRosterPhysicians) < 13 ? 13 : count($activeRosterPhysicians);

            for ($i = 0; $i < $countUntil; $i++) { 
                if ($account->pipeline->practiceTime == 'hours') {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '').(isset($activeRosterPhysicians[$i]["hours"]) ? " (".$activeRosterPhysicians[$i]["hours"].")" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"].(isset($activeRosterAPPs[$i]["hours"]) ? " (".$activeRosterAPPs[$i]["hours"].")" : '') : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                } else {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                }

                $sheet->row($rosterBenchRow, $row);

                $rosterBenchRow++;
                $rosterBenchCount++;
            }
        } else {
            $countUntil = count($activeRosterAPPs) < 13 ? 13 : count($activeRosterAPPs);

            for ($i = 0; $i < $countUntil; $i++) {
                if ($account->pipeline->practiceTime == 'hours') {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '').(isset($activeRosterPhysicians[$i]["hours"]) ? " (".$activeRosterPhysicians[$i]["hours"].")" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"].(isset($activeRosterAPPs[$i]["hours"]) ? " (".$activeRosterAPPs[$i]["hours"].")" : '') : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
                    ];
                } else {
                    $row = [
                        $rosterBenchCount,
                        isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]["name"].((isset($activeRosterPhysicians[$i]["isSMD"]) && $activeRosterPhysicians[$i]["isSMD"] == 1) ? " (SMD)" : '') : '',
                        isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]["firstShift"] ? Carbon::parse($activeRosterPhysicians[$i]["firstShift"])->format('m-d-Y') : '') : '',
                        $rosterBenchCount,
                        isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]["name"] : '',
                        isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]["firstShift"] ? Carbon::parse($activeRosterAPPs[$i]["firstShift"])->format('m-d-Y') : '') : ''
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
                    'PHYS/PRN',
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
                    isset($benchPhysicians[$i]) ? 'PHYS/PRN' : '',
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

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Account  $account
     * @param  \App\Filters\AccountFilter  $filter
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportIndex(Account $account, AccountFilter $filter, Request $request)
    {
        $queryString = $request->query();

        $employees = Employee::with('person')->where('active', true)->get()->sortBy->fullName();
        $practices = Practice::where('active', true)->orderBy('name')->get();
        $divisions = Division::where('active', true)->orderBy('name')->get();
        $RSCs = RSC::where('active', true)->orderBy('name')->get();
        $regions = Region::where('active', true)->orderBy('name')->get();
        $affiliations = SystemAffiliation::all();

        if(count($queryString) == 0) {
            $accounts = [];
        } else {
            $accounts = Account::select('id','name','siteCode','city','state','startDate','endDate','parentSiteCode','RSCId','operatingUnitId')
                ->withGlobalScope('role', new AccountScope)
                ->with([
                    'rsc',
                    'region',
                    'recruiter.employee.person',
                    'manager.employee.person',
                ])
                ->where('active', true)
                ->termed(false)
                ->filter($filter)->get();
        }

        return view('admin.accounts.export.index', compact('accounts', 'employees', 'practices', 'divisions', 'regions', 'RSCs', 'affiliations'));
    }

    /**
     * Export to pdf multiple accounts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkExport(Request $request) {
        if ($request->ids) {

            set_time_limit(600);

            $ids = $request->ids;

            if(count($ids) > 100) {
                $ids = array_slice($ids, 0, 100);
            }

            $accounts = Account::whereIn('id', $ids)->get();

            if ($accounts) {
                $this->exportPDF($accounts, 'pdf');

                $zipper = new \Chumper\Zipper\Zipper;

                $files = glob(public_path('exports/*'));
                $zipper->make('reports.zip')->add($files)->close();

                $file = new Filesystem;

                $file->deleteDirectory(public_path('exports'));

                return response()->download(public_path('reports.zip'))->deleteFileAfterSend(true);
            }
        } else {
            flash(__('Use the filters to get data first.'));
            return back();
        }
    }

    /**
     * Export to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($accounts) {
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
                'practices', 'summary',
            ]);

            $summary = $account->summary;

            $percentRecruitedPhys = 0;
            $percentRecruitedApp = 0;
            $percentRecruitedPhysReport = 0;
            $percentRecruitedAppReport = 0;
        
            if ($summary) {
                if($summary->{'Complete Staff - Phys'} && $summary->{'Complete Staff - Phys'} > 0) {
                    $percentRecruitedPhys = ($summary->{'Current Staff - Phys'} / $summary->{'Complete Staff - Phys'}) * 100;
                }
                if($summary->{'Complete Staff - APP'} && $summary->{'Complete Staff - APP'} > 0) {
                    $percentRecruitedApp = ($summary->{'Current Staff - APP'} / $summary->{'Complete Staff - APP'}) * 100;
                }
                $percentRecruitedPhysReport = $percentRecruitedPhys > 100 ? 100 : $percentRecruitedPhys;
                $percentRecruitedAppReport = $percentRecruitedApp > 100 ? 100 : $percentRecruitedApp;
            }

            $activeRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                return $rosterBench->activity == 'physician' && $rosterBench->place == 'roster';
            })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
            ->reject(function($rosterBench){
                return $rosterBench->signedNotStarted;
            })->sortByDesc(function($rosterBench){
                return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->name);
            });

            $benchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                return $rosterBench->activity == 'physician' && $rosterBench->place == 'bench';
            })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
            ->reject(function($rosterBench){
                return $rosterBench->signedNotStarted;
            })->sortByDesc(function($rosterBench){
                return sprintf('%-12s%s', $rosterBench->isChief, $rosterBench->name);
            });

            $activeRosterAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                return $rosterBench->activity == 'app' && $rosterBench->place == 'roster';
            })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
            ->reject(function($rosterBench){
                return $rosterBench->signedNotStarted;
            })->sortBy('name');

            $benchAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
                return $rosterBench->activity == 'app' && $rosterBench->place == 'bench';
            })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })
            ->reject(function($rosterBench){
                return $rosterBench->signedNotStarted;
            })->sortBy('name');

            $recruitings = $account->pipeline->recruitings->reject(function($recruiting) { 
                return !is_null($recruiting->declined); 
            })->sortBy('name');

            $locums = $account->pipeline->locums->reject(function($locum) { 
                return !is_null($locum->declined); 
            })->sortBy('name');

            $declines = $account->pipeline->recruitings->concat($account->pipeline->locums)
            ->filter(function($locum) { 
                return !is_null($locum->declined); 
            })->sortBy('name');

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

            $sheetName = $account->name.', '.$account->siteCode.' - Ops Review';

            $fileInfo = Excel::create($sheetName, function($excel) use ($account, $percentRecruitedPhys, $percentRecruitedApp, $percentRecruitedPhysReport, $percentRecruitedAppReport, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $recruitings, $locums, $declines, $resigneds, $credentialersPhys, $credentialersAPP){
                $excel->sheet('Summary', function($sheet) use ($account, $percentRecruitedPhys, $percentRecruitedApp, $percentRecruitedPhysReport, $percentRecruitedAppReport, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $recruitings, $locums, $declines, $resigneds, $credentialersPhys, $credentialersAPP){

                    $accountInfo = $account->name.', '.$account->siteCode.' '.$account->address.' '.($account->recruiter ? $account->recruiter->fullName() : '').', '.($account->manager ? $account->manager->fullName() : '');

                    $sheet->mergeCells('A26:K26');
                    $sheet->mergeCells('A27:K27');
                    $sheet->mergeCells('B4:J6');
                    $sheet->mergeCells('C13:I13');
                    $sheet->mergeCells('C17:E17');
                    $sheet->mergeCells('G17:I17');
                    $sheet->mergeCells('C18:D18');
                    $sheet->mergeCells('G18:H18');

                    $sheet->cell('B2', function($cell) {
                        $cell->setValue('Summary');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('B4', function($cell) use ($accountInfo) {
                        $cell->setValue($accountInfo);
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cells('B8:B10', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('E8:E10', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('H8:H9', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('C13:I17', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('C18:C24', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('D19:E19', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('G18:G24', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cells('H19:I19', function($cells) {
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cell('B8', function($cell) {
                        $cell->setValue('Medical Director');
                    });

                    $sheet->cell('C8', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->medicalDirector);
                    });

                    $sheet->cell('B9', function($cell) {
                        $cell->setValue('SVP');
                    });

                    $sheet->cell('C9', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->svp);
                    });

                    $sheet->cell('B10', function($cell) {
                        $cell->setValue('Service Line');
                    });

                    $sheet->cell('C10', function($cell) use ($account) {
                        $cell->setValue($account->practices->count() ? $account->practices->first()->name : '');
                    });

                    $sheet->cell('E8', function($cell) {
                        $cell->setValue('RMD');
                    });

                    $sheet->cell('F8', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->rmd);
                    });

                    $sheet->cell('E9', function($cell) {
                        $cell->setValue('DOO');
                    });

                    $sheet->cell('F9', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->dca);
                    });

                    $sheet->cell('E10', function($cell) {
                        $cell->setValue('Service Line Time');
                    });

                    $sheet->cell('F10', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->practiceTime);
                    });

                    $sheet->cell('H8', function($cell) {
                        $cell->setValue('RSC');
                    });

                    $sheet->cell('I8', function($cell) use ($account) {
                        $cell->setValue($account->rsc ? $account->rsc->name : '');
                    });

                    $sheet->cell('H9', function($cell) {
                        $cell->setValue('Operating Unit');
                    });

                    $sheet->cell('I9', function($cell) use ($account) {
                        $cell->setValue($account->region ? $account->region->name : '');
                    });

                    $sheet->cell('C13', function($cell) {
                        $cell->setValue('Complete Staffing and Current Openings');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('C15', function($cell) {
                        $cell->setValue('SMD');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('E15', function($cell) {
                        $cell->setValue('AMD');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('G15', function($cell) {
                        $cell->setValue('PHYS');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('I15', function($cell) {
                        $cell->setValue('APP');
                        $cell->setFontFamily('Calibri (Body)');
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cells('C17:G17', function($cells) {
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setFontSize(11);
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                    $sheet->cell('C17', function($cell) {
                        $cell->setValue('Physician');
                    });

                    $sheet->cell('G17', function($cell) {
                        $cell->setValue('APPs');
                    });

                    $sheet->cell('C18', function($cell) {
                        $cell->setValue('Full Time Hours');
                    });
                    $sheet->cell('G18', function($cell) {
                        $cell->setValue('Full Time Hours');
                    });

                    $sheet->cell('E18', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->fullTimeHoursPhys);
                    });
                    $sheet->cell('I18', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->fullTimeHoursApps);
                    });

                    $sheet->cell('D19', function($cell) {
                        $cell->setValue('Hours');
                    });
                    $sheet->cell('E19', function($cell) {
                        $cell->setValue('FTEs');
                    });

                    $sheet->cell('H19', function($cell) {
                        $cell->setValue('Hours');
                    });
                    $sheet->cell('I19', function($cell) {
                        $cell->setValue('FTEs');
                    });

                    $sheet->cell('C20', function($cell) {
                        $cell->setValue('Haves');
                    });
                    $sheet->cell('C21', function($cell) {
                        $cell->setValue('Needs');
                    });
                    $sheet->cell('C22', function($cell) {
                        $cell->setValue('Openings');
                    });
                    $sheet->cell('C23', function($cell) {
                        $cell->setValue('Percent Recruited Actual');
                    });
                    $sheet->cell('C24', function($cell) {
                        $cell->setValue('Percent Recruited Reported');
                    });

                    $sheet->cell('D20', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianHaves);
                    });
                    $sheet->cell('D21', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianNeeds);
                    });
                    $sheet->cell('D22', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianOpenings);
                    });
                    $sheet->cell('D23', function($cell) use ($percentRecruitedPhys) {
                        $cell->setValue(number_format($percentRecruitedPhys, 1).'%');
                    });
                    $sheet->cell('D24', function($cell) use ($percentRecruitedPhysReport) {
                        $cell->setValue(number_format($percentRecruitedPhysReport, 1).'%');
                    });

                    $sheet->cell('E20', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianFTEHaves);
                    });
                    $sheet->cell('E21', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianFTENeeds);
                    });
                    $sheet->cell('E22', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffPhysicianFTEOpenings);
                    });

                    $sheet->cell('G20', function($cell) {
                        $cell->setValue('Haves');
                    });
                    $sheet->cell('G21', function($cell) {
                        $cell->setValue('Needs');
                    });
                    $sheet->cell('G22', function($cell) {
                        $cell->setValue('Openings');
                    });
                    $sheet->cell('G23', function($cell) {
                        $cell->setValue('Percent Recruited Actual');
                    });
                    $sheet->cell('G24', function($cell) {
                        $cell->setValue('Percent Recruited Reported');
                    });

                    $sheet->cell('H20', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsHaves);
                    });
                    $sheet->cell('H21', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsNeeds);
                    });
                    $sheet->cell('H22', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsOpenings);
                    });
                    $sheet->cell('H23', function($cell) use ($percentRecruitedApp) {
                        $cell->setValue(number_format($percentRecruitedApp, 1).'%');
                    });
                    $sheet->cell('H24', function($cell) use ($percentRecruitedAppReport) {
                        $cell->setValue(number_format($percentRecruitedAppReport, 1).'%');
                    });

                    $sheet->cell('I20', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsFTEHaves);
                    });
                    $sheet->cell('I21', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsFTENeeds);
                    });
                    $sheet->cell('I22', function($cell) use ($account) {
                        $cell->setValue($account->pipeline->staffAppsFTEOpenings);
                    });

                    $sheet->cell('A26', function($cell) use ($accountInfo) {
                        $cell->setValue('Current Roster');
                        $cell->setBackground('#1eb1ed');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cell('A27', function($cell) use ($accountInfo) {
                        $cell->setValue('Physycian');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->cells('A28:K28', function($cells) use ($accountInfo) {
                        $cells->setBackground('#d0cece');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
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
                        'Regigned',
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


                    $sheet->row(28, $rosterPhysicianFields);

                    $currentRosterPhysicianStart = 29;

                    foreach ($activeRosterPhysicians as $rosterPhysician) {
                        $row = [
                            $rosterPhysician->isSMD,
                            $rosterPhysician->isAMD,
                            $rosterPhysician->name,
                            $rosterPhysician->hours,
                            strtoupper($rosterPhysician->contract),
                            $rosterPhysician->interview ? $rosterPhysician->interview->format('m/d/Y') : '',
                            $rosterPhysician->contractOut ? $rosterPhysician->contractOut->format('m/d/Y') : '',
                            $rosterPhysician->contractIn ? $rosterPhysician->contractIn->format('m/d/Y') : '',
                            $rosterPhysician->firstShift ? $rosterPhysician->firstShift->format('m/d/Y') : '',
                            $rosterPhysician->notes,
                            $rosterPhysician->signedNotStarted
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                            $rosterAPP->isChief,
                            '',
                            $rosterAPP->name,
                            $rosterAPP->hours,
                            strtoupper($rosterAPP->contract),
                            $rosterAPP->interview ? $rosterAPP->interview->format('m/d/Y') : '',
                            $rosterAPP->contractOut ? $rosterAPP->contractOut->format('m/d/Y') : '',
                            $rosterAPP->contractIn ? $rosterAPP->contractIn->format('m/d/Y') : '',
                            $rosterAPP->firstShift ? $rosterAPP->firstShift->format('m/d/Y') : '',
                            $rosterAPP->notes,
                            $rosterAPP->signedNotStarted
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->mergeCells('A'.($currentBenchPhysicianStart+1).':K'.($currentBenchPhysicianStart+1));

                    $sheet->cell('A'.($currentBenchPhysicianStart+1), function($cell) use ($accountInfo) {
                        $cell->setValue('Physician');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                            $benchPhysician->signedNotStarted
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                            $benchAPP->signedNotStarted
                        ];

                        $sheet->row($currentBenchAPPStartTable, $row);

                        $currentBenchAPPStartTable++;
                    }

                    $recruitingPipelineStart = $currentBenchAPPStartTable+2;

                    $sheet->mergeCells('A'.$recruitingPipelineStart.':K'.$recruitingPipelineStart);

                    $sheet->cell('A'.$recruitingPipelineStart, function($cell) use ($accountInfo) {
                        $cell->setValue('Rrecruiting Pipeline');
                        $cell->setBackground('#00a65a');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
                    });

                    $sheet->mergeCells('A'.($credentialingPipelineStart+1).':K'.($credentialingPipelineStart+1));

                    $sheet->cell('A'.($credentialingPipelineStart+1), function($cell) use ($accountInfo) {
                        $cell->setValue('Physician');
                        $cell->setBackground('#d0cece');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                            $credentialer->notes,
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
                        $cell->setAlignment('center');
                        $cell->setValignment('center');
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
                            $credentialer->notes,
                            ''
                        ];

                        $sheet->row($credentialingPipelineAPPStart, $row);

                        $credentialingPipelineAPPStart++;
                    }

                    $sheet->cells('A1:K'.$credentialingPipelineAPPStart, function($cells) {
                        $cells->setFontFamily('Calibri (Body)');
                        $cells->setFontSize(11);
                    });

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

                    // $headersStyle = array(
                    //     'borders' => array(
                    //         'outline' => array(
                    //             'style' => 'medium',
                    //             'color' => array('rgb' => '000000'),
                    //         ),
                    //         'inside' => array(
                    //             'style' => 'medium',
                    //             'color' => array('rgb' => '000000'),
                    //         ),
                    //     ),
                    // );

                    // $sheet->setAutoSize(true);

                    $sheet->setWidth(array(
                        'A'     => 9,
                        'B'     => 15,
                        'C'     => 20,
                        'D'     => 15,
                        'E'     => 16,
                        'F'     => 18,
                        'G'     => 20,
                        'H'     => 14,
                        'I'     => 10,
                        'J'     => 28,
                        'K'     => 17
                    ));

                    $heights = array(
                        4   => 35
                    );

                    // for($x = $recruitingTable[0]; $x <= ($credentialingTable[1]); $x++) {
                    //         $heights[$x] = 25;
                    // }

                    $sheet->setHeight($heights);

                    $sheet->getStyle('A1:K1')->applyFromArray($topBorder);
                    $sheet->getStyle('A'.($credentialingPipelineAPPStart+1).':K'.($credentialingPipelineAPPStart+1))->applyFromArray($bottomBorder);

                    $sheet->getStyle('A27:K'.$currentRosterPhysicianStart)->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$currentRosterAppStart.':K'.$currentRosterAppStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($currentBenchPhysicianStart+1).':K'.$currentBenchPhysicianStartTable)->applyFromArray($tableStyle);
                    $sheet->getStyle('A'.$currentBenchAPPStart.':K'.$currentBenchAPPStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($recruitingPipelineStart+1).':K'.$recruitingPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($locumsPipelineStart+1).':K'.$locumsPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($declinedPipelineStart+1).':K'.$declinedPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($resignedPipelineStart+1).':K'.$resignedPipelineStartTable)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($credentialingPipelineStart+1).':K'.$credentialingPipelinePhysicianStart)->applyFromArray($tableStyle);

                    $sheet->getStyle('A'.($credentialingPipelinePhysicianStart+2).':K'.$credentialingPipelineAPPStart)->applyFromArray($tableStyle);

                    $sheet->getStyle('B8:C10')->applyFromArray($tableStyle);
                    $sheet->getStyle('E8:F10')->applyFromArray($tableStyle);
                    $sheet->getStyle('H8:I9')->applyFromArray($tableStyle);

                    $sheet->getStyle('C17:E18')->applyFromArray($tableStyle);
                    $sheet->getStyle('D19:E19')->applyFromArray($tableStyle);
                    $sheet->getStyle('C20:E24')->applyFromArray($tableStyle);

                    $sheet->getStyle('G17:I18')->applyFromArray($tableStyle);
                    $sheet->getStyle('H19:I19')->applyFromArray($tableStyle);
                    $sheet->getStyle('G20:I24')->applyFromArray($tableStyle);

                    $sheet->setBorder("A2:K7", 'none');
                    $sheet->setBorder("A4:A6", 'none');
                    $sheet->setBorder("A11:K16", 'none');
                    $sheet->setBorder("A17:B24", 'none');
                    $sheet->setBorder("F17:F24", 'none');
                    $sheet->setBorder("J17:K24", 'none');
                    $sheet->setBorder("K4:K6", 'none');

                    $sheet->setBorder("A".$currentRosterPhysicianStart.":K".$currentRosterPhysicianStart, 'none');
                    $sheet->setBorder("A".($currentRosterPhysicianStart+1).":K".($currentRosterPhysicianStart+1), 'none');

                    $sheet->setBorder("A".$currentRosterAppStartTable.":K".$currentRosterAppStartTable, 'none');
                    $sheet->setBorder("A".($currentRosterAppStartTable+1).":K".($currentRosterAppStartTable+1), 'none');

                    $sheet->setBorder("A".$currentBenchPhysicianStartTable.":K".$currentBenchPhysicianStartTable, 'none');
                    $sheet->setBorder("A".($currentBenchPhysicianStartTable+1).":K".($currentBenchPhysicianStartTable+1), 'none');

                    $sheet->setBorder("A".$currentBenchAPPStartTable.":K".$currentBenchAPPStartTable, 'none');
                    $sheet->setBorder("A".($currentBenchAPPStartTable+1).":K".($currentBenchAPPStartTable+1), 'none');

                    $sheet->setBorder("A".$recruitingPipelineStartTable.":K".$recruitingPipelineStartTable, 'none');
                    $sheet->setBorder("A".($recruitingPipelineStartTable+1).":K".($recruitingPipelineStartTable+1), 'none');

                    $sheet->setBorder("A".$locumsPipelineStartTable.":K".$locumsPipelineStartTable, 'none');
                    $sheet->setBorder("A".($locumsPipelineStartTable+1).":K".($locumsPipelineStartTable+1), 'none');

                    $sheet->setBorder("A".$declinedPipelineStartTable.":K".$declinedPipelineStartTable, 'none');
                    $sheet->setBorder("A".($declinedPipelineStartTable+1).":K".($declinedPipelineStartTable+1), 'none');

                    $sheet->setBorder("A".$resignedPipelineStartTable.":K".$resignedPipelineStartTable, 'none');
                    $sheet->setBorder("A".($resignedPipelineStartTable+1).":K".($resignedPipelineStartTable+1), 'none');

                    $sheet->setBorder("A".$credentialingPipelinePhysicianStart.":K".$credentialingPipelinePhysicianStart, 'none');
                    $sheet->setBorder("A".($credentialingPipelinePhysicianStart+1).":K".($credentialingPipelinePhysicianStart+1), 'none');

                    $sheet->setBorder("A".$credentialingPipelineAPPStart.":K".$credentialingPipelineAPPStart, 'none');
                });
            })->store('pdf', public_path('exports'), true);
        }
    }
}
