<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContentModel extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		//loginValidate ();
	}

	public function getContent($param){
		$sql='SELECT * FROM  `binoh_posts` where ID = "'.$param['content_id'].'"';
		$query=$this->db->query($sql);
		$data=$query->row();
		return $data;
	}
	public function upContent($data){
		$this->db->where('id',$data['content_id']);
		$result=$this->db->update('binoh_posts',$data['update_arr']);
		return $result;
	}
	public function insertContent($data){
		//return var_dump($data);exit;
		$result_post=$this->db->insert('binoh_posts',$data['update_arr']);
		$post_id=mysql_insert_id();
		//$result_term='insert into `binoh_term_relationships` 
		$insert_arr=array();
		$insert_arr['object_id']=$post_id;
		$insert_arr['term_taxonomy_id']=$data['category_id'];
		$insert_arr['term_order']=0;
		//return var_dump($insert_arr);
		$result_insert=$this->db->insert('binoh_term_relationships',$insert_arr);
		return $result_insert && $result_post;
	}
	
}
