<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function barangay(Request $request)
    {
        $request->validate([
            'municipality' => 'required',
        ]);

        $code = DB::connection('mysql_tupaics')->table('addresscitymun')
            ->where('citymunDesc', $request->input('municipality'))
            ->select('addresscitymun.citymuncode')->get();

        $barangay = DB::connection('mysql_tupaics')->table('addressbrgy')
            ->where('citymunCode', $code[0]->citymuncode)
            ->select('brgyDesc AS barangay')->get();

        return response()->json($barangay);
    }
}
