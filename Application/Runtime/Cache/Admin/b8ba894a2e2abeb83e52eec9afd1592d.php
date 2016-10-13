<?php if (!defined('THINK_PATH')) exit();?>
<?php if(is_array($tree)): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i; if(($pid) == $list['id']): ?><option value="<?php echo ($list["id"]); ?>" selected="selected"><?php echo str_repeat("&nbsp;",($list['depth']-1)*4);?>|- <?php echo ($list["name"]); ?></option> 
<?php else: ?>
<option value="<?php echo ($list["id"]); ?>" ><?php echo str_repeat("&nbsp;",($list['depth']-1)*4);?>|- <?php echo ($list["name"]); ?></option><?php endif; ?>
		<?php if(!empty($list['_child'])): echo R('Cms/treeselect', array($list['_child'])); endif; endforeach; endif; else: echo "" ;endif; ?>