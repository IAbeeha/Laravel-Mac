<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function createPost(Request $request)
    {
        $validate_fields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $validate_fields['title'] = strip_tags($validate_fields['title']);
        $validate_fields['body'] = strip_tags($validate_fields['body']);
        $validate_fields['user_id'] = auth()->id();
        Post::create($validate_fields);
        return "created";
    }

    public function editPage(Post $post)
    {
        if (auth()->user()->id !== $post['user_id']) {
            return redirect('/');
        }
        return view('edit_post', ['post' => $post]);
    }


    public function postEdit(Post $post, Request $request)
    {
        if (auth()->user()->id !== $post['user_id']) {
            return redirect('/');
        }

        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);
        return redirect('/');
    }

    // public function deletePost(Post $post)
    // {
    //     if (auth()->user()->id === $post['user_id']) {
    //         $post->delete();
    //     }
    //     return redirect('/');
    // }
    public function postIndex()
    {
        $posts = Post::with('user')->get();
    
        // Loop through each post and add the likes and dislikes counts
        $postsWithLikesDislikes = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user' => $post->user,
                'likes' => $post->likes()->where('liked', true)->count(),
                'dislikes' => $post->likes()->where('liked', false)->count()
            ];
        });
        return response()->json($postsWithLikesDislikes);
    }
}
