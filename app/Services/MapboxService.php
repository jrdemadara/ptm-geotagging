<?php

namespace App\Services;

use GuzzleHttp\Client;

class MapboxService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.mapbox.com',
        ]);
    }

    public function reverseGeocode($lat, $lon)
    {
        $accessToken = 'pk.eyJ1Ijoiam9obm55cm9nZXJzIiwiYSI6ImNsZ2VsNjFwczA3enAzZXBzdXpmMTV3ZW8ifQ.WlDD82JvR3WQ3MS9I9Ldmw';
        $response = $this->client->request('GET', "/geocoding/v5/mapbox.places/$lon,$lat.json", [
            'query' => [
                'access_token' => $accessToken,
                'types' => 'locality',
                'language' => 'en',
                'limit' => 1,
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        // Extract the barangay name or relevant data from the response
        $barangay = isset($data['features'][0]['text']) ? $data['features'][0]['text'] : null;

        return $barangay;
    }
}
