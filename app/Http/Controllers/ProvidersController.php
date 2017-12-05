<?php

namespace App\Http\Controllers;
use App\User;
use App\PipelineRosterBench;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Http\Requests\DashboardRequest;
use Illuminate\Support\Facades\Storage;
use App\Filters\ProvidersFilter;

class ProvidersController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ProvidersFilter $filter)
    {
        $queryString = $request->query();

        $accounts = Account::where('active', true)->orderBy('name')->get();

        if(count($queryString) == 0) {
            $sites = collect();
        } else {
            $providers = PipelineRosterBench::with('pipeline.account')->whereNotNull('fileToCredentialing')
            ->orWhere('signedNotStarted', 1)->filter($filter)->get();

            $sites = $providers->groupBy(function($provider) {
                return $provider->pipeline->account->name;
            });

            $temp = array();

            foreach ($sites as $key => $site) {
                $stages = $site->groupBy('stage');
                
                if(!isset($stages[1])) {
                    $stages[1] = array();
                }

                if(!isset($stages[2])) {
                    $stages[2] = array();
                }

                if(!isset($stages[3])) {
                    $stages[3] = array();
                }

                $temp[$key] = $stages;
            }

            $sites = collect($temp);
        }

        $params = compact('sites', 'accounts');

        return view('admin.providers.index', $params);
    }
}
