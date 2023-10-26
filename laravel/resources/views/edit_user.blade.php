<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <h1>Edit User</h1>
  <form action="/edit_user/{{$user->id}}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{$user->name}}">
    <input type="email" name="email" value="{{$user->email}}">
     <input type="password" name="password" value="*****">

    <button>Save Changes</button>
  </form>
</body>
</html>