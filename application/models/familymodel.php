<?php

class FamilyModel extends App_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get () {
        $sql = "
            SELECT
                pk_family_id id,
                name,
                email,
                student_fname,
                student_lname,
                cohort_name
            FROM tb_family
            WHERE is_active = 1
                AND is_deleted = 0
            ORDER BY name
            ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    public function insert ($name, $email, $studentFname, $studentLname, $cohort, $houseId) {
        $sql = "
            INSERT INTO tb_family (
                name,
                email,
                student_fname,
                student_lname,
                cohort_name,
                fk_pk_house_id,
                datetime_created,
                datetime_updated
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                NOW()
            )";

        $params = array($name, $email, $studentFname, $studentLname, $cohort, $houseId);

        $this->db->query($sql, $params);
        return $this->db->insert_id();
    }

    public function update ($id, $name, $email, $studentFname, $studentLname, $cohort, $houseId) {
        $sql = "
            UPDATE tb_family
            SET name = ?,
                email = ?,
                student_fname = ?,
                student_lname = ?,
                cohort_name = ?,
                fk_pk_house_id = ?,
                datetime_updated = NOW()
            WHERE pk_family_id = ?
        ";

        $params = array($name, $email, $studentFname, $studentLname, $cohort, $houseId, $id);

        $this->db->query($sql, $params);
    }

    public function delete ($id) {
        $sql = "
            UPDATE tb_family
            SET is_active = 0,
                is_deleted = 1,
                datetime_updated = NOW()
            WHERE pk_family_id = ?
            ";

        $params = array($id);
        $this->db->query($sql, $params);
    }
    
    public function searchFamilies ($query, $houseId=0) {
        $sql = "
            SELECT
                f.pk_family_id familyid,
                f.name familyname,
                f.student_fname studentfname,
                f.student_lname studentlname,
                f.cohort_name cohort,
                h.pk_house_id houseid,
                h.name housename
            FROM tb_family f
            JOIN tb_house h
            ON f.fk_pk_house_id = h.pk_house_id
            WHERE f.is_active = 1
                AND f.is_deleted = 0
                AND f.name LIKE ?
                AND (h.pk_house_id = ? OR ? = 0)
            ORDER BY f.name ASC
            ";
        
        $params = array('%' . $query . '%', $houseId, $houseId);
        $result = $this->db->query($sql, $params);
        return $result->result_array();
    }

    public function getAllForAdmin () {
        $sql = "
            SELECT
                f.pk_family_id id,
                f.name,
                f.email,
                h.pk_house_id houseid,
                h.name housename
            FROM tb_family f
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY f.name ASC
        ";

        $result = $this->db->query($sql);
        return $result->result_array();
    }

    public function getExportData () {
        $sql = "
            SELECT
                f.name AS 'Family Name',
                f.email AS 'Family Email',
                f.student_fname AS 'Student First Name',
                f.student_lname AS 'Student Last Name',
                f.cohort_name AS 'Cohort',
                h.name AS 'House',
                f.datetime_created AS 'Date of Creation'
            FROM tb_family f
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE f.is_active = 1
                AND f.is_deleted = 0
                AND h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY f.name ASC
        ";

        $result = $this->db->query($sql);
        return $result;
    }
}