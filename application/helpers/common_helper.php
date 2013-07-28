<?php
// 判断总览权限权限
function checkMainPermissions($menuid) {
	
	$ssn = $_SESSION ["ssn"];
	$permissions = FALSE;
	if (! isnull ( $ssn )) {
		$sql = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm 
				on agu.amg_id = agm.amg_id where agu.amu_uid = " . sqlFilter ( $ssn ) . " and sm_id = " . sqlFilter ( $menuid ) . "
				union 
				select sm_id from t_admin_menuUser where amu_uid = " . sqlFilter ( $ssn ) . " and sm_id = " . sqlFilter ( $menuid );
		
		$con = DB ();
		$result = $con->query ( $sql );
                
		if ($result->num_rows() > 0) {
			$permissions = TRUE;
                        return $permissions;
		}else{
                        $permissions = FALSE;
                        return $permissions;
                }
	}
	
	return $permissions;
}

function cutstr($string, $length, $dot = ' ...') {
	global $charset;
	$charset = 'utf-8';
	if (strlen ( $string ) <= $length) {
		return $string;
	}
	$string = str_replace ( array (
			'&',
			'"',
			'<',
			'>' 
	), array (
			'&',
			'"',
			'<',
			'>' 
	), $string );
	$strcut = '';
	if (strtolower ( $charset ) == 'utf-8') {
		$n = $tn = $noc = 0;
		while ( $n < strlen ( $string ) ) {
			$t = ord ( $string [$n] );
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n ++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr ( $string, 0, $n );
	} else {
		for($i = 0; $i < $length; $i ++) {
			$strcut .= ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
		}
	}
	$strcut = str_replace ( array (
			'&',
			'"',
			'<',
			'>' 
	), array (
			'&',
			'"',
			'<',
			'>' 
	), $strcut );
	return $strcut . $dot;
}

// 登录监测
function loginValidate() {
	session_start ();
	if (isNull ( @$_SESSION ["ssn"] )) {
		if ($_SERVER ['QUERY_STRING']) {
			$url = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['PHP_SELF'] . '?' . $_SERVER ['QUERY_STRING'];
		} else {
			$url = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['PHP_SELF'];
		}
		echo "<script>";
		echo "location.href='" . site_url ( "login" ) . "?referrer=$url'";
		echo "</script>";
	}
}

/*
 * 分页查询
 */

function getPageResult($sql, $orderby, $url, $init_para = array()) {
	$con = DB ();
	// 分解sql语句，获取总记录总条数
	$findex = strpos ( $sql, 'from' );
	$count_sql = 'select count(1) total from (' . $sql . ') a';
	
	if (isNull ( @$init_para ['dbFlag'] )) {
		
		$resule = $con->query ( $count_sql );
	}
	$total = $resule->row ()->total;
	
	// 获取参数，
	$page_size = @$init_para ['__pageSize'];
	if (isNull ( $page_size )) {
		$page_size = request ( '__pageSize' );
	}
	// __ c_ s_
	if (isNull ( $page_size )) {
		$page_size = 10;
	}
	
	// 获取当前的页数。
	$page_index = @$init_para ['__pageIndex'];
	if (isNull ( $page_index )) {
		$page_index = request ( '__pageIndex' );
	}
	if (isNull ( $page_index )) {
		$page_index = 1; // 默认第一页
	}
	
	// 去掉select 中的select
	$sindex = strpos ( $sql, 'select' );
	$sql = substr ( $sql, $sindex + 7 );
	
	// 拼装sql
	$e_sql = "
			select * from (
				select 
					row_number() over({$orderby}) as rownumber,
		{$sql}) as temp 
			where rownumber between " . (($page_index - 1) * $page_size + 1) . " and " . $page_index * $page_size . " 
		";
	
	if (isNull ( @$init_para ['dbFlag'] )) {
		$result = $con->query ( $e_sql );
	}
	
	$result_data = array ();
	foreach ( $result->result_array () as $row ) {
		$data_row = array ();
		foreach ( $row as $key => $value ) {
			$data_row [$key] = @iconvutf ( $value );
		}
		
		array_push ( $result_data, $data_row );
	}
	
	$total_page = ceil ( $total / $page_size );
	
	$page_top = '<font color="#cc0000" style="font-size:12px; font-weight:normal">(共' . $total . '条结果)</font>';
	// $page_foot = '<div class="page-wrap">';
	
	$page_foot = '';
	if ($page_index == 1) {
		// $page_foot .= ' < ';
		$page_foot .= '';
	} else {
		$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . ($page_index - 1) . '\');" > < </a>';
	}
	
	if ($total_page > 7) {
		if ($page_index <= 4) {
			
			$i = 1;
			for($i; $i <= 5; $i ++) {
				if ($i == $page_index) {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');" id="page-wraped"> ' . $i . ' </a>';
				} else {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');"> ' . $i . ' </a>';
				}
			}
			$page_foot .= '....';
			$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $total_page . '\');"> ' . $total_page . ' </a>'; // 最后一页
		} else if ($page_index > ($total_page - 4)) {
			
			$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'1\');" > 1 </a>';
			$page_foot .= '....';
			
			$i = $total_page - 4;
			for($i; $i <= $total_page; $i ++) {
				if ($page_index == $i) {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');" id="page-wraped"> ' . $i . ' </a>';
				} else {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');"> ' . $i . ' </a>';
				}
			}
		} else {
			
			$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'1\');" > 1 </a>'; // 第一页
			$page_foot .= '....';
			$i = $page_index - 2;
			$max_page = $page_index + 3;
			if ($max_page > $total_page) {
				$max_page = $total_page;
			}
			for($i; $i < $max_page; $i ++) {
				if ($page_index == $i) {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');" id="page-wraped"> ' . $i . ' </a>';
				} else {
					$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');"> ' . $i . ' </a>';
				}
			}
			$page_foot .= '....';
			// 最后一页
			$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $total_page . '\');" id="page-wraped"> ' . $total_page . ' </a>';
		}
	} else {
		$i = 1;
		for($i; $i <= $total_page; $i ++) {
			if ($page_index == $i) {
				$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');" id="page-wraped"> ' . $i . ' </a>';
			} else {
				$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . $i . '\');"> ' . $i . ' </a>';
			}
		}
	}
	if ($page_index == $total_page) {
		// $page_foot .= ' > ';
		$page_foot .= '';
	} else {
		$page_foot .= '<a href="javascript:goto_page(\'' . $url . '\', \'' . ($page_index + 1) . '\');"> > </a>';
	}
	
	if ($total_page > 1) {
		$page_foot .= '<span>跳至： <input type="text" id="__page_input" class="writed" style="width:30px; height:20px"></span>';
		$page_foot .= '<span  class="btn-fj2"><a href="javascript:goto_pagenumber(\'' . $url . '\',\'' . $total_page . '\');">GO</a></span>';
	}
	
	// $page_foot .= '</div>';
	if ($total == 0) {
		$page_foot = '';
	}
	
	// 放数据集到对应的参数中。同时生成字符串。
	$data = array ();
	$data ['__pageSize'] = $page_size; // 每页记录条数
	$data ['__pageIndex'] = $page_index; // 当前页
	$data ['__pageTotal'] = $total; // 总记录条数
	$data ['__pageRs'] = $result_data; // 结果集合
	$data ['__pageTop'] = $page_top; // 上分页
	$data ['__pageFoot'] = $page_foot; // 下分页
	$data ['__pageCount'] = $total_page; // 总页数
	return $data;
}


// 获取主菜单
function getNavi($naviuid) {

	if (isNull ( $naviuid )) {
		$naviuid = '6CA7CF4E-D252-C5D6-84EF-F22EC05BAA79';
	}
	$navi_item = array('6CA7CF4E-D252-C5D6-84EF-F22EC05BAA79','DE47392D-BA34-628B-ECAC-6FDA120B7668');
	$ssn = @$_SESSION ["ssn"];
	if (isNull ( @$_SESSION ["ssn"] )) {
		redirect ( site_url ( "login" ) );
	}
	$con = DB ();
	$sql_smid = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm 
					on agu.amg_id = agm.amg_id where agu.amu_uid = $ssn";
	
	$sql_smid_user = "select amu.sm_id from dbo.t_admin_menuUser amu where amu.amu_uid = $ssn";
	
	$sql = "select sm_sort,convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm 
					inner join dbo.t_sys_menuFlat smf
						on sm.sm_id = smf.sm_id where smf.smf_tier = 0 and sm.sm_id != '31DEB53E-6FD9-3FB1-1BD0-1857BA0B78C8' 
						and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user)) order by sm_sort";  //这个修改了一个排序
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$result = $query->result ();
	
	/* echo "<pre>";
	print_r($result);
	echo "</pre>";exit; */
	$html = "";
	$ct = 1;
	if ($query->num_rows () > 0) {		
		foreach ( $result as $row ) {
			
			if ($row->sm_id == $naviuid) {
				//if ($row->sm_id == $navi_item) {
				if(in_array($row->sm_id, $navi_item)){	
					$html .= "<li id='nav2_item' name='nav2_item'  class='menu_blur'><a style='cursor:pointer'>" . iconvutf ( $row->sm_title ) . "</a>";
					
					$html .= "<ul>";
					$sql = "select convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm 
									inner join dbo.t_sys_menuFlat smf
										on sm.sm_id = smf.sm_id where smf.smf_tier = 1 and smf.smf_parentId = '$row->sm_id' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user)) order by sm.sm_sort ";
					
					$qe = $con->query ( iconvgbk ( $sql ) );
					$res = $qe->result ();
					if ($qe->num_rows () > 0) {
						foreach ( $res as $r ) {
							$html .= "<li><a href='" . site_url ( $r->sm_path ) . "'><img src='common/images/nav-two.png'>" . iconvutf ( $r->sm_title ) . "</a></li>";
						}
					}
					$html .= "</ul></li>";
				} else {
					$html .= "<li class='nav-normal'><a href='" . site_url ( $row->sm_path ) . "'>" . iconvutf ( $row->sm_title ) . "</a></li>";
				}
				if ($ct != $query->num_rows ()) {
					$html .= "<li><img src='common/images/fg.gif' height='35' width='2'></li>";
				}
			} else {
				//if ($row->sm_id == $navi_item) {
				if(in_array($row->sm_id, $navi_item)){ 
					$html .= "<li id='nav2_item' name='nav2_item'  class='menu_blur'><a style='cursor:pointer'>" . iconvutf ( $row->sm_title ) . "</a>";
					$html .= "<ul>";
					$sql = "select convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm 
									inner join dbo.t_sys_menuFlat smf
										on sm.sm_id = smf.sm_id where smf.smf_tier = 1 and smf.smf_parentId = '$row->sm_id' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))
											order by sm.sm_sort";
					$qe = $con->query ( iconvgbk ( $sql ) );
					$res = $qe->result ();
					if ($qe->num_rows () > 0) {
						foreach ( $res as $r ) {
							$html .= "<li><a href='" . site_url ( $r->sm_path ) . "'><img src='common/images/nav-two.png'>" . iconvutf ( $r->sm_title ) . "</a></li>";
						}
					} 
					$html .= "</ul></li>";
				} else {
					$html .= "<li class='nav-normal'><a href='" . site_url ( $row->sm_path ) . "'>" . iconvutf ( $row->sm_title ) . "</a></li>";
				}
				if ($ct != $query->num_rows ()) {
					$html .= "<li><img src='common/images/fg.gif' height='35' width='2'></li>";
				}
			}
			$ct ++;
		}
	} else {
		redirect ( "main" );
	}
	return $html;
} 
// 判断权限
function checkPermissions($naviuid, $menuid) {
	
	if (isNull ( $naviuid )) {
		$naviuid = 'F28F4F2C-366D-8654-F3F4-DFC8C5CBF4BE';
	}
	if (isNull ( $menuid )) {
		$menuid = '5D8B08CF-3FC5-15E7-D399-A3CCC907353E';
	}
	
	$ssn = $_SESSION ["ssn"];
	$permissions = '0';
	if (! isnull ( $ssn )) {
		$sql = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm 
				on agu.amg_id = agm.amg_id where agu.amu_uid = " . sqlFilter ( $ssn ) . " and sm_id = " . sqlFilter ( $menuid ) . "
				union 
				select sm_id from t_admin_menuUser where amu_uid = " . sqlFilter ( $ssn ) . " and sm_id = " . sqlFilter ( $menuid );
		
		$con = DB ();
		
		$result = $con->query ( $sql );
		if ($result->num_rows > 0) {
			$permissions = '1';
		}
	}
	
	if ($permissions == '0') {
		header ( 'HTTP/1.1 301 Moved Permanently' );
		header ( 'Location: ' . base_url () . 'index.php/public/error/' );
		exit ();
	}
}

