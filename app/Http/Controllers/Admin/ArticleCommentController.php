<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleComment::with(['article', 'user'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $comments = $query->paginate(30);

        $stats = [
            'total' => ArticleComment::count(),
            'pending' => ArticleComment::where('status', 'pending')->count(),
            'approved' => ArticleComment::where('status', 'approved')->count(),
            'rejected' => ArticleComment::where('status', 'rejected')->count(),
        ];

        return view('admin.article-comments.index', compact('comments', 'stats'));
    }

    public function approve(ArticleComment $comment)
    {
        $comment->approve();
        return back()->with('success', 'Comment approved!');
    }

    public function reject(ArticleComment $comment)
    {
        $comment->reject();
        return back()->with('success', 'Comment rejected.');
    }

    public function destroy(ArticleComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
