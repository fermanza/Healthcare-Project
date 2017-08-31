<?php

namespace App\Http\Controllers;

use JavaScript;
use App\Account;
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
            'practices',
        ]);
        $pipeline = $account->pipeline;
        $region = $account->region;
        $practice = $account->practices->count() ? $account->practices->first() : null;
        $practiceTimes = config('pipeline.practice_times');
        $recruitingTypes = config('pipeline.recruiting_types');
        $contractTypes = config('pipeline.contract_types');
        $accounts = Account::where('active', true)->orderBy('name')->get();

        if ($practice && $practice->isIPS() && $pipeline->practiceTime == 'hours') {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 180 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 180 : $pipeline->fullTimeHoursApps;
        } else {
            $pipeline->fullTimeHoursPhys = $pipeline->fullTimeHoursPhys == 0 ? 120 : $pipeline->fullTimeHoursPhys;
            $pipeline->fullTimeHoursApps = $pipeline->fullTimeHoursApps == 0 ? 120 : $pipeline->fullTimeHoursApps;
        }

        $params = compact(
            'account', 'pipeline', 'region', 'practice', 'practiceTimes',
            'recruitingTypes', 'contractTypes', 'accounts'
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
        $pipeline->save();

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

        $today = Carbon::today();

        // Creating the new document...
        $word = new \PhpOffice\PhpWord\PhpWord();
        $documentName = 'Short Form.docx';

        $boldFontStyle = array('name' => 'Cambria(Body)', 'size' => 9, 'bold' => true);
        $boldUnderlinedFontStyle = array('name' => 'Cambria(Body)', 'size' => 9, 'bold' => true, 'underline' => 'single');
        $normalFontStyle = array('name' => 'Cambria(Body)', 'size' => 9);
        $paragraphCenterStyle = array('align' => 'center');
        $footerStyle = array('name' => 'ArialMT', 'size' => 8);

        $section = $word->addSection();

        $section->addImage(
            'swoosh.png',
            array(
                'width'            => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(21.59),
                'height'           => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(6),
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
            '<w:br/><w:br/><w:br/>'.$account->name.' '.($account->practices->count() ? $account->practices->first()->name : '').'<w:br/>'.
            $account->city.','.$account->state.'<w:br/>'.
            Carbon::today()->format('F d, Y').'<w:br/>',
            $boldFontStyle,
            $paragraphCenterStyle
        );


        ////// Elements for lists /////////
        $currentRosterPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'roster' && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
        });

        $currentBenchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'physician';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortByDesc(function($rosterBench){
            return sprintf('%-12s%s', $rosterBench->isSMD, $rosterBench->isAMD, $rosterBench->name);
        });

        $locumsMD = $account->pipeline->locums->filter(function($locum) {
            return $locum->type == 'md';
        })->reject(function($locum){
            return $locum->declined;
        })->sortBy('name');

        $currentRosterAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'roster' && $rosterBench->activity == 'app';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortBy('name');

        $currentBenchAPP = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->place == 'bench' && $rosterBench->activity == 'app';
        })->reject(function($rosterBench){
            return $rosterBench->resigned;
        })->sortBy('name');

        $locumsAPP = $account->pipeline->locums->filter(function($locum) {
            return $locum->type == 'app';
        })->reject(function($locum){
            return $locum->declined;
        })->sortBy('name');

        $futureRosters = $account->pipeline->rostersBenchs->filter(function($rosterBench) use ($today) {
            if($rosterBench->firstShift) {
                $firstShift = Carbon::parse($rosterBench->firstShift);

                return $firstShift->gte($today);
            }
        });

        $futureLocums = $account->pipeline->locums->filter(function($locum) use ($today) {
            if($locum->startDate) {
                $startDate = Carbon::parse($locum->startDate);

                return $startDate->gte($today);
            }
        });
        /////// End of Elements for lists /////////


        /////// Lists ///////////
        $currentRosterPhysiciansList = '';
        foreach ($currentRosterPhysicians as $rosterPhysician) {
            $currentRosterPhysiciansList.= $rosterPhysician->name.' '.
            ($rosterPhysician->isAMD && $rosterPhysician->isSMD ? 'MD, SMD ' : ($rosterPhysician->isAMD ? 'AMD ' : ($rosterPhysician->isSMD ? 'SMD ' : ''))).'('.$rosterPhysician->hours.')<w:br/>';
        }

        $currentBenchPhysiciansList = '';
        foreach ($currentBenchPhysicians as $benchPhysician) {
            $currentBenchPhysiciansList.= $benchPhysician->name.' '.
            ($benchPhysician->isAMD && $benchPhysician->isSMD ? 'MD, SMD ' : ($benchPhysician->isAMD ? 'AMD ' : ($benchPhysician->isSMD ? 'SMD ' : ''))).'('.$benchPhysician->hours.')<w:br/>';
        }

        $locumsMDList = '';
        foreach ($locumsMD as $locumMD) {
            $locumsMDList.= $locumMD->name.' MD '.'('.$locumMD->agency.')<w:br/>';
        }

        $currentRosterAPPList = '';
        foreach ($currentRosterAPP as $rosterAPP) {
            $currentRosterAPPList.= $rosterAPP->name.' '.
            ($rosterAPP->isAMD && $rosterAPP->isSMD ? 'MD, SMD ' : ($rosterAPP->isAMD ? 'AMD ' : ($rosterAPP->isSMD ? 'SMD ' : ''))).'('.$rosterAPP->hours.')<w:br/>';
        }

        $currentBenchAPPList = '';
        foreach ($currentBenchAPP as $benchAPP) {
            $currentBenchAPPList.= $benchAPP->name.' '.
            ($benchAPP->isAMD && $benchAPP->isSMD ? 'MD, SMD ' : ($benchAPP->isAMD ? 'AMD ' : ($benchAPP->isSMD ? 'SMD ' : ''))).'('.$benchAPP->hours.')<w:br/>';
        }

        $locumsAPPList = '';
        foreach ($locumsAPP as $locumAPP) {
            $locumsAPPList.= $locumAPP->name.' MD '.'('.$locumAPP->agency.')<w:br/>';
        }

        $futureRostersList = '';
        foreach ($futureRosters as $futureRoster) {
            $futureRostersList .= $futureRoster->name.' - '.$futureRoster->notes.'<w:br/>';
        }

        $futureLocumsList = '';
        foreach ($futureLocums as $futureLocum) {
            $futureLocumsList .= $futureLocum->name.' - '.$futureLocum->notes.'<w:br/>';
        }

        $recruitingsList = '';
        foreach ($account->pipeline->recruitings as $recruiting) {
            $recruitingsList .= $recruiting->name.' - '.$recruiting->notes.'<w:br/>';
        }
        /////// End of Lists ///////////

        // Define table style arrays
        $styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80);
        // Define cell style arrays
        $styleCell = array('valign'=>'top');
        // Add table style
        $word->addTableStyle('myOwnTableStyle', $styleTable);
        // Add table
        $table = $section->addTable('myOwnTableStyle');

        //Line break -> <w:br/>
        
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

        $section->addText('<w:br/>FTE Physicians required: '.$account->pipeline->staffPhysicianFTEOpenings.'<w:br/>'.
            'Current need: '.$account->pipeline->staffPhysicianFTENeeds.'<w:br/><w:br/>',
            $normalFontStyle
        );

        $section->addText('FTE Apps required: '.$account->pipeline->staffAppsFTEOpenings.'<w:br/>'.
            'Current need: '.$account->pipeline->staffAppsFTENeeds,
            $normalFontStyle
        );

        $footer = $section->addFooter();
        $footer->addText('3916 State Street | Suite 200 | Santa Barbara, CA 93105', $normalFontStyle, array('align' => 'right'));

        $section->addPageBreak();

        $section2 = $word->addSection();

        $header = $section2->addHeader();
        //$header->addText('ENVISION PHYSICIAN SERVICES', $normalFontStyle);
        $header->addPreserveText('ENVISION PHYSICIAN SERVICES           {PAGE}', $normalFontStyle, array('align' => 'right'));

        $section2->addText('Providers Hired who have not started', $boldUnderlinedFontStyle);
        $section2->addText($futureRostersList, $normalFontStyle);

        $section2->addText('Locums who have not started', $boldUnderlinedFontStyle);
        $section2->addText($futureLocumsList, $normalFontStyle);

        $section2->addText('Pipeline/candidate update', $boldUnderlinedFontStyle);
        $section2->addText($recruitingsList, $normalFontStyle);

        $footer2 = $section2->addFooter();

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
        })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })->sortBy('name');

        $activeRosterPhysicians = $activeRosterPhysicians->values();

        $benchPhysicians = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->activity == 'physician' && $rosterBench->place == 'bench';
        })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })->sortBy('name');

        $benchPhysicians = $benchPhysicians->values();

        $activeRosterAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->activity == 'app' && $rosterBench->place == 'roster';
        })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })->sortBy('name');

        $activeRosterAPPs = $activeRosterAPPs->values();

        $benchAPPs = $account->pipeline->rostersBenchs->filter(function($rosterBench) {
            return $rosterBench->activity == 'app' && $rosterBench->place == 'bench';
        })->reject(function($rosterBench) { return !is_null($rosterBench->resigned); })->sortBy('name');

        $benchAPPs = $benchAPPs->values();

        Excel::create('Pipeline', function($excel) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs){
            $excel->sheet('Summary', function($sheet) use ($account, $activeRosterPhysicians, $activeRosterAPPs, $benchPhysicians, $benchAPPs){
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

                if(count($activeRosterPhysicians) >= count($activeRosterAPPs)) {
                    for ($i = 0; $i < count($activeRosterPhysicians); $i++) { 
                        $row = [
                            $rosterBenchCount,
                            $activeRosterPhysicians[$i]->name,
                            $activeRosterPhysicians[$i]->firstShift ? Carbon::parse($activeRosterPhysicians[$i]->firstShift)->format('m-d-Y') : '',
                            $rosterBenchCount,
                            isset($activeRosterAPPs[$i]) ? $activeRosterAPPs[$i]->name : '',
                            isset($activeRosterAPPs[$i]) ? ($activeRosterAPPs[$i]->firstShift ? Carbon::parse($activeRosterAPPs[$i]->firstShift)->format('m-d-Y') : '') : ''
                        ];

                        $sheet->row($rosterBenchRow, $row);

                        $rosterBenchRow++;
                        $rosterBenchCount++;
                    }
                } else {
                    for ($i = 0; $i < count($activeRosterAPPs); $i++) { 
                        $row = [
                            $rosterBenchCount,
                            isset($activeRosterPhysicians[$i]) ? $activeRosterPhysicians[$i]->name : '',
                            isset($activeRosterPhysicians[$i]) ? ($activeRosterPhysicians[$i]->firstShift ? Carbon::parse($activeRosterPhysicians[$i]->firstShift)->format('m-d-Y') : '') : '',
                            $rosterBenchCount,
                            $activeRosterAPPs[$i]->name,
                            $activeRosterAPPs[$i]->firstShift ? Carbon::parse($activeRosterAPPs[$i]->firstShift)->format('m-d-Y') : ''
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

                ///////// Team Members //////////
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

                $sheet->cells('H14:I15', function($cells) {
                    $cells->setBackground('#b5c7e6');
                });

                $sheet->cells('H5:I15', function($cells) {
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

                $sheet->cell('I5', function($cell) use ($account) {
                    $cell->setValue($account->svp ? $account->svp->fullName() : '');
                });
                $sheet->cell('I6', function($cell) use ($account) {
                    $cell->setValue($account->rmd ? $account->rmd->fullName() : '');
                });
                $sheet->cell('I7', function($cell) use ($account) {
                    $cell->setValue($account->dca ? $account->dca->fullName() : '');
                });
                $sheet->cell('I8', function($cell) use ($account) {
                    $cell->setValue('');
                });
                $sheet->cell('I9', function($cell) use ($account) {
                    $cell->setValue($account->recruiter ? $account->recruiter->fullName() : '');
                });
                $sheet->cell('I10', function($cell) use ($account) {
                    $cell->setValue($account->credentialer ? $account->credentialer->fullName() : '');
                });
                $sheet->cell('I11', function($cell) use ($account) {
                    $cell->setValue('');
                });
                $sheet->cell('I12', function($cell) use ($account) {
                    $cell->setValue('');
                });
                $sheet->cell('I13', function($cell) use ($account) {
                    $cell->setValue('');
                });
                $sheet->cell('I14', function($cell) use ($account) {
                    $cell->setValue('Physician Opens');
                });
                $sheet->cell('I15', function($cell) use ($account) {
                    $cell->setValue('APP Opens');
                });
                ///////// Team Members //////////


                /////// Bench Table ////////
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
                            isset($benchPhysicians[$i]) ? ($benchPhysicians[$i]->firstShift ? Carbon::parse($activeRosterPhysicians[$i]->firstShift)->format('m-d-Y') : '') : '',
                            'APP/4',
                            $benchAPPs[$i]->name,
                            $benchAPPs[$i]->firstShift ? Carbon::parse($benchAPPs[$i]->firstShift)->format('m-d-Y') : ''
                        ];

                        $sheet->row($benchTableStartData, $row);

                        $benchTableStartData++;
                    }
                }
                /////// Bench Table ////////

                /////// Recruiting Table /////////
                $recruitingTableStart = $benchTableStartData+2;

                $sheet->mergeCells('A'.$recruitingTableStart.':F'.$recruitingTableStart);
                $sheet->mergeCells('G'.$recruitingTableStart.':H'.$recruitingTableStart);
                $sheet->mergeCells('D'.($recruitingTableStart+1).':I'.($recruitingTableStart+1));

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
                    $cell->setValue('Stage');
                });

                $sheet->cell('D'.($recruitingTableStart+1), function($cell) use ($account) {
                    $cell->setValue('Notes');
                });

                $sheet->cell('A'.($recruitingTableStart+1).':D'.($recruitingTableStart+1), function($cell) use ($account) {
                    $cell->setBackground('#fff1ce');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(11);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $recruitingTableDataStart = $recruitingTableStart+2;

                foreach ($account->pipeline->recruitings as $recruiting) {
                    $sheet->mergeCells('D'.$recruitingTableDataStart.':I'.$recruitingTableDataStart);

                    $row = [
                        strtoupper($recruiting->contract),
                        $recruiting->name,
                        '',
                        $recruiting->notes
                    ];

                    $sheet->row($recruitingTableDataStart, $row);

                    $recruitingTableDataStart++;
                }
                /////// Recruiting Table /////////

                ////// Credentialing Table ////////
                $credentialingTableStart = $recruitingTableDataStart+2;

                $sheet->mergeCells('A'.$credentialingTableStart.':F'.$credentialingTableStart);
                $sheet->mergeCells('G'.$credentialingTableStart.':H'.$credentialingTableStart);
                $sheet->mergeCells('F'.($credentialingTableStart+1).':G'.($credentialingTableStart+1));
                $sheet->mergeCells('H'.($credentialingTableStart+1).':I'.($credentialingTableStart+1));

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
                    $cell->setValue('Contract Received');
                });

                $sheet->cell('D'.($credentialingTableStart+1), function($cell) use ($account) {
                    $cell->setValue('File to Credentialing');
                });

                $sheet->cell('E'.($credentialingTableStart+1), function($cell) use ($account) {
                    $cell->setValue('APP to Hospital');
                });

                $sheet->cell('F'.($credentialingTableStart+1), function($cell) use ($account) {
                    $cell->setValue('Privilege Goal');
                });

                $sheet->cell('H'.($credentialingTableStart+1), function($cell) use ($account) {
                    $cell->setValue('Notes');
                });

                $sheet->cell('A'.($credentialingTableStart+1).':H'.($credentialingTableStart+1), function($cell) use ($account) {
                    $cell->setBackground('#fff1ce');
                    $cell->setFontFamily('Calibri (Body)');
                    $cell->setFontSize(11);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $credentialingTableDataStart = $credentialingTableStart+2;

                $sheet->row($credentialingTableDataStart, ['','','','','','','']);
                ////// Credentialing Recruiting Table ////////

                ////// Requirements Table ////////
                $requirementsTableStart = $credentialingTableDataStart+2;

                $sheet->mergeCells('A'.$requirementsTableStart.':I'.$requirementsTableStart);
                $sheet->mergeCells('B'.($requirementsTableStart+1).':I'.($requirementsTableStart+1));
                $sheet->mergeCells('B'.($requirementsTableStart+2).':I'.($requirementsTableStart+2));
                $sheet->mergeCells('B'.($requirementsTableStart+3).':I'.($requirementsTableStart+3));
                $sheet->mergeCells('B'.($requirementsTableStart+4).':I'.($requirementsTableStart+4));
                $sheet->mergeCells('B'.($requirementsTableStart+5).':I'.($requirementsTableStart+5));

                $sheet->cell('A'.$requirementsTableStart, function($cell) use ($account) {
                    $cell->setValue('Account Requirements');
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

                $sheet->cells('A'.($benchTableStart+1).':F'.($benchTableStartData), function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A'.($recruitingTableStart+1).':F'.($recruitingTableDataStart), function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A'.($credentialingTableStart+1).':F'.($credentialingTableDataStart), function($cells) {
                    $cells->setFontColor('#000000');
                    $cells->setFontFamily('Calibri (Body)');
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cells('A'.($requirementsTableStart+1).':F'.($requirementsTableStart+5), function($cells) {
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

                $sheet->setWidth(array(
                    'A'     => 12,
                    'B'     => 15,
                    'C'     => 10,
                    'D'     => 12,
                    'E'     => 10,
                    'F'     => 10,
                    'G'     => 1,
                    'H'     => 13,
                    'I'     => 17,
                ));

                $sheet->setHeight(array(
                    $rosterBenchRow => 3
                ));

                $sheet->getStyle('A1:I2')->applyFromArray($tableStyle);
                $sheet->getStyle('H4:I14')->applyFromArray($tableStyle);
                $sheet->getStyle('H15:I15')->applyFromArray($tableStyle);
                $sheet->getStyle('A4:F'.($rosterBenchRow+1))->applyFromArray($tableStyle);
                $sheet->getStyle('A'.$benchTableStart.':F'.($benchTableStartData))->applyFromArray($tableStyle);
                $sheet->getStyle('A'.$recruitingTableStart.':I'.$recruitingTableDataStart)->applyFromArray($tableStyle);
                $sheet->getStyle('A'.$credentialingTableStart.':I'.$credentialingTableDataStart)->applyFromArray($tableStyle);
                $sheet->getStyle('A'.$requirementsTableStart.':I'.($requirementsTableStart+5))->applyFromArray($tableStyle);
            });
        })->download('xlsx'); 
    }
}
