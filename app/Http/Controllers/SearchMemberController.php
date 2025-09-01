<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchMemberController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            "keyword" => "required|string",
        ]);

        $data = Profile::when($request->filled("keyword"), function ($query) use ($request) {
            $query->where(function ($sub) use ($request) {
                $sub->where("lastname", "like", "%{$request->keyword}%")
                    ->orWhere("firstname", "like", "%{$request->keyword}%")
                    ->orWhere("middlename", "like", "%{$request->keyword}%")
                    ->orWhere("extension", "like", "%{$request->keyword}%");
            });
        })
            ->select(
                "qrcode",
                "precinct",
                "lastname",
                "firstname",
                "middlename",
                "extension",
                "birthdate",
                "phone",
                "occupation",
                "purok",
                "has_ptmid as isptmid",
            )
            ->orderBy("lastname", "asc")
            ->get();

        return response()->json($data);
    }
}
