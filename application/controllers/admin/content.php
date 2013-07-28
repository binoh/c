<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content extends CI_Controller {
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
		$html=$this->load->view(MODUAL_NAME.'/content_left',$data,true);
		echo json_encode($html);
	}
	public function mainShow2()
	{
		$this->load->model(MODUAL_NAME.'/term');
		$data=$this->term->getCategory();
		$html='';
		 foreach($data as $value){
		         $html.='<div class="list_tilte"><span><a target="" href="'.DIR.'/content/getContent?tax_id='.$value['term_taxonomy_id'].'">'.$value['name'].'</a></span></div>';
		         if(isset($value['son'])){
						foreach($value['son'] as $value2){
		         $html.='<div class="list_detail"><ul><li><a href="'.DIR.'/content/getContent?tax_id='.$value2['term_taxonomy_id'].'">'.$value2['name'].'</a></li></ul></div>';
		         		}
					}
		 		}
			echo json_encode($html);
		//var_dump($data['category'][1]['son']['name']);exit;
		//$this->load->view(MODUAL_NAME.'/content_left',$data);
	}
	public function getContent(){
		$term_taxonomy_id=request('category_id');
		$this->load->model(MODUAL_NAME.'/term');
		$data['content']=$this->term->getContent($term_taxonomy_id);
		//var_dump($data['content']);exit;
		$html=$this->load->view(MODUAL_NAME.'/content_list',$data,true);
		echo json_encode($html);
	}
	public function addContentShow(){
		$data['category_id']=request('category_id');
		$this->load->model(MODUAL_NAME.'/term');
		$data['category']=$this->term->getCategory();
		$html=$this->load->view(MODUAL_NAME.'/content_add',$data,true);
		echo json_encode($html);
	}
	public function delContent(){
		$content_id=request('content_id');
		$category_id=request('category_id');
		echo json_encode($content_id);
	}
	public function editContent(){
		//编辑文章页面显示
		$param['content_id']=request('content_id');
		$category_id=request('category_id');
		$this->load->model(MODUAL_NAME.'/contentmodel');
		$data['content']=$this->contentmodel->getContent($param);
		$this->load->model(MODUAL_NAME.'/term');
		$data['category']=$this->term->getCategory();
		$data['content_id']=$param['content_id'];
		//var_dump($data['category']);exit;
		$data['category_id']=$category_id;
		$html=$this->load->view(MODUAL_NAME.'/content_edit',$data,true);
		echo json_encode($html);
	}
	function modifyContent(){
		//修改文章 
		$title=request('title');
		$content=request('content');
		$content_id=request('content_id');
		$update_arr=array();
		$update_arr['post_title']=$title;
		$update_arr['post_content']=$content;
		$param['content_id']=$content_id;
		$param['update_arr']=$update_arr;
		$this->load->model(MODUAL_NAME.'/contentmodel');
		$result=$this->contentmodel->upContent($param);
		echo json_encode($result);
		//$sql='update binoh_posts set post_title="'.$content_title.'" post_content="'.$content_con.'" where ID='
		 
	}
	function addContent(){
		//修改文章
		$data=request('data');
		//echo var_dump($data);exit;
		$data=json_decode($data);
		//echo var_dump($_POST['data']);exit;
		//echo var_dump($data);exit;
		$title=$data->title;
		$content=$data->content;
		$category_id=$data->category_id;
		/* echo var_dump($_POST['data']);exit;
		echo $category_id;exit; */
		$update_arr=array();
		$update_arr['post_title']=$title;
		$update_arr['post_content']=$content;
		$param['category_id']=$category_id;
		$param['update_arr']=$update_arr;
		$this->load->model(MODUAL_NAME.'/contentmodel');
		//var_dump($param);exit;
		$result=$this->contentmodel->insertContent($param);
		echo json_encode($result);
		//$sql='update binoh_posts set post_title="'.$content_title.'" post_content="'.$content_con.'" where ID='
			
	}
}
