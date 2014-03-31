<?php

require_once 'application/libraries/google-api-php-client/src/Google_Client.php';

class Login extends App_Controller {
    public $layout = 'noheader';
    
    private $GOOGLE_DEVELOPER_KEY;
    private $GOOGLE_CLIENT_ID;
    private $GOOGLE_CLIENT_SECRET;
    private $GOOGLE_REDIRECT_URI;
    private $GOOGLE_DOMAIN;
    private $GOOGLE_APPLICATION_NAME;
    
    public function __construct() {
        parent::__construct();
        
        // Load the Google App parameters
        $this->config->load('google');
        $this->GOOGLE_DEVELOPER_KEY = $this->config->item('GOOGLE_DEVELOPER_KEY');
        $this->GOOGLE_CLIENT_ID = $this->config->item('GOOGLE_CLIENT_ID');
        $this->GOOGLE_CLIENT_SECRET = $this->config->item('GOOGLE_CLIENT_SECRET');
        $this->GOOGLE_REDIRECT_URI = $this->config->item('GOOGLE_REDIRECT_URI');
        $this->GOOGLE_DOMAIN = $this->config->item('GOOGLE_DOMAIN');
        $this->GOOGLE_APPLICATION_NAME = $this->config->item('GOOGLE_APPLICATION_NAME');
    }

    /** 
     * This is the main login screen.  It prompt the user to log in with their Google App credentials.
     */
    public function index() {
        $this->load->model('UserModel');
        $this->UserModel->get(1);

        // Generate a token to prevent cross-site request forgery (this should be checked in oauth2callback()
        $csrf = $this->generateCSRFToken();
        $state = $this->session->userdata('csrf_state');
        
        // Create a Google client to access their API
        $client = $this->getGoogleClient();
        $client->setState($state);
        
        // Generate the authentication URL and append the hd parameter to limit the valid domains
        $authUrl = $client->createAuthUrl();
        $authUrl .= '&hd=' . $this->GOOGLE_DOMAIN;
        
        // Figure out how many minutes have been logged to date
        $this->load->model('InteractionModel');
        $minutesLogged = $this->InteractionModel->getMinutesLoggedThirtyDays();
        
        // Load the view
        $data = array();
        $data['authUrl'] = $authUrl;
        $data['minutesLogged'] = $minutesLogged;
        $this->load->view('login/index', $data);
    }
    
    /**
     * This action is hit when Google redirects users for the OAuth callback
     */
    public function oauth2callback() {
        $test_state = $this->input->get('state');
        $code = $this->input->get('code');
        
        // Validate the state passed back against what's stored in the session
        // TODO
        
        // Create a Google client to access their API
        $client = $this->getGoogleClient();
        
        // Exchange the request token (code) for an access token
        if ($code) {
            // Authenticate the request token (do the exchange)
            $client->authenticate($code);
            
            // Store the access token into the session
            $this->session->set_userdata('access_token', $client->getAccessToken());
            
            // Redirect back here and drop the code parameter (+ others)
            redirect('/login/oauth2callback');
        }
        
        // Make sure the Google client is updated with the appropriate access token
        if ($this->session->userdata('access_token')) {
            $client->setAccessToken($this->session->userdata('access_token'));
        }
        
        if ($client->getAccessToken()) {
            // Grab the access token so we can pass it in API calls
            $accessToken = json_decode($client->getAccessToken(), true);
            $accessToken = $accessToken['access_token'];
            
            // Hit the Google user info endpoint to pull data about this user
            $url = 'https://www.googleapis.com/oauth2/v3/userinfo';
            $url .= '?access_token=' . $accessToken;
            $data = $this->curl_get_contents($url);
            $gUserJson = json_decode($data, true);
            
            $googleId   = $gUserJson['sub'];
            $fname      = $gUserJson['given_name'];
            $lname      = $gUserJson['family_name'];
            $email      = $gUserJson['email'];
            $imgUrl     = array_key_exists('picture', $gUserJson) ? $gUserJson['picture'] : 'https://lh4.googleusercontent.com/-XhEeWdutwX4/AAAAAAAAAAI/AAAAAAAAAAA/v9WBDF4QrR0/s48-c-k-no/photo.jpg';  // Default profile image
            $gender     = array_key_exists('gender', $gUserJson) ? $gUserJson['gender'] : 'male';

            // Load the User Model
            $this->load->model('UserModel');
            
            // First try to match the user against a registered user
            $user = $this->UserModel->findUserByGoogleId($googleId);
            if ($user) {
                // Update fields for this user
                $id = $user->pk_user_id;
                
                // If any of the following fields differ from what we have recorded, update the user
                if ($user->google_id != $googleId ||
                    $user->email != $email ||
                    $user->fname != $fname ||
                    $user->lname != $lname ||
                    $user->gender != $gender ||
                    $user->img_url != $imgUrl) {
                    $this->UserModel->update($id, $googleId, $email, $fname, $lname, $gender, $imgUrl);
                }
                
                // Fetch the user for the session
                $this->UserModel->get($id);

                // Get redirect URL, if any, from the session
                if ($this->session->userdata('redirect')) {
                    $redirect = $this->session->userdata('redirect');
                    $this->session->unset_userdata('redirect');
                } else {
                    $redirect = '/';
                }

                // Redirect
                redirect($redirect);
                exit;
            }
            
            // See if there's an entry for the user, but we just don't have the Google ID yet
            $user = $this->UserModel->findUserByEmail($email);
            if ($user) {
                // Update fields for this user
                $id = $user->pk_user_id;
                $this->UserModel->update($id, $googleId, $email, $fname, $lname, $gender, $imgUrl);
                
                // Fetch the user for the session
                $this->UserModel->get($id);
                
                // Redirect
                redirect('/');
                exit;
            }
            
            // Otherwise, create a new user
            $id = $this->UserModel->insert($googleId, $email, $fname, $lname, $gender, $imgUrl);
            $this->UserModel->get($id);
            
            // Redirect
            redirect('/');
            exit;
        }
        
        // If we failed to get an access token, it's probably because we were denied access.  Redirect.
        redirect('/');
        exit;
    }
    
    /**
     * This action is called when the user tries to log out of the application
     */
    public function logout() {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        redirect('/');
        exit;
    }
    
    protected function getGoogleClient() {
        // Instantiate a new Google client
        $client = new Google_Client();
        $client->setApplicationName($this->GOOGLE_APPLICATION_NAME);
        $client->setClientId($this->GOOGLE_CLIENT_ID);
        $client->setclientsecret($this->GOOGLE_CLIENT_SECRET);
        $client->setredirecturi($this->GOOGLE_REDIRECT_URI);
        $client->setDeveloperKey($this->GOOGLE_DEVELOPER_KEY);
        $client->setScopes(array_merge($client->getScopes(), array('openid', 'email', 'profile')));
        $client->setApprovalPrompt('auto');
        
        return $client;
    }
    
    protected function generateCSRFToken() {
        // Generate a MD5 string
        $state = md5(rand());
        
        // Store the string in the session
        $this->session->set_userdata('csrf_state', $state);
        
        return array(
            'CLIENT_ID' => $this->GOOGLE_CLIENT_ID,
            'STATE' => $state,
            'APPLICATION_NAME' => $this->GOOGLE_APPLICATION_NAME
        );
    }
    
    protected function curl_get_contents($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}