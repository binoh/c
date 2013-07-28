<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台登陆</title>
<link href="../template/css/login.css" rel="stylesheet" type="text/css" />
<script src="../template/js/chai.js" type="text/javascript"></script>
<script src="../template/js/new.js" type="text/javascript"></script>
</head>
<body>
<div class="box w960">
       
       <div class="body left w960" style="margin-top:100px;">
               <div class="login">
               <form action="login.php" method="post">
                       <div class="user">
                             <div class="u_left">
                             <strong>帐&nbsp;&nbsp;号：</strong>
                             </div>
                             <div class="u_right">
                             <input name="username" type="text" class="input" />
                             </div>
                       </div>
                       <div class="user">
                              <div class="u_left">
                              <strong>密&nbsp;&nbsp;码：</strong>
                             </div>
                             <div class="u_right">
                             <input name="password" type="password" class="input" />
                             </div>
                       </div>
                       <div class="user">
                              <div class="u_left">
                              <strong>验证码：</strong>
                              </div>
                              <div class="yan_middle">
                              <input name="yan" type="text" size="5" maxlength="4" class="input2"  />
                         </div>
                              <div class="yan_right">
                              <img src="yan.php"/>
                              </div>
                       </div>
                       <div class="user">
                              <div class="den">
                       <input type="submit" name="submit" value="登陆" class="input" />
                              </div>
                       </div>
              </form>  
                       <div class="user">
                       
                       </div>
                       <div class="msg">
                       {$msg}
                       </div>
               </div>
               
       </div>
       
</div>       
</body>
</html>
