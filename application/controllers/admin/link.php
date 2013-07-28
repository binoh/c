<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Link extends CI_Controller {
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
	
	public function getMember(){
		$this->load->model(MODUAL_NAME.'/memberorder');
		$data['member']=$this->memberorder->getMember();
		//var_dump($data['member']);exit;
		$this->load->view(MODUAL_NAME.'/link_list',$data);
	}
}
