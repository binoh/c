<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Term extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		//loginValidate ();
	}

	public function getCategory()
	{
		$sql = 'select a.term_taxonomy_id,b.term_id,b.name,b.term_group from binoh_term_taxonomy as a left join binoh_terms as b on a.term_id = b.term_id where a.taxonomy = "category" and a.parent = 0 order by b.term_group ';
		$query = $this->db->query($sql);
		$catagory= $query->result();
		$sql = 'select a.term_taxonomy_id,b.term_id,b.name,a.parent,b.term_group from binoh_term_taxonomy a left join binoh_terms b on a.term_id = b.term_id where a.taxonomy ="category" and a.parent <> 0 order by b.term_group';
		$query = $this->db->query($sql);
		$catagory2 = $query->result();
		$m=0;
		foreach($catagory as $value){
			$data[$m]['term_id']=$value->term_id;
			$data[$m]['name']=$value->name;
			$data[$m]['term_taxonomy_id']=$value->term_taxonomy_id;
			foreach($catagory2 as $value2){
				$n=0;
				if($value2->parent == $value->term_id){
					$data[$m]['son'][$n]['term_id']=$value2->term_id;
					$data[$m]['son'][$n]['name']=$value2->name;
					$data[$m]['son'][$n]['parent']=$value2->parent;
					$data[$m]['son'][$n]['term_taxonomy_id']=$value2->term_taxonomy_id;
					$n++;
				}
			}
			$m++;
		}
		return $data;
		//echo $this->config->item('base_url');exit;
		//var_dump($data);exit;
	}
	public function getContent($tax_id){
		//$sql='select * from binoh_posts where id in(select object_id from binoh_term_relationships where term_taxonomy_id ="'.$tax_id.'")';
		$sql='select a.object_id,a.term_order,b.* from binoh_term_relationships a right join binoh_posts b on a.object_id =b.id where term_taxonomy_id ="'.$tax_id.'"';
		$query=$this->db->query($sql);
		$data=$query->result_array();
		//echo $sql;exit;
		return $data;
	}
	public function update($id_arr,$update_arr){
		foreach($id_arr as $key=>$value){
		$query[]=$this->db->update('binoh_terms',$update_arr[$key],$value);
		//$result=$query == false ?false:true;
		}
		$result=eval('return '.join('&&',$query).';');
		return $result;
	}
	public function insert($in_arr){
		foreach($in_arr as $key=>$value){
			$new_arr2=array('name'=>$value['name'],'term_group'=>$value['term_group'],'slug'=>'test');
			$query[]=$this->db->insert('binoh_terms',$new_arr2);
			$term_id=mysql_insert_id();
			$new_arr=array('term_taxonomy_id'=>$term_id,'term_id'=>$term_id,'parent'=>$value['parent'],'taxonomy'=>'category');
			$query2[]=$this->db->insert('binoh_term_taxonomy',$new_arr);
		}
		$result=eval('return '.join('&&',$query).';');
		return $result;
	}
	
}
