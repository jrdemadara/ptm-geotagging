<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchMemberController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            "keyword" => "required|string",
        ]);

        $data = DB::connection("mysql_tupaics")
            ->table("recipient")
            ->where("isdelete", 0)
            ->where(function ($query) use ($request) {
                $query
                    ->where("lastname", "like", "%{$request->keyword}%")
                    ->orWhere("firstname", "like", "%{$request->keyword}%")
                    ->orWhere("middlename", "like", "%{$request->keyword}%")
                    ->orWhere("extension", "like", "%{$request->keyword}%");
            })
            ->select(
                "precintno AS precinct",
                "lastname",
                "firstname",
                "middlename",
                "extension",
                "birthdate",
                "contactno AS contact",
                "occupation",
                "isptmid",
            )
            ->get();

        return response()->json($data);
    }
}
