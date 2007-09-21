<?php
require_once('init.php');
require_once('table_classes/group.php');

class addpreview_page extends pagebase {

	//properties
	private $group = null;
	
	public function __construct(){
		parent::__construct();
	}
	
	//load
	protected function load(){
		if(session_read('group') == ''){
			redirect(WWW_SERVER . "/add/about/");
		}else{
			$this->group = session_read('group');
		}
	}
	
	//setup
	protected function setup (){
	
	}

	//Bind
	protected function bind() {

		//page vars
	    $this->page_title = "preview your group";
	    $this->menu_item = "add";	
	    $this->show_tracker = true;		
	    $this->tracker_location = 5;			
		$this->assign('group', $this->group);
		$this->assign('preview', true);
		
		$this->display_template();
					
	}

	//Unbind
	protected function unbind (){
	
	}

	//Validate
	protected function validate (){

	}

	//Process page
	protected function process (){

		//save data
		$this->group->confirmed = 0;
		$this->group->set_url_id();
		if(!$this->group->insert()){
			trigger_error('Error saving group');
			$this->add_warning("Something went wrong when we tried to save your group. (We're looking into it).");
		}else{
		
			//send confirmation email
			$confirmation = factory::create('confirmation');
			$confirmation->send($this->contact_email->from_email, 
				EMAIL_PREFIX . "Confirm your group'" . $this->group->name . " on " . SITE_NAME,
				"Click on the link below to confirm you want to add your to " . SITE_NAME . ":",
				"groups", $this->group->group_id);
		
			//clear the session
			session_write('group', null);

			//send to check email page
			redirect(WWW_SERVER . '/checkemail.php?type=group');
		}

	}

}

//create class instance
$addpreview_page = new addpreview_page();

?>