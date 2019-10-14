<!DOCTYPE html>
<html>
<head>
    <title>微信上传素材</title>
</head>
<body>
<center>

    <form action="<?php echo e(url('wechat/doupload')); ?>" class="all" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        素材类型: <select name="up_type">
            <option value="1">临时素材</option>
            <option value="2">永久素材</option>
        </select><br />
        <span>上传临时/永久图片</span>
        文件：<input type="file" name="image" value=""><br/><br/>
        <span>上传临时/永久语音</span>
        文件：<input type="file" name="voice" value=""><br/><br/>
        <span>上传临时/永久视频</span>
        文件：<input type="file" name="video" value=""><br/><br/>
        <span>上传临时/永久缩略图</span>
        文件：<input type="file" name="thumb" value=""><br/><br/>
        <input type="submit" name="" value="提交">
    </form>
</center>


</body>
</html><?php /**PATH D:\WW\www\zxc\resources\views/wechat/upload_sucai.blade.php ENDPATH**/ ?>