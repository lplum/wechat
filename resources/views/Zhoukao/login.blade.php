<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<table>
		<tr>
			<td>用户名</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="pwd"></td>
		</tr>
		<tr>
			<td>第三方</td>
			<td><button type="button" id='tn'>微信授权登陆</button></td>
		</tr>
	</table>
	<script src="{{asset('/js/jquery-3.2.1.min.js')}}"></script>
	<script type="text/javascript">
		$(function(){
			$('#tn').click(function(){
				window.location.href="{{url('/zhou/wlogin')}}";
			});
		});

	</script>
</body>
</html>