// 判断菜单是否打开
function checkMenuOpen($checkMenu, $curMenu) {
	$ssn = $_SESSION ["ssn"];
	$con = DB ();
	$sql_smid = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm 
					on agu.amg_id = agm.amg_id where agu.amu_uid = $ssn";
	
	$sql_smid_user = "select amu.sm_id from dbo.t_admin_menuUser amu where amu.amu_uid = $ssn";
	$sql = "select convert(varchar(50),smf.smf_parentId) smf_parentId,smf.smf_tier as smf_tier from dbo.t_sys_menu sm 
							inner join dbo.t_sys_menuFlat smf
								on sm.sm_id = smf.sm_id where smf.sm_id = '$curMenu' and (smf.smf_parentId in ($sql_smid) or smf.smf_parentId in ($sql_smid_user))
									order by sm.sm_sort";
	// echo "----".$checkMenu;
	// echo $sql;
	$query = $con->query ( iconvgbk ( $sql ) );
	$result = $query->result_array ();
	foreach ( $result as $row ) {
		if ($row ["smf_parentId"] == $checkMenu) {
			// echo "----pid==";
			return true;
		} else {
			if ($row ["smf_tier"] == 1) {
				// echo "---tier=1";
				return false;
			} else {
				// echo "---next";
				return checkMenuOpen ( $checkMenu, $row ["smf_parentId"] );
			}
		}
	}
}

// 获取决策平台子菜单,支持最多取三级菜单
function getStrategyMenu($naviuid) {
	if (isNull ( $naviuid )) {
		return;
		// $naviuid = 'F28F4F2C-366D-8654-F3F4-DFC8C5CBF4BE';
	}
	$ssn = $_SESSION ["ssn"];
	$con = DB ();
	$sql_smid = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm
	on agu.amg_id = agm.amg_id where agu.amu_uid = $ssn";
	
	$sql_smid_user = "select amu.sm_id from dbo.t_admin_menuUser amu where amu.amu_uid = $ssn";
	
	$sql = "select top 1 smf.smf_tier from dbo.t_sys_menu sm
	inner join dbo.t_sys_menuFlat smf
	on sm.sm_id = smf.sm_id where smf.smf_parentId = '$naviuid' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))";
	
	$tier = $con->query ( $sql )->row ()->smf_tier;
	$space = "";
	
	$sql = "select convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm
	inner join dbo.t_sys_menuFlat smf
	on sm.sm_id = smf.sm_id where smf.smf_tier = $tier and smf.smf_parentId = '$naviuid' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))
	order by sm.sm_sort";
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$result = $query->result ();
	$html = "";
	if ($query->num_rows () > 0) {
		foreach ( $result as $key => $row ) {
			$html .= "<li id='lyzd-wrap'><a href='javascript:;'  onclick=showdiv('divResearch{$key}')><label id=divResearch{$key}_showing><img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label>&nbsp;" . iconvutf ( $row->sm_title ) . "</a>";
			
			$_sql = "select convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm
			inner join dbo.t_sys_menuFlat smf
			on sm.sm_id = smf.sm_id where  smf.smf_parentId = '" . $row->sm_id . "' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))
			order by sm.sm_sort";
			
			$_query = $con->query ( iconvgbk ( $_sql ) );
			$_result = $_query->result ();
			if ($_query->num_rows () > 0) {
				$html .= "<ul   id='divResearch{$key}' style='display:none'>";
				foreach ( $_result as $_row ) {
					$html .= "<li id='subnav-list'><a href='" . site_url ( $_row->sm_path ) . "'  style='padding-left:25px;'>" . iconvutf ( $_row->sm_title ) . "</a></li>";
				}
				$html .= "</ul>";
			}
			$html .= "</li>";
		}
	}
	return $html;
}

// 获取菜单下的选项卡
function getTablist($sm_id = '', $tabid = '', $ic_id = '', $type = '',$mult=0) {
	$con = DB ();
	if(!$mult){
		$where = $sm_id ? "sm_id='" . $sm_id . "' and type =null" : "ic_id='" . $ic_id . "'";
	}else{
		$where = "ic_id='" . $ic_id . "'" ;
	}
	
	
	$sql = "select tc_id,tc_name,no_figure,type from t_tab_config where $where order by tc_sort";
	$query = $con->query ( $sql );
	$ret = $query->result ();
	$tabHtml = '';
	if (count ( $ret ) > 7) {
		$i = 0;
		
		$tabHtml = '<div id="tags_more1"><ul id="tags">';
		foreach ( $ret as $k => $v ) {
			if ($i < 7) {
				if ($v->tc_name != '-') {
					if ($tabid == '') {
						if ($k == 0) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,'$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						}
					} else {
						if ($v->tc_id == $tabid) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A></li>";
						}
					}
				}
				$i ++;
			} else if ($i == 7) {
				$tabHtml .= '<li style="" id="more_tab"><a  href="javascript:;">>></a></li>';
				$tabHtml .= '</ul></div>';
				$tabHtml .= '<div id="tags_more2" style="display:none"><ul id="tags" class="tagscc">';
				
				$tabHtml .= '<li style="" id="more_tab2"><a  href="javascript:;"><<</a></li>';
				
				$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
				$i ++;
			} else if ($i > 7 && $i < 14) {
				if ($v->tc_id == $tabid) {
					$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
				} else {
					$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A></li>";
				}
				$i ++;
			} else if ($i == 14) {
				$tabHtml .= '<li style="" id="more_tab3"><a  href="javascript:;">>></a></li>';
				$tabHtml .= '</ul></div>';
				$tabHtml .= '<div id="tags_more3" style="display:none"><ul id="tags" class="tagscc">';
				if (count ( $ret ) > 14) {
					$tabHtml .= '<li style="" id="more_tab21"><a  href="javascript:;"><<</a></li>';
				}
				$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li
>";
				$i ++;
			} else if ($i > 14 && $i < 21) {
				if ($v->tc_id == $tabid) {
					$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
				} else {
					$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A></li>";
				}
				$i ++;
			} else if ($i == 21) {
				$tabHtml .= '<li style="" id="more_tab4"><a  href="javascript:;">>></a></li>';
				$tabHtml .= '</ul></div>';
				$tabHtml .= '<div id="tags_more4" style="display:none"><ul id="tags" class="tagscc">';
				if (count ( $ret ) > 21) {
					$tabHtml .= '<li style="" id="more_tab31"><a  href="javascript:;"><<</a></li>';
				}
				$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li
>";
				$i ++;
			} else {
				$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,1,'$sm_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
				$i ++;
			}
		}
		$tabHtml .= '</div>';
	} else {
		$tabHtml .= '<ul id="tags">';
		if ($ic_id) {
			foreach ( $ret as $k => $v ) {
				$sencond_channel = array(1382,1398,1375,1326);
				if( $type == '002V' && in_array($v->tc_id, $sencond_channel)) continue;
				if ($v->tc_name != '-') {
					if ($tabid == '') {
						if ($k == 0) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,$v->no_figure,'$sm_id','$v->type','$ic_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,$v->no_figure,'$sm_id','$v->type','$ic_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						}
					} else {
						if ($v->tc_id == $tabid) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,$v->no_figure,'$sm_id','$v->type','$ic_id') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,$v->no_figure,'$sm_id','$v->type','$ic_id') href='javascript:void(0)'>$v->tc_name</A></li>";
						}
					}
				}
			}
			$tabHtml .= '</ul>';
		} else {
			foreach ( $ret as $k => $v ) {
				$v->no_figure = $v->no_figure == null ? 0 : $v->no_figure;
				if ($v->tc_name != '-') {
					if ($tabid == '') {
						if ($k == 0) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id',$v->no_figure,'$type') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id',$v->no_figure,'$type') href='javascript:void(0)'>$v->tc_name</A> </li>";
						}
					} else {
						if ($v->tc_id == $tabid) {
							$tabHtml .= "<li class=selectTag><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id',$v->no_figure,'$type') href='javascript:void(0)'>$v->tc_name</A> </li>";
						} else {
							$tabHtml .= "<li><A onClick=selectTag('tagContent$k',this,$v->tc_id,'','$sm_id',$v->no_figure,'$type') href='javascript:void(0)'>$v->tc_name</A></li>";
						}
					}
				}
			}
			$tabHtml .= '</ul>';
		}
	}
	
	return @iconvutf ( $tabHtml );
}

