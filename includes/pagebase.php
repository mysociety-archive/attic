<?php
require_once ("init.php");
require_once ("smarty/intsmarty.class.php");
require_once 'tracking.php';

abstract class pagebase {

 	//properties
	protected $warnings;
	protected $onloadscript = "";
	protected $set_focus_control ="";
    protected $smarty;
    protected $page_title = "";
    protected $menu_item = "";
    protected $show_tracker = false;
    protected $tracker_location = 0;
    protected $viewstate = array();
    protected $command = "";    
    protected $argument = "";
    protected $warn_controls =  array();
    protected $post_inputs = array();
    protected $smarty_template ="";
	protected $data = null;
	protected $rss_link = '';
	protected $track = '';
	protected $use_body_script = false;	

    //Constructor
    public function __construct(){

        //setup smarty, with default template
        $this->reset_smarty($this->get_template_name());
        
        //check for postback and grab any commands
        if (isset($_POST["_is_postback"])) {
            $this->unserialize_viewstate();
            $this->get_postback_command();
        }

        $this->warnings = array();
		
        //load function (a way of child classes running code at the point of class construction)
        $this->load();
        //check for postback vs load
        if (isset($_POST["_is_postback"])) {
            $this->get_data();
            $this->unbind();
            $this->process();		
        }else{
            $this->setup();
            $this->bind();
        }
    }

    //Display template (also assigns default properties)
    public function display_template($echo = false){

        $this->smarty->assignLang("site_name", SITE_NAME);                  
        $this->smarty->assignLang("site_tag_line", SITE_TAG_LINE);                 
        $this->smarty->assign("root_dir", ROOT_DIR);        
        $this->smarty->assign("www_server", WWW_SERVER);
        $this->smarty->assign("admin_server", ADMIN_SERVER);
        $this->smarty->assign("domain", DOMAIN);
        $this->smarty->assign("secure_server", SECURE_SERVER);
        $this->smarty->assign("form_action", htmlspecialchars($_SERVER['PHP_SELF']) . '?' . $_SERVER["QUERY_STRING"]);
        $this->smarty->assign("onloadscript", $this->onloadscript);
        if ($this->track)
            $this->track = track_code($this->track);
        $this->smarty->assign('track', $this->track);
        if(sizeof($this->warn_controls) == 0){
            $this->smarty->assign("set_focus_control", $this->set_focus_control);
        }else{
            $this->smarty->assign("set_focus_control", $this->warn_controls[0]);		
        }
        $this->smarty->assign("warnings", $this->warnings);
        $this->smarty->assign("data", $this->data);
        $this->smarty->assign("show_warnings", sizeof($this->warnings) >0);
        $this->smarty->assign("warn_controls", $this->warn_controls);        
        $this->smarty->assign("page_title", htmlspecialchars($this->page_title));
        $this->smarty->assign("menu_item", $this->menu_item);		
        $this->smarty->assign("tracker_location", $this->tracker_location);				
        $this->smarty->assign("show_tracker",$this->show_tracker);				
        $this->smarty->assign("viewstate", $this->serialize_viewstate());
        $this->smarty->assign("googleanalytics", DOC_DIR . "templates/googleanalytics.tpl");
		$this->smarty->assign("rss_link", $this->rss_link);
		$this->smarty->assign("current_url", $_SERVER['REQUEST_URI']);
		$this->smarty->assign("google_maps_key", GOOGLE_MAPS_KEY);
		$this->smarty->assign("use_body_script", $this->use_body_script);

        foreach($this->warn_controls  as $warn_control) {
            $this->assign('warn_' . $warn_control, true);
        }

        if ($echo != false){
            $this->smarty->display($this->smarty_template);
        }else{
           echo $this->smarty->fetch($this->smarty_template);    
        }
    }

	// assign smarty var
	public function assign($name, $value){
		$this->smarty->assign($name, $value);
	}
	
	public function assignLang($name, $value){
		$this->smarty->assignLang($name, $value);
	}

    //Reset smarty object and template path
    public function reset_smarty($template_file){

    	$template_folder = null;
        $this->smarty = new IntSmarty();
        $this->smarty->compile_dir = SMARTY_PATH;
        $this->smarty->compile_check = true;
        $this->smarty->force_compile = DEVSITE === true;
        $this->smarty->template_dir = TEMPLATE_DIR;
        $this->smarty->lang_path = LANGUAGE_DIR;

        //call setup (basically a standard smarty constuct with the translation thrown in)
        $this->smarty->setup("en-us");

        $this->smarty_template = $template_file;
    }
    
    //Get template name - uses reflection to try and guess the template name
    //saves having to assign it in *every* file
    public function get_template_name(){

       return str_replace("_","",str_replace("_page", "", get_class($this))) . ".tpl";
    }

    //serialise and hash data for storing between postbacks
    private function serialize_viewstate(){
        return base64_encode(serialize($this->viewstate));
    }
    
    private function unserialize_viewstate(){
        if (isset($_POST['_viewstate'])){
            $this->viewstate = unserialize(
            base64_decode($_POST['_viewstate']));
        }
    }

	public function add_warning($warning){
		array_push($this->warnings, $warning);
	}
	
	public function clear_warnings(){
		$this->warnings = array();
	}

	public function add_warn_control($control_id){
		array_push($this->warn_controls, $control_id);
	}

	public function clear_warn_controls(){
		$this->warn_controls = array();
	}

	protected function get_data(){

		$this->data = $_POST;
	}

	protected function load(){
	
	}
	
	protected function setup(){
	
	}
	
	protected function bind(){
		$this->display_template();
	}
	
	protected function unbind(){
	
	}
	
	protected function validate(){
	
	}
	
	protected function process(){
	
	}
    
    private function get_postback_command(){
        if (isset($_POST["_postback_command"])){
            $this->command = $_POST["_postback_command"];
        }
        if (isset($_POST["_postback_argument"])){
            $this->argument = $_POST["_postback_argument"];
        }
    }

	protected function set_user($user){
		$_SESSION['user'] = $user;
	}
	
	protected function get_user(){
		if(isset($_SESSION['user'])){
			return $_SESSION['user'];
		}
	}
	
	protected function is_signed_in(){
		return isset($_SESSION['user']);	
	}
	
	protected function signout(){
		unset($_SESSION['user']);
	}

}
    
?>
