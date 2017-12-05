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
use App\Group;
use App\Provider;
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

        $onlyAccount = $account;

        $account->load([
            'pipeline' => function ($query) {
                $query->with([
                    'rostersBenchs.provider', 'recruitings.provider', 'locums.provider',
                ]);
            },
            'recruiter.employee' => function ($query) {
                $query->with('person', 'manager.person');
            },
            'division.group.region',
            'practices', 'summary',
            'providers'
        ]);

        $pipeline = $onlyAccount->pipeline;
        $summary = $account->summary;
        $region = $account->region;
        $practice = $account->practices->count() ? $account->practices->first() : null;
        $practiceTimes = config('pipeline.practice_times');
        $recruitingTypes = config('pipeline.recruiting_types');
        $contractTypes = config('pipeline.contract_types');
        $benchContractTypes = config('pipeline.bench_contract_types');
        $accounts = Account::where('active', true)->orderBy('name')->get();
        $providers = $account->providers;

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
            'recruitingTypes', 'contractTypes', 'benchContractTypes', 'accounts', 'providers',
            'percentRecruitedPhys', 'percentRecruitedApp', 'percentRecruitedPhysReport', 'percentRecruitedAppReport'
        );

        JavaScript::put($params);

        if($account->rsc && $account->rsc->name == 'West') {
            return view('admin.accounts.pipeline.west', $params);
        }

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
        $recruitingsPhysList = $lists['recruitingsPhysList'];
        $recruitingsAppList = $lists['recruitingsAppList'];
        $credentialingsPhysList = $lists['credentialingsPhysList'];
        $credentialingsAppList = $lists['credentialingsAppList'];

        $address = $account->googleAddress.' | '.$account->city.', '.$account->state.' '.$account->zipCode;

        // Creating the new document...
        $word = new \PhpOffice\PhpWord\PhpWord();
        $documentName = $account->name.', '.$account->siteCode.' - '.Carbon::now()->format('m_d_Y').'.docx';

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

        if ($account->pipeline->practiceTime == 'hours') {
            $physNeeds = $account->pipeline->staffPhysicianFTENeeds;
            $physOpenings = $account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves;
        } else {
            $physNeeds = $account->pipeline->staffPhysicianNeeds;
            $physOpenings = $account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves;
        }

        $section->addText('<w:br/>FTE Physicians required: '.round($physNeeds,1).'<w:br/>'.
            'Current need: '.$this->roundnum($physOpenings, 0.5).'<w:br/>',
            $normalFontStyle
        );

        if ($account->pipeline->practiceTime == 'hours') {
            $appNeeds = $account->pipeline->staffAppsFTENeeds;
            $appOpenings = $account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves;
        } else {
            $appNeeds = $account->pipeline->staffAppsNeeds;
            $appOpenings = $account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves;
        }

        $section->addText('FTE Apps required: '.round($appNeeds,1).'<w:br/>'.
            'Current need: '.$this->roundnum($appOpenings, 0.5).'<w:br/>',
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

        if($recruitingsPhysList || $recruitingsAppList) {
            $section->addText('Pipeline/Candidate Update', $boldUnderlinedFontStyle);
            $section->addText('PHYS', $boldUnderlinedFontStyle);
            $section->addText($recruitingsPhysList, $normalFontStyle);
            $section->addText('APP', $boldUnderlinedFontStyle);
            $section->addText($recruitingsAppList, $normalFontStyle);
        }

        if($credentialingsPhysList || $credentialingsAppList) {
            $section->addText('Credentialing', $boldUnderlinedFontStyle);
            $section->addText('PHYS', $boldUnderlinedFontStyle);
            $section->addText($credentialingsPhysList, $normalFontStyle);
            $section->addText('APP', $boldUnderlinedFontStyle);
            $section->addText($credentialingsAppList, $normalFontStyle);
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

        $accountPrevMonthIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

        $accountYTDIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

        $sheetName = $account->name.', '.$account->siteCode.' - Ops Review - '.Carbon::now()->format('m/d/Y');

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
                $sheet->getStyle('A'.$requirementsTable[0].':I'.($requirementsTable[0]+4))->applyFromArray($tableStyle);

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
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
        });

        $currentRosterPhysicians = $currentRosterPhysicians->values();

        $currentBenchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
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
            return $rosterBench->resigned;
        })->sortBy('name');

        $currentRosterAPP = $currentRosterAPP->values();

        $currentBenchAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'app';
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

        $credentialingsPhys = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->signedNotStarted && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->reject(function($recruiting){
            return $recruiting->completed;
        })->sortBy('name');

        $credentialingsPhys = $credentialingsPhys->values();

        $credentialingsApp = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->signedNotStarted && $rosterBench->activity == 'app';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->reject(function($recruiting){
            return $recruiting->completed;
        })->sortBy('name');

        $credentialingsApp = $credentialingsApp->values();

        $recruitingsPhys = $account->pipeline->recruitings->filter(function($recruiting) {
            return $recruiting->type == 'phys';
        })->reject(function($recruiting) {
            return $recruiting->declined;
        })->sortBy('name');

        $recruitingsApp = $account->pipeline->recruitings->filter(function($recruiting) {
            return $recruiting->type == 'app';
        })->reject(function($recruiting) {
            return $recruiting->declined;
        })->sortBy('name');
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

        $recruitingsPhysList = '';
        foreach ($recruitingsPhys as $key => $recruitingPhys) {
            $recruitingsPhysList .= $recruitingPhys->name.' - '.htmlspecialchars($recruitingPhys->notes);

            $recruitingsPhysList.= '<w:br/>';
        }

        $recruitingsAppList = '';
        foreach ($recruitingsApp as $key => $recruitingApp) {
            $recruitingsAppList .= $recruitingApp->name.' - '.htmlspecialchars($recruitingApp->notes);

            $recruitingsAppList.= '<w:br/>';
        }

        $credentialingsPhysList = '';
        foreach ($credentialingsPhys as $key => $credentialingPhys) {
            $credentialingsPhysList .= $credentialingPhys->name.' - '.htmlspecialchars($credentialingPhys->credentialingNotes);

            $credentialingsPhysList.= '<w:br/>';
        }

        $credentialingsAppList = '';
        foreach ($credentialingsApp as $key => $credentialingApp) {
            $credentialingsAppList .= $credentialingApp->name.' - '.htmlspecialchars($credentialingApp->credentialingNotes);

            $credentialingsAppList.= '<w:br/>';
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
            'recruitingsPhysList' => $recruitingsPhysList,
            'recruitingsAppList' => $recruitingsAppList,
            'credentialingsPhysList' => $credentialingsPhysList,
            'credentialingsAppList' => $credentialingsAppList,
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

    private function roundnum($num, $nearest){ 
        return round($num / $nearest) * $nearest; 
    } 

    private function createMembersTable($sheet, $account, $accountPrevMonthIncComp, $accountYTDIncComp) {
        $SMD = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->isSMD;
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

            $cell->setValue($this->roundnum($result, 0.5));
        });
        $sheet->cell('I16', function($cell) use ($account) {
            if ($account->pipeline->practiceTime == 'hours') {
                $result = $account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves;
            } else {
                $result = $account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves;
            }

            $cell->setValue($this->roundnum($result, 0.5));
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
            $physicianOpenings = $this->roundnum($account->pipeline->staffPhysicianFTENeeds - $account->pipeline->staffPhysicianFTEHaves, 0.5);
            $appOpenings = $this->roundnum($account->pipeline->staffAppsFTENeeds - $account->pipeline->staffAppsFTEHaves, 0.5);
        } else {
            $physicianOpenings = $this->roundnum($account->pipeline->staffPhysicianNeeds - $account->pipeline->staffPhysicianFTEHaves, 0.5);
            $appOpenings = $this->roundnum($account->pipeline->staffAppsNeeds - $account->pipeline->staffAppsFTEHaves, 0.5);
        }

        $physicianNegative = $physicianOpenings < 0 ? true : false;
        $appNegative = $appOpenings < 0 ? true : false;

        $physicianDecimal = $this->roundnum($physicianOpenings - floor($physicianOpenings), 0.5);
        $appDecimal = $this->roundnum($appOpenings - floor($appOpenings), 0.5);

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
            $countUntil = count($activeRosterAPPs) < 14 ? 14 : count($activeRosterAPPs);

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
        $groups = Group::where('active', true)->get()->sortBy('name');

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

        return view('admin.accounts.export.index', compact('accounts', 'employees', 'practices', 'divisions', 'regions', 'RSCs', 'affiliations', 'groups'));
    }

    /**
     * Export to pdf multiple accounts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkExport(Request $request) {
        if ($request->ids) {

            $ids = $request->ids;

            if(count($ids) > 100) {
                $email = \Auth::user()->email;

                \Artisan::queue('export-accounts-pdf', [
                    'email' => $email,
                    'ids' => $ids
                ]);

                flash(__('An email will be sent to your email after the process is done.'));

                return back();
            } else {
                $accounts = Account::whereIn('id', $ids)->with([
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

                if ($accounts) {
                    $fileInfo = $this->exportPDF($accounts);

                    return response()->download(public_path('exports/'.$fileInfo['file']))->deleteFileAfterSend(true);
                }
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
        $timestamp = Carbon::now()->format('m_d_Y');

        $fileInfo = Excel::create('Accounts_Batch_Print - '.$timestamp, function($excel) use ($accounts){

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
                $accountPrevMonthIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();
                $accountYTDIncComp = AccountSummary::where('accountId', $account->id)->orderBy('MonthEndDate', 'desc')->first();

                $sheetName = (strlen($account->name) > 31) ? substr($account->name,0,28).'...' : $account->name;
                $sheetName = str_replace("/", "_", $sheetName);
                $sheetName = str_replace("?", "_", $sheetName);

                $excel->sheet($sheetName, function($sheet) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs, $credentialers, $recruitings, $accountPrevMonthIncComp, $accountYTDIncComp){
                    
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

        return $fileInfo;
    }

    public function downloadZip(Request $request) {
        $timestamp = $request->timestamp;
        $fileSystem = new Filesystem;

        $file = public_path('exports/Accounts_Batch_Print_'.$timestamp.'.pdf');

        if($fileSystem->exists($file)) {
            return response()->download($file)->deleteFileAfterSend(true);
        } else {
            flash(__("That file has already been downloaded and it's not on the server anymore."));

            return redirect()->route('admin.accounts.export');
        }
    }
}
