<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Resources\V2\LanguageCollection;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    public function getList(Request $request)
    {
        return Cache::rememberForever('app.languages', function () {
            return new LanguageCollection(Language::all()); 
        });
    }
}
