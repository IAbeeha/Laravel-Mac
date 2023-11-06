<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;
use Illuminate\Validation\Rule;


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
    $posts = Post::with('user')->paginate($perPage);

    // Loop through each post and add the likes and dislikes counts
    $postsWithLikesDislikes = $posts->map(function ($post) {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'user' => $post->user,
            'likes' => $post->likes()->where('liked', true)->count(),
            'dislikes' => $post->likes()->where('liked', false)->count(),
            'image_url'=> $post->image_url

        ];
    });
    // error_log($postsWithLikesDislikes);
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
        $final= [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'user' => $post->user,
                'likes' => $post->likes()->where('liked', true)->count(),
                'dislikes' => $post->likes()->where('liked', false)->count(),
                'image_url'=> $post->image_url
            ];
        // error_log($final);

        return response()->json($final);
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

    if ($like) {
        $like->update(['liked' => true]);
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
        $like->update(['liked' => false]);
    } else {
        $user->likes()->create([
            'post_id' => $post->id,
            'liked' => false,
        ]);
    }
    return response()->json(['message' => 'post disliked']);
}



}
