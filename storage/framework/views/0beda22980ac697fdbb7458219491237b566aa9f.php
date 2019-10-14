<table border="1" align="center">
	<tr>
		<td>粉丝OPENID</td>
		<td>返回标签页</td>
	</tr>
	<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($v); ?></td>
            <td><a href="<?php echo e(url('/wechat/taglist')); ?>">标签页</a></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table><?php /**PATH D:\wnnp\www\zxc\resources\views/Wechat/user_tag.blade.php ENDPATH**/ ?>