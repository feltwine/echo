<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $feedPosts = Post::with('user')
            ->selectRaw('(vote_count + comment_count) / POW(EXTRACT(EPOCH FROM (NOW() - created_at))/3600 + 2, 1.5) as popularity_score, posts.*')
            ->orderByDesc('popularity_score')
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('home.partials.feed-posts', compact('feedPosts'))->render();  // Fix path from "homes" to "home"
            $nextPage = $feedPosts->nextPageUrl();
            return response()->json([
                'html' => $html,
                'nextPage' => $nextPage,
            ]);
        }

        return view('home.index', compact('feedPosts'));
    }
}
