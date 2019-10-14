<html>
    <head>
        <title>用户列表</title>
    </head>
    <body>
        <center>
            <form method="post" action="{{url('/wechat/usertaglist')}}">
                @csrf
                <input type="submit" value="提交">
                <input type="hidden" value="{{$tagid}}" name="tagid">
                <table border="1">
                    <tr>
                        <td></td>
                        <td>用户昵称</td>
                        <td>用户openid</td>
                        <td>操作</td>
                    </tr>
                    @foreach($data as $v)
                    <tr>
                        <td><input type="checkbox" name="openid_list[]" value="{{$v['openid']}}"></td>
                        <td>{{$v['nickname']}}</td>
                        <td>{{$v['openid']}}</td>
                        <td>
                            <a href="{{url('wechat/get_user_detail',['openid'=>$v['openid']])}}">查看详情</a>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </form>
        </center>
    </body>
</html>