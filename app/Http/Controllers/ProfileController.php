<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Livelihood;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // Create or update the profile
        $profile = Profile::updateOrCreate([
            'lastname' => Str::lower($request->input('lastname')),
            'firstname' => Str::lower($request->input('firstname')),
            'middlename' => Str::lower($request->input('middlename')),
        ], [
            'extension' => Str::lower($request->input('extension')),
            'birthdate' => $request->input('birthdate'),
            'occupation' => Str::lower($request->input('occupation')),
            'phone' => $request->input('phone'),
            'lat' => $request->input('lat'),
            'lon' => $request->input('lon'),
            'user_id' => $user->id,
        ]);

        // Create or update beneficiaries
        if ($request->has('beneficiaries')) {
            $beneficiariesJson = $request->input('beneficiaries');
            if (!is_null($beneficiariesJson)) {
                $beneficiaries = json_decode($beneficiariesJson, true);
                foreach ($beneficiaries as $beneficiaryData) {
                    Beneficiary::updateOrCreate([
                        'profile_id' => $profile->id,
                        'fullname' => Str::lower($beneficiaryData['fullname']),
                    ], [
                        'precinct' => $beneficiaryData['precinct'],
                        'birthdate' => $beneficiaryData['birthdate'],
                    ]);
                }
            }
        }

        // Create or update livelihoods
        if ($request->has('livelihoods')) {
            $livelihoodsJson = $request->input('livelihoods');
            if (!is_null($livelihoodsJson)) {
                $livelihoods = json_decode($livelihoodsJson, true);

                if (isset($livelihoods['livelihoods'])) {
                    foreach ($livelihoods['livelihoods'] as $livelihood) {
                        Livelihood::updateOrCreate([
                            'profile_id' => $profile->id,
                            'livelihood' => Str::lower($livelihood),
                        ]);
                    }
                }

            }

        }

        // Create or update skills
        if ($request->has('skills')) {
            $skillsJson = $request->input('skills');
            if (!is_null($skillsJson)) {
                $skills = json_decode($skillsJson, true);
                if (isset($skills['skills'])) {
                    foreach ($skills['skills'] as $skill) {
                        Skill::updateOrCreate([
                            'profile_id' => $profile->id,
                            'skill' => Str::lower($skill),
                        ]);
                    }
                }

            }

        }

        // Store photos
        $this->storePhoto('solo', $request->input('personalPhoto'), $profile->id);
        // $this->storePhoto('family', $request->input('familyPhoto'), $profile->id);
        // $this->storePhoto('household', $request->input('livelihoodPhoto'), $profile->id);

        return response()->json(['message' => 'Profile created successfully'], 201);

    }

    private function storePhoto($path, $photo, $userId)
    {
        $image_64 = base64_encode($photo);
        Log::debug('Decoded Photo: ' . print_r($photo, true));

        // $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        // $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        // $image = str_replace($replace, '', $image_64);
        // $image = str_replace(' ', '+', $image);
        // $imageName = $userId . '.' . $extension;
        // Storage::disk(`local/$path`)->put($imageName, base64_decode($image));
    }
}
