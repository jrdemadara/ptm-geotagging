<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeMemberController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'municipality' => 'required',
        ]);

        $code = DB::connection('mysql_tupaics')->table('addresscitymun')
            ->where('citymunDesc', $request->input('municipality'))
            ->select('addresscitymun.citymuncode')->get();

        $data = DB::connection('mysql_tupaics')->table('recipient')
            ->where('municipality', $code[0]->citymuncode)
            ->where('isdelete', 0)
            ->select('precintno AS precinct', 'lastname', 'firstname', 'middlename',
                'extension', 'birthdate', 'contactno AS contact', 'occupation',
                'isptmid')->get();

        return response()->json($data);
    }
}
