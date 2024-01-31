<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MunicipalityController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::connection('mysql_tupaics')->table('addresscitymun')
            ->where('provCode', '1265')
            ->select('addresscitymun.citymuncode', 'addresscitymun.citymundesc')->get();

        return response()->json($data);
    }
}
