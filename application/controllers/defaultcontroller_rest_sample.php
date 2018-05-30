<?php

if (! defined ( 'mutyurphpmvc_inited' ))
	exit ( 'No direct script access allowed' );
	
/**
 *  @file workframe.php
 *  @brief Demonstrate controllers using in MutyurPHPMVC. Project home: https://github.com/vajayattila/MutyurPHPMVC
 *	@author Vajay Attila (vajay.attila@gmail.com)
 *  @copyright MIT License (MIT)
 *  @date 2017.04.11-2018.05.28
 *  @version 1.0.0.0
 */

class defaultcontroller extends workframe{
	protected $m_lang;
	protected $m_model;
	protected $m_session;
	protected $restserver; // for rest server	
	
	public function __construct(){
		parent::__construct();
		defaultcontroller::setup_dependencies(
			defaultcontroller::get_class_name(), '1.0.0.1', 'controller',	
			array(
				'defaultmodel'=>'1.0.0.2',
				'workframe'=>'1.0.0.2',
				'sesshandler'=>'1.0.0.1',
				'languagehandler'=>'1.0.0.0',
			)
		);
		$this->m_session=$this->load_extension('sesshandler');
		$this->m_model=$this->load_model('defaultmodel');
		$this->m_lang=$this->load_extension('languagehandler');
		$this->restserver=$this->load_extension('demorestserver');	// for restserver	
	}
	
	public function get_class_name() {
		return 'defaultcontroller';
	}
	
	public function index($caption=''){
		$slang=$this->m_session->get('language');
		// set language
		if($this->get_query_parameter('lang')=='hun'){
			$this->m_lang->set_language('hungarian');
			$this->m_session->set('language', 'hungarian');
		} else if($this->get_query_parameter('lang')=='eng'){
			$this->m_lang->set_language('english');
			$this->m_session->set('language', 'english');
		} else if($slang) {
			$this->m_lang->set_language($slang);
		}
		// set template
		$template=$this->get_query_parameter('template');
		if($this->get_query_parameter('template')=='default'){
			$this->m_session->set('css_template', 'default');
		} else if($this->get_query_parameter('template')=='dark'){
			$this->m_session->set('css_template', 'dark');
		} else if($this->get_query_parameter('template')=='positive'){
			$this->m_session->set('css_template', 'positive');
		} else if($this->get_query_parameter('template')=='negative'){
			$this->m_session->set('css_template', 'negative');
		} else {
			$template=$this->m_session->get('css_template');
		}
		if(!$template){
			$template=$this->get_config_value('design', 'css_template');
			if(!$template){
				$template='default';
			}
		}
		$data=array(
			'baseurl' => $this->m_model->get_base_url(),
			'message' => $this->m_model->get_message($this->m_lang),
			'request_uri' => $this->get_request_uri(),
			'dependencies' => $this->get_array_of_dependencies(),
			'template' => $template,	
			'caption' => $caption!==''?$caption:$this->m_lang->get_item('caption')
		);
		$this->load_view('defaultview', $this->m_model, $data);		
	}

	public function test(){
		echo 'Test is works!<br>'; 
		echo $this->get_request_method().'<br>'; // Which request method was used to access the page; i.e. 'GET', 'HEAD', 'POST', 'PUT'. */
		$params=$this->get_query_parameters();
		print_r($params);
		echo '<br>';
		if(is_array($params)&&0<sizeof($params)){
			foreach($params as $item){
				$name=$item['name'];
				echo $name."=>".$this->get_query_parameter($name).'<br>';
			}
		}
	}

	public function service(){ // for restservice
		/*
			Examples: 
			For GET methods:
			http://127.0.0.1:8001/service?action=restsrv_getversion
			http://127.0.0.1:8001/service?action=echo&message=helloworld

			For POST method with curl:
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "restsrv_getversion"}'
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "echo","message": "Hello World!"}'
			
		*/
		$this->restserver->execute(); 
	}	
	
}

// Eof defaultcontroller.php
