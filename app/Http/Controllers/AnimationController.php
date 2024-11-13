<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimationController extends Controller
{
    public function markAnimationShown(Request $request)
    {
        session(['birthday_animation_shown' => true]);
        // Set the session to expire after 1 day (24 hours)
        $request->session()->put('birthday_animation_shown_expiry', now()->addDay());
        return response()->json(['success' => true]);
    }
    
}
