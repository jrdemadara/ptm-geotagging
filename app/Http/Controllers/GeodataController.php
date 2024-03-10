<?php

namespace App\Http\Controllers;

use App\Models\Profile;

class GeodataController extends Controller
{
    public function index()
    {
        $data = Profile::select('lat', 'lon')->get();
        return response()->json($data);
    }
}
