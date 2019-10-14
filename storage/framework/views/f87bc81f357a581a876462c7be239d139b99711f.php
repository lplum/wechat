<html>
    <head>
        <title>用户列表</title>
    </head>
    <body>
        <center>
            <table border="1">
                <tr>
                    <td>昵称</td>
                    <td>用户openid</td>
                </tr>
                <?php $__currentLoopData = $info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td></td>
                        <td><?php echo e($v); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </center>
    </body>
</html><?php /**PATH D:\WW\www\zxc\resources\views/Wechat/wechat_user_list.blade.php ENDPATH**/ ?>