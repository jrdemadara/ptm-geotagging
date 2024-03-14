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

        $data = DB::connection('mysql_tupaics')->table('financial')
            ->join('recipient', 'financial.recserial', '=', 'recipient.recserial')
            ->join('stattype', 'financial.statserial', '=', 'stattype.statserial')
            ->where('recipient.municipality', $code[0]->citymuncode)
            ->where('recipient.isdelete', 0)
            ->select('recipient.precintno AS precinct', 'recipient.lastname', 'recipient.firstname', 'recipient.middlename',
                'recipient.extension', 'recipient.birthdate', 'recipient.contactno AS contact', 'recipient.occupation',
                'recipient.isptmid', 'stattype.statname AS assistance', 'financial.amount', 'financial.dateavailed')->get();

        return response()->json($data);
    }
}
