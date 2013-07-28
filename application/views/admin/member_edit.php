<form>
<input type='hidden' value="<?php echo $member['ID']?>" name='id'>
<input type='text' value="<?php echo $member['display_name']?>" name='display_name'>
<input type='text' value="<?php echo $member['user_login']?>" name='user_login'>
<input type='text' value="<?php echo $member['user_email']?>" name='user_email'>
用户状态：
<input type='radio' value="0" name='user_status' <?php echo $member['user_status']?'checked="checked"':''; ?>>使用
<input type='radio' value="1" name='user_status' <?php echo $member['user_status']?'checked="checked"':'';?>>冻结
新密码:<input type='password' value="" name='pw1'>
新密码:<input type='password' value="" name='pw2'>
<select>
		<option>文章管理员</option>
		<option>文章管理员</option>
		<option>超级管理员</option>
</select>
<div id='mem_sub'>提交</div>
<div id='mem_message'></div>
</form>
<script>
$("#mem_sub").click(function(){
	var data=$('form').serialize();
	var url="<?php echo DIR;?>/member/memberEdit";
	$.ajax({
		url:url,
		data:data,
		dataType:'json',
		type:'post',
		success:function(e){
			var message=e?'更新成功':'更新失败';
			$("#mem_message").html(message);
		}
		});
	
	
});

</script>
