<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request) {
        // Näitame ainult avalikke postitusi
        $posts = Post::with(['author', 'category', 'tags'])->public()->latest('published_at')->paginate(5)->withQueryString();

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug) {
        $post = Post::with(['author', 'category', 'tags'])->public()->where('slug', $slug)->firstOrFail();

        // Otsib üles kommentaarid, mis on seotud selle postitusega (aint heakskiidetud)
        $comments = $post->comments()->approved()->with('author')->latest()->paginate(5);

        return view('blog.show', compact('post', 'comments'));
    }

}
