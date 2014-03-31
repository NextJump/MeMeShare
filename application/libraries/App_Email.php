<?php
class App_Email extends CI_Email {
    
    public function to($to) {
        if (ENVIRONMENT !== 'production') {
            // Get current user
            $CI =& get_instance();
            $CI->load->library('session');
            $user = $CI->session->userdata('user');
            $to = $user->email;
        }
        return parent::to($to);
    }
    
    public function from($from, $name = '') {
        $CI =& get_instance();
        $CI->config->load('email');
        
        // Force the from name to pull from the config files
        $name = $CI->config->item('from_name');
        return parent::from($from, $name);
    }
    
    public function subject($subject) {
        $CI =& get_instance();
        $CI->config->load('email');
        
        // Prepend the subject with the subject prefix from the config file
        $subjectPrefix = $CI->config->item('subject_prefix');
        return parent::subject($subjectPrefix . $subject);
    }
}