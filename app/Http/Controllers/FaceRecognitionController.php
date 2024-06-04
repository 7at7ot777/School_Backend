<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaceRecognitionController extends Controller
{
    public function trainUsersWithAvatars()
    {
        // Get all users where avatar_url is not null
        $users = User::where('avatar_url','!=','')->get();
        foreach ($users as $user) {
            $data[] =
                [
                    'image_url' => $user->avatar_url,
                    'label' => $user->id . "|" . $user->name,
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
