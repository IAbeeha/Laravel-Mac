<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;
use Illuminate\Validation\Rule;
use App\Models\Comment;


use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' =>  'required|string|email',
            'password' =>'required|string',
        ]);
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json('User not found');
        }
        return $this->createNewToken($token);
    }
  
   

 public function postIndex(Request $request)
{
    // error_log($request);
    // return response()->json($request);
    $perPage = 10;//$request->input('per_page', 10);
    $posts = Post::with('user')->orderBy('id', 'DESC')->paginate($perPage);
// error_log();
    // Loop through each post and add the likes and dislikes counts
    $postsWithLikesDislikes = $posts->map(function ($post) {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'user' => $post->user,
            'likes' => $post->likes()->where('liked', true)->count(),
            'dislikes' => $post->likes()->where('liked', false)->count(),
            'image_url'=> $post->image_url,
            'created_at'=> ($post->created_at)->format('F j, Y')
        ];
    });
    // error_log($postsWithLikesDislikes);
    return response()->json([
        'data'=>$postsWithLikesDislikes, 
    'total_pages'=>$posts->lastPage()]);
}

public function Myposts(Request $request)
{
    $user = auth()->user(); 

    $perPage = $request->input('per_page', 10); 
    $posts = Post::with('user')
        ->where('user_id', $user->id)
        ->paginate($perPage);

        $postsWithLikesDislikes = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'user' => $post->user,
                'likes' => $post->likes()->where('liked', true)->count(),
                'dislikes' => $post->likes()->where('liked', false)->count(),
                'image_url'=> $post->image_url,
                'created_at'=> ($post->created_at)->format('F j, Y')
            ];
        });
        return response()->json([
                'data'=>$postsWithLikesDislikes, 
            'total_pages'=>$posts->lastPage()]);
}


    public function createPost(Request $request)
    {
        $validate_fields = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image_url'=> 'required'
        ]);
        $validate_fields['title'] = strip_tags($validate_fields['title']);
        $validate_fields['body'] = strip_tags($validate_fields['body']);
        $validate_fields['image_url'] = strip_tags($validate_fields['image_url']);
        $validate_fields['user_id'] = auth()->id();
        Post::create($validate_fields);
        return response()->json("CREATED BLOG ");
    }

    public function register(Request $request) {
        if (User::where('email', $request['email'])->first()) {
            return "Email already taken";
        } elseif (strlen($request['password']) < 5) {
            return "The length of the password should be atleast 5 characters.";
        } else {
            $validate_fields = $request->validate([
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'password' => ['required', 'min:4']
            ]);
            $validate_fields['password'] = bcrypt($validate_fields['password']);
            $user = User::create($validate_fields);
            auth()->login($user);
            return "user created";
        }
    }
    
    public function deletePost(Request  $request)
    {
        $post = Post::where('id', $request["id"])->first();
        if($post)
        {
            if ($post->user_id != auth()->user()->id) 
            {
                return response()->json(['error' => 'Only owner can delete his post']);
            }

            if($post->delete()) 
            {
                return response()->json(['message' => 'Post deleted successfully']);
            }
        }
        else
            {
             return response()->json(['message' => 'Post not found']);
            }

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function userProfile() {
        return response()->json(auth()->user());
    }

    public function postEdit(Post $post, Request $request)
    {        
      
        if (auth()->user()->id != $post['user_id'] ) {
            return response()->json(['message' => 'only owner can edit a post']);
        }

        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        if ($request['image_url']==null)
       { 
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
       } 
       else {
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['image_url'] = strip_tags($request['image_url']);
       }
        $post->update($incomingFields);
        return response()->json(['message' => 'post updated']);
    }
    public function getPost(Post $post){
        $postWithComments = Post::with('comments')->find($post->id);

        // Transform post data
        $final = [
            'id' => $postWithComments->id,
            'title' => $postWithComments->title,
            'body' => $postWithComments->body,
            'user' => $postWithComments->user,
            'likes' => $postWithComments->likes()->where('liked', true)->count(),
            'dislikes' => $postWithComments->likes()->where('liked', false)->count(),
            'image_url'=> $postWithComments->image_url,
            'comments' => $this->transformComments($postWithComments->comments),
        ];
    
        return response()->json($final);
    }
   
    private function transformComments($comments) {
        return $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'user' => $comment->user,
                'created_at' => $comment->created_at->format('F j, Y')

                // Add any other fields you want to include in the response
            ];
        });
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function like(Post $post)
{
    // error_log($post);
    $user = auth()->user();
    $like = $user->likes()->where('post_id', $post->id)->first();
    // error_log();
    if ($like) {
        if ($like->liked==1){
            $like->delete();
            return response()->json(['message' => 'post unliked']);

        }
        else {
            $like->update(['liked' => true]);
        }
    } else {
        $user->likes()->create([
            'post_id' => $post->id,
            'liked' => true,
        ]);
    }
    return response()->json(['message' => 'post liked']);
}

public function dislike(Post $post)
{
    $user = auth()->user();
    $like = $user->likes()->where('post_id', $post->id)->first();

    if ($like) {
        if ($like->liked==0){
            $like->delete();
            return response()->json(['message' => 'dislike removed']);
        }
        else {
        $like->update(['liked' => false]);
        }
    } else {
        $user->likes()->create([
            'post_id' => $post->id,
            'liked' => false,
        ]);
    }
    return response()->json(['message' => 'post disliked']);
}


public function search(Request $request)
{
    $searchTerm = $request->input('search');
    
    // Perform the search in the database
    $posts = Post::where('title', 'like', '%' . $searchTerm . '%')->get();
    $postsWithLikesDislikes = $posts->map(function ($post) {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'user' => $post->user,
            'likes' => $post->likes()->where('liked', true)->count(),
            'dislikes' => $post->likes()->where('liked', false)->count(),
            'image_url'=> $post->image_url,
            'created_at'=> ($post->created_at)->format('F j, Y'),
        ];
    });
    return response()->json($postsWithLikesDislikes);
}
public function create_comment(Request $request, $postId)
    {
        $post = Post::find($postId);
        // error_log($post);
        $comment = new Comment();
        $comment->user_id = auth()->user()->id; // Assuming you're using Laravel's built-in authentication
        $comment->body = $request->input('body');
        error_log($request);
        $post->comments()->save($comment);
        return  response()->json(['message' => 'comment created sucessfully']);
    }


}
