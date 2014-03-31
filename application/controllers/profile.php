<?php
class Profile extends App_Controller {

    public function __construct () {
        parent::__construct();
        $this->load->model('InteractionModel');
        $this->load->model('UserModel');
        $this->load->library('email');
        $this->email->set_newline("\r\n");
    }

    public function index () {
        $uriParams = $this->uri->uri_to_assoc();
        $userId = (int)$uriParams['id'];

        $userInfo = $this->UserModel->getPublicInfo($userId);

        $minutesLogged = $this->InteractionModel->getMinutesLoggedUserThirtyDays($userId);

        $data = array(
            'mentorId' => $userId,
            'mentorInfo' => $userInfo,
            'minutesLogged' => $minutesLogged
        );
        
        $this->pageTitle = $userInfo['fname'] . (substr($userInfo['fname'], -1) == 's' ? "'" : "'s");
        $this->pageSubtitle = 'Profile';
        $this->menuItem = 'Profile';
        $this->load->view('profile/index', $data);
    }
}