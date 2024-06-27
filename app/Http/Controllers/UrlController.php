<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrlRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\ShortenedUrl;
use Illuminate\Support\Str;
use Inertia\Inertia;


class UrlController extends Controller
{
    public function storeUrl(StoreUrlRequest $request){
        
        try{

            $original_url = $request->input('original_url');
            $shortened_url = $this->shortenUrl($original_url);
            
            $url_safety_information = $this->checkUrlSafety($original_url);

            if (!$url_safety_information['safe']) {
                return response()->json([
                    'message' => $url_safety_information['message']
                ], 400); // Return a 400 Bad Request if URL is not safe
            }

            ShortenedUrl::create([
                'original_url' => $original_url,
                'shortened_url' => $shortened_url
            ]);    

        }catch(\Exception $ex){
            \Log::error($ex);
            return response()->json([
                'message' => 'There was an error, please try again later!'
            ], 400);
        }

        return response()->json([
            'message' => 'The Url Was Successfully Shortened!',
            'shortened_url' => $shortened_url
        ], 200);
    }


    private function shortenUrl(String $original_url, int $length = 6){
        
        $domain = config('app.url');

        do {
            $alpha_numeric_digits = Str::random($length);
        } while (ShortenedUrl::where('shortened_url', $domain.'/'.$alpha_numeric_digits)->exists());

        return $domain.'/'.$alpha_numeric_digits;
    }


    private function checkUrlSafety(String $original_url){

    
        $google_api_key = config('app.google_safe_browsing_api_key');

        $response = Http::post("https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$google_api_key}", [
            'client' => [
                'clientId' => 'kristofer',
                'clientVersion' => '1.0'
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [
                    ['url' => $original_url]
                ]
            ]
        ]);
    
        if ($response->successful()) {
            $matches = $response->json();
            if (!empty($matches['matches'])) {
                return ['safe' => false, 'message' => 'The URL is not safe.'];
            }
            return ['safe' => true, 'message' => 'The URL is safe.'];
        }

        return ['safe' => false, 'message' => 'A problem occurred while checking the URL safety.'];

    }

}
