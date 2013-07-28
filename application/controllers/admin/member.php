<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {
	public function __construct(){
		parent::__construct();
		define('MODUAL_NAME','admin');
		define('DIR',$this->config->base_url().'index.php/admin');
		define('TEMPROOT','/common/'.MODUAL_NAME);
		//loginValigate();
	}

	public function index()
	{
		$data['dir']=DIR.'/member';
		$this->load->view(DIR.'/comtent_left',$data);
	}
	public function mainShow(){
		$this->load->model(MODUAL_NAME.'/memberorder');
		$data['member']=$this->memberorder->getMember();
		//var_dump($data['member']);exit;
		$html=$this->load->view(MODUAL_NAME.'/member_list',$data,true);
		echo json_encode($html);
	}
	function memberEditShow(){
		$id=request('id');
		$this->load->model(MODUAL_NAME.'/memberorder');
		$data['member']=$this->memberorder->memberEditShow($id);
		$html=$this->load->view(MODUAL_NAME.'/member_edit',$data,true);
		echo json_encode($html);
	}
	function memberEdit(){
		$where['ID']=request('id');
		$data['user_login']=request('user_login');
		$data['display_name']=request('display_name');
		$data['user_email']=request('user_email');
		$data['user_status']=request('user_status');
		$data['user_pass']=request('pw1');
		$this->load->model(MODUAL_NAME.'/memberorder');
		$statu=$this->memberorder->upMember($data,$where);
		echo json_encode($statu);
	}
	
}
