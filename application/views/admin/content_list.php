<a href='javascript:void(0);' id='addnews'>填加文章</a>
<div id="content_list">
  <table width="99%" border="0" align="center"  cellpadding="3" cellspacing="1" class="table_style">
    <tr>
          <td width="19%" class="left_title_1">文章标题</td>
          <td width="40%" class="left_title_1">文章简介</td>
           <td width="7%" class="left_title_1">点击数</td>
           <td width="13%" class="left_title_1">发布时间</td>            
          <td width="10%" class="left_title_1">删除</td>
           <td width="10%" class="left_title_1">编辑</td>
    </tr>
   
    <?php foreach($content as $value){?>
    <tr>
    <td width="19%"><?php echo $value['post_title'];?></td>
    <td width="40%"></td>
    <td width="7%"></td>
    <td width="13%"><?php echo $value['post_date'];?></td>    
   
    <td width="10%"><a class='del' value="<?php echo $value['ID'];?>">删除</a></td>
    <td width="10%"><a class='edit' value="<?php echo $value['ID'];?>">编辑</a></td>
    </tr>
    <?php }?>
    暂无数据
    <tr>
     <td colspan="7"></td>
    </tr>
  </table>
</div>
<script>
	$('.del').click(function(){
		var content_id=$(this).attr('value');
		var category_id=$(".selected").attr('value');
		var url=encodeURI("<?php echo DIR;?>/content/delContent?content_id="+content_id+"&category_id="+category_id);
		$.ajax({
			type:'get',
			url:url,
			dataType:'json',
			success:function(data){
				$('#content_list').html(data);
			}
			});
	});
	$('.edit').click(function(){
		var content_id=$(this).attr('value');
		var category_id=$(".selected").attr('value');
		var url=encodeURI("<?php echo DIR;?>/content/editContent?content_id="+content_id+"&category_id="+category_id);
		$.ajax({
			type:'get',
			url:url,
			dataType:'json',
			success:function(data){
				$('#content_list').html(data);
			}
			});
	});
	$('#addnews').click(function(){
		var category_id=$(".selected").attr('value');
		var url=encodeURI("<?php echo DIR;?>/content/addContentShow?category_id="+category_id);
		$.ajax({
			type:'get',
			url:url,
			dataType:'json',
			success:function(data){
				$('#content_list').html(data);
			}
			});
	});
</script>

