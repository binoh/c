<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class termAll extends CI_Controller {
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
		$this->load->view(DIR.'/comtent_left',$data);
	}
	public function mainShow()
	{
		$this->load->model(MODUAL_NAME.'/term');
		$data['category']=$this->term->getCategory();
		$html=$this->load->view(MODUAL_NAME.'/category_list',$data,true);
		echo json_encode($html);
	}
	public function updateTerm()
	{
		$sort=json_decode(request('sort'));
		$m=0;
		$n=0;
// 		var_dump($data);exit;
		foreach($sort as $key=>$value){
			if($key !== 'insert'){
			$id_arr[$m]['term_id']=$key;
			$up_data[$m]['term_group']=$value->term_group;
			$up_data[$m]['name']=$value->name;
			$m++;}else{
			$in_data[$n]['term_group']=$value->term_group;
			$in_data[$n]['name']=$value->name;
			$in_data[$n]['parent']=$value->parent;
			$n++;
			}
		}
		//var_dump($up_data);exit;
		$this->load->model(MODUAL_NAME.'/term');
		$statue_up=$this->term->update($id_arr,$up_data);
		$statue_in=$this->term->insert($in_data);
		echo json_encode($statue_in && $statue_up);
	}
	
}
