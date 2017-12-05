<?php

namespace App\Http\Controllers;
use App\User;
use App\PipelineRosterBench;
use App\PipelineLocum;
use App\PipelineRecruiting;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Http\Requests\DashboardRequest;
use Illuminate\Support\Facades\Storage;

class ProvidersController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

        return view('admin.providers.index');
    }
}
