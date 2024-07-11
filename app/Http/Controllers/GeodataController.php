<?php

namespace App\Http\Controllers;

use App\Models\Profile;

class GeodataController extends Controller
{
    public function index()
    {
        $data = Profile::select('lon', 'lat', 'lastname', 'firstname', 'middlename', 'precinct', 'barangay', 'purok', 'phone', 'occupation')->get();
        return response()->json($data);
    }
}