function getDimIndexlist($tabid = 1, $place = 'left', $menuid = '') {
	$con = DB();
	// $sql = "select tic.ic_id,ic_key,is_top,display_ic_name,is_display,kpi_id
	// from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid order by tic.ic_id";
	// //底部title加上单位
	// if($place == 'bottom'){
	// $sql = "select id_unit,tic.ic_id ic_id,tic.ic_key
	// ic_key,is_top,display_ic_name,is_display,kpi_id,id_unit from t_index_desc
	// tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid and tid.ic_key=tic.ic_key order by tic.ic_id";
	// }
	
	$sql = "select tic.ic_id,ic_key,tic.mc_key,is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,ti.it_ic_class as it_ic_class,ti.display_is_figure from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid and tic.fic_id is null order by ti.it_ic_rank";
	// 底部title加上单位
	if ($place == 'bottom') {
		$sql = "select id_unit,tic.ic_id ic_id,tic.ic_key ic_key,display_is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,id_unit from t_index_desc tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid and tid.ic_key=tic.ic_key order by ti.it_ic_rank";
	}
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$ret = $query->result ();
	/* echo "<pre>";
	print_r($ret);
	echo "</pre>";
	exit(); */
	$indexHtml = '<ul id=f_ul>';
	$it_ic_class = 'N';
	$boke=array(3760,3768,3775,3756,3770,3774,3773,3755,3779,3771,3761,3777);
	$sport = array(3721,3719);
	$amusement =array(3740,3753);
	$news = array(3781,3780,3784);
	foreach ( $ret as $k => $v ) {
		//print_r($v);exit();
		if ($place == 'bottom' && (! (intval ( $v->ic_key ) < 1000) || strpos ( $v->ic_key, "_" ) > 0)) {
			if ($v->display_is_top == 1) {
				
				$url = "http://main.dp.erp.sina.com.cn/index.php/strategy/sIndex/getSingleIndex?menuid=$menuid&index_id=$v->ic_key";
				$indexHtml .= "<th>$v->display_ic_name($v->id_unit)<a href='javascript:;' onclick=window.open('$url','_blank','height=500,width=1100,top='+(screen.height-500)/2+',left='+(screen.width-1100)/2+',toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,target=_top')><img src='common/images/strategy/top.gif' style='vertical-align:middle;margin-left:15px;'></a></th>";
			} else {
				$indexHtml .= "<th>$v->display_ic_name($v->id_unit)</th>";
			}
		} elseif ($place == 'left') {
			if ($it_ic_class != $v->it_ic_class && ! empty ( $v->it_ic_class )) {
				$it_ic_class = $v->it_ic_class;
				
				$indexHtml .= '<tr height=25px>';
				if (strlen ( $it_ic_class ) <= 8) {
					$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>';
				} else if (strlen ( $it_ic_class ) <= 12) {
					$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>';
				}
				$indexHtml .= "<td valign='top' align='left' colspan='3'><h3 style='padding-left:0px'>$show_class</h3></td>";
			}
			if ($v->is_display == '1' && $k == 0) {
				$indexHtml .= "<li><input  name='selectIndex[]' text='".$v->ic_id."' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->display_ic_name</div>";
				//$indexHtml .= "<li><input  name='selectIndex[]' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->display_ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql ); 
				if ($sub_query->num_rows () > 0) {
					//$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked  class='getline'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked   value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input disabled='disabled' text='".$v->ic_id."' title='".$subv->ic_main_rank."'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "'  class='getline' '/><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' text='".$v->ic_id."' name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			} else if (intval ( $v->ic_key ) < 1000 && strpos ( iconvutf ( $v->display_ic_name ), "目标值" ) > 0) {
				$indexHtml .= '<li style="display:none">';
				$indexHtml .= "<input   name='selectIndex[]' text='".$v->ic_id."' flag='" . $k . "' type='checkbox'  class='getline'  api='1'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
			} else if ($v->is_display == '1') {
				// $indexHtml.="<li><input name='selectIndex[]' flag='" . $k .
				// "' type='checkbox' value='" . $v->ic_key . "'
				//  checked/><div
				// style='display:inline-block;'>$v->display_ic_name</div><em><input
				// type='radio' name='raoIndex[]' value='" . $v->ic_key . "'
				// onclick='getDate()'></em></li>";
// 				$sqls="select * from t_index2tab where tc_id=$tabid";				
				$indexHtml .= "<li><input  text='".$v->ic_id."'  name='selectIndex[]' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->display_ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql );
				if ($sub_query->num_rows () > 0) {
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio'   name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input disabled='disabled' title='".$subv->ic_main_rank."'  class='getline'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "' /><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' text='".$v->ic_id."' name='raoIndex[]'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			} else {
			// $indexHtml.="<li><input name='selectIndex[]' flag='" . $k .
				// "' type='checkbox' value='" . $v->ic_key . "'
				// /><div
				// style='display:inline-block;'>$v->display_ic_name</div><em><input
				// type='radio' name='raoIndex[]' value='" . $v->ic_key . "'
				// onclick='getDate()'></em></li>";
				$indexHtml .= "<li><input  name='selectIndex[]' text='".$v->ic_id."' flag='" . $k . "' type='checkbox' class='getline' value='" . $v->mc_key.$v->ic_id . "' /><div style='display:inline-block;'>$v->display_ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql );
				if ($sub_query->num_rows () > 0) {
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input text='".$v->ic_id."' title='".$subv->ic_main_rank."'   class='getline'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "' /><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' text='".$v->ic_id."' name='raoIndex[]'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			}
		}
		
	}
	$indexHtml .= '</ul>';
	if ($place == 'left') {
		// $indexHtml.='</table>';
	}
	return @iconvutf ( $indexHtml );
}
function getDim2Indexlist($ic_id = 1, $place = 'left', $menuid = '') {
	$con = DB();
	$sql = "select * from t_index_config where is_mult_dim=$ic_id and fic_id is null";
	$query = $con->query ( iconvgbk ( $sql ) );
	$ret = $query->result ();
	$indexHtml = '<ul id=f_ul>';
	foreach ( $ret as $k => $v ) {
		if ($place == 'left') {
			if ($v->is_display == '1' && $k == 0) {
				$indexHtml .= "<li><input  name='selectIndex[]' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->ic_name</div>";
				//$indexHtml .= "<li><input  name='selectIndex[]' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql );
				if ($sub_query->num_rows () > 0) {
					//$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked  class='getline'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked    value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input disabled='disabled' title='".$subv->ic_main_rank."'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "'  class='getline' '/><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			}else if ($v->is_display == '1') {
				$indexHtml .= "<li><input    name='selectIndex[]' flag='" . $k . "' type='checkbox'  class='getline'  value='" . $v->mc_key.$v->ic_id . "'  checked/><div style='display:inline-block;'>$v->ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql );
				if ($sub_query->num_rows () > 0) {
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio'   name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input disabled='disabled' title='".$subv->ic_main_rank."'  class='getline'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "' /><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' name='raoIndex[]'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			} else {
				$indexHtml .= "<li><input  class='getline'   name='selectIndex[]' flag='" . $k . "' type='checkbox'  value='" . $v->mc_key.$v->ic_id . "' /><div style='display:inline-block;'>$v->ic_name</div>";
				$sub_sql = "select * from t_index_config where fic_id=$v->ic_id";
				$sub_query = $con->query ( $sub_sql );
				if ($sub_query->num_rows () > 0) {
					$indexHtml .= "<div>[<a  id=fic_id_a$k value=$k href='javascript:;' onclick=showsub($k)>+</a>]</div><em><input type='radio' name='raoIndex[]' checked value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
					$indexHtml .= "<li style='display:none;'><ul  id=fic_id$k style=' margin-left: 20px;'>";
					foreach ( $sub_query->result () as $subk => $subv ) {
						$indexHtml .= "<li><input title='".$subv->ic_main_rank."'   class='getline'  name='selectIndex[]' flag='" . $subk . "' type='checkbox'  value='" . $subv->mc_key.$subv->ic_id. "' /><div style='display:inline-block;'>$subv->ic_name</div><em><input type='radio' name='raoIndex[]' value='" . $subv->mc_key.$subv->ic_id . "' onclick='getDate()'></em></li>";
					}
					$indexHtml .= '</ul></li>';
				} else {
					$indexHtml .= "<em><input type='radio' name='raoIndex[]'  value='" . $v->mc_key.$v->ic_id . "' onclick='getDate()'></em></li>";
				}
			}
		}

	}
	$indexHtml .= '</ul>';
	return @iconvutf ( $indexHtml );
}

function getTablelist($tabid = 1, $place = 'bottom', $menuid = '',$ic_id,$ic_table_type='001V'){
	$con = DB ();
	// $sql = "select tic.ic_id,ic_key,is_top,display_ic_name,is_display,kpi_id
	// from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid order by tic.ic_id";
	// //底部title加上单位
	// if($place == 'bottom'){
	// $sql = "select id_unit,tic.ic_id ic_id,tic.ic_key
	// ic_key,is_top,display_ic_name,is_display,kpi_id,id_unit from t_index_desc
	// tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid and tid.ic_key=tic.ic_key order by tic.ic_id";
	// }
	$sql = "select tic.ic_id,ic_key,tic.mc_key,is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,ti.it_ic_class as it_ic_class from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid order by ti.it_ic_rank";
	// 底部title加上单位
	if ($place == 'bottom') {
		$sql = "select dimension_type1,id_unit,tic.ic_id ic_id,tic.ic_key ic_key,display_is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,id_unit from t_index_desc tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid and tid.ic_key=tic.ic_id order by ti.it_ic_rank";
	} 
	
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$ret = $query->result ();
	$arr_id = array();
	$ic_id = "'".$ic_id."'";
	//echo $ic_id;exit;
	$arr_id = explode(',',$ic_id);
	//print_r($arr_id);exit;
	$indexHtml = '';
	$it_ic_class = 'N';
	foreach ( $ret as $k => $v ){
		if(in_array("'".$v->ic_id."'",$arr_id)){
			//注释的if语句  不能保证开头为数字的指标名称，其他的限制$v->ic_key 不值何意
		//	if ($place == 'bottom' && (intval ( $v->ic_key ) == 0 || ! (intval ( $v->ic_key ) < 1000) || strpos ( $v->ic_key, "_" ) > 0)) {
		if ($place == 'bottom') {
				if ($v->display_is_top == 1) {
						$url = "http://main.dp.erp.sina.com.cn/index.php/strategy/sIndex/getSingleIndex?menuid=$menuid&index_id=$v->ic_key";
						$indexHtml .= "<th>$v->display_ic_name($v->id_unit)<a href='javascript:;' onclick=window.open('$url','_blank','height=500,width=1100,top='+(screen.height-500)/2+',left='+(screen.width-1100)/2+',toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,target=_top')><img src='common/images/strategy/top.gif' style='vertical-align:middle;margin-left:15px;'></a></th>";
					} else {
						$_sql = "select * from t_index_config where ic_id='$v->ic_id' and no_point=1";
						$_query = $con->query ( $_sql );
						if ($_query->num_rows () > 0) {
							$indexHtml .= "<th>$v->display_ic_name($v->id_unit)<a title='' href='" . site_url ( "strategy/index/multiDimension?sm_id=$menuid&ic_id=$v->ic_id&ic_table_type=$ic_table_type" ) . "'></a></th>";
						} else {
							$indexHtml .= "<th>$v->display_ic_name($v->id_unit)</th>";
						}					
				}
			} elseif ($place == 'left') {
				if ($it_ic_class != $v->it_ic_class && ! empty ( $v->it_ic_class )) {
					$it_ic_class = $v->it_ic_class;
					
					$indexHtml .= '<tr height=25px>';
					if (strlen ( $it_ic_class ) <= 8) {
						$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>';
					} else if (strlen ( $it_ic_class ) <= 12) {
						$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>';
					}
					$indexHtml .= "<td valign='top' align='left' colspan='3'><h3 style='padding-left:0px'>$show_class</h3></td>";
				}
				if ($v->is_display == '1' && $k == 0) {
					$indexHtml .= '<tr height=25px>';
					$indexHtml .= "<td valign='top'><input name='selectIndex[]' flag='" . $k . "' type='checkbox'  value='" . $v->ic_key . "' onclick='getLineInit()' checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' checked value='" . $v->ic_key . "' onclick='getDate()'></td>";
				} else if (intval ( $v->ic_key ) < 1000 && strpos ( iconvutf ( $v->display_ic_name ), "目标值" ) > 0) {
					$indexHtml .= '<tr height="0px;" style="display:none">';
					$indexHtml .= "<td valign='top'><input name='selectIndex[]' flag='" . $k . "' type='checkbox' api='1'  value='" . $v->ic_key . "' onclick='getLineInit()' checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
				} else if ($v->is_display == '1') {
					$indexHtml .= '<tr height=25px>';
					$indexHtml .= "<td valign='top'><input name='selectIndex[]' flag='" . $k . "' type='checkbox'  value='" . $v->ic_key . "' onclick='getLineInit()' checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
				} else {
					$indexHtml .= '<tr height=25px>';
					$indexHtml .= "<td valign='top'><input name='selectIndex[]' flag='" . $k . "' type='checkbox'  value='" . $v->ic_key . "' onclick='getLineInit()'/></td><td valign='top' style='width:120px; line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
				}
				$indexHtml .= '</tr>';
			}
		}
	}

	if ($place == 'left') {
		$indexHtml .= '</table>';
	}
	return @iconvutf ( $indexHtml );
}

function getIndexlist($tabid = 1, $place = 'bottom', $menuid = '',$ic_table_type='001V') {
	$con = DB ();
	// $sql = "select tic.ic_id,ic_key,is_top,display_ic_name,is_display,kpi_id
	// from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid order by tic.ic_id";
	// //底部title加上单位
	// if($place == 'bottom'){
	// $sql = "select id_unit,tic.ic_id ic_id,tic.ic_key
	// ic_key,is_top,display_ic_name,is_display,kpi_id,id_unit from t_index_desc
	// tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and
	// ti.tc_id=$tabid and tid.ic_key=tic.ic_key order by tic.ic_id";
	// }
	
	$sql = "select tic.ic_id,ic_key,tic.mc_key,is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,ti.it_ic_class as it_ic_class from t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid order by ti.it_ic_rank";
	// 底部title加上单位
	if ($place == 'bottom') {
		$sql = "select ic_menu_type1,id_unit,tic.ic_id ic_id,tic.ic_key ic_key,display_is_top,display_ic_name,ti.display_is_display as is_display,kpi_id,id_unit from t_index_desc tid,t_index_config tic,t_index2tab ti where tic.ic_id=ti.ic_id and ti.tc_id=$tabid and tid.ic_key=tic.ic_id order by ti.it_ic_rank";
	}
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$ret = $query->result ();
	
	$indexHtml = '';
	$it_ic_class = 'N'; 
	foreach ( $ret as $k => $v ) {
		if ($place == 'bottom' && (intval ( $v->ic_key ) == 0 || ! (intval ( $v->ic_key ) < 1000) || strpos ( $v->ic_key, "_" ) > 0)) {
			if ($v->display_is_top == 1) {
				$url = "http://main.dp.erp.sina.com.cn/index.php/strategy/sIndex/getSingleIndex?menuid=$menuid&index_id=$v->ic_key";
				$indexHtml .= "<th>$v->display_ic_name($v->id_unit)<a href='javascript:;' onclick=window.open('$url','_blank','height=500,width=1100,top='+(screen.height-500)/2+',left='+(screen.width-1100)/2+',toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,target=_top')><img src='common/images/strategy/top.gif' style='vertical-align:middle;margin-left:15px;'></a></th>";
			} else {
				$_sql = "select * from t_index_config where ic_id='$v->ic_id' and no_point=1";
				$_query = $con->query ( $_sql );
				if ($_query->num_rows () > 0) {
					$indexHtml .= "<th>$v->display_ic_name($v->id_unit)<a title='' href='" . site_url ( "strategy/index/multiDimension?sm_id=$menuid&ic_id=$v->ic_id&ic_table_type=$ic_table_type" ) . "'></a></th>";
				} else {
					$indexHtml .= "<th>$v->display_ic_name($v->id_unit)</th>";
				}
			
			}
		} elseif ($place == 'left') {
			if ($it_ic_class != $v->it_ic_class && ! empty ( $v->it_ic_class )) {
				$it_ic_class = $v->it_ic_class;
				$indexHtml .= '<tr height=25px>';
				if (strlen ( $it_ic_class ) <= 8) {
					$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</s>';
				} else if (strlen ( $it_ic_class ) <= 12) {
					$show_class = '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>' . $it_ic_class . '<s>&nbsp;&nbsp;&nbsp;&nbsp;</s>';
				}
				$indexHtml .= "<td valign='top' align='left' colspan='3'><h3 style='padding-left:0px'>$show_class</h3></td>";
			}
			if ($v->is_display == '1' && $k == 0) {
				$indexHtml .= '<tr height=25px>';
				$indexHtml .= "<td valign='top'><input  name='selectIndex[]' class='getline' flag='" . $k . "' type='checkbox'  value='" . $v->mc_key.$v->ic_id . "'  checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' checked value='" . $v->ic_key . "' onclick='getDate()'></td>";
			} else if (intval ( $v->ic_key ) < 1000 && strpos ( iconvutf ( $v->display_ic_name ), "目标值" ) > 0) {
				$indexHtml .= '<tr height="0px;" style="display:none">';
				$indexHtml .= "<td valign='top'><input name='selectIndex[]' class='getline' flag='" . $k . "' type='checkbox' api='1'  value='" . $v->mc_key.$v->ic_id . "'  checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
			} else if ($v->is_display == '1') {
				$indexHtml .= '<tr height=25px>';
				$indexHtml .= "<td valign='top'><input name='selectIndex[]' class='getline' flag='" . $k . "' type='checkbox'  value='" . $v->mc_key.$v->ic_id . "'  checked/></td><td valign='top' style='width:120px;line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
			} else {
				$indexHtml .= '<tr height=25px>';
				$indexHtml .= "<td valign='top'><input name='selectIndex[]' class='getline' flag='" . $k . "' type='checkbox'  value='" . $v->mc_key.$v->ic_id . "' /></td><td valign='top' style='width:120px; line-height:15px;'> $v->display_ic_name </td><td valign='top' align='right'><input type='radio' name='raoIndex[]' value='" . $v->ic_key . "' onclick='getDate()'></td>";
			}
			$indexHtml .= '</tr>';
		}
	}
	if ($place == 'left') {
		$indexHtml .= '</table>';
	}
	return @iconvutf ( $indexHtml );
}

// 获取子菜单
function getMenu($naviuid, $menuid, $flag = 0, $ftier = 0) {
	if (isNull ( $naviuid )) {
		$naviuid = 'F28F4F2C-366D-8654-F3F4-DFC8C5CBF4BE';
	}
	if (isNull ( $menuid )) {
		$menuid = '5D8B08CF-3FC5-15E7-D399-A3CCC907353E';
	}
	$ssn = $_SESSION ["ssn"];
	$con = DB ();
	$sql_smid = "select agm.sm_id from dbo.t_admin_group2user agu inner join dbo.t_admin_group2menu agm 
					on agu.amg_id = agm.amg_id where agu.amu_uid = $ssn";
	
	$sql_smid_user = "select amu.sm_id from dbo.t_admin_menuUser amu where amu.amu_uid = $ssn";
	
	$sql = "select top 1 smf.smf_tier from dbo.t_sys_menu sm 
					inner join dbo.t_sys_menuFlat smf
						on sm.sm_id = smf.sm_id where smf.smf_parentId = '$naviuid' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))";
	
	$tier = $con->query ( $sql )->row ()->smf_tier;
	$space = "";
	
	if ($ftier == 0) {
		$ftier = $tier;
	}
	
	// for($i=0;$i<($tier-1);$i++){
	// $space .="&nbsp;&nbsp;&nbsp;&nbsp;";
	// }
	
	$sql = "select convert(varchar(50),sm.sm_id) sm_id,sm.sm_title,sm.sm_path from dbo.t_sys_menu sm 
					inner join dbo.t_sys_menuFlat smf
						on sm.sm_id = smf.sm_id where smf.smf_tier = $tier and smf.smf_parentId = '$naviuid' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))
							order by sm.sm_sort";
	
	$query = $con->query ( iconvgbk ( $sql ) );
	$result = $query->result ();
	$html = "";
	if ($query->num_rows () > 0) {
		foreach ( $result as $row ) {
			$_sql = "select top 1 smf.smf_tier from dbo.t_sys_menu sm 
							inner join dbo.t_sys_menuFlat smf
								on sm.sm_id = smf.sm_id where smf.smf_parentId = '" . $row->sm_id . "' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))";
			$_query = $con->query ( $_sql );
			
			if ($_query->num_rows () == 0) {
				if ($row->sm_id == $menuid) {
					if ($tier > 2) {
						if ($flag == 100) {
							$html .= "<li id='subnav-list'><a  class='" . $row->sm_id . "' style='padding-left:23px; width:138px; color:#515151; ' href='" . site_url ( $row->sm_path ) . "' id='subnav-list' ><strong>" . $space . iconvutf ( $row->sm_title ) . "</strong></a></li>";
						} else {
							$html .= "<li style='position:relative;width:145px;'><a  class='" . $row->sm_id . "' href='" . site_url ( $row->sm_path ) . "' onmouseover=\"showdiv('" . $row->sm_id . "','" . $tier . "')\" id='subnav-list' style=\"padding-left:23px; width:138px; color:#515151\">" . $space . iconvutf ( $row->sm_title ) . "</a></li>";
						}
					} else {
						if ($flag == 100) {
							$html .= "<li id='subnav-list'><a  class='" . $row->sm_id . "' style='padding-left:23px; width:138px; color:#515151; ' href='" . site_url ( $row->sm_path ) . "' id=\"selected\" ><strong>" . $space . iconvutf ( $row->sm_title ) . "</strong></a></li>";
						} else {
							$html .= "<li style='position:relative;width:145px;'><a  class='" . $row->sm_id . "' href='" . site_url ( $row->sm_path ) . "' onmouseover=\"showdiv('" . $row->sm_id . "','" . $tier . "')\" id=\"selected\" style=\"padding-left:23px; width:138px; color:#515151\">" . $space . iconvutf ( $row->sm_title ) . "</a></li>";
						}
					}
				} else {
					if ($flag == 100) {
						$html .= "<li id='subnav-list'><a  class='" . $row->sm_id . "' style='padding-left:23px; width:130px; color:#515151' href='" . site_url ( $row->sm_path ) . "' ><strong>" . $space . iconvutf ( $row->sm_title ) . "</strong></a></li>";
					} else {
						$html .= "<li  style='position:relative;width:145px;'><a   class='" . $row->sm_id . "' onmouseover=\"showdiv('" . $row->sm_id . "','" . $tier . "')\"   href='" . site_url ( $row->sm_path ) . "' id=\"subnav-list\" style=\"padding-left:23px; width:130px; color:#515151\">" . $space . iconvutf ( $row->sm_title ) . "</a></li>";
					}
				}
			} else {
				$sm_path = $row->sm_path;
				$sm_id = $row->sm_id;
				$sm_title = $row->sm_title;
				
				$__sql = "select convert(varchar(50),sm.sm_id) sm_id,sm_path from dbo.t_sys_menu sm 
                                            inner join dbo.t_sys_menuFlat smf
                                                    on sm.sm_id = smf.sm_id where smf.smf_parentId = '" . $row->sm_id . "' and (sm.sm_id in ($sql_smid) or sm.sm_id in ($sql_smid_user))
                                                            order by sm.sm_sort";
				$__query = $con->query ( $__sql );
				if ($__query->num_rows () > 0) {
					$sm_path = $__query->row ()->sm_path;
				}
				
				// $__result = $__query->result();
				// $__menus = array();
				// foreach($__result as $r){
				// array_splice($__menus,count($__menus),0,$r->sm_id);
				// }
				// echo
				// "---f:".$fisrtTier."---".$tier."-".iconvutf($row->sm_title);
				if (checkMenuOpen ( $row->sm_id, $menuid )) {
					// if ($ftier == $tier) {
					// $html .= "<li id='lyzd-wrap'><a href='javascript:;'
					// onclick=\"showsubdiv('" . $row->sm_id . "')\"
					// ><strong><label id='" . $sm_id . "_showing'>" . $space .
					// "<img style='vertical-align:middle;margin-bottom:2px'
					// src='common/images/strategy/down.png'></label> " .
					// iconvutf($sm_title) . "</strong></a>";
					// } else {
					// $html .= "<li id='lyzd-wrap'><a href='" .
					// site_url($sm_path) . "' ><label id='" . $sm_id .
					// "_showing'>" . $space . "<img
					// style='vertical-align:middle;margin-bottom:2px'
					// src='common/images/strategy/down.png'></label> " .
					// iconvutf($sm_title) . "</a>";
					// }
					
					// $html .= "<ul id='" . $sm_id . "'
					// style='padding-left:15px;'>";
					// $html .= getMenu($sm_id, $menuid, 0, $ftier);
					// $html .= "</ul></li>";
					
					if ($tier > 1) {
						if ($ftier == $tier) {
							$html .= "<li id='lyzd-wrap'><a  class='" . $row->sm_id . "' href='" . site_url ( $sm_path ) . "'><strong><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</strong></a>";
						} else {
							$html .= "<li id='lyzd-wrap' class='" . $row->sm_id . "'  style='position:relative;width:145px;z-index:100'><a href='javascript:;' style='padding-left:23px;'  onmouseover=\"showdiv('" . $row->sm_id . "','" . $tier . "')\"  > " . iconvutf ( $sm_title ) . "</a>";
						}
						
						$html .= "<ul id='" . $sm_id . "' style='padding: 8px 0 8px 8px;text-align: center;display:none;position:absolute;left:140px;top:-1px;z-index:99;background-color: RGB(242,242,242);'>";
						$html .= getMenu ( $sm_id, $menuid, 0, $ftier );
						$html .= "</ul></li>";
					} else {
						if ($ftier == $tier) {
							$html .= "<li id='lyzd-wrap'><a  class='" . $row->sm_id . "' href='javascript:;'  onclick=\"showsubdiv('" . $row->sm_id . "')\" ><strong><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</strong></a>";
						} else {
							$html .= "<li id='lyzd-wrap' class='" . $row->sm_id . "' ><a  class='" . $row->sm_id . "' href='" . site_url ( $sm_path ) . "'  style='padding-left:23px;'  onmouseover=\"showdiv('" . $row->sm_id . "')\"  ><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</a>";
						}
						
						$html .= "<ul    id='" . $sm_id . "' style='padding-left:15px;' class='over'>";
						$html .= getMenu ( $sm_id, $menuid, 0, $ftier );
						$html .= "</ul></li>";
					}
				
				} else {
					// 三级菜单采用弹出样式
					if ($tier > 1) {
						if ($ftier == $tier) {
							$html .= "<li id='lyzd-wrap'><a  class='" . $row->sm_id . "' href='" . site_url ( $sm_path ) . "'><strong><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</strong></a>";
						} else {
							$html .= "<li id='lyzd-wrap' class='" . $row->sm_id . "'  style='position:relative;width:145px;z-index:100'><a href='javascript:;' style='padding-left:23px;'  onmouseover=\"showdiv('" . $row->sm_id . "','" . $tier . "')\"  > " . iconvutf ( $sm_title ) . "</a>";
						}
						
						$html .= "<ul  id='" . $sm_id . "' style='padding: 8px 0 8px 8px;text-align: center;display:none;position:absolute;left:140px;top:-1px;z-index:99;background-color: RGB(242,242,242);'>";
						$html .= getMenu ( $sm_id, $sm_id, 0, $ftier );
						$html .= "</ul></li>";
					} else {
						if ($ftier == $tier) {
							$html .= "<li id='lyzd-wrap'><a  class='" . $row->sm_id . "' href='javascript:;'  onclick=\"showsubdiv('" . $row->sm_id . "')\" ><strong><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</strong></a>";
						} else {
							$html .= "<li id='lyzd-wrap' class='" . $row->sm_id . "' ><a  class='" . $row->sm_id . "' href='" . site_url ( $sm_path ) . "'  style='padding-left:23px;'  onmouseover=\"showdiv('" . $row->sm_id . "')\"  ><label id='" . $sm_id . "_showing'>" . $space . "<img style='vertical-align:middle;margin-bottom:2px' src='common/images/strategy/right.png'></label> " . iconvutf ( $sm_title ) . "</a>";
						}
						
						$html .= "<ul id='" . $sm_id . "' style='padding-left:15px;display:none;'  class='over'>";
						$html .= getMenu ( $sm_id, $sm_id, 0, $ftier );
						$html .= "</ul></li>";
					}
				}
			}
		}
	} else {
		redirect ( "welcome" );
	}
	
	return $html;
}

