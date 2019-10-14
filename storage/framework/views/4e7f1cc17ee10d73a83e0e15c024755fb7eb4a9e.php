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
			<td><button type="button" id='btn'>微信授权登陆</button></td>
		</tr>
	</table>
	<script src="<?php echo e(asset('/js/jquery-3.2.1.min.js')); ?>"></script>
	<script type="text/javascript">
		$(function(){
			$('#btn').click(function(){
				window.location.href="<?php echo e(url('/wechat/wlogin')); ?>";
			});
		});

	</script>
</body>
</html><?php /**PATH D:\WW\www\zxc\resources\views/Wechat/login.blade.php ENDPATH**/ ?>