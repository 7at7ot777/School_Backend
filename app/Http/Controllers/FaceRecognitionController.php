<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaceRecognitionController extends Controller
{
    private $modelUrl = 'http://127.0.0.1:5000/';
    public function trainUsersWithAvatars()
    {
        // Get all users where avatar_url is not null
        $users = User::where('avatar_url','!=','')->get();
        foreach ($users as $user) {
            $data[] =
                [
                    'image_url' => $user->avatar_url,
                    'label' => $user->id,
                ];
        }
                // Make a POST request to the training endpoint
                 $response = Http::post('http://127.0.0.1:5000/train', $data);

                // Handle the response as needed
                if ($response->successful()) {
                    return response()->json(['success' =>'training complete'],200);

                } else {
                    // Training request failed
                    return response()->json(['error' =>'An error has occured'],400);
                }
    }

    public function detect(Request $request)
    {
        // Validate the request to ensure an image is present
        $request->validate([
            'image' => 'required|image|max:10240', // max 10MB
        ]);

        // Get the uploaded image
        $image = $request->file('image');

        // Create a Guzzle HTTP client
        $client = new Client();

        // Send the image to the Python API
        try {
            $response = $client->post($this->modelUrl.'detect', [
                'multipart' => [
                    [
                        'name'     => 'image',
                        'contents' => fopen($image->getPathname(), 'r'),
                        'filename' => $image->getClientOriginalName(),
                    ],
                ],
            ]);

            // Decode the JSON response
            $responseData = json_decode($response->getBody(), true);

            // Check if the response contains face labels
            if (isset($responseData['face_labels']) || $responseData['face_labels'] > 0) {
                    return response()->json([
                        'user_id' => $responseData['face_labels'][0]
                    ], 201);
            } else {
                return response()->json([
                    'message' => 'No face labels found in the response',
                    'response' => $responseData
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while communicating with the Python API',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

//    TODO: remove this function
    private function downloadAvatar($imageUrl)
    {
        // Make a GET request to the image URL
        $response = Http::get($imageUrl);

        // Check if the request was successful
        if ($response->successful()) {
            // Get the contents of the response (image data)
            $imageData = $response->body();

            // Generate a unique file name
            $fileName = uniqid() . '.jpg';

            // Store the image in the storage disk
            Storage::disk('public')->put($fileName, $imageData);

            // Return the file path
            return $fileName;
        } else {
            // If the request was not successful, return null
            return null;
        }


    }
}
