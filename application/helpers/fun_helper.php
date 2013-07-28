<?php
//和业务无关的通用工具方法集

//编码
if(!function_exists('iconvgbk'))
{
   function iconvgbk($value)   
  {   
    return iconv("utf-8","gbk//IGNORE",$value);
  }  
}

if(!function_exists('iconvutf'))
{
   function iconvutf($value)   
  {   
    return iconv("gbk","utf-8//IGNORE",$value);
  }  
}

//唯一Id
if(!function_exists('uuid'))
{
   function uuid($prefix = '')   
  {   
    $chars = md5(uniqid(mt_rand(), true));   
    $uuid  = substr($chars,0,8) . '-';   
    $uuid .= substr($chars,8,4) . '-';   
    $uuid .= substr($chars,12,4) . '-';   
    $uuid .= substr($chars,16,4) . '-';   
    $uuid .= substr($chars,20,12);   
    return $prefix . strtoupper($uuid);
  }  
}

//sql过滤
if ( ! function_exists('sqlFilter'))
{
	function sqlFilter($value)
	{
		if(isNull($value)){
           return 'null';
		}
		return "'". str_replace("'", "''", $value) . "'";
	}
}

//接收请求
if (!function_exists('request'))
{
	function request($name)
	{
		$value = @$_GET[$name];
        if(isNull($value)){
          $value = @$_POST[$name];
		  $value = str_replace("'", "''", $value);
		}
		return $value;
	}
}

//非空判断
if (!function_exists('isNull'))
{
	function isNull($value)
	{
		
		if ($value == null ) {
			return true;
		}elseif (is_string($value) && trim ( $value ) == '')  {
			return true;
		}elseif (is_array($value) && count($value)<1) {
			return true;
		}
		return false;
	}
}
//4位随机字符串
if (! function_exists ( 'randStr' )) {
	function randStr($len = 4) {
		$chars = '0123456789';
		mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
		$password = '';
		while ( strlen ( $password ) < $len )
			$password .= substr ( $chars, (mt_rand () % strlen ( $chars )), 1 );
		return $password;
	}
}

//utf-8格式字符串长度
if (! function_exists ( 'abslength' )) {
	function abslength($str){
		if(empty($str)){
			return 0;
		}
		if(function_exists('mb_strlen')){
			return mb_strlen($str,'utf-8');
		}
		else {
			preg_match_all("/./u", $str, $ar);
			return count($ar[0]);
		}
	}
}
if (! function_exists ( 'cmp' )) {
	function cmp($a,$b){
		if(intval($a) == intval($b)) return 0;
		return (intval($a) > intval($b))? 1 : -1; 
	}
}

/*
    utf-8编码下截取中文字符串,参数可以参照substr函数
    @param $str 要进行截取的字符串
    @param $start 要进行截取的开始位置，负数为反向截取
    @param $end 要进行截取的长度
*/
if (! function_exists ( 'utf8_substr' )) {
	function utf8_substr($str,$start=0) {
		if(empty($str)){
			return false;
		}
		if (function_exists('mb_substr')){
			if(func_num_args() >= 3) {
				$end = func_get_arg(2);
				return mb_substr($str,$start,$end,'utf-8');
			}
			else {
				mb_internal_encoding("UTF-8");
				return mb_substr($str,$start);
			}       
		}
		else {
			$null = "";
			preg_match_all("/./u", $str, $ar);
			if(func_num_args() >= 3) {
				$end = func_get_arg(2);
				return join($null, array_slice($ar[0],$start,$end));
			}
			else {
				return join($null, array_slice($ar[0],$start));
			}
		}
	}
}