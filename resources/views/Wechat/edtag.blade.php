<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>修改标签</title>
</head>
<body>
<center>
    <form action="{{url('wechat/do_edit_tag')}}" method="post">
        @csrf
        <input type="hidden" name="tag_id" value="{{$id}}">
        标签名:<input type="text" name="name" value="{{$name}}">
        <input type="submit" value="提交">
    </form>
</center>

</body>
</html>