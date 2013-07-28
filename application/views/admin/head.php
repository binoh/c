<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>B2C商城</title>
<link rel="stylesheet" href="<?php echo TEMPROOT;?>/css/common.css" type="text/css" />
<title>管理导航区域</title>
</head>
<script  type="text/javascript">
var preClassName = "man_nav_1";
function list_sub_nav(Id,sortname){
   if(preClassName != ""){
      getObject(preClassName).className="bg_image";
   }
   if(getObject(Id).className == "bg_image"){
      getObject(Id).className="bg_image_onclick";
      preClassName = Id;
	  showInnerText(Id);
	  window.top.frames['leftFrame'].outlookbar.getbytitle(sortname);
	  window.top.frames['leftFrame'].outlookbar.getdefaultnav(sortname);
   }
}

function showInnerText(Id){
    var switchId = parseInt(Id.substring(8));
	var showText = "对不起没有信息！";
	switch(switchId){
	    case 1:
		   showText =  "欢迎进入商城后台管理系统!";
		   break;
	    case 2:
		   showText =  "内容管理";
		   break;
	    case 3:
		   showText =  "内容维护";
		   break;		   
	    case 4:
		   showText =  "会员列表";
		   break;	
	   	   		   
	}
	getObject('show_text').innerHTML = showText;
}
 //获取对象属性兼容方法
 function getObject(objectId) {
    if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId);
    } else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId);
    } else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
    } else {
	return false;
    }
}
</script>
</head>

<body>
<div id="nav">
<ul>
<a href="<?php echo DIR;?>/main/pageSwitch?act=main" target="mainFrame"><li id="man_nav_1"  onclick="list_sub_nav(id,'后台首页')"  class="bg_image_onclick">后台首页</li></a>
<a href="<?php echo DIR;?>/content/pageSwitch" target="leftFrame"><li id="man_nav_2" onclick="list_sub_nav(id,'内容管理')"  class="bg_image">内容管理</li></a>
<a href="order.php" target="mainFrame"><li id="man_nav_3" onclick="list_sub_nav(id,'内容维护')"  class="bg_image">内容维护</li></a>
<a href="<?php echo DIR;?>/member/getMember" target="mainFrame"><li id="man_nav_4" onclick="list_sub_nav(id,'会员列表')"  class="bg_image">会员列表</li></a>
</ul>
</div>
<div id="sub_info">
&nbsp;&nbsp;<img src="<?php echo TEMPROOT;?>/images/hi.gif" />&nbsp;<span id="show_text">欢迎进入商城后台管理系统!</span>
</div>



</body>
</html>
