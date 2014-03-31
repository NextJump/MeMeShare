<?php
class Interaction extends App_Controller {

    public function __construct () {
        parent::__construct();
        $this->load->model('InteractionModel');
        $this->load->model('UserModel');
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $this->pageTitle = 'Log';
        $this->pageSubtitle = 'Interaction';
    }

    public function index () {

    }

    public function log () {
        $userId = $this->user->id;
        $family = $this->UserModel->getFamily($userId);
        $types  = $this->InteractionModel->getAllTypes();

        $data = array(
            'family' => $family,
            'types'  => $types,
            'user'   => $this->user
        );

        $this->menuItem = 'Log';
        $this->load->view('interaction/log', $data);
    }

    public function addnew () {
        $userId           = $this->user->id;
        $familyId         = (int)$this->input->post('familyid');
        $familyName       = $this->input->post('familyname');
        $dateInteraction  = $this->input->post('dateinteraction');
        $duration         = (int)$this->input->post('duration');
        $desc             = $this->input->post('desc');
        $typeId           = (int)$this->input->post('typeid');
        $isPrivate        = 1;

        $data = array(
            'userId' => $userId,
            'familyId' => $familyId,
            'dateInteraction' => $dateInteraction,
            'duration' => $duration,
            'desc' => nl2br($desc),
            'typeId' => $typeId,
            'isPrivate' => $isPrivate
        );

        $id = $this->InteractionModel->insert($data);

        if ($id > 0) {
            // Truncate description to a certain char length, stopping on a word.
            if (strlen($desc) > 200) {
                $desc = substr($desc,0,197);
                $desc = substr($desc,0,strrpos($desc,' ')).'...';
            }

            $this->email->from('no-reply@memeshare.org', 'Mentorship Meeting Share');
            $this->email->subject('New Interaction with your Student');

            $emailData = array(
                'logoImg' => base_url().'assets/img/emailLogo.png',
                'posterImgUrl' =>$this->user->img_url,
                'posterFname' => $this->user->fname,
                'posterLname' => $this->user->lname,
                'familyName' => $familyName,
                'description' => $desc,
                'linkUrl' => base_url().'home/index/interactionid/'.$id,
                'baseUrl' => base_url()
            );

            $users = $this->UserModel->getUsersByFamily($familyId);

            foreach ($users as $u) {
                if ((int)$u['id'] !== (int)$userId) {
                    $this->email->to($u['email']);
                    $emailData['receiverFname'] = $u['fname'];
                    $message = $this->load->view('email/template_new_interaction', $emailData, TRUE);
                    $this->email->message($message);
                    $this->email->send();
                }
            }
        }

        echo json_encode(array('status'=>'cool'));
        exit;
    }
}