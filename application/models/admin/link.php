<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class linkOrder extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		//loginValidate ();
	}
	public function getLink(){
		$sql='select * from binoh_comments';
		$query=$this->db->query($sql);
		$data=$query->result_array();
		//var_dump($data);exit;
		return $data;
	}
}
