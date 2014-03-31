<?php

class Admin extends App_Controller {

    public function __construct () {
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->model('FamilyModel');
        $this->load->model('HouseModel');
        $this->load->model('InteractionModel');
        $this->pageTitle = 'Admin';
        $this->pageSubtitle = 'Dashboard';
        $this->layout = 'default';
    }

    public function index () {
        if ((int)$this->user->is_admin !== 1) {
            redirect('/');
        }

        $data['families'] = $this->FamilyModel->getAllForAdmin();
        $data['mentors']   = $this->UserModel->getAllForAdmin();
        $data['houses']   = $this->HouseModel->getAllForAdmin();

        $this->menuItem = 'Admin';
        $this->load->view('admin/index', $data);
    }

    public function exportdata () {
        $this->layout = 'nolayout';

        $uriParams   = $this->uri->uri_to_assoc();
        $startDate = $uriParams['start'];
        $endDate   = $uriParams['end'];

        $this->load->dbutil();
        $result = $this->InteractionModel->getExportData($startDate, $endDate);
        $delimiter = ",";
        $newline = "\r\n";
        $this->load->helper('download');
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        $filename = 'interactions_'.$startDate.'_'.$endDate.'.csv';
        force_download($filename, $data);
    }

    public function exportfamilydata () {
        $this->layout = 'nolayout';

        $this->load->dbutil();
        $result = $this->FamilyModel->getExportData();
        $delimiter = ",";
        $newline = "\r\n";
        $this->load->helper('download');
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        $filename = 'families_'.date('Ymd', time()).'.csv';
        force_download($filename, $data);
    }

    public function exportmentordata () {
        $this->layout = 'nolayout';

        $this->load->dbutil();
        $result = $this->UserModel->getExportData();
        $delimiter = ",";
        $newline = "\r\n";
        $this->load->helper('download');
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        $filename = 'mentors_'.date('Ymd', time()).'.csv';
        force_download($filename, $data);
    }

    public function exporthousedata () {
        $this->layout = 'nolayout';

        $this->load->dbutil();
        $result = $this->HouseModel->getExportData();
        $delimiter = ",";
        $newline = "\r\n";
        $this->load->helper('download');
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        $filename = 'houses_'.date('Ymd', time()).'.csv';
        force_download($filename, $data);
    }

    public function updatementorfamily () {
        $mentorId = (int)$this->input->post('mentorid');
        $familyId = (int)$this->input->post('familyid');

        if ($mentorId <= 0 || !is_numeric($mentorId) || $familyId <= 0 || !is_numeric($familyId)) {
            echo json_encode(array('status'=>'failed', 'message'=>'Invalid input'));
            exit;
        }

        $this->UserModel->setFamily($mentorId, $familyId);

        // Reset the user object
        $this->resetUser();

        echo json_encode(array('status'=>'ok'));
        exit;
    }

    public function updatementoradmin () {
        $mentorId = (int)$this->input->post('mentorid');
        $isAdmin = (int)$this->input->post('isadmin');

        if ($mentorId <= 0 || !is_numeric($mentorId) || ($isAdmin !== 1 && $isAdmin !== 0)) {
            echo json_encode(array('status'=>'failed', 'message'=>'Invalid input'));
            exit;
        }

        $this->UserModel->setAdmin($mentorId, $isAdmin);
        echo json_encode(array('status'=>'ok'));
        exit;
    }

    public function updateFamilyInfo () {
        $familyId     = (int)$this->input->post('familyid');
        $name         = $this->input->post('familyname');
        $email        = $this->input->post('familyemail');
        $studentFname = $this->input->post('familystudentfname');
        $studentLname = $this->input->post('familystudentlname');
        $cohort       = $this->input->post('familycohort');
        $houseId      = (int)$this->input->post('houseid');

        if ($houseId <= 0 || !is_numeric($houseId) || $familyId <= 0 || !is_numeric($familyId)) {
            echo json_encode(array('status'=>'failed', 'message'=>'Invalid input'));
            exit;
        }

        $this->FamilyModel->update($familyId, $name, $email, $studentFname, $studentLname, $cohort, $houseId);
        echo json_encode(array('status'=>'ok'));
        exit;
    }

    public function updateHouseInfo () {
        $houseId  = (int)$this->input->post('houseid');
        $name     = $this->input->post('housename');
        $email    = $this->input->post('houseemail');
        $location = $this->input->post('houselocation');

        if ($houseId <= 0 || !is_numeric($houseId)) {
            echo json_encode(array('status'=>'failed', 'message'=>'Invalid input'));
            exit;
        }

        $this->HouseModel->update($houseId, $name, $email, $location);
        echo json_encode(array('status'=>'ok'));
        exit;
    }

    public function addnewfamily () {
        $fname         = $this->input->post('fname');
        $lname         = $this->input->post('lname');
        $email        = $this->input->post('email');
        $cohort       = $this->input->post('cohort');
        $houseId      = (int)$this->input->post('houseid');

        if ($houseId <= 0 || !is_numeric($houseId) || ($fname == "") || ($lname == "") || ($email == "") || ($cohort == "")) {
            echo json_encode(array('status'=>'failed', 'message'=>'Invalid input'));
            exit;
        }

        $id = $this->FamilyModel->insert($fname.' '.$lname, $email, $fname, $lname, $cohort, $houseId);
        echo json_encode(array('status'=>'ok', 'id'=>$id));
        exit;
    }

    public function addNewHouse () {
        $name     = $this->input->post('housename');
        $email    = $this->input->post('houseemail');
        $location = $this->input->post('houselocation');

        $this->HouseModel->insert($name, $email, $location);
        echo json_encode(array('status'=>'ok'));
        exit;
    }

    public function gethouses() {
        // Require that a query be passed into this action
        $q = $this->input->post('q');
        if (!$q) {
            exit;
        }
        
        // Search for houses based on the query string
        $houses = $this->HouseModel->searchHouses($q);
        echo json_encode(array('houses' => $houses));
        exit;
    }
}