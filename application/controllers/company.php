<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {
	public function __construct(){
		parent::__construct();
		//loginValidate ();
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}
}
