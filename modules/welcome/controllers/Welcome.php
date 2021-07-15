<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends AppController {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view('welcome_message');
	}
}
