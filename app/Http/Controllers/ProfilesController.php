<?php

namespace App\Http\Controllers;

use App\Models\Profile;
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
}
