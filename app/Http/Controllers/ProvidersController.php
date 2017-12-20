<?php

namespace App\Http\Controllers;
use App\User;
use App\PipelineRosterBench;
use App\PipelineRecruiting;
use App\PipelineLocum;
use App\Account;
use App\ProviderAccount;
use App\Provider;
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

            $contractIn = PipelineRosterBench::with('pipeline.account', 'provider.accounts')->whereNotNull('contractIn')->where('firstShift', '>', Carbon::now()->format('Y-m-d'))->where('completed', 0)->where('signedNotStarted', 0)->filter($filter)->get();

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

        JavaScript::put($params);

        return view('admin.providers.index', $params);
    }

    public function switch(Request $request)
    {
        $stage = $request->stage;
        $provider = json_decode(json_encode($request->provider));

        if ($stage == 2) {
            $actualProvider = PipelineLocum::where('id', $provider->id)->first();

            if(!$actualProvider) {
                $actualProvider = PipelineRecruiting::where('id', $provider->id)->first();
            }

            $rosterBench = new PipelineRosterBench;
            $rosterBench->pipelineId = $provider->pipelineId;
            $rosterBench->activity = $provider->type == 'phys' ? 'physician' : 'app';
            $rosterBench->name = $provider->name;
            $rosterBench->hours = 0;
            $rosterBench->interview = $provider->interview;
            $rosterBench->contractIn = $provider->contractIn;
            $rosterBench->contractOut = $provider->contractOut;
            $rosterBench->firstShift = $provider->firstShift;
            $rosterBench->type = $provider->type;
            $rosterBench->providerId = $provider->providerId;

            if($rosterBench->save()) {
                $actualProvider->delete();
            }
        } else {
            $rosterBench = PipelineRosterBench::where('id', $provider->id)->first();
            $rosterBench->signedNotStarted = 1;
            $rosterBench->save();
        }

        return $rosterBench;
    }

    public function addHospitals(Request $request)
    {
        $provider = Provider::where('id', $request->providerId)->first();

        if(!$provider) {
            return;
        }

        $provider->accounts()->sync($request->hospitals ? $request->hospitals : [], false);

        return $provider->load('accounts');
    }
}
