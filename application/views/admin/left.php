<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>左侧导航栏</title>
<link rel="stylesheet" href="<?php echo TEMPROOT;?>/css/common.css" type="text/css" />
</head>
<body>
<div id="left_content">
     <div id="user_info">欢迎您，<strong>ADMIN</strong><br />[<a href="../action/ajax.php?do=out" target="_parent" onclick="return confirm('确定要退出商城管理后台吗?');">&nbsp;&nbsp;退出&nbsp;&nbsp;</a>]</div>
	<div id="main_nav">
	     <div id="left_main_nav">
         <ul><li class="left_back">常用操作</li></ul>
         <ul><li class="left_back">文章管理</li></ul>
         <ul><li class="left_back">会员管理</li></ul>
         <ul><li class="left_back">权限管理</li></ul>
         <ul><li class="left_back">退出登陆</li></ul>
         <ul><li class="left_back"></li></ul>
         </div>
		 <div id="right_main_nav">
         <div class="list_tilte"><span>商品管理</span></div>
         <div class="list_detail"><ul><li><a href="goods.php?goods=add" target="mainFrame">添加新商品</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="goods.php?goods=cat" target="mainFrame">商品分类</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="goods.php?goods=brand" target="mainFrame">商品品牌</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="order.php?order=list" target="mainFrame">订单列表</a></li></ul></div>
         <div class="list_tilte"><span>信息管理</span></div>
         <div class="list_detail"><ul><li><a href="wen.php?wen=list" target="mainFrame">文章列表</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="wen.php?wen=cat" target="mainFrame">文章分类</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="wen.php?wen=pub" target="mainFrame">信息发布</a></li></ul></div>
          <div class="list_tilte"><span>会员管理</span></div>
         <div class="list_detail"><ul><li><a href="user.php?user=list" target="mainFrame">会员列表</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="user.php?user=change" target="mainFrame">填加会员</a></li></ul></div>
          <div class="list_tilte"><span>权限管理</span></div>
         <div class="list_detail"><ul><li><a href="super.php?super=list" target="mainFrame">管理员列表</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="super.php?super=add" target="mainFrame">填加管理员</a></li></ul></div>
         <div class="list_detail"><ul><li><a href="#">管理员日志</a></li></ul></div>
         </div>
	 </div>
</div>
</body>
</html>