// 获取面包屑
function getCrumbs($menuid) {
	if (isNull ( $menuid )) {
		$menuid = '5D8B08CF-3FC5-15E7-D399-A3CCC907353E';
	}
	
	$con = DB ();
	$html = "";
	$sql = "select sm_crumbs from dbo.t_sys_menu where sm_id = '$menuid'";
	$row = $con->query ( iconvgbk ( $sql ) )->row ();
	if (count ( $row ) > 0) {
		$html = iconvutf ( $row->sm_crumbs );
	}
	return $html;
	
	/*
	 * $html = getCrumbs2($menuid); return $html;
	 */
}

function getCrumbs2($menuid) {
	
	$site_url = site_url ();
	$command = "exec prc_get_crumbs '" . $menuid . "','" . $site_url . "'";
	$con = DB ();
	$rs = $con->query ( $command );
	$crumbs = '';
	if ($rs->num_rows > 0) {
		$row = $rs->row ();
		$crumbs = iconvutf ( $row->crumbs );
	}
	
	return $crumbs;
}

// 部门下拉列表
function getDepartment() {
	$con = DB ( 'ttm' );
	$sql = "select distinct LOV_KEY ,LOV_VALUE from ttm.dbo.chrm_view_employee cve
				inner join ttm.dbo.chrm_view_lov cvl on cvl.LOV_KEY = cve.HR_MAIN_DEPT and cvl.LOV_NAME='LOV_CHRM_HUM_MAINDEPT'
				where cve.WORK_STATUS<>4 and cve.HR_MAIN_DEPT<>'' and  cve.HR_MAIN_DEPT is not null 
				order by LOV_KEY ";
	$query = $con->query ( $sql );
	$html = "";
	if ($query->num_rows () > 0) {
		$result = $query->result ();
		foreach ( $result as $row ) {
			$html .= "<option value='" . $row->LOV_KEY . "'>" . iconvutf ( $row->LOV_VALUE ) . "</option>";
		}
	}
	return $html;
}

