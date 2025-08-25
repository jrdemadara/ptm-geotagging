<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MunicipalityController extends Controller
{
    public function index()
    {
        $data = DB::connection("mysql_tupaics")
            ->table("addresscitymun")
            ->where("provCode", "1265")
            ->select("addresscitymun.citymuncode AS code", "addresscitymun.citymundesc AS name")
            ->get();

        return response()->json($data);
    }

    public function barangay(Request $request)
    {
        $request->validate([
            "municipality" => "required",
        ]);

        $barangay = DB::connection("mysql_tupaics")
            ->table("addressbrgy")
            ->where("citymunCode", $request->input("municipality"))
            ->select("addressbrgy.brgycode AS code", "addressbrgy.brgydesc AS name")
            ->get();

        return response()->json($barangay);
    }
}
