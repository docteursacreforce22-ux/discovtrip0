<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    // ── Liste des articles ───────────────────────────────────
    public function index(Request $request)
    {
        $category = $request->input('category');

        $posts = BlogPost::published()
            ->when($category, fn ($q) => $q->byCategory($category))
            ->with('author')
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Cache::remember('blog.categories', 3600, fn () =>
            BlogPost::published()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
        );

        $recentPosts = Cache::remember('blog.recent', 1800, fn () =>
            BlogPost::published()->orderByDesc('published_at')->limit(4)->get()
        );

        return view('pages.blog.index', compact('posts', 'categories', 'recentPosts', 'category'));
    }

    // ── Article unique ───────────────────────────────────────
    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        // Incrémenter les vues (sans cache — compteur réel)
        $post->increment('views_count');

        // Articles similaires (même catégorie, sauf lui-même)
        $related = Cache::remember('blog.related.' . $post->id, 1800, fn () =>
            BlogPost::published()
                ->where('id', '!=', $post->id)
                ->where('category', $post->category)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get()
        );

        // Offres liées (suggestions de réservation en bas d'article)
        $suggestedOffers = Cache::remember('blog.offers.featured', 3600, fn () =>
            Offer::published()->where('is_featured', true)->with('city')->limit(3)->get()
        );

        return view('pages.blog.show', compact('post', 'related', 'suggestedOffers'));
    }
}