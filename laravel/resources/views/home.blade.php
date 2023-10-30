<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Document</title>
</head>

<body>
  @auth
  <p> congratulations you are logged in </p>

  <p><a href="/edit_user/{{Auth::user()->id}}">Edit User</a></p>
  <form action="/delete_user/{{Auth::user()->id}}" method="POST">
    @csrf
    @method('DELETE')
    <button>Delete User</button>
  </form>

  <form action="/logout" method="POST">
    @csrf
    <button> Log out </button>
  </form>

  <div style="border: 3px solid black;">
    <h2>Create a new Blog</h2>
    <form action="/create_post" method="POST">
      @csrf
      <input name="title" type="text" placeholder="title">
      <textarea name="body" placeholder="body......"></textarea>
      <button>Save Post</button>
    </form>
  </div>
  </form>


  <div style="border: 3px solid black;">
    <h2>All Posts</h2>
    @foreach($posts as $post)
    <div style="background-color: gray; padding: 10px; margin: 10px;">
      <h3>{{$post['title']}} by {{$post->user->name}} </h3>
      {{$post['body']}}
      <p><a href="/edit_post/{{$post->id}}">Edit Post</a></p>
      <form action="/delete_post/{{$post->id}}" method="POST">
        @csrf
        @method('DELETE')
        <button>Delete</button>
      </form>
    </div>
    @endforeach
  </div>
  @else
  <div style="border: 3px solid black;">
    <h2>Register</h2>
    <form action="/register" method="POST">
      @csrf
      <input name="name" type="text" placeholder="name">
      <input name="email" type="text" placeholder="email">
      <input name="password" type="password" placeholder="password">
      <button>Register</button>
    </form>
  </div>

  <div style="border: 3px solid black;">
    <h2>Log in</h2>
    <form action="/login" method="POST">
      @csrf
      <input name="email" type="email" placeholder="email">
      <input name="password" type="password" placeholder="password">
      <button>Log in</button>
    </form>
  </div>
  @endauth

</body>

</html>