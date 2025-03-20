<?php

namespace App\Http\Controllers;

use App\Models\Hub;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HubController extends Controller
{
    public function index(Request $request)
    {
        $hubs = Hub::orderByDesc('followers_count')
            ->paginate(
                perPage: $request->get('per_page', 20), // Default 20 per page
                page: $request->get('page', 1), // Default page 1
            );

        // Handle AJAX requests for load more functionality
        if ($request->ajax()) {
            $html = view('hubs.partials.hub-list', compact('hubs'))->render();
            $nextPage = $hubs->nextPageUrl();
            return response()->json([
                'html' => $html,
                'nextPage' => $nextPage,
            ]);
        }

        return view('hubs.index', compact('hubs'));
    }

//    public function show($slug)
//    {
//        $hub = Hub::where('slug', $slug)->firstOrFail();
//        $feedPosts = $hub->posts()->latest()->paginate(10);  // Adjust per page as needed
//
//        return view('hubs.show', compact('hub', 'feedPosts'));
//    }

    public function show($slug)
    {
        $hub = Hub::where('slug', $slug)->firstOrFail();
        $sort = request()->get('sort', 'popular');

        $posts = $hub->posts();

        // Apply sorting
        switch ($sort) {
            case 'new':
                $posts = $posts->latest();
                break;
            case 'controversial':
                // For "controversial" - posts with similar amounts of likes and dislikes
                $posts = $posts->withCount(['up_votes', 'down_votes'])
                    ->orderByRaw('ABS(likes_count - dislikes_count) ASC')
                    ->orderByRaw('(likes_count + dislikes_count) DESC');
                break;
            case 'popular':
            default:
                // "Popular" or trending algorithm - recent posts with high engagement
                $posts = $posts->selectRaw('(vote_count + comment_count) / POW(EXTRACT(EPOCH FROM (NOW() - created_at))/3600 + 2, 1.5) as popularity_score, posts.*')
                ;
        }

        $feedPosts = $posts->paginate(20);

        return view('hubs.show', compact('hub', 'feedPosts', 'sort'));
    }

}
