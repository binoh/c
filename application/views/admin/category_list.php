<div id="category_list" style='float:left;width:150px;'>
          <?php foreach($category as $value){?>
         <li value="<?php echo $value['term_taxonomy_id'];?>">
         	<a href='javascript:void();'><?php echo $value['name'];?></a>
         	<p style='display: inline;'>更多</p>
         	<div style='display:none;'>
         	是否在导航显示：<input type='radio' name='show'>是<input type='radio' name='show'>否
         	外部链接:<input type='input' name='outurl' value='默认为空，代表内部栏目'>
         	</div>
         	<?php if(isset($value['son'])){?>
	        <ul id='category_son'>
				 <?php foreach($value['son'] as $value2){?>
		         <li value="<?php echo $value2['term_taxonomy_id'];?>" style='margin-left:20px'>
		         <a href='javascript:void();'><?php echo $value2['name'];?></a><p>更多</p>
		         <div style='display:none;'>
	         	是否在导航显示：<input type='radio' name='show'>是<input type='radio' name='show'>否
	         	外部链接:<input type='input' name='outurl' value='默认为空，代表内部栏目'>
	         	</div>
	         	<div class='add_term'>
				填加栏目
				</div>
		         </li>
	         	<?php }?>
	      </ul>
	       </li>
			<?php }
			else{
				echo $content='<ul id="category_son" class="">
				<div class="add_term son_add"  style="margin-left:20px;">填加栏目</div>
				</ul>
				';
				
			}
}?>
			<div class='add_term'>
				填加栏目
			</div>
</div>
	
  <p><a href='javascript:void(0);' id='update'>更新</a></p>
  <input type='text' value='abccc' id='in'>
  <div id='message'>消息</div>
  	
         <script>
         $("#category_list").sortable({
        	 placeholder: "ui-state-highlight"
             });
         $('#category_list').find('p').each(function(index,element){
             
             $(element).bind('click',function(){
                 //console.log(element);
			 $(element).nextAll('div').toggle();
             });
         });
        
        // $( "#category_list, #category_son" ).disableSelection();
         $("#category_list").disableSelection();
         function addInput(){
             $("#category_list").find('li').each(function(index,element){
             $(element).children('a:first').dblclick(function(){
             val=$(this).text();
             var html='<input type="text" value="'+val+'" name="">';
             $(element).children('a:first').html(html);
             $(element).children('a:first').children('input').focus().bind('blur',function(){
            	 val=$(this).val();
                 var html='<input type="text" value="'+val+'" name="">';
                 $(element).children('a:first').html(val);
             });
             });
           
         });
         }
         addInput();
        // $('#in').dbclick(function(){
            // $('#in').focus().bind('blur',function(){
				//$(this).val('aa');
       //  });
         //填加栏目事件
         $('.add_term').click(function(){
             var p_con='<li class="new_term" value="insert"><a href="javascript:void();">双击修改栏目名称</a></li>';
            // var s_con='<ul><li class="new_term" value="insert"><a href="javascript:void();">abc</a></li><ul>';
              // var  content=$(this).hasClass('son_add')?$(this).before(content):$(this).before(content);
            	 $(this).before(p_con);
        	 	 addInput();
         });
         //页面提交
         $('#update').click(function(){
        	 var data='{';
             $('#category_list').find('li').each(function(index,element){
                 var category=$(element).attr('value');
                 if(category =='insert' && $(element).parent().is("#category_son")){
                     var parent=$(element).parent().parent().attr('value');
                 }else{
                     var parent='0';
                 }
                 var name=$(element).children('a:first').text();
                 name=encodeURI(name);
                 data+='"'+category+'":{"term_group":"'+index+'","name":"'+name+'","parent":"'+parent+'"},';
             });
             var data=data.substr(0,data.length-1);
             data+='}';
             //console.log(data);return;
             var url="<?php echo DIR;?>/termall/updateTerm";
             var data_get='sort='+data;
             $.ajax({
                 type:'get',
                 data:data_get,
                 url:url,
                 dataType:'json',
                 success:function(e){
                     var mess=e?'更新成功':'更新失败';
                     $('#message').html(mess);
                 }
                 });
             });
        
        
         </script>
