<html>
    <head>
        <title>用户列表</title>
    </head>
    <body>
        <center>
            <form method="post" action="<?php echo e(url('/wechat/usertaglist')); ?>">
                <?php echo csrf_field(); ?>
                <input type="submit" value="提交">
                <input type="hidden" value="<?php echo e($tagid); ?>" name="tagid">
                <table border="1">
                    <tr>
                        <td></td>
                        <td>用户昵称</td>
                        <td>用户openid</td>
                        <td>操作</td>
                    </tr>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><input type="checkbox" name="openid_list[]" value="<?php echo e($v['openid']); ?>"></td>
                        <td><?php echo e($v['nickname']); ?></td>
                        <td><?php echo e($v['openid']); ?></td>
                        <td>
                            <a href="<?php echo e(url('wechat/get_user_detail',['openid'=>$v['openid']])); ?>">查看详情</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
            </form>
        </center>
    </body>
</html><?php /**PATH D:\wnnp\www\zxc\resources\views/Wechat/wechat_user_list.blade.php ENDPATH**/ ?>