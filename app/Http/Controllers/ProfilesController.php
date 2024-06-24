<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilesController extends Controller
{
    public function index()
    {
        $profiles = Profile::select('id', 'lastname', 'firstname', 'middlename', 'extension', 'birthdate', 'occupation', 'phone', 'lat', 'lon', 'has_ptmid', 'created_at')->paginate(10);

        $profilesWithImages = [];

        // Add base64 encoded images to each profile
        foreach ($profiles as $profile) {
            // Array to hold base64 encoded images
            $profileData = $profile->toArray();

            // Image types and their respective storage directories
            $imageTypes = [
                'solo' => 'solo',
                'family' => 'family',
                'household' => 'household',
            ];

            // Loop through each image type
            foreach ($imageTypes as $type => $directory) {
                // Assuming the image file names are based on the profile ID and image type
                $imagePath = $directory . '/' . $profile->id . '.jpg';

                // Check if the image file exists
                if (Storage::exists($imagePath)) {
                    // Read the file contents
                    $imageData = Storage::get($imagePath);

                    // Encode the contents to base64
                    $base64 = base64_encode($imageData);

                    // Add the base64 encoded image to the profile data
                    $profileData[$type] = $base64;
                }
            }

            // Add profile data with images to the result array
            $profilesWithImages[] = $profileData;
        }

        // Extract pagination information
        $paginationInfo = [
            'current_page' => $profiles->currentPage(),
            'next_page' => $profiles->nextPageUrl(),
            'last_page' => $profiles->lastPage(),
        ];

        // Return JSON response with paginated profiles including base64 encoded images
        return response()->json(['profiles' => $profilesWithImages, 'pagination' => $paginationInfo]);

    }

    public function fetchProfileImages(Request $request)
    {
        // Validate the request
        $request->validate([
            'client_secret' => 'required|string',
            'profile_id' => 'required|integer',
        ]);

        $clientSecret = env('CLIENT_SECRET');

        if ($request->input('client_secret') == $clientSecret) {

            // Construct the file path (assuming all images are in JPG format)
            $profileId = $request->input('profile_id');
            $soloPath = "profile/solo/{$profileId}.jpg";
            $familyPath = "profile/family/{$profileId}.jpg";
            $householdPath = "profile/household/{$profileId}.jpg";

            // Check if the file exists
            if (!Storage::disk('local')->exists($soloPath)) {
                return response()->json(['error' => 'solo image not found.'], 404);
            }
            if (!Storage::disk('local')->exists($familyPath)) {
                return response()->json(['error' => 'family image not found.'], 404);
            }
            if (!Storage::disk('local')->exists($householdPath)) {
                return response()->json(['error' => 'household image not found.'], 404);
            }

            // Retrieve and encode the image
            $soloImageContents = Storage::disk('local')->get($soloPath);
            $familyImageContents = Storage::disk('local')->get($familyPath);
            $householdImageContents = Storage::disk('local')->get($householdPath);

            $solobase64Image = base64_encode($soloImageContents);
            $familybase64Image = base64_encode($familyImageContents);
            $householdbase64Image = base64_encode($householdImageContents);

            // Prepare the response (assuming JPG)
            $mimeType = 'image/jpeg';

            return response()->json([
                'profile_id' => $profileId,
                'soloImage' => 'data:' . $mimeType . ';base64,' . $solobase64Image,
                'familyImage' => 'data:' . $mimeType . ';base64,' . $familybase64Image,
                'householdImage' => 'data:' . $mimeType . ';base64,' . $householdbase64Image,
            ]);

        } else {
            return response()->json([
                'message' => 'invalid credentials',
                'error' => 'UNAUTHORIZED',
            ], 401);

        }

    }

}
