<?php

class UserModel extends App_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get($id) {
        $sql = "
            SELECT
                u.pk_user_id id,
                u.google_id,
                u.email,
                u.fname,
                u.lname,
                u.gender,
                u.img_url,
                u.datetime_created,
                u.is_admin
            FROM tb_user u
            WHERE u.pk_user_id = ?
            LIMIT 1
        ";

        $params = array($id);
        $result = $this->db->query($sql, $params);
        $user = $result->row(0);
        
        // Fetch the user's family as well
        $family = $this->getFamily($user->id);
        $user->family = $family;
        
        // Store the user "object" into the session
        $this->session->set_userdata('user', $user);
    }

    public function findUserByGoogleId($googleId) {
        $sql = "
            SELECT
                u.pk_user_id,
                u.google_id,
                u.email,
                u.fname,
                u.lname,
                u.gender,
                u.img_url,
                u.datetime_created,
                u.datetime_updated,
                u.is_active,
                u.is_deleted
            FROM tb_user u
            WHERE u.google_id = ?";
        
        $params = array($googleId);
        $result = $this->db->query($sql, $params);
        
        $user = $result->row(0);
        return $user;
    }
    
    public function findUserByEmail($email) {
        $sql = "
            SELECT
                u.pk_user_id,
                u.google_id,
                u.email,
                u.fname,
                u.lname,
                u.gender,
                u.img_url,
                u.datetime_created,
                u.datetime_updated,
                u.is_active,
                u.is_deleted
            FROM tb_user u
            WHERE u.email = ?";
        
        $params = array($email);
        $result = $this->db->query($sql, $params);
        
        $user = $result->row(0);
        return $user;
    }
    
    /**
     * Creates a new user in the database with the specified parameters
     */
    public function insert($googleId, $email, $fname, $lname, $gender, $imgUrl) {
        $sql = "
            INSERT INTO tb_user (
                google_id,
                email,
                fname,
                lname,
                gender,
                img_url,
                datetime_created,
                datetime_updated,
                is_active,
                is_deleted
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                NOW(),
                1,
                0
            )";
        
        $params = array($googleId, $email, $fname, $lname, $gender, $imgUrl);
        $result = $this->db->query($sql, $params);
        $id = $this->db->insert_id();
        return $id;
    }

    public function update($id, $googleId, $email, $fname, $lname, $gender, $imgUrl) {
        $sql = "
            UPDATE tb_user
            SET google_id = ?,
                email = ?,
                fname = ?,
                lname = ?,
                gender = ?,
                img_url = ?,
                datetime_updated = NOW()
            WHERE pk_user_id = ?
        ";
        
        $params = array($googleId, $email, $fname, $lname, $gender, $imgUrl, $id);
        $result = $this->db->query($sql, $params);
    }

    public function delete () {
        
    }

    public function getFamily ($userId) {
        $sql = "
            SELECT
                f.pk_family_id familyid,
                f.name familyname,
                f.email familyemail,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort,
                h.pk_house_id houseid,
                h.name housename
            FROM tb_family_user_map fum
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE fum.fk_pk_user_id = ?
                AND fum.is_active = 1
                AND fum.is_deleted = 0
                AND f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ";

        $params = array($userId);
        $result = $this->db->query($sql, $params);

        return $result->row_array(0);
    }
    
    public function getFamilyByFamilyId ($familyId) {
        $sql = "
            SELECT
                f.pk_family_id familyid,
                f.name familyname,
                f.email familyemail,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort,
                h.pk_house_id houseid,
                h.name housename
            FROM tb_family f
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE f.pk_family_id = ?
                AND f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ";

        $params = array($familyId);
        $result = $this->db->query($sql, $params);

        return $result->row_array(0);
    }

    public function setFamily ($userId, $familyId) {
        $result = $this->getFamily($userId);
        if (count($result) > 0) {
            $sql = "
                UPDATE tb_family_user_map
                SET fk_pk_family_id = ?,
                    datetime_updated = NOW()
                WHERE fk_pk_user_id = ?
            ";
        } else {
            $sql = "
                INSERT INTO tb_family_user_map (
                    fk_pk_family_id,
                    fk_pk_user_id,
                    datetime_created,
                    datetime_updated
                )
                VALUES (
                    ?,
                    ?,
                    NOW(),
                    NOW()
                )";
        }

        $params = array($familyId, $userId);
        $this->db->query($sql, $params);
    }

    public function getPublicInfo ($id) {
        $sql = "
            SELECT
                u.pk_user_id id,
                u.email,
                u.fname,
                u.lname,
                u.gender,
                u.img_url,
                u.datetime_created,
                f.pk_family_id familyid,
                f.name familyname,
                f.email familyemail,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort,
                h.pk_house_id houseid,
                h.name housename,
                h.email houseemail
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE u.pk_user_id = ?
            LIMIT 1
        ";

        $params = array($id);
        $result = $this->db->query($sql, $params);

        $info = $result->row_array(0);

        $user = array();
        $user['id'] = $info['id'];
        $user['email'] = $info['email'];
        $user['fname'] = $info['fname'];
        $user['lname'] = $info['lname'];
        $user['gender'] = $info['gender'];
        $user['imgurl'] = $info['img_url'];
        $user['datecreated'] = $info['datetime_created'];
        $user['datecreatedts_sec'] = strtotime($info['datetime_created']);
        $user['family'] = array(
            'id' => $info['familyid'],
            'name' => $info['familyname'],
            'email' => $info['familyemail']
        );
        $user['house'] = array(
            'id' => $info['houseid'],
            'name' => $info['housename'],
            'email' => $info['houseemail']
        );

        return $user;
    }

    public function getAll () {
        $sql = "
            SELECT
                u.pk_user_id userid,
                u.fname,
                u.lname,
                u.email,
                u.img_url,
                f.pk_family_id familyid,
                f.name familyname,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            WHERE u.is_active = 1
                AND u.is_deleted = 0
        ";

        $result = $this->db->query($sql);
        return $result->result_array();
    }
    
    public function searchUsers ($query) {
        $sql = "
            SELECT
                u.pk_user_id userid,
                u.fname,
                u.lname,
                CONCAT(u.fname, ' ', u.lname) fullname,
                u.email,
                u.img_url,
                f.pk_family_id familyid,
                f.name familyname,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort,
                h.pk_house_id houseid,
                h.name housename
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE u.is_active = 1
                AND u.is_deleted = 0
                AND (
                    u.fname LIKE ?
                    OR u.lname LIKE ?
                    OR (u.fname + ' ' + u.lname) LIKE ?
                )
                
            ORDER BY f.name ASC
            ";
        
        $query = '%' . $query . '%';
        $params = array($query, $query, $query);
        $result = $this->db->query($sql, $params);
        return $result->result_array();
    }

    public function getUsersByFamily ($familyId) {
        $sql = "
            SELECT
                u.pk_user_id id,
                u.email,
                u.fname
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
                AND fum.fk_pk_family_id = ?";
        
        $params = array($familyId);
        $result = $this->db->query($sql, $params);
        
        $users = $result->result_array();
        return $users;
    }

    public function getAllForAdmin () {
        $sql = "
            SELECT
                u.pk_user_id id,
                u.fname,
                u.lname,
                u.email,
                f.pk_family_id familyid,
                f.name familyname,
                h.name housename,
                u.is_admin isadmin
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE u.is_active = 1
                AND u.is_deleted = 0
                AND fum.is_active = 1
                AND fum.is_deleted = 0
                AND f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY u.fname ASC
        ";

        $result = $this->db->query($sql);
        return $result->result_array();
    }

    public function getExportData () {
        $sql = "
            SELECT
                CONCAT(u.fname, ' ', u.lname) AS 'Name',
                h.name AS 'House',
                f.cohort_name AS 'Cohort',
                f.name AS 'Student',
                u.email AS 'Email',
                (CASE u.is_admin
                    WHEN 1 THEN 'Yes'
                    ELSE 'No'
                END) AS 'Admin?'
            FROM tb_user u
            JOIN tb_family_user_map fum
                ON u.pk_user_id = fum.fk_pk_user_id
            JOIN tb_family f
                ON fum.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE u.is_active = 1
                AND u.is_deleted = 0
                AND fum.is_active = 1
                AND fum.is_deleted = 0
                AND f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY h.name ASC,
                f.cohort_name ASC
        ";

        $result = $this->db->query($sql);
        return $result;
    }

    public function setAdmin ($userId, $isAdmin) {
        $sql = "
            UPDATE tb_user
            SET is_admin = ?,
                datetime_updated = NOW()
            WHERE pk_user_id = ?
        ";

        $params = array($isAdmin, $userId);
        $this->db->query($sql, $params);
    }
}