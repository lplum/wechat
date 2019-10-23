<html>
    <head>
        <title></title>
    </head>
    <body>
        <center>
            <table border="1">
                <tr>
                    <td>用户名</td>
                    <td>二维码图片</td>
                    <td>推广数量</td>
                    <td>操作</td>
                </tr>
                @foreach($user_info as $v)
                <tr>
                    <td>{{$v->name}}</td>
                    <td><img src="{{asset($v->qrcode_url)}}" alt="" width="150"></td>
                    <td>{{$v->share_num}}</td>
                    <td><a href="{{url('/wechat/create_qrcode')}}?uid={{$v->id}}">生成专属二维码</a></td>
                </tr>
                @endforeach
            </table>
        </center>
    </body>
</html>