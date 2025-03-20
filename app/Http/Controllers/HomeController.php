<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    // Method for displaying the popular posts
    public function popular(Request $request)
    {
        $feedPosts = $this->getPopularFeedPosts();

        if ($request->ajax()) {
            $html = view('home.partials.feed-posts', compact('feedPosts'))->render();
            $nextPage = $feedPosts->nextPageUrl();
            return response()->json([
                'html' => $html,
                'nextPage' => $nextPage,
            ]);
        }

        return view('home.index', compact('feedPosts'));
    }

    // Index method, checks if the user is logged in
    public function index(Request $request)
    {
        $feedPosts = $this->getPopularFeedPosts();

        // If the user is logged in, show the home page
        if (auth()->check()) {
            if ($request->ajax()) {
                $html = view('home.partials.feed-posts', compact('feedPosts'))->render();
                $nextPage = $feedPosts->nextPageUrl();
                return response()->json([
                    'html' => $html,
                    'nextPage' => $nextPage,
                ]);
            }

            return view('home.index', compact('feedPosts'));
        }
        if ($request->ajax()) {
            $html = view('home.partials.feed-posts', compact('feedPosts'))->render();
            $nextPage = $feedPosts->nextPageUrl();
            return response()->json([
                'html' => $html,
                'nextPage' => $nextPage,
            ]);
        }

        // If the user is not logged in, show the home page with popular posts
        return view('home.index', compact('feedPosts'));
    }

    // Private method to get the feed posts
    private function getPopularFeedPosts()
    {
        return Post::with('user')
            ->selectRaw('(vote_count + comment_count) / POW(EXTRACT(EPOCH FROM (NOW() - created_at))/3600 + 2, 1.5) as popularity_score, posts.*')
            ->orderByDesc('popularity_score')
            ->paginate(20);
    }
}

