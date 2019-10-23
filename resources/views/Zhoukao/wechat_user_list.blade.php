<html>
    <head>
        <title>用户列表</title>
    </head>
    <body>
        <center>
            <form method="post" action="{{url('/zhou/push')}}">
                @csrf
                <input type="submit" value="提交">
                <table border="1">
                    <tr>
                        <td></td>
                        <td>用户昵称</td>
                        <td>用户openid</td>
                    </tr>
                    @foreach($data as $v)
                    <tr>
                        <td><input type="checkbox" name="openid_list[]" value="{{$v['openid']}}"></td>
                        <td>{{$v['nickname']}}</td>
                        <td>{{$v['openid']}}</td>
                    </tr>
                    @endforeach
                </table>
            </form>
        </center>
    </body>
</html>