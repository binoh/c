<div id="select_nav" style='float:left;width:150px;'>
          <?php foreach($category as $value){?>
         <li value="<?php echo $value['term_taxonomy_id'];?>">
         	<a href='javascript:void();'>
         	<?php if(count($value) == 3){
         		$input=$value['term_id'] == $category_id?'<input type="checkbox" checked="checked">':'<input type="checkbox">';
         		echo $input.$value['name'];}
         		else{
         		echo $value['name'];
    	 		}?></a>
         	<?php if(isset($value['son'])){?>
	         <ul>
				 <?php foreach($value['son'] as $value2){?>
		         <li value="<?php echo $value2['term_taxonomy_id'];?>">
		         <a href='javascript:void();'><?php
		         $input2=$value2['term_id'] == $category_id?'<input type="checkbox" checked="checked">':'<input type="checkbox">';
		         echo $input2.$value2['name'];
		         ?></a>
		         </li>
	         	<?php }?>
	         </ul>
			<?php }}?>
		 </li>
</div>
<script src="/common/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/common/ckfinder/ckfinder.js" type="text/javascript"></script>
<div id="man_zone">
<form action="wen_do.php?do=a_edit" name="text" method="post">
  <table width="99%" border="0" align="center"  cellpadding="3" cellspacing="1" class="table_style">
    <tr>
      <td width="24%" class="left_title_1">文章标题</td>
      <td width="25%" class="">
      <input name="a_title" type="text" value="" /></td>
      <input name="category_id" type="hidden" value="<?php echo $category_id;?>" />
       <td width="24%" class="left_title_1"></td>
       <td width="25%" class=""></td>    
    </tr>
    
    <tr>
     <td colspan="4"><textarea name="con" class="ckeditor">
					</textarea></td>
    </tr>
    <script type="text/javascript">
    if( CKEDITOR.instances['con'] ){ 
        CKEDITOR.remove(CKEDITOR.instances['con']); 
    }
    // 启用 CKEitor 的上传功能，使用了 CKFinder 插件
    var ckValue=CKEDITOR.replace( 'con', {
        filebrowserBrowseUrl        : '/common/ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl   : '/common/ckfinder/ckfinder.html?Type=Images',
        filebrowserFlashBrowseUrl   : '/common/ckfinder/ckfinder.html?Type=Flash',
        filebrowserUploadUrl   : '/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl   : '/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl   : '/common/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
    });
   //console.log(CKEDITOR.instances['con']);
</script>
        <tr><td colspan="4">
        <a href="javascript:void(0);" id='submit'>更新文章</a><div id='message'></div>
        </td>
        </tr>
  </table>
  </form>
  </div>
  <script>
 $("#select_nav").treeview({
      		persist: "location",
      		collapsed: false,
      		unique: true
      	});
$('#submit').click(function(){
			var title=$("input[name='a_title']").val();
			//var content=$("textarea[name='con']").val();
			var content=ckValue.document.getBody().getHtml();
			var category_id=$("input[name='category_id']").val();
			//alert(content);return;
			$("#message").html("文章正在上传中...");
			var data='{"title":"'+encodeURI(title)+'","content":"'+encodeURI(content)+'","category_id":"'+category_id+'"}';
			var url="<?php echo DIR;?>/content/addContent";
			$.ajax({
				type:'post',
				data:'data='+data,
				url:url,
				//dataType:'json',
				success:function(data){
					if(data){
						$("#message").html('上传成功！');
					}else{
						$("#message").html('数据传输失败，请重新上传！');
					}
				}
				});
});

	</script>