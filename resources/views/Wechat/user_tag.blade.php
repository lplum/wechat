<table border="1" align="center">
	<tr>
		<td>粉丝OPENID</td>
		<td>返回标签页</td>
	</tr>
	@foreach($data as $v)
        <tr>
            <td>{{$v}}</td>
            <td><a href="{{url('/wechat/taglist')}}">标签页</a></td>
        </tr>
        @endforeach
</table>