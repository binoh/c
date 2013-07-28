<div id="navigation" style='float:left;width:150px;'>
          <?php foreach($category as $value){?>
         <li value="<?php echo $value['term_taxonomy_id'];?>">
         	<a href='javascript:void();'><?php echo $value['name'];?></a>
         	<?php if(isset($value['son'])){?>
	         <ul>
				 <?php foreach($value['son'] as $value2){?>
		         <li value="<?php echo $value2['term_taxonomy_id'];?>">
		         <a href='javascript:void();'><?php echo $value2['name'];?></a>
		         </li>
	         	<?php }?>
	         </ul>
			<?php }}?>
		 </li>
</div>
         <div id='content_list' style='float:right;width:600px;'>
         </div>
         <script>
         $("#navigation").find('li').each(function(index,element){
             $(element).bind('click',function(){
                 var category_id=$(element).attr('value');
                 url=encodeURI("<?php echo DIR;?>/content/getContent?category_id="+category_id);
                 $.ajax({
                     type:'get',
                     url:url,
                     dataType:'json',
                     success:function(data){
                         $('#content_list').html(data);
                         $(element).parent().find('li').removeClass('selected');
                         $(element).addClass('selected');
                     }
                     });
             });
            
         });
         $("#navigation").treeview({
      		persist: "location",
      		collapsed: false,
      		unique: true
      	});
         </script>
