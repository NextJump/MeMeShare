<?php

class User {

    public function index() {
        $CI =& get_instance();

        if (!isset($CI->user)) {
            if ($CI->session->userdata('user')) {
                // Make the session User object available in the controller
               $CI->user = $CI->session->userdata('user');
            } else {
                // If a user is hitting a page but requires authentication, redirect to the login/index action
                $controller = $CI->router->fetch_class();
                $action = $CI->router->fetch_method();
                
                if (!(in_array($controller, array('login', 'environmentdebug')) &&
                        in_array($action, array('index', 'oauth2callback')))) {
                    $uri = $CI->uri->uri_string();
                    $CI->session->set_userdata('redirect', '/'.$uri);
                    redirect('/login');
                    exit;
                }
            }
        }
    }
}