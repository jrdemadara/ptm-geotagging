<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeAssistanceController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::connection('mysql_tupaics')->table('stattype')
            ->where('isdelete', 0)
            ->select('statname AS assistance')->get();

        return response()->json($data);
    }
}
