<html>
<head>
    <title></title>
</head>
<body>
<center>
    <form action="<?php echo e(url('/wechat/do_push')); ?>" method="post">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="tagid" value="<?php echo e($tagid); ?>">
        消息：
        <textarea name="message" id="" cols="30" rows="10"></textarea>
        <br>
        <br>
        <input type="submit" value="提交">
    </form>
</center>
</body>
</html><?php /**PATH D:\wnnp\www\zxc\resources\views/Wechat/pushtag.blade.php ENDPATH**/ ?>