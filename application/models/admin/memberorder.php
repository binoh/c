<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class memberOrder extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		//loginValidate ();
	}
	public function getMember(){
		$sql='select * from binoh_users';
		$query=$this->db->query($sql);
		$data=$query->result_array();
		//var_dump($data);exit;
		return $data;
	}
	function memberEditShow($id){
		$sql='select * from binoh_users where ID="'.$id.'"';
		$query=$this->db->query($sql);
		$data=$query->row_array();
		//var_dump($data);exit;
		return $data;
	}
	function upMember($data,$where){
		//var_dump($where);exit;
		$query=$this->db->update('binoh_users',$data,$where);
		return $query;
	}
}
