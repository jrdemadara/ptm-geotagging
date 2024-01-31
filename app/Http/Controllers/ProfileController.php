<?php

namespace App\Http\Controllers;

use App\Models\Livelihood;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'middlename' => 'required|string',
            'extension' => 'required|string',
            'birthdate' => 'required|string',
            'occupation' => 'required|string',
            'phone' => 'required|numeric',
            'lat' => 'required|string',
            'lon' => 'required|string',
            'solo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'family' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'household' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'beneficiaries' => 'required',
            'livelihoods' => 'required',
            'skills' => 'required',
        ]);

        $user = Auth::user();

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

        if ($request->beneficiaries) {
            foreach ($request->beneficiaries as $beneficiary) {
                Profile::updateOrCreate([
                    'fullname' => Str::lower($beneficiary['fullname']),
                ], [
                    'precint' => $beneficiary['precint'],
                    'birthdate' => $beneficiary['birthdate'],
                    'profile_id' => $profile->id,
                ]);

            }
        }

        if ($request->livelihoods) {
            foreach ($request->livelihoods as $livelihood) {
                Livelihood::create([
                    'livelihood' => Str::lower($livelihood['livelihood']),
                    'profile_id' => $profile->id,
                ]);

            }
        }

        if ($request->skills) {
            foreach ($request->skills as $skill) {
                Skill::create([
                    'skill' => Str::lower($skill['skill']),
                    'profile_id' => $profile->id,
                ]);
            }
        }

        $this->storePhoto('solo', $request->solo, $profile->id);
        $this->storePhoto('family', $request->family, $profile->id);
        $this->storePhoto('household', $request->household, $profile->id);

        return response(201);
    }

    private function storePhoto($path, $photo, $userId)
    {
        $image_64 = $photo;
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = $userId . '.' . $extension;
        Storage::disk(`local/$path`)->put($imageName, base64_decode($image));
    }
}
