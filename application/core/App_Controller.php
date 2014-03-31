<?php
class App_Controller extends CI_Controller {
    public $layout = 'default';
    
    public function __construct () {
        parent::__construct();
        date_default_timezone_set('America/New_York');
        $this->load->library('user_agent');
    }
    
    public function resetUser() {
        if (!isset($this->user)) {
            return;
        }
    
        $this->load->model('UserModel');
    
        // Update the session's User object
        $this->UserModel->get($this->user->id);
        
        // Pull the session user object back into this controller
        $this->user = $this->session->userdata('user');
    }
}