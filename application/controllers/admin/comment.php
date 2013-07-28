<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller {
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
		$this->load->model(MODUAL_NAME.'/commentorder');
		$data['comment_arr']=$this->commentorder->getComment();
		$html=$this->load->view(MODUAL_NAME.'/comment_list',$data,true);
		echo json_encode($html);
	}
}
