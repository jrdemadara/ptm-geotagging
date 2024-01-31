<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'municipality' => 'required|numeric',
        ]);

        $data = DB::connection('mysql_tupaics')->table('recipient')
            ->where('municipality', $request->input('municipality'))
            ->select('recipient.precintno', 'recipient.lastname',
                'recipient.firstname', 'recipient.middlename',
                'recipient.extension', 'recipient.birthdate', 'recipient.contactno')->get();

        return response()->json($data);
    }
}
