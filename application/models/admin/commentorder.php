<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class commentOrder extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		//loginValidate ();
	}
	public function getComment(){
		$sql='select a.*,b.ID,b.post_title from binoh_comments as a right join binoh_posts as b on a.comment_post_ID=b.ID';
		$query=$this->db->query($sql);
		$parent=$query->result_array();
		return $parent;
	}
	function getPageComment($comment_ID){
		
	}
}
