<form action="/wechat/doaddtag" method="post">
	@csrf
	<table>
		<tr>
			<td>标签名</td>
			<td><input type="text" name="tag_name"></td>
		</tr>
		<tr>
			<td colspan="2"><button>添加</button></td>
		</tr>
	</table>
</form>