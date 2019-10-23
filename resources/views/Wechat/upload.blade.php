<html>
<head>
    <title>素材列表</title>
</head>
<body>
<center>
    <form action="{{url('wechat/doupload')}}" method="post" enctype="multipart/form-data">
        @csrf
        <select name="type" id="">
            <option value="image">图片</option>
            <option value="voice">音频</option>
            <option value="video">视频</option>
            <option value="thumb">缩略图</option>
        </select>
        <input type="file" name="file_name" value="">
        <input type="submit" value="提交">
    </form>
</center>
</body>
</html>