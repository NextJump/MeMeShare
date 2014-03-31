<?php
class Home extends App_Controller {

    public function __construct () {
        parent::__construct();
        $this->load->model('InteractionModel');
        $this->load->model('UserModel');
        $this->pageTitle = 'Interactions';
        $this->menuItem = 'Home';
        $this->load->library('email');
        $this->email->set_newline("\r\n");
    }

    public function index () {
        $userId = $this->user->id;
        $userFamily = $this->user->family;
        
        // If the user doesn't belong to a family, redirect through the FTU process
        if (empty($userFamily)) {
            $userFamilyId = 0;
            $userHouseId = 0;
        } else {
            $userFamilyId = $userFamily['familyid'];
            $userHouseId = $userFamily['houseid'];
        }
        
        // Pull in filters
        $uriParams = $this->uri->uri_to_assoc();
        $filters = array(
            'interactionid' => array_key_exists('interactionid', $uriParams) ? (int)$uriParams['interactionid'] : 0,
            'familyid' => array_key_exists('familyid', $uriParams) ? (int)$uriParams['familyid'] : 0,
            'houseid' => $userHouseId
        );

        $data = array(
            'userId' => $userId,
            'houseId' => $userHouseId,
            'familyId' => $userFamilyId,
            'filters' => $filters
        );

        // Set the selected menu item
        if ($filters['familyid'] > 0) {
            // Fetch the family associated with the family ID
            $family = $this->UserModel->getFamilyByFamilyId($filters['familyid']);
            $this->menuItem = 'Family';
            $this->containerIdentifier = 'family';
            $this->pageTitle = array_key_exists('studentfname', $family) ? $family['studentfname'] . (substr($family['studentfname'], -1) == 's' ? "'" : "'s") : 'My';
            $this->pageSubtitle = 'Family';
        } else {
            $this->menuItem = 'Home';
            $this->containerIdentifier = '';
            $this->pageTitle = array_key_exists('housename', $userFamily) ? $userFamily['housename'] : 'My';
            $this->pageSubtitle = 'House';
        }
        $this->load->view('home/index', $data);
    }

    public function reply () {
        $userId        = $this->user->id;
        $interactionId = (int)$this->input->post('interactionid');
        $isLike        = (int)$this->input->post('islike');
        $likeState      = (int)$this->input->post('likestate');
        $commentText   = $this->input->post('commenttext');

        if (is_numeric($isLike) && $isLike === 1 && is_numeric($likeState) && $likeState === 1) {
            $id = $this->InteractionModel->like($interactionId, $userId);
        } else if (is_numeric($isLike) && $isLike === 1 && is_numeric($likeState) && $likeState === 0) {
            $id = $this->InteractionModel->unlike($interactionId, $userId);
        } else {
            $id = $this->InteractionModel->comment($interactionId, $userId, $commentText);
        }
        
        // Fetch the reply
        $reply = $this->InteractionModel->getReply($id);

        $interactionUser = $this->InteractionModel->getInteractionUser($interactionId);

        if ($id > 0 && $likeState > 0) {
            if (is_numeric($isLike) && $isLike === 1) {
                $this->sendLikeEmail($interactionUser['email'], $interactionUser['fname'], $interactionId);
            } else {
                $this->sendCommentEmail($interactionUser['email'], $interactionUser['fname'], $commentText, $interactionId);
            }
        }

        echo json_encode(array('status'=>'ok', 'reply' => $reply));
        exit;
    }

    protected function sendLikeEmail ($receiverEmail, $receiverFname, $interactionId) {
        $this->email->from('no-reply@memeshare.org',  'Mentorship Meeting Share');
        $this->email->to($receiverEmail);
        $this->email->subject('New Like on your Interaction');

        $emailData = array(
            'logoImg' => base_url().'assets/img/emailLogo.png',
            'yourFname' => $receiverFname,
            'likerImgUrl' =>$this->user->img_url,
            'likerFname' => $this->user->fname,
            'likerLname' => $this->user->lname,
            'linkUrl' => base_url().'home/index/interactionid/'.$interactionId,
        );

        $message = $this->load->view('email/template_like', $emailData, TRUE);

        $this->email->message($message);
        $this->email->send();
    }

    protected function sendCommentEmail ($receiverEmail, $receiverFname, $commentText, $interactionId) {
        $this->email->from('no-reply@memeshare.org', 'Mentorship Meeting Share');
        $this->email->to($receiverEmail);
        $this->email->subject('New Comment on your Interaction');

        $emailData = array(
            'logoImg' => base_url().'assets/img/emailLogo.png',
            'yourFname' => $receiverFname,
            'commentorImgUrl' =>$this->user->img_url,
            'commentorFname' => $this->user->fname,
            'commentorLname' => $this->user->lname,
            'commentText' => $commentText,
            'linkUrl' => base_url().'home/index/interactionid/'.$interactionId,
            'baseUrl' => base_url()
        );

        $message = $this->load->view('email/template_comment', $emailData, TRUE);

        $this->email->message($message);
        $this->email->send();
    }

    public function getinteractions () {
        $userId   = $this->input->post('userid');
        $lastId   = $this->input->post('lastid');
        $houseId  = $this->input->post('houseid');
        $familyId = $this->input->post('familyid');
        $interactionId = $this->input->post('interactionid');
        $limit    = $this->input->post('limit');

        $data = array();

        if ($userId && (int)$userId > 0) {
            $data['userid'] = (int)$userId;
        }

        if ($houseId && (int)$houseId > 0) {
            $data['houseid'] = (int)$houseId;
        }

        if ($familyId && (int)$familyId > 0) {
            $data['familyid'] = (int)$familyId;
        }
        
        if ($interactionId && (int)$interactionId > 0) {
            $data['interactionid'] = (int)$interactionId;
        }

        if(!$lastId) {
            $lastId = 0;
        }

        if(!$limit) {
            $limit = 10;
        }

        $interactions = $this->InteractionModel->get($this->user, $lastId, $limit, $data);
        krsort($interactions);
        $interactions = array_values($interactions);
        echo json_encode(array('status'=>'ok', 'interactions'=>$interactions));
        exit;
    }

    public function getfamilies() {
        // Require that a query be passed into this action
        $q = $this->input->post('q');
        if (!$q) {
            exit;
        }
        
        // Search for families based on the query string
        $this->load->model('FamilyModel');
        $families = $this->FamilyModel->searchFamilies($q);
        echo json_encode(array('families' => $families));
        exit;
    }
    
    public function getusers() {
        // Require that a query be passed into this action
        $q = $this->input->post('q');
        if (!$q) {
            exit;
        }
        
        // Search for mentors based on the query string
        $this->load->model('UserModel');
        $users = $this->UserModel->searchUsers($q);
        echo json_encode(array('users' => $users));
        exit;
    }
    
    public function search() {
        // Require that a query be passed into this action
        $q = $this->input->post('q');
        if (!$q) {
            exit;
        }
        
        // Search for mentors based on the query string
        $this->load->model('UserModel');
        $users = $this->UserModel->searchUsers($q);
        
        // Search for families based on the query string (narrow it to the scope of your house)
        $this->load->model('FamilyModel');
        $families = $this->FamilyModel->searchFamilies($q, $this->user->family['houseid']);
        
        echo json_encode(array('users' => $users, 'families' => $families));
        exit;
    }
    
    public function setfamily () {
        $familyId = $this->input->post('familyid');
        
        // Update the family for the current user
        $this->UserModel->setFamily($this->user->id, $familyId);
        
        // Reset the user object
        $this->resetUser();
        
        echo json_encode(array('family' => $this->user->family));
        exit;
    }
}
