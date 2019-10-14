<html>
<head>
    <title></title>
</head>
<body>
<center>
    <h1>公众号标签管理</h1>
    <a href="<?php echo e(url('/wechat/addtag')); ?>">增加标签</a>
    <br>
    <br>
    <br>
    <table border="1">
        <tr>
            <td>tag_id</td>
            <td>tag_name</td>
            <td>标签下粉丝数</td>
            <td>操作</td>
        </tr>
        <?php $__currentLoopData = $info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($v['id']); ?></td>
            <td><?php echo e($v['name']); ?></td>
            <td><?php echo e($v['count']); ?></td>
            <td><a href="<?php echo e(url('wechat/deltag',['id'=>$v['id']])); ?>">删除</a> | <a href="<?php echo e(url('')); ?>">修改</a></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</center>
</body>
</html><?php /**PATH D:\WW\www\zxc\resources\views/Wechat/tag_list.blade.php ENDPATH**/ ?>