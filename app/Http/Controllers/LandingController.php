<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the landing page.
     */
    public function index()
    {
        // Get featured articles
        $featuredArticles = \App\Models\Article::published()
            ->featured()
            ->with(['user', 'categories'])
            ->take(3)
            ->get();
        
        // Get latest articles
        $latestArticles = \App\Models\Article::published()
            ->with(['user', 'categories'])
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();
        
        return view('pages.landing', compact('featuredArticles', 'latestArticles'));
    }

    /**
     * Store a contact message.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
