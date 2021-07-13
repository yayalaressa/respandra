<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Theme {
	
		private $theme_data = array();
		
		function __construct() {
			$this->CI =& get_instance();
		}

		private function _set($name, $value){
			$this->theme_data[$name] = $value;
		}

		private function _load($theme = '', $view = '', $view_data = array(), $return = FALSE)
		{  
			$this->_set('content', $this->CI->load->view($view, $view_data, TRUE));	
			$this->CI->load->view($theme, $this->theme_data, $return);
		}
		
		public function render($view = '', $data = array(), $return = FALSE)
		{
			$this->_load('layout',  $view, $data, $return);
		}
		
}