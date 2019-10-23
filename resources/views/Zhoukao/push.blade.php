<html>
<head>
    <title></title>
</head>
<body>
<center>
    <form action="{{url('/zhou/do_push')}}" method="post">
        @csrf
        <input type="hidden" name="openid" value="{{$openid_list}}">
        消息：
        <textarea name="message" id="" cols="30" rows="10"></textarea>
        <br>
        <br>
        <input type="submit" value="提交">
    </form>
</center>
</body>
</html>