<?php
namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Beneficiary;
use App\Models\Livelihood;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssistanceController extends Controller
{

    public function validateProfile(Request $request)
    {
        $qrcode     = $request->query('qrcode');
        $assistance = $request->query('assistance');

        // Fetch the single profile based on the provided qrcode
        $profile = Profile::select(
            'id',
            'lastname',
            'firstname',
            'middlename',
            'extension',
            'precinct',
            'barangay',
            'purok',
            'phone',
            'lat',
            'lon',
        )
            ->where('qrcode', $qrcode)
            ->first();

        $assistanceExists = Assistance::where('profile_id', $profile->id)
            ->where('assistance', Str::lower($assistance))
            ->where('released_at', now()->format('Y-m-d'))
            ->exists();

        // Check if the profile exists
        if ($profile) {
            // Add the image URL or Base64 to the profile
            $imagePath = storage_path("app/profile/solo/{$profile->id}.jpg");

            if (file_exists($imagePath)) {
                // Convert the image to base64 and add it to the profile
                $imageData      = base64_encode(file_get_contents($imagePath));
                $profile->image = 'data:image/jpeg;base64,' . $imageData; // Adding data URI scheme
            } else {
                $profile->image = null; // Or set a default placeholder image in base64
            }

            // Add the assistanceExists flag to the response
            $profile->assistance_exists = $assistanceExists;

            // Return the profile with the image data and assistance status
            return response()->json($profile);
        }

        // Return a not found response if the profile doesn't exist
        //return response()->json(['data' => $assistanceExists], 200);
        return response()->json(['error' => 'Profile not found'], 404);
    }

    public function validateProfilePersonal(Request $request)
    {
        $qrcode = $request->query('qrcode');

        // Fetch the single profile based on the provided qrcode
        $profile = Profile::select(
            'id',
            'lastname',
            'firstname',
            'middlename',
            'extension',
            'precinct',
            'barangay',
            'purok',
            'phone',
            'status',
            'lat',
            'lon',
        )
            ->where('qrcode', $qrcode)
            ->first();

        // Check if the profile exists
        if ($profile) {

            $livelihoodList = Livelihood::where('profile_id', $profile->id)->get(['*'])
                ->makeHidden(['created_at', 'updated_at', 'deleted_at']);

            $beneficiaryList = Beneficiary::where('profile_id', $profile->id)->get(['*'])
                ->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            $skillList = Skill::where('profile_id', $profile->id)->get(['*'])
                ->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            $assitanceList = Assistance::where('profile_id', $profile->id)->get(['*'])
                ->makeHidden(['created_at', 'updated_at', 'deleted_at']);

            // Define the paths for the images
            $imagePaths = [
                'solo'      => storage_path("app/profile/solo/{$profile->id}.jpg"),
                'family'    => storage_path("app/profile/family/{$profile->id}.jpg"),
                'household' => storage_path("app/profile/household/{$profile->id}.jpg"),
            ];

            // Assign Base64 or null to the profile attributes
            foreach ($imagePaths as $key => $path) {
                $profile->$key = $this->getBase64Image($path);
            }

            $data = [
                'profile'     => $profile,
                'livelihood'  => $livelihoodList,
                'beneficiary' => $beneficiaryList,
                'skill'       => $skillList,
                'assistance'  => $assitanceList,
            ];

            // Return the profile with the image data and assistance status
            return response()->json($data);
        }

        // Return a not found response if the profile doesn't exist
        return response()->json(['error' => 'Profile not found'], 404);
    }

    public function save(Request $request)
    {
        $request->validate([
            'assistance'  => 'required|string',
            'amount'      => 'required|numeric',
            'released_at' => 'required|string',
            'profile_id'  => 'required|integer',
        ]);

        $assistance = Assistance::create([
            'assistance'  => Str::lower($request->input('assistance')),
            'amount'      => $request->input('amount'),
            'released_at' => $request->input('released_at'),
            'profile_id'  => $request->input('profile_id'),
        ]);

        return response()->json([
            'message'    => 'Assistance record created successfully.',
            'assistance' => $assistance,
        ], 201);

    }

    public function fetchByDateRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $data = DB::table('assistance')
            ->selectRaw("
        assistance.released_at AS date,
        UPPER(assistance.assistance) AS assistance,
        UPPER(CONCAT(profiles.lastname, ', ', profiles.firstname, ' ', COALESCE(profiles.middlename, ''))) AS fullname,
        UPPER(profiles.barangay) AS barangay,
        UPPER(profiles.purok) AS purok,
        assistance.amount AS amount,
        indorser.fullname AS endorser
    ")
            ->join('profiles', 'assistance.profile_id', '=', 'profiles.id')
            ->leftJoin('tagging', 'profiles.id', '=', 'tagging.profile_id')
            ->leftJoin('indorser', 'tagging.indorser_id', '=', 'indorser.id')
            ->whereBetween('assistance.released_at', [$startDate, $endDate])
            ->where('assistance.amount', '>', 0)
            ->orderByRaw('COALESCE(indorser.fullname, "") ASC') // Sort null values first
            ->get();

        return response()->json($data);

    }

    // Helper function to process images and return Base64 or null
    public function getBase64Image($filePath)
    {
        if (file_exists($filePath)) {
            return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($filePath));
        }
        return null;
    }

}
