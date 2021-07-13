<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends AdminController {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$d = array();
		$d = new stdClass;
		$d->heading = 'Dashboard Heading';
		$d->title = 'Dashboard Title';
		$d->description = 'Dashboard Description';
		$d->url = 'Dashboard Link';

		$this->theme->render('welcome', array(
			'title' => 'Dashboard',
			'heading' => 'Dashboard',
			'is_role' => 'admin',
			'breadcrumb' => 'dashboard',
			'dashboard' => $d
		));
	}
}
