<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/common/js/jquery.treeview.css" />
<script src="/common/js/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="/common/js/jquery.treeview.js" type="text/javascript"></script>
<script src="/common/js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="/common/js/highstock.js" type="text/javascript"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<title>后台首页</title>

</head>
<body>
	<div class='top' style='width:100%;height:100px;'>
	top
	</div>
	<div class='body'>
		<div class='left' id='menu' style='width:150px;height:auto;float:left;'>
		<li id='content'>填加内容</li></br>
		<li id='category'>栏目管理</li></br>
		<li id='statistics'>网站统计</li></br>
		<li id='member'>会员管理</li></br>
		<li id='comment'>评论管理</li></br>
		</div>
		<div class='right' style='width:800px;height:auto;float:left;'>
		right
		</div>
	</div>
<script>
$('#menu').children('li').each(function(index,element){
	var id=$(element).attr('id');
	switch(id){
	case 'content':
		var where = 'content';
		break;
	case 'category':
		var where = 'termall';
		break;
	case 'statistics':
		var where = 'statistics';
		break;
	case 'member':
		var where = 'member';
		break;
	case 'comment':
		var where = 'comment';
		break;
	default:
		var where = '';
	break;
	};
	
	$(element).bind('click',function(){
		var url=encodeURI("<?php echo DIR;?>/"+where+"/mainShow");
		$.ajax({
			type:'get',
			url:url,
			dataType:'json',
			success:function(data){
				$('.right').html(data);
			}
			});
	});
});
</script>
</body>
</html>
