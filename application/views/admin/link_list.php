<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>评论列表</title>
<link rel="stylesheet" href="<?php echo TEMPROOT;?>/css/common.css" type="text/css" />
<link rel="stylesheet" href="<?php echo TEMPROOT;?>/css/goods_list.css" type="text/css" />
</head>
<body>
<div id="man_zone">
  <table width="99%" border="0" align="center"  cellpadding="3" cellspacing="1" class="table_style">
    <tr>
      <td width="25%" class="left_title_1">用户名</td>
       <td width="15%" class="left_title_1">用户邮箱</td>
       <td width="10%" class="left_title_1">评论时间</td>    
         <td width="10%" class="left_title_1">评论内容</td>
          <td width="10%" class="left_title_1">删除</td>
           <td width="10%" class="left_title_1">编辑</td>
    </tr>
    <?php foreach($link as $value){?>
    <tr>
    <td width="15%"><?php echo $value['comment_author']?></td>
    <td width="15%"><?php echo $value['comment_author_email'];?></td>
    <td width="10%"><?php echo $value['comment_date'];?></td>    
    <td width="10%"><?php echo $value['comment_content'];?></td>
    <td width="10%"><a href="user_do.php?do=del&u_id=<?php echo $value['comment_ID'];?>">删除</a></td>
    <td width="10%"><a href="user_do.php?do=edit&u_id=<?php echo $value['comment_ID'];?>">编辑</a></td>
    </tr>
    <?php }?>
    <tr>
     <td colspan="6"></td>
    </tr>
  </table>
</div>
</body>
</html>