// 提取公告
function getAnnouncement() {
	$con = DB ();
	$sql = "select aa_content,t.key_name from t_admin_announcement a
                    inner join
                    (select value,key_name from t_sys_Dict where type='announcementType') t
                    on a.aa_announceType=t.value
                    where DATEADD(dd,
                        case a.aa_publishDays
                            when 1 then 3
                            when 2 then 5
                            when 3 then 7
                        end,
                        aa_publishDate)>=GETDATE()";
	$query = $con->query ( $sql );
	$result = $query->result ();
	$html = "";
	if ($query->num_rows () > 0) {
		foreach ( $result as $row ) {
			$html .= "【" . iconvutf ( $row->key_name ) . "】";
			$html .= iconvutf ( $row->aa_content );
		}
	}
	return $html;
}

// 记录日志
function write_log($menu_id, $actoin_id) {
	$res = '';
	
	$ssn = $_SESSION ['ssn'];
	if (! isnull ( $menu_id ) && ! isnull ( $actoin_id ) && ! isnull ( $ssn )) {
		$sql = "insert into t_admin_operationLog (aol_date,aol_userNum,aol_operMenuId,aol_operAction)
				values(GETDATE()," . sqlFilter ( $ssn ) . "," . sqlFilter ( $menu_id ) . "," . $actoin_id . ")";
		$con = DB ();
		$query = $con->query ( iconvgbk ( $sql ) );
		$res = 'SUCCESS';
	}
	
	return $res;
}

