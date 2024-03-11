<?php

namespace App\Http\Controllers;

use App\Models\Profile;

class GeodataController extends Controller
{
    public function index()
    {
        $data = Profile::select('lon', 'lat', 'lastname', 'firstname', 'middlename')->get();
        return response()->json($data);
    }
}
