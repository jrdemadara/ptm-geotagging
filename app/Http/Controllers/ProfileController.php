<?php

namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Beneficiary;
use App\Models\Livelihood;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'data' => 'required|json',
            'personalPhoto' => 'required',
            'familyPhoto' => 'required',
            'livelihoodPhoto' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Extract data from the request
        $data = json_decode($request->input('data'), true);
        $personalPhoto = $request->file('personalPhoto');
        $familyPhoto = $request->file('familyPhoto');
        $livelihoodPhoto = $request->file('livelihoodPhoto');

        // Get beneficiaries, skills, livelihoods and assistance from the data
        $beneficiariesJson = $data['beneficiaries'] ?? [];
        $skillsJson = $data['skills'] ?? [];
        $livelihoodsJson = $data['livelihoods'] ?? [];
        $assistanceJson = $data['assistance'] ?? [];

        // Create or update the profile
        $profile = Profile::updateOrCreate([
            'lastname' => Str::lower($data['lastname']),
            'firstname' => Str::lower($data['firstname']),
            'middlename' => Str::lower($data['middlename']),
            'phone' => $data['phone'],
        ], [
            'extension' => Str::lower($data['extension']),
            'birthdate' => $data['birthdate'],
            'occupation' => Str::lower($data['occupation']),
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'qrcode' => $data['qrcode'],
            'has_ptmid' => $data['hasptmid'],
            'user_id' => auth()->id(),
        ]);

        // Create or update beneficiaries
        if (!is_null($beneficiariesJson)) {
            foreach ($beneficiariesJson as $beneficiaryData) {
                Beneficiary::updateOrCreate([
                    'profile_id' => $profile->id,
                    'fullname' => Str::lower($beneficiaryData['fullname']),
                ], [
                    'precinct' => $beneficiaryData['precinct'],
                    'birthdate' => $beneficiaryData['birthdate'],
                ]);
            }
        }

        // Create or update skills
        if (!is_null($skillsJson)) {
            foreach ($skillsJson as $skill) {
                Skill::updateOrCreate([
                    'profile_id' => $profile->id,
                    'skill' => Str::lower($skill),
                ]);
            }
        }

        // Create or update livelihoods
        if (!is_null($livelihoodsJson)) {
            foreach ($livelihoodsJson as $livelihood) {
                Livelihood::updateOrCreate([
                    'profile_id' => $profile->id,
                    'livelihood' => Str::lower($livelihood),
                ]);
            }
        }

        // Create or update assistance
        if (!is_null($assistanceJson)) {
            foreach ($assistanceJson as $assistance) {
                Assistance::updateOrCreate([
                    'profile_id' => $profile->id,
                    'assistance' => Str::lower($assistance['assistance']),

                ], [
                    'amount' => $assistance['amount'],
                    'released_at' => $assistance['released_at'],
                ]);
            }
        }

        //Store photos
        if (!is_null($personalPhoto)) {
            $this->storePhoto('solo', $personalPhoto, $profile->id);
            $this->storePhoto('family', $familyPhoto, $profile->id);
            $this->storePhoto('household', $livelihoodPhoto, $profile->id);
        }

        return response()->json(['message' => 'Profile created successfully'], 201);
    }

    private function storePhoto($path, $photo, $userId)
    {
        $imageData = file_get_contents($photo);
        $photo = base64_decode($imageData);
        $filename = $userId . '.jpg';
        Storage::disk('local')->put($path . '/' . $filename, $photo);
    }

}
