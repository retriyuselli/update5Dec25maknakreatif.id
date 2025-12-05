<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index()
    {
        $featuredPosts = Blog::where('is_featured', true)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $recentPosts = Blog::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $categories = Blog::where('is_published', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('blog.index', compact('featuredPosts', 'recentPosts', 'categories'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment views count
        $blog->increment('views_count');

        $relatedPosts = Blog::where('is_published', true)
            ->where('category', $blog->category)
            ->where('id', '!=', $blog->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('blog.detail', compact('blog', 'relatedPosts'));
    }

    /**
     * Display blog posts by category.
     */
    public function category($category)
    {
        $posts = Blog::where('is_published', true)
            ->where('category', $category)
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('blog.category', compact('posts', 'category'));
    }

    /**
     * Search blog posts.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $posts = Blog::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('excerpt', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->orWhereJsonContains('tags', $query);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('blog.search', compact('posts', 'query'));
    }
}
