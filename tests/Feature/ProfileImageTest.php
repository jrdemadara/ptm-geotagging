<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileImageTest extends TestCase
{
    /**
     * Test fetching profile images with valid credentials.
     *
     * @return void
     */
    public function testFetchProfileImagesValid()
    {
        $profileId = 6; // Replace with a valid profile ID

        // Make a request to the endpoint
        $response = $this->postJson('/api/fetch-profile-images', [
            'client_secret' => 'd5b740b3b5d272bea86c19e694eb71b375ab7603e23872ede42b67aab52b868d',
            'profile_id' => $profileId,
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'profile_id' => $profileId,
                'soloImage' => 'data:image/jpeg;base64,' . base64_encode('dummy content'),
                'familyImage' => 'data:image/jpeg;base64,' . base64_encode('dummy content'),
                'householdImage' => 'data:image/jpeg;base64,' . base64_encode('dummy content'),
            ]);
    }

    /**
     * Test fetching profile images with invalid profile_id.
     *
     * @return void
     */
    // public function testFetchProfileImagesInvalidProfileId()
    // {
    //     // Mock environment variables
    //     config(['app.client_secret' => 'your_client_secret']);

    //     // Make a request with an invalid profile_id
    //     $response = $this->postJson('/api/fetch-profile-images', [
    //         'client_secret' => 'your_client_secret',
    //         'profile_id' => 'invalid_profile_id',
    //     ]);

    //     // Assert unauthorized access
    //     $response->assertStatus(401)
    //         ->assertJson([
    //             'message' => 'invalid credentials',
    //             'error' => 'UNAUTHORIZED',
    //         ]);
    // }

    /**
     * Test fetching profile images with missing client_secret.
     *
     * @return void
     */
    // public function testFetchProfileImagesMissingClientSecret()
    // {
    //     // Make a request without client_secret
    //     $response = $this->postJson('/api/fetch-profile-images', [
    //         'profile_id' => 'valid_profile_id',
    //     ]);

    //     // Assert unauthorized access
    //     $response->assertStatus(401)
    //         ->assertJson([
    //             'message' => 'invalid credentials',
    //             'error' => 'UNAUTHORIZED',
    //         ]);
    // }
}
