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
    public function export(Account $account) {
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
            'envision.png',
            array(
                'width' => 160,
                'height' => 40,
                // 'marginTop'     => -1,
                // 'marginLeft'    => -1,
                // 'wrappingStyle' => 'behind'
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
            return $rosterBench->firstShift && ($rosterBench->firstShift->gte($today));
        });

        $futureLocums = $account->pipeline->locums->filter(function($locum) use ($today) {
            return $locum->startDate && ($locum->startDate->gte($today));
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
}
