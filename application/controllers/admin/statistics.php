<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends CI_Controller {
	public function __construct(){
		parent::__construct();
		define('MODUAL_NAME','admin');
		define('DIR',$this->config->base_url().'index.php/admin');
		define('TEMPROOT','/common');
		//loginValigate();
	}

	public function index()
	{
		$data['dir']=DIR.'/main';
		//$this->load->view(DIR.'/comtent_left',$data);
	}
	public function mainShow()
	{
		$this->load->model(MODUAL_NAME.'/term');
		$data['category']=$this->term->getCategory();
		$html=$this->load->view(MODUAL_NAME.'/statistics',$data,true);
		echo json_encode($html);
	}
	
}
