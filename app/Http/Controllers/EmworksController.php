<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Emworks;

class EmworksController extends Controller
{
    public function findByProviderId(Request $request)
    {
        $providerId = $request->providerId;

        $emworks = Emworks::where('providerId', $providerId)->first();

        return $emworks;
    }
}