//将浏览者的行为记录到日志
function record_user_log($smid,$operation='浏览',$is_mult=0){
	$flag = false;
	$operation = @iconvgbk($operation);
	$log_date = time();
	$uid = $_SESSION['ssn'];
	$select_sql = "select b.sui_userName,b.sui_email,a.sm_crumbs from t_sys_menu a,t_sys_userInfo b where a.sm_id='{$smid}' and b.sui_userNum='{$uid}'";
	//$select_sql = "insert into t_sys_log(uid,smid,log_date,operation,is_mult) values('{$uid}','{$smid}','{$log_date}','{$operation}','{$is_mult}')";
	
	/*$sql = "select a.log_date,a.is_mult,c.sui_userName,c.sui_email,b.sm_crumbs from t_sys_log  a  join  t_sys_menu  b  on a.smid=b.sm_id  join t_sys_userInfo c on a.uid=c.sui_userNum".
			" where a.log_date>={$begin_date} and a.log_date<={$end_date} and (c.sui_userName like '%{$keyword}%' or c.sui_email like '%{$keyword}%' or b.sm_crumbs like '%{$keyword}%')  order by a.log_date desc";*/
	//$insert_sql = '';
	$con = DB ();
	$query = $con->query($select_sql);
	foreach($query->result() as $key=>$v){
		if($is_mult){
			$crumbs = $v->sm_crumbs.iconvgbk(" > 多维分析");
			$insert_sql = "insert into t_sys_logs(log_date,name,email,crumbs,operation) values('{$log_date}','{$v->sui_userName}','{$v->sui_email}','{$crumbs}','{$operation}')";
		}else{
			$insert_sql = "insert into t_sys_logs(log_date,name,email,crumbs,operation) values('{$log_date}','{$v->sui_userName}','{$v->sui_email}','{$v->sm_crumbs}','{$operation}')";
		}
		
	}
	$q = $con->query($insert_sql);
	if($q){
		return true; 
	}
	return $flag;
}

function getCheckNum($tabid) {
	$con = DB ();
	// $tabid=request('tabid');
	if (! $tabid)
		return '';
	$sql = "select *  from t_index_config tic,t_index2tab ti where ic_name NOT LIKE '%目标值%' and  tic.ic_id=ti.ic_id and ti.tc_id=$tabid and display_is_display=1 order by ti.it_ic_rank";
	$query = $con->query ( iconvgbk ( $sql ) );
	$num1 = $query->num_rows ();
	$sql = "select * from t_index_config tic,t_index2tab ti where ic_name NOT LIKE '%目标值%' and  tic.ic_id=ti.ic_id and ti.tc_id=$tabid order by ti.it_ic_rank";
	$query = $con->query ( iconvgbk ( $sql ) );
	$num2 = $query->num_rows ();
	$ischeck = '';
	if ($num1 == $num2) {
		$ischeck = 'checked=checked';
	}
	return $ischeck;
}
function excel_export($data_name, $data, $excelFileName, $sheetTitle) {
	/*
	 * excel导出函数 $data为从数据库中获取到的数据 $excelFileName下载的excel的文件名称
	 * $sheetTitle第一个工作区的名称
	 */

    /* 包含进phpexcel文件 */
    require_once './include/Classes/PHPExcel.php';
	require_once './include/Classes/PHPExcel/Writer/Excel2007.php';
	
	/* 实例化类 */
	$objPHPExcel = new PHPExcel ();
	
	/* 设置输出的excel文件为2007兼容格式 */
	// $objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);//非2007格式
	$objWriter = new PHPExcel_Writer_Excel2007 ( $objPHPExcel );
	
	/* 设置当前的sheet */
	$objPHPExcel->setActiveSheetIndex ( 0 );
	
	$objActSheet = $objPHPExcel->getActiveSheet ();
	
	/* sheet标题 */
	$objActSheet->setTitle ( $sheetTitle );
	
	$i = 1;
	$j = 'A';
	foreach ( $data_name as $value_name ) {
		
		$objActSheet->setCellValue ( $j . $i, $value_name );
		$j ++;
	}
	
	$i = 2;
	foreach ( $data as $value ) {
		/* excel文件内容 */
		$j = 'A';
		foreach ( $value as $value2 ) {
			// $value2=iconv("gbk","utf-8",$value2);
			$objActSheet->setCellValue ( $j . $i, $value2 );
			$j ++;
		}
		$i ++;
	}
	
	/* 生成文件 */
    /* $putPutFileName = "test.xlsx";
      $objWriter->save($putPutFileName); */

    /* 生成到浏览器，提供下载 */
    header ( "Pragma: public" );
	header ( "Expires: 0" );
	header ( "Cache-Control:must-revalidate,post-check=0,pre-check=0" );
	header ( "Content-Type:application/force-download" );
	header ( "Content-Type:application/vnd.ms-execl" );
	header ( "Content-Type:application/octet-stream" );
	header ( "Content-Type:application/download" );
	header ( 'Content-Disposition:attachment;filename="' . $excelFileName . '.xlsx"' );
	header ( "Content-Transfer-Encoding:binary" );
	$objWriter->save ( 'php://output' );
	
	/*
	 * 设置excel的属性： //设置当前的sheet $objPHPExcel->setActiveSheetIndex(0);
	 * //设置sheet的name $objPHPExcel->getActiveSheet()->setTitle(’Simple’); //创建人
	 * $objPHPExcel->getProperties()->setCreator(”Maarten Balliauw”); //最后修改人
	 * $objPHPExcel->getProperties()->setLastModifiedBy(”Maarten Balliauw”);
	 * //标题 $objPHPExcel->getProperties()->setTitle(”Office 2007 XLSX Test
	 * Document”); //题目 $objPHPExcel->getProperties()->setSubject(”Office 2007
	 * XLSX Test Document”); //描述
	 * $objPHPExcel->getProperties()->setDescription(”Test document for Office
	 * 2007 XLSX, generated using PHP classes.”); //关键字
	 * $objPHPExcel->getProperties()->setKeywords(”office 2007 openxml php”);
	 * //种类 $objPHPExcel->getProperties()->setCategory(”Test result file”);
	 * ——————————————————————————————————————– //设置单元格的值 //$t=$key+1
	 * $objPHPExcel->getActiveSheet()->setCellValue(’A$t′, ‘String’);
	 * $objPHPExcel->getActiveSheet()->setCellValue(’A2′, 12);
	 * $objPHPExcel->getActiveSheet()->setCellValue(’A3′, true);
	 * $objPHPExcel->getActiveSheet()->setCellValue(’C5′, ‘=SUM(C2:C4)’);
	 * $objPHPExcel->getActiveSheet()->setCellValue(’B8′, ‘=MIN(B2:C5)’);
	 * //合并单元格 $objPHPExcel->getActiveSheet()->mergeCells(’A18:E22′); //分离单元格
	 * $objPHPExcel->getActiveSheet()->mergeCells(’A18:E22′);
	 * $objPHPExcel->getActiveSheet()->mergeCells(’A28:B28′);
	 * $objPHPExcel->getActiveSheet()->unmergeCells(’A28:B28′); //保护cell
	 * $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // Needs
	 * to be set to true in order to enable any worksheet protection!
	 * $objPHPExcel->getActiveSheet()->protectCells(’A3:E13′, ‘PHPExcel’);
	 * //设置格式 // Set cell number formats echo date(’H:i:s’) . ” Set cell number
	 * formats\n”;
	 * $objPHPExcel->getActiveSheet()->getStyle(’E4′)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
	 * $objPHPExcel->getActiveSheet()->duplicateStyle(
	 * $objPHPExcel->getActiveSheet()->getStyle(’E4′), ‘E5:E13′ ); //设置宽width //
	 * Set column widths
	 * $objPHPExcel->getActiveSheet()->getColumnDimension(’B')->setAutoSize(true);
	 * $objPHPExcel->getActiveSheet()->getColumnDimension(’D')->setWidth(12);
	 * //设置font
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFont()->setName(’Candara’);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFont()->setSize(20);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFont()->setBold(true);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E1′)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D13′)->getFont()->setBold(true);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E13′)->getFont()->setBold(true);
	 * //设置align
	 * $objPHPExcel->getActiveSheet()->getStyle(’D11′)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D12′)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D13′)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	 * $objPHPExcel->getActiveSheet()->getStyle(’A18′)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
	 * //垂直居中
	 * $objPHPExcel->getActiveSheet()->getStyle(’A18′)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	 * //设置column的border
	 * $objPHPExcel->getActiveSheet()->getStyle(’A4′)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B4′)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	 * $objPHPExcel->getActiveSheet()->getStyle(’C4′)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D4′)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E4′)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	 * //设置border的color
	 * $objPHPExcel->getActiveSheet()->getStyle(’D13′)->getBorders()->getLeft()->getColor()->setARGB(’FF993300′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D13′)->getBorders()->getTop()->getColor()->setARGB(’FF993300′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’D13′)->getBorders()->getBottom()->getColor()->setARGB(’FF993300′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E13′)->getBorders()->getTop()->getColor()->setARGB(’FF993300′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E13′)->getBorders()->getBottom()->getColor()->setARGB(’FF993300′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’E13′)->getBorders()->getRight()->getColor()->setARGB(’FF993300′);
	 * //设置填充颜色
	 * $objPHPExcel->getActiveSheet()->getStyle(’A1′)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	 * $objPHPExcel->getActiveSheet()->getStyle(’A1′)->getFill()->getStartColor()->setARGB(’FF808080′);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	 * $objPHPExcel->getActiveSheet()->getStyle(’B1′)->getFill()->getStartColor()->setARGB(’FF808080′);
	 * //加图片 $objDrawing = new PHPExcel_Worksheet_Drawing();
	 * $objDrawing->setName(’Logo’); $objDrawing->setDescription(’Logo’);
	 * $objDrawing->setPath(’./images/officelogo.jpg’);
	 * $objDrawing->setHeight(36);
	 * $objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); $objDrawing =
	 * new PHPExcel_Worksheet_Drawing(); $objDrawing->setName(’Paid’);
	 * $objDrawing->setDescription(’Paid’);
	 * $objDrawing->setPath(’./images/paid.png’);
	 * $objDrawing->setCoordinates(’B15′); $objDrawing->setOffsetX(110);
	 * $objDrawing->setRotation(25); $objDrawing->getShadow()->setVisible(true);
	 * $objDrawing->getShadow()->setDirection(45);
	 * $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	 * //在默认sheet后，创建一个worksheet echo date(’H:i:s’) . ” Create new Worksheet
	 * object\n”; $objPHPExcel->createSheet();
	 */
}
function tran($num)
{
	$v = explode('.',$num);//把整数和小数分开
	$rl = '';//小数部分的值
        if(count($v)>1)$rl=$v[1];
	$j = strlen($v[0]) % 3;//整数有多少位
	if($j == 0) $j = 3;
	$w = ceil(strlen($v[0]) / 3 - 1);
	$sl = substr($v[0], 0, $j);//前面不满三位的数取出来
	$sr = substr($v[0], $j);//后面的满三位的数取出来
	$i = 0;
	$rvalue = '';
	while( $i <= strlen($sr) ){
		$insert = ',';
		$rvalue = $rvalue.$insert.substr($sr, $i, 3);//三位三位取出再合并，按逗号隔开
		$i = $i + 3;
		$w--;
	}
	$rvalue = $sl.$rvalue;
	$rvalue = substr($rvalue,0,strlen($rvalue)-1);//去掉最后一个逗号
	$rvalue = explode(',',$rvalue);//分解成数组

	if($rvalue[0]==0){
		array_shift($rvalue);//如果第一个元素为0，删除第一个元素
	}
	
	if($num<1000)
		return $num;
	else
		$rv = $rvalue[0];//前面不满三位的数
	for($i = 1; $i < count($rvalue); $i++){
		$rv = $rv.','.$rvalue[$i];
	}
	if(!empty($rl)){
		$rvalue = $rv.'.'.$rl;//小数不为空，整数和小数合并
	}else{
		$rvalue = $rv;//小数为空，只有整数
	}
	return $rvalue;
}

//绘制分页条
function drawPagebar($page_index, $total_page){
	$page_foot = '';
	if($page_index == 1){
		//$page_foot .= ' < ';
		$page_foot .= '';
	}else{
		$page_foot .= '<a href="javascript:goto_js_page('.($page_index-1).','.$total_page.');" > < </a>';
	}
	
	if($total_page > 7){
		if($page_index <= 4) {
			$i = 1;
			for($i; $i <= 5; $i++){
				if($i == $page_index) {
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');" id="page-wraped"> ' . $i . ' </a>';
				}else{
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');"> ' . $i . ' </a>';
				}
			}
			$page_foot .= '....';
			$page_foot .= '<a href="javascript:goto_js_page('.$total_page.','.$total_page.');"> ' . $total_page . '</a>'; //最后一页
		}else if($page_index > ($total_page - 4)) {
			$page_foot .= '<a href="javascript:goto_js_page(1,'.$total_page.');" > 1 </a>';
			$page_foot .= '....';
	
			$i = $total_page - 4;
			for($i; $i <= $total_page; $i++) {
				if($page_index == $i){
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');" id="page-wraped"> ' . $i . ' </a>';
				}else{
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');"> ' . $i . ' </a>';
				}
			}
		}else{
			$page_foot .= '<a href="javascript:goto_js_page(1,'.$total_page.');" > 1 </a>';     //第一页
			$page_foot .= '....';
			$i = $page_index - 2;
			$max_page = $page_index + 3;
			if($max_page > $total_page) {
				$max_page = $total_page;
			}
			for($i; $i < $max_page; $i++) {
				if($page_index == $i) {
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');" id="page-wraped"> ' . $i . ' </a>';
				}else{
					$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');"> ' . $i . ' </a>';
				}
			}
			$page_foot .= '....';
			//最后一页
			$page_foot .= '<a href="javascript:goto_js_page('.$total_page.','.$total_page.');" > ' . $total_page . '</a>';
		}
	}else{
		$i = 1;
		for ($i; $i <= $total_page; $i++) {
			if ($page_index == $i) {
				$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');" id="page-wraped"> ' . $i . ' </a>';
			} else {
				$page_foot .= '<a href="javascript:goto_js_page('.$i.','.$total_page.');"> ' . $i . '</a>';
			}
		}
	}
	
	if ($page_index == $total_page) {
		//$page_foot .= ' > ';
		$page_foot .= '';
	} else {
		$page_foot .= '<a href="javascript:goto_js_page('.($page_index+1).','.$total_page.');"> > </a>';
	}
	if ($total_page > 1) {
		$page_foot .= '<span>跳至： <input type="text" id="__page_input" class="writed" style="width:30px; height:20px"></span>';
		$page_foot .= '<span  class="btn-fj2"><a href="javascript:goto_js_page(0,'.$total_page.');" style="text-align:center">GO</a></span>';
	}
	//$page_foot .= '</div>';
	if ($total_page == 0) {
		$page_foot = '';
	}
	return $page_foot;
}
// send mail
function send_mail($email, $subject, $info) {
	require ("system/engine/mail_smtp.php");
	
	$send_res = "";
	$send_res .= $info;
	$smtpserver = "staff.sina.com.cn"; // SMTP服务器
	$smtpserverport = 25; // SMTP服务器端口
	$smtpusermail = "datasupport@staff.sina.com.cn"; // SMTP服务器的用户邮箱
	$smtpemailto = $email; // 发送给谁
	$smtpuser = "datasupport"; // SMTP服务器的用户帐号
	$smtppass = "zy2m33lb"; // SMTP服务器的用户密码
	$mailsubject = $subject; // 邮件主题
	$mailbody = $send_res; // 邮件内容
	$mailtype = "HTML"; // 邮件格式（HTML/TXT）,TXT为文本邮件
	                    // $mailsubject = iconv('gb2312//ignore',
	                    // 'utf-8//ignore', $mailsubject);
	$mailsubject = "=?UTF-8?B?" . base64_encode ( $mailsubject ) . "?=";
	// $mailbody = iconv('gb2312//ignore', 'utf-8//ignore', $mailbody);
	// 这里面的一个true是表示使用身份验证,否则不使用身份验证.
	$smtp = new smtp ( $smtpserver, $smtpserverport, true, $smtpuser, $smtppass );
	// $smtp->debug = TRUE;//是否显示发送的调试信息
	$smtp->sendmail ( $smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype, "", "", "", "UTF-8" );
}

/*	$pagesize		每页显示的数码
	 * 	$total			数据的总数目
	 */ 
  function getPage($p,$pagesize,$total){
		$pagecc = '';
		$tabid = request ( 'tabid' ) ? request ( 'tabid' ) : 1; 
		$pages = ceil ( $total / $pagesize );
		if($pages>1){
			$space3 = '&nbsp;&nbsp;&nbsp;';
			$pagecc .= '(' . $p . '/' . $pages . '页)' . $space3;
			if ($p > 1) {
				$pre = $p - 1;
				$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')>第一页</a>' . $space3 . '<a href=javascript:gopages(' . $pre . ',' . $tabid . ')>上一页</a>' . $space3;
			}
			if ($p > $pages || ! is_numeric ( $p )) {
				$pagecc = '无效页码';
			}else{
				for($i = 1; $i <= $pages; $i ++){
					$left = $p - 1;
					$left2 = $p - 2;
					$right = $p + 1;
					if ($p >= $pages - 1 && $pages > 3) { // 总页数大于3的末尾三页的样式
						if ($p != $pages) {
							$pagecc .= '<a href=javascript:gopages(' . $left . ',' . $tabid . ')>[' . $left . ']</a><a href=javascript:gopages(' . $p . ',' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> ' . $p . ' </font></a><a href=javascript:gopages(' . $right . ',' . $tabid . ')>[' . $right . ']</a>';
							break;
						} else { // 最后一页
							$pagecc .= '<a href=javascript:gopages(' . $left2 . ',' . $tabid . ')>[' . $left2 . ']</a><a href=javascript:gopages(' . $left . ',' . $tabid . ')>[' . $left . ']</a><a href=javascript:gopages(' . $p . ',' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> ' . $p . ' </font></a>';
							break;
						}
					} else {
						if ($p == 1) { // 第一页
							if ($pages == 2) { // 总页数为2
								$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 1 </font></a><a href=javascript:gopages(2,' . $tabid . ')>[2]</a>';
								break;
							} elseif ($pages == 3) { // 总页数为3
								$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 1 </font></a><a href=javascript:gopages(2,' . $tabid . ')>[2]</a><a href=javascript:gopages(3,' . $tabid . ')>[3]</a>';
								break;
							} else {
								$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 1 </font></a><a href=javascript:gopages(2,' . $tabid . ')>[2]</a><a href=javascript:gopages(3,' . $tabid . ')>[3]</a>...';
								break;
							}
						} else {
							if ($pages == 2) { // 总页数为2的第2页样式
								$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')>[1]</a><a href=javascript:gopages(2,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 2 </font></a>';
								break;
							} elseif ($pages == 3) { // 总页数为3
								if ($p == 2) { // 第2页
									$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')>[1]</a><a href=javascript:gopages(2,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 2 </font></a><a href=javascript:gopages(3,' . $tabid . ')>[3]</a>';
									break;
								} else { // 第3页
									$pagecc .= '<a href=javascript:gopages(1,' . $tabid . ')>[1]</a><a href=javascript:gopages(2,' . $tabid . ')>[2]</a><a href=javascript:gopages(3,' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> 3 </font></a>';
									break;
								}
							} else { // 总页数大于3的样式
								$pagecc .= '<a href=javascript:gopages(' . $left . ',' . $tabid . ')>[' . $left . ']</a><a href=javascript:gopages(' . $p . ',' . $tabid . ')><font color="#cc0000" style="font-size:12px; font-weight:normal"> ' . $p . ' </font></a><a href=javascript:gopages(' . $right . ',' . $tabid . ')>[' . $right . ']</a>...';
								break;
							}
						}
					}
				}
			}
			if ($p < $pages) {
				$pre = $p + 1;
				$pagecc .= $space3 . '<a href=javascript:gopages(' . $pre . ',' . $tabid . ')>下一页</a>' . $space3 . '<a href=javascript:gopages(' . $pages . ',' . $tabid . ')>最后一页</a>' . $space3;
			}
			$pagecc .= '(共' . $total . '条结果)' . $space3 . '<input type=text name="p" size=2 id="page"><input type=button  onclick=gopages($("#page").val(),' . $tabid . ') value=Go>';
		}
		return $pagecc;
	}
	function ShowMsg($msg, $gourl, $onlymsg=0, $limittime=0)
	{
		$htmlhead  ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$htmlhead .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n<head>\r\n<title>提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
		$htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0' bgcolor='#FFFFFF'>\r\n<center>\r\n<script>\r\n";
		$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";
		$litime = ($limittime==0 ? 1000 : $limittime);
		$func = '';
		if($gourl=='-1')
		{
			if($limittime==0) $litime = 5000;
			$gourl = "javascript:history.go(-1);";
		}
		if($gourl=='' || $onlymsg==1)
		{
			$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
		}
		else
		{
			$func .= "      var pgo=0;
			function JumpUrl(){
			if(pgo==0){ location='$gourl'; pgo=1; }
		}\r\n";
		$rmsg = $func;
		$rmsg .= "document.write(\"<br /><div style='width:450px;padding:0px;border:1px solid #DADADA;'>";
				$rmsg .= "<div style='padding:6px;font-size:12px;border-bottom:1px solid #DADADA;background:#DBEEBD;';'><b>提示信息！</b></div>\");\r\n";
				$rmsg .= "document.write(\"<div style='height:130px;font-size:10pt;background:#ffffff'><br />\");\r\n";
				$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
				$rmsg .= "document.write(\"";
			if($onlymsg==0)
			{
			if( $gourl != 'javascript:;' && $gourl != '')
			{
				$rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
				$rmsg .= "<br/></div>\");\r\n";
				$rmsg .= "setTimeout('JumpUrl()',$litime);";
			}
			else
			{
				$rmsg .= "<br/></div>\");\r\n";
			}
			}
			else
			{
			$rmsg .= "<br/><br/></div>\");\r\n";
		}
		$msg  = $htmlhead.$rmsg.$htmlfoot;
		}
			echo $msg;
			exit();
		}
		
		
