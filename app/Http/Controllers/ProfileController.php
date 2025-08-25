<?php

namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Beneficiary;
use App\Models\Livelihood;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\Tesda;
use App\Models\TesdaCourse;
use App\Services\MapboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $totalCount = Profile::count();
        $totalCountByUser = Profile::where("user_id", $userId)->count();

        return response()->json([
            "user_id" => $userId,
            "total_count" => $totalCount,
            "total_count_by_user" => $totalCountByUser,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            "data" => "required|json",
            "personalPhoto" => "required",
            "familyPhoto" => "required",
            "livelihoodPhoto" => "required",
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 400);
        }

        // Extract data from the request
        $data = json_decode($request->input("data"), true);
        $personalPhoto = $request->file("personalPhoto");
        $familyPhoto = $request->file("familyPhoto");
        $livelihoodPhoto = $request->file("livelihoodPhoto");

        // Get beneficiaries, skills, livelihoods and assistance from the data
        $beneficiariesJson = $data["beneficiaries"] ?? [];
        $skillsJson = $data["skills"] ?? [];
        $livelihoodsJson = $data["livelihoods"] ?? [];
        $tesdaJson = $data["tesda"] ?? [];
        $assistanceJson = $data["assistance"] ?? [];
        // Create or update the profile
        $profile = Profile::updateOrCreate(
            [
                "qrcode" => $data["qrcode"],
            ],
            [
                "precinct" => Str::lower($data["precinct"]),
                "lastname" => Str::lower($data["lastname"]),
                "firstname" => Str::lower($data["firstname"]),
                "middlename" => Str::lower($data["middlename"]),
                "phone" => $data["phone"],
                "extension" => Str::lower($data["extension"]),
                "birthdate" => $data["birthdate"],
                "occupation" => Str::lower($data["occupation"]),
                "lat" => $data["lat"],
                "lon" => $data["lon"],
                "municipality" => $data["municipality"],
                "barangay" => $data["barangay"],
                "purok" => Str::lower($data["purok"]),
                "has_ptmid" => $data["hasptmid"],
                "is_muslim" => $data["ismuslim"],
                "user_id" => auth()->id(),
            ],
        );

        // Create or update beneficiaries
        if ($beneficiariesJson !== null) {
            foreach ($beneficiariesJson as $beneficiaryData) {
                Beneficiary::updateOrCreate(
                    [
                        "profile_id" => $profile->id,
                        "fullname" => Str::lower($beneficiaryData["fullname"]),
                    ],
                    [
                        "precinct" => $beneficiaryData["precinct"],
                        "birthdate" => $beneficiaryData["birthdate"],
                        "is_muslim" => $beneficiaryData["ismuslim"],
                        "qrcode" =>
                            $beneficiaryData["qrcode"] ?: "{$profile->id}-{$profile->qrcode}",
                    ],
                );
            }
        }

        // Create or update skills
        if ($skillsJson !== null) {
            foreach ($skillsJson as $skill) {
                Skill::updateOrCreate([
                    "profile_id" => $profile->id,
                    "skill" => Str::lower($skill),
                ]);
            }
        }

        // Create or update livelihoods
        if ($livelihoodsJson !== null) {
            foreach ($livelihoodsJson as $livelihood) {
                Livelihood::updateOrCreate([
                    "profile_id" => $profile->id,
                    "livelihood" => Str::lower($livelihood["livelihood"]),
                    "description" => Str::lower($livelihood["description"]),
                ]);
            }
        }

        // Create or update assistance
        // if (!is_null($assistanceJson)) {
        //     foreach ($assistanceJson as $assistance) {
        //         Assistance::create([
        //             'profile_id' => $profile->id,
        //             'assistance' => Str::lower($assistance['assistance']),
        //             'amount' => $assistance['amount'],
        //             'released_at' => $assistance['released_at'],
        //         ]);
        //     }
        // }

        // Create or update tesda
        if ($tesdaJson !== null) {
            foreach ($tesdaJson as $tesda) {
                $data = Tesda::updateOrCreate([
                    "profile_id" => $profile->id,
                    "name" => Str::lower($tesda["name"]),
                ]);

                TesdaCourse::updateOrCreate([
                    "tesda_id" => $data->id,
                    "course" => Str::lower($tesda["course"]),
                ]);
            }
        }

        //Store photos
        if ($personalPhoto !== null) {
            $this->storePhoto("solo", $personalPhoto, $profile->id);
            $this->storePhoto("family", $familyPhoto, $profile->id);
            $this->storePhoto("household", $livelihoodPhoto, $profile->id);
        }

        return response()->json(["message" => "Profile created successfully"], 201);
    }

    private function storePhoto($path, $photo, $userId)
    {
        $imageData = file_get_contents($photo);
        $photo = base64_decode($imageData);
        $filename = $userId . ".jpg";
        Storage::disk("local")->put($path . "/" . $filename, $photo);
    }

    // protected $mapboxService;

    // public function __construct(MapboxService $mapboxService)
    // {
    //     $this->mapboxService = $mapboxService;
    // }

    // public function getBarangayName($lat, $lon)
    // {
    //     $barangay = $this->mapboxService->reverseGeocode($lat, $lon);

    //     return $barangay;
    // }
}
