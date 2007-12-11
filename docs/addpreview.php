<?php
require_once('../includes/init.php');
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

		//convert any urls to links and add paragraphs (we do this perminently on save)
		$html_desc = raw_urls_to_links($this->group->description);
		$html_desc = new_lines_to_paragraphs($html_desc);

		$this->group->created_date = mktime();

		//page vars
	    $this->page_title = "preview your group";
	    $this->menu_item = "add";	
	    $this->show_tracker = true;		
	    $this->tracker_location = 5;			
		$this->assign('group', $this->group);
		$this->assign('description', $html_desc);
		$this->assign('preview', true);
		$this->assign('dead_links', true);
		$this->assign('map_js', true);
		$this->assign('mini_map', 1);		
		$this->assign('google_maps_key', GOOGLE_MAPS_KEY);
		$this->onloadscript = 'load(' . $this->group->long_centroid . ', ' .
		$this->group->lat_centroid .', ' . ($this->group->zoom_level -1) . ')';
		
	

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

		//convert raw urls to links
		$this->group->description = raw_urls_to_links($this->group->description);
		$this->group->description = new_lines_to_paragraphs($this->group->description);	

		//Standard save, user creating a new group
		if($this->group->mode == "user"){
			//save data
			$this->group->confirmed = 0;
			$this->group->set_url_id();
			if(!$this->group->insert()){
				trigger_error('Error saving group');
				$this->add_warning($this->smarty->translate("Something went wrong when we tried to save your group. (We're looking into it)."));
			}else{

				//send confirmation email
				$confirmation = factory::create('confirmation');
				$confirmation->send($this->group->created_email, 
					EMAIL_PREFIX . "Confirm the group '" . $this->group->name . "'  on " . SITE_NAME,
					"Click on the link below to confirm you want to add " . $this->group->name . " to " . SITE_NAME . ":",
					"groups", $this->group->group_id);

				//clear the session
				session_write('group', null);

				//send to check email page
				redirect(WWW_SERVER . '/checkemail.php?type=group');
			}
		}elseif($this->group->mode == "admin"){
			if(!$this->group->update()){
				trigger_error('Error saving group (in admin mode)');
				$this->add_warning($this->smarty->translate("Something went wrong when we tried to update this group."));
			}else{
				redirect(ADMIN_SERVER . "/searchgroups.php?q=" . urlencode($this->group->name) . "&mode=saved");
			}
		}else{
			trigger_error("Unknown mode when saving group: " . $this->group->mode);	
		}

	}

}

//create class instance
$addpreview_page = new addpreview_page();

?>
