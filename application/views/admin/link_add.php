<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link rel="stylesheet" href="{$temroot}css/common.css" type="text/css" />
<link rel="stylesheet" href="{$temroot}css/main.css" type="text/css" />
</head>

<body>
<div id="man_zone_main">
<form action="admin_do.php?do=bandadd" method="post" enctype="multipart/form-data">
  <table width="99%" border="0" align="center"  cellpadding="3" cellspacing="1" class="table_style">
    <tr>
      <td width="18%" class="left_title"><span class="left-title">品牌名称</span></td>
      <td width="81%"><input type="text" name="b_name" /></td>
    </tr>
    <tr>
      <td class="left_title">品牌网址</td>
      <td><input type="text" name="b_url" value="http://" /></td>
    </tr>
    <tr>
      <td class="left_title">品牌logo</td>
      <td><input type="file" name="file" /></td>
     
    </tr>
   
    <tr>
      <td class="left_title">品牌描述</td>
      <td><textarea name="des">
           </textarea></td>
    </tr>
    <tr>
      <td class="left_title">是否显示</td>
      <td><input type="radio" value="1" name="brand_show" checked="checked" />是<input type="radio" value="1" name="brand_show" />否</td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="b_submit" value="提交" /></td>
      
    </tr>
  </table>
  </form>
</body>
</html>
