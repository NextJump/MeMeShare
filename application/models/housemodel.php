<?php

class HouseModel extends App_Model {
    public function __construct() {
        $this->load->database();
        parent::__construct();
    }

    public function get () {
        $sql = "
            SELECT
                pk_house_id id,
                name,
                email,
                location
            FROM tb_house
            WHERE is_active = 1
                AND is_deleted = 0
            ORDER BY name
            ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    public function insert ($name, $email, $location) {
        $sql = "
            INSERT INTO tb_house (
                name,
                email,
                location,
                datetime_created,
                datetime_updated
            )
            VALUES (
                ?,
                ?,
                ?,
                NOW(),
                NOW()
            )";

        $params = array($name, $email, $location);

        $this->db->query($sql, $params);
    }

    public function update ($id, $name, $email, $location) {
        $sql = "
            UPDATE tb_house
            SET name = ?,
                email = ?,
                location = ?,
                datetime_updated = NOW()
            WHERE pk_house_id = ?
        ";

        $params = array($name, $email, $location, $id);

        $this->db->query($sql, $params);
    }

    public function delete ($id) {
        $sql = "
            UPDATE tb_house
            SET is_active = 0,
                is_deleted = 1,
                datetime_updated = NOW()
            WHERE pk_house_id = ?
            ";

        $params = array($id);
        $this->db->query($sql, $params);
    }

    public function searchHouses ($query) {
        $sql = "
            SELECT
                h.pk_house_id houseid,
                h.name housename
            FROM tb_house h
            WHERE h.is_active = 1
                AND h.is_deleted = 0
                AND h.name LIKE ?
            ORDER BY h.name ASC
            ";
        
        $params = array('%' . $query . '%');
        $result = $this->db->query($sql, $params);
        return $result->result_array();
    }

    public function getAllForAdmin () {
        $sql = "
            SELECT
                h.pk_house_id id,
                h.name name,
                h.email email,
                h.location location
            FROM tb_house h
            WHERE h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY h.name ASC
        ";

        $result = $this->db->query($sql);
        return $result->result_array();
    }

    public function getExportData () {
        $sql = "
            SELECT
                h.name AS 'House',
                h.email AS 'Email',
                h.location AS 'Location',
                h.datetime_created AS 'Date of Creation'
            FROM tb_house h
            WHERE h.is_active = 1
                AND h.is_deleted = 0
            ORDER BY h.name ASC
        ";

        $result = $this->db->query($sql);
        return $result;
    }
}