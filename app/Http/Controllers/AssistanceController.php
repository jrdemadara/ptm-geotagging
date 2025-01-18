<?php
namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Profile;
use Illuminate\Http\Request;
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
            'phone'
        )
            ->where('qrcode', $qrcode)
            ->first();

        $assistanceExists = Assistance::where('profile_id', $profile->id)
            ->where('assistance', Str::lower($assistance))
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
        return response()->json($profile);

        // Return a not found response if the profile doesn't exist
        //return response()->json(['error' => 'Profile not found'], 404);
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

}
