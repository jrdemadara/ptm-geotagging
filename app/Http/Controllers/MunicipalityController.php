<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MunicipalityController extends Controller
{
    public function index()
    {
        $data = DB::connection('mysql_tupaics')->table('addresscitymun')
            ->where('provCode', '1265')
            ->select('addresscitymun.citymuncode AS code', 'addresscitymun.citymundesc AS name')->get();

        return response()->json($data);
    }
}
