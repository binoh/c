
<div id="man_zone">
  <table width="99%" border="0" align="center"  cellpadding="3" cellspacing="1" class="table_style">
    <tr>
      <td width="25%" class="left_title_1">用户名</td>
       <td width="15%" class="left_title_1">用户邮箱</td>
       <td width="10%" class="left_title_1">联系方式</td>    
         <td width="10%" class="left_title_1">用户状态</td>
          <td width="10%" class="left_title_1">删除</td>
           <td width="10%" class="left_title_1">编辑</td>
    </tr>
    <?php foreach($member as $value){?>
    <tr>
    <td width="15%"><?php echo $value['display_name']?></td>
    <td width="15%"><?php echo $value['user_email'];?></td>
    <td width="10%"><?php echo $value['user_url'];?></td>    
    <td width="10%"><?php echo $value['user_status'];?></td>
    <td width="10%"><a href="javascript:void(0);" class='m_del' value="<?php echo $value['ID'];?>">删除</a></td>
    <td width="10%"><a href="javascript:void(0);" class='m_edit' value="<?php echo $value['ID'];?>">编辑</a></td>
    </tr>
    <?php }?>
    <tr>
     <td colspan="6"></td>
    </tr>
  </table>
</div>
<script>
		$(".m_edit").click(function(){
			var id=$(this).attr('value');
			 var url="<?php echo DIR;?>/member/memberEditShow";
			 var data='id='+id;
			$.ajax({
				type:'get',
				dataType:'json',
				url:url,
				data:data,
				success:function(e){
					$('.right').html(e);
				}
				});

		});
</script>
