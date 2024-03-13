<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeAssistanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'municipality' => 'required',
        ]);

        $code = DB::connection('mysql_tupaics')->table('addresscitymun')
            ->where('citymunDesc', $request->input('municipality'))
            ->select('addresscitymun.citymuncode')->get();

        $data = DB::connection('mysql_tupaics')->table('stattype')
            ->where('isdelete', 0)
            ->select('statname')->get();

        return response()->json($data);
    }
}
