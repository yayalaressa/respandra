<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


use \Kanti\HubUpdater;
use \Michelf\MarkdownExtra;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

/* load the MX_Router class */
require APPPATH . "third_party/MX/Controller.php";

class MY_Controller extends MX_Controller {}

class Controller extends MX_Controller
{	

	function __construct() 
	{
		parent::__construct();
		$helpers = array('dispatch', 'url', 'file', 'form', 'email');
		$libraries = array('session', 'flash_message', 'theme', 'form_validation', 'upload', 'email', 'pagination');
		$this->load->helper($helpers);
		$this->load->library($libraries);
		$this->_hmvc_fixes();
	}
	
	function _hmvc_fixes()
	{		
		//fix callback form_validation		
		//https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;
	}

}

class AdminController extends MX_Controller
{	

	function __construct() 
	{
		parent::__construct();
		$helpers = array('dispatch', 'url', 'file', 'form', 'email');
		$libraries = array('session', 'flash_message', 'theme', 'form_validation', 'upload', 'email', 'pagination');
		$this->load->helper($helpers);
		$this->load->library($libraries);
		$this->_hmvc_fixes();
	}
	
	function _hmvc_fixes()
	{		
		//fix callback form_validation		
		//https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;
	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
