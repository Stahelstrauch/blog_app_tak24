<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    public function index() {
        $needsAction = Post::with('author')->needsAction()->latest('updated_at')->get();

        $scheduled = Post::with('author')->scheduled()->orderby('published_at')->get();

        $recent = Post::with('author')->published()->latest()->get();

        $pendingComments = Comment::with(['author', 'post'])->where('status', 'pending')->latest()->get();

        $archivedPosts = Post::with('author')->where('status', 'archived')->latest()->get();

        $orphanComments = Comment::with(['author', 'post' => fn($q) => $q->withTrashed()])->where(function ($q) {
        $q->orWhereHas('post', fn($qq) => $qq->onlyTrashed());})->latest()->get();

        $trashedPosts = Post::with('author')->onlyTrashed()->latest()->get();

        return view('admin.dashboard', compact('needsAction', 'scheduled', 'recent', 'pendingComments', 'archivedPosts', 'orphanComments', 'trashedPosts'));    

    }

    public function updateStatus(Request $request, Comment $comment) {
        // Moderaatoril peab olema õigus uuendada
        $this->authorize('update', $comment);

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'hidden', 'spam'])],
        ]);
        
        $comment->update(['status' => $data['status']]);

        return back()->with('status', 'Staatus uuendatud: '.$data['status']);
    }
}
