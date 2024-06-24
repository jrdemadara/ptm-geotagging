<?php

namespace Tests\Integration;

use App\Services\MapboxService;
use Tests\TestCase;

class MapboxServiceIntegrationTest extends TestCase
{
    public function testReverseGeocodeWithRealMapboxApi()
    {
        // Coordinates for a location known to return a specific barangay
        $lat = 6.6466191;
        $lon = 124.6104502;

        // Instantiate the MapboxService
        $mapboxService = new MapboxService();

        // Call the method to test
        $barangayName = $mapboxService->reverseGeocode($lat, $lon);

        // Output the barangay name
        echo "\nBarangay Name: $barangayName\n";

        // Ensure the result is not null
        $this->assertNotNull($barangayName);

        // Optionally, if you know the expected barangay name, assert it
        // $this->assertEquals('Expected Barangay', $barangayName);
    }
}
