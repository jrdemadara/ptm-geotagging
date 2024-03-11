<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeController extends Controller
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
            ->select('recipient.precintno', 'recipient.lastname',
                'recipient.firstname', 'recipient.middlename',
                'recipient.extension', 'recipient.birthdate', 'recipient.contactno', 'recipient.occupation')->get();

        return response()->json($data);
    }
}
