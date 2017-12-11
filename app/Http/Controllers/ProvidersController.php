<?php

namespace App\Http\Controllers;
use App\User;
use App\PipelineRosterBench;
use App\PipelineRecruiting;
use App\PipelineLocum;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Http\Requests\DashboardRequest;
use Illuminate\Support\Facades\Storage;
use App\Filters\ProvidersFilter;
use Carbon\Carbon;
use JavaScript;

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
            $locums = PipelineLocum::with('pipeline.account', 'provider.accounts')->whereNull('declined')->filter($filter)->get();
            $recruitings = PipelineRecruiting::with('pipeline.account', 'provider.accounts')->whereNull('declined')->filter($filter)->get();

            $interviews = $locums->merge($recruitings);

            $credentialings = PipelineRosterBench::with('pipeline.account', 'provider.accounts')->where(function($query){
                $query->whereNotNull('fileToCredentialing')
                ->orWhere('signedNotStarted', 1);
            })->where('completed', 0)->filter($filter)->get();

            $contractIn = PipelineRosterBench::with('pipeline.account', 'provider.accounts')->whereNotNull('contractIn')->where('firstShift', '>', Carbon::now()->format('Y-m-d'))->where('completed', 0)->filter($filter)->get();

            $interviews_sites = $interviews->groupBy(function($provider) {
                return $provider->pipeline->account->name;
            });

            $contractIn_sites = $contractIn->groupBy(function($provider) {
                return $provider->pipeline->account->name;
            });

            $credentialings_sites = $credentialings->groupBy(function($provider) {
                return $provider->pipeline->account->name;
            });

            $temp = array();

            foreach ($interviews_sites as $key => $site) {
                if(!isset($temp[$key])) {
                    $temp[$key] = array();
                    $temp[$key][1] = array();
                    $temp[$key][1][] = $site;
                } else {
                    $temp[$key][1][] = $site;
                }
            }

            foreach ($contractIn_sites as $key => $site) {
                if(!isset($temp[$key])) {
                    $temp[$key] = array();
                    $temp[$key][2] = array();
                    $temp[$key][2][] = $site;
                } else {
                    $temp[$key][2][] = $site;
                }
            }

            foreach ($credentialings_sites as $key => $site) {
                if(!isset($temp[$key])) {
                    $temp[$key] = array();
                    $temp[$key][3] = array();
                    $temp[$key][3][] = $site;
                } else {
                    $temp[$key][3][] = $site;
                }
            }

            foreach ($interviews_sites as $key => $site) {
                if(!isset($temp[$key][1])) {
                    $temp[$key][1] = array();
                }

                if(!isset($temp[$key][2])) {
                    $temp[$key][2] = array();
                }

                if(!isset($temp[$key][3])) {
                    $temp[$key][3] = array();
                }

                ksort($temp[$key]);
            }

            ksort($temp);

            $sites = collect($temp);
        }

        $params = compact('sites', 'accounts');

        return view('admin.providers.index', $params);
    }

    public function switch(Request $request)
    {
        return $request->all();
    }
}
