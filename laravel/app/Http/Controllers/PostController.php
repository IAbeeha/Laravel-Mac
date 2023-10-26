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
        return redirect('/');
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

    public function deletePost(Post $post)
    {
        if (auth()->user()->id === $post['user_id']) {
            $post->delete();
        }
        return redirect('/');
    }
}
