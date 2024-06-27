<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortenedUrl;
use Inertia\Inertia;

class DashboardController extends Controller
{
    
    public function index(){
        return Inertia::render('Dashboard/index');
    }


    public function redirectToUrl(String $hash){

        try{
            
            $domain = config('app.url');
            $shortened_url = $domain . '/' . $hash;
         
            $original_url = ShortenedUrl::where('shortened_url', $shortened_url)->select('original_url')->firstOrFail();

            return redirect()->away($original_url->original_url);

        }catch(\Exception $ex){
            \Log::error($ex);
        }


        return redirect()->route('home');

    }
}
