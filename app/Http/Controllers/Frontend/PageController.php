<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function pages($slug)
    {
        try {
            $page = Page::where('slug', $slug)->first();
            return view('frontend.pages.index',compact('page'));
        } catch (\Exception $th) {
            abort(404);
        }
    }
}
