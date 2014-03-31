<?php

class InteractionModel extends App_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get ($sessionUser, $lastId = 0, $limit = 10, $args = array()) {
        $sqlWhere = "";
        $params = array();

        if (isset($args['userid']) && $args['userid'] > 0 && is_numeric($args['userid'])) {
            $sqlWhere .= "
                AND i.fk_pk_user_id = ?
                ";
            $params[] = $args['userid'];
        }

        if (isset($args['familyid']) && $args['familyid'] > 0 && is_numeric($args['familyid'])) {
            $sqlWhere .= "
                AND i.fk_pk_family_id = ?
                ";
            $params[] = $args['familyid'];
        }

        if (isset($args['houseid']) && $args['houseid'] > 0 && is_numeric($args['houseid'])) {
            $sqlWhere .= "
                AND f.fk_pk_house_id = ?
                ";
            $params[] = $args['houseid'];
        }
        
        if (isset($args['interactionid']) && $args['interactionid'] > 0 && is_numeric($args['interactionid'])) {
            $sqlWhere .= "
                AND i.pk_interaction_id = ?
                ";
            $params[] = $args['interactionid'];
        }

        if ($lastId > 0) {
            $sqlWhere .= "
                AND i.pk_interaction_id < ?
                ";
            $params[] = $lastId;
        }

        $params[] = $limit;

        $sql = "
            SELECT
                *
            FROM(
                SELECT
                    i.pk_interaction_id id,
                    i.fk_pk_user_id userid,
                    u.fname,
                    u.lname,
                    u.img_url,
                    u.email,
                    i.fk_pk_family_id familyid,
                    f.name familyname,
                    f.student_fname studentfname,
                    f.student_lname studentlname,
                    f.cohort_name cohort,
                    i.datetime_created dateposted,
                    i.datetime_interaction dateinteracted,
                    i.duration,
                    i.description,
                    i.fk_pk_interaction_type_id typeid,
                    t.name typename,
                    t.fk_pk_parent_interaction_type_id typeparentid,
                    tp.name typeparentname,
                    i.is_private isprivate,
                    f.fk_pk_house_id houseid,
                    h.name housename,
                    c.pk_interaction_comment_id commentid,
                    c.fk_pk_user_id commentuserid,
                    cu.fname commentuserfname,
                    cu.lname commentuserlname,
                    cu.img_url commentuserimgurl,
                    c.is_like commentislike,
                    c.comment_text commenttext,
                    c.datetime_created commentdatetime,
                    @curRank := IF(@curId != i.pk_interaction_id, @curRank + 1, @curRank) AS rank,
                    @curId := i.pk_interaction_id 
                FROM tb_interaction i
                JOIN tb_interaction_type t
                    ON i.fk_pk_interaction_type_id = t.pk_interaction_type_id
                JOIN tb_interaction_type tp
                    ON t.fk_pk_parent_interaction_type_id = tp.pk_interaction_type_id
                JOIN tb_family f
                    ON i.fk_pk_family_id = f.pk_family_id
                JOIN tb_house h
                    ON f.fk_pk_house_id = h.pk_house_id
                JOIN tb_user u
                    ON i.fk_pk_user_id = u.pk_user_id
                LEFT JOIN tb_interaction_comment c
                    ON c.fk_pk_interaction_id = i.pk_interaction_id
                    AND c.is_active = 1
                    AND c.is_deleted = 0
                LEFT JOIN tb_user cu
                    ON c.fk_pk_user_id = cu.pk_user_id,
                (SELECT @curRank := 0) r,
                (SELECT @curId := 0) cid
                WHERE i.is_active = 1
                    AND i.is_deleted = 0
                    AND t.is_active = 1
                    AND t.is_deleted = 0
                    AND h.is_active = 1
                    AND h.is_deleted = 0
                    ".$sqlWhere."
                ) tt
            WHERE tt.rank > @curRank - ?
            ORDER BY tt.id DESC,
                tt.commentid ASC
            ";

        $result = $this->db->query($sql, $params);

        $interactions = array();

        foreach ($result->result_array() as $row) {
            $comment = array();
            if (!is_null($row['commentid'])) {
                $comment = array(
                    "id" => (int)$row['commentid'],
                    "user" => array(
                        "id" => (int)$row['commentuserid'],
                        "fname" => $row['commentuserfname'],
                        "lname" => $row['commentuserlname'],
                        "imgurl" => $row['commentuserimgurl']
                    ),
                    "islike" => (int)$row['commentislike'],
                    "text" => $row['commenttext'],
                    "datetime" => $row['commentdatetime'],
                    "datetimets_sec" => strtotime($row['commentdatetime'])
                );
            }

            if (isset($interactions[$row['id']])) {
                if (!empty($comment)) {
                    if((int)$comment['islike'] === 1) {
                        $interactions[$row['id']]['likes'][] = $comment;
                        $interactions[$row['id']]['likecount']++;
                        $interactions[$row['id']]['hasliked'] = $interactions[$row['id']]['hasliked'] || ($comment['user']['id'] === (int)$sessionUser->id);
                    } else {
                        $interactions[$row['id']]['comments'][] = $comment;
                        $interactions[$row['id']]['commentscount']++;
                    }
                }
            } else {
                $ir = array();
                $ir['id'] = (int)$row['id'];
                $ir['user'] = array(
                    'id' => (int)$row['userid'],
                    'fname' => $row['fname'],
                    'lname' => $row['lname'],
                    'email' => $row['email'],
                    'imgurl' => $row['img_url']
                );
                $ir['family'] = array(
                    'id' => (int)$row['familyid'],
                    'name' => $row['familyname'],
                    'studentfname' => $row['studentfname'],
                    'studentlname' => $row['studentlname'],
                    'cohort' => $row['cohort']
                );
                $ir['dateinteracted'] = $row['dateinteracted'];
                $ir['dateinteractedts_sec'] = strtotime($row['dateinteracted']);
                $ir['dateinteracted_formatted'] = date('M j, Y', strtotime($row['dateinteracted']));
                $ir['dateposted'] = $row['dateposted'];
                $ir['datepostedts_sec'] = strtotime($row['dateposted']);
                $ir['dateposted_formatted'] = date('M j, Y', strtotime($row['dateposted']));
                $ir['duration'] = (int)$row['duration'];
                $ir['description'] = $row['description'];
                $ir['type'] = array(
                    'id' => (int)$row['typeid'],
                    'name' => $row['typename'],
                    'parentid' => (int)$row['typeparentid'],
                    'parentname' => $row['typeparentname']
                );
                $ir['isprivate'] = (int)$row['isprivate'];
                $ir['house'] = array(
                    'id' => (int)$row['houseid'],
                    'name' => $row['housename']
                );
                $ir['comments'] = array();
                $ir['likes'] = array();
                $ir['likecount'] = 0;
                $ir['commentscount'] = 0;
                $ir['hasliked'] = false;
                $ir['commentsenabled'] = true;
                if (!empty($comment)) {
                    if((int)$comment['islike'] === 1) {
                        $ir['likes'][] = $comment;
                        $ir['likecount']++;
                        $ir['hasliked'] = $ir['hasliked'] || ($comment['user']['id'] === (int)$sessionUser->id);
                    } else {
                        $ir['comments'][] = $comment;
                        $ir['commentscount']++;
                    }
                }
                
                $interactions[$row['id']] = $ir;
                unset($ir);
            }
            unset($comment);
            
            // If the user shouldn't see certain details about the interaction, hide them here
            if (!($sessionUser->family['houseid'] == $row['houseid'] || $sessionUser->id == $row['userid'])) {
                $interactions[$row['id']]['description'] = '';
                $interactions[$row['id']]['commentsenabled'] = false;
                $interactions[$row['id']]['comments'] = array();
            }
        }

        return $interactions;
    }

    public function insert ($data) {
        $sql = "
            INSERT INTO tb_interaction (
                fk_pk_user_id,
                fk_pk_family_id,
                datetime_interaction,
                datetime_created,
                datetime_updated,
                duration,
                description,
                fk_pk_interaction_type_id,
                is_private
            )
            VALUES (
                ?,
                ?,
                ?,
                NOW(),
                NOW(),
                ?,
                ?,
                ?,
                ?
            )";

        $params = array(
            $data['userId'],
            $data['familyId'],
            date('Y-m-d', strtotime($data['dateInteraction'])),
            $data['duration'],
            $data['desc'],
            $data['typeId'],
            $data['isPrivate']
        );

        $this->db->query($sql, $params);
        return $this->db->insert_id();
    }

    public function update () {
        
    }

    public function delete ($id) {
        $sql = "
            UPDATE tb_interaction
            SET is_active = 0,
                is_deleted = 1,
                datetime_updated = NOW()
            WHERE pk_interaction_id = ?
            ";

        $params = array($id);
        $this->db->query($sql, $params);
    }

    public function like ($id, $userid) {
        $sql = "
            INSERT INTO tb_interaction_comment (
                fk_pk_interaction_id,
                fk_pk_user_id,
                is_like,
                datetime_created,
                datetime_updated
            )
            VALUES (
                ?,
                ?,
                1,
                NOW(),
                NOW()
            )";

        $params = array($id, $userid);
        $this->db->query($sql, $params);
        
        // Return the inserted ID
        $id = $this->db->insert_id();
        return $id;
    }
    
    public function unlike ($id, $userid) {
        $sql = "
            UPDATE tb_interaction_comment
            SET is_active = 0,
                is_deleted = 1
            WHERE fk_pk_interaction_id = ?
            AND fk_pk_user_id = ?
            AND is_like = 1";

        $params = array($id, $userid);
        $this->db->query($sql, $params);
        
        // Return the interaction ID
        return $id;
    }

    public function comment ($id, $userid, $comment) {
        $sql = "
            INSERT INTO tb_interaction_comment (
                fk_pk_interaction_id,
                fk_pk_user_id,
                is_like,
                comment_text,
                datetime_created,
                datetime_updated
            )
            VALUES (
                ?,
                ?,
                0,
                ?,
                NOW(),
                NOW()
            )";

        $params = array($id, $userid, $comment);
        $this->db->query($sql, $params);
        
        // Return the inserted ID
        $id = $this->db->insert_id();
        return $id;
    }
    
    public function getReply ($id) {
        $sql = "
            SELECT
                c.pk_interaction_comment_id commentid,
                c.fk_pk_user_id commentuserid,
                cu.fname commentuserfname,
                cu.lname commentuserlname,
                cu.img_url commentuserimgurl,
                c.is_like commentislike,
                c.comment_text commenttext,
                c.datetime_created commentdatetime
            FROM tb_interaction_comment c
            JOIN tb_user cu
                ON c.fk_pk_user_id = cu.pk_user_id
            WHERE c.pk_interaction_comment_id = ?
                AND c.is_active = 1
                AND c.is_deleted = 0";
        
        $params = array($id);
        $result = $this->db->query($sql, $params);
        $row = $result->row_array();
        
        if ($row) {
            $comment = array(
                'id' => (int)$row['commentid'],
                'user' => array(
                    'id' => (int)$row['commentuserid'],
                    'fname' => $row['commentuserfname'],
                    'lname' => $row['commentuserlname'],
                    'imgurl' => $row['commentuserimgurl']
                ),
                'islike' => (int)$row['commentislike'],
                'text' => $row['commenttext'],
                'datetime' => $row['commentdatetime'],
                'datetimets_sec' => strtotime($row['commentdatetime'])
            );
            return $comment;
        }
        return false;
    }

    public function getAllTypes () {
        $sql = "
            SELECT
                t.pk_interaction_type_id id,
                t.name,
                t.fk_pk_parent_interaction_type_id parent_id,
                p.name parent_name
            FROM tb_interaction_type t
            LEFT JOIN tb_interaction_type p
                ON t.fk_pk_parent_interaction_type_id = p.pk_interaction_type_id
            WHERE t.is_active = 1
                AND t.is_deleted = 0
            ORDER BY t.pk_interaction_type_id
            ";
        $result = $this->db->query($sql);

        $types = array();
        
        foreach ($result->result_array() as $row) {
            if ($row['parent_id'] == null) {
                $types[$row['id']] = array(
                    'name' => $row['name'],
                    'sub_types' => array()
                );
            } else {
                if (isset($types[$row['parent_id']])) {
                    $types[$row['parent_id']]['sub_types'][] = $row;
                }
            }
        }

        return $types;
    }

    public function getMinutesLoggedThirtyDays () {
        $sql = "
            SELECT
                COALESCE(SUM(duration), 0) minuteslogged
            FROM tb_interaction t
            WHERE t.is_active = 1
                AND t.is_deleted = 0
                AND t.datetime_created >= NOW() - INTERVAL 30 DAY
            ";
        $result = $this->db->query($sql);
        return $result->row(0)->minuteslogged;
    }

    public function getInteractionUser ($interactionId) {
        $sql = "
            SELECT
                u.pk_user_id userid,
                u.fname,
                u.lname,
                u.email,
                u.img_url
            FROM tb_interaction t
            JOIN tb_user u
                ON t.fk_pk_user_id = u.pk_user_id
            WHERE t.pk_interaction_id = ?
            ";

        $params = array($interactionId);
        $result = $this->db->query($sql, $params);
        return $result->row_array(0);
    }

    public function getMinutesLoggedUser ($userId) {
        $sql = "
            SELECT
                COALESCE(SUM(duration), 0) minuteslogged
            FROM tb_interaction t
            JOIN tb_user u
                ON t.fk_pk_user_id = u.pk_user_id
            WHERE t.is_active = 1
                AND t.is_deleted = 0
                AND u.pk_user_id = ?
            ";
        $params = array($userId);
        $result = $this->db->query($sql, $params);
        return $result->row(0)->minuteslogged;
    }

    public function getMinutesLoggedUserThirtyDays ($userId) {
        $sql = "
            SELECT
                COALESCE(SUM(duration), 0) minuteslogged
            FROM tb_interaction t
            JOIN tb_user u
                ON t.fk_pk_user_id = u.pk_user_id
            WHERE t.is_active = 1
                AND t.is_deleted = 0
                AND u.pk_user_id = ?
                AND t.datetime_created >= NOW() - INTERVAL 30 DAY
            ";
        $params = array($userId);
        $result = $this->db->query($sql, $params);
        return $result->row(0)->minuteslogged;
    }

    public function getExportData ($startDate, $endDate) {
        $endDate = $endDate.' 23:59:59';
        $sql = "
            SELECT
                i.datetime_created AS 'Timestamp',
                u.fname AS 'Mentor First Name',
                u.lname AS 'Mentor Last Name',
                DATE(i.datetime_interaction) AS 'Date of Event',
                itp.name AS 'Interaction Type',
                it.name AS 'Interaction Sub-type',
                i.duration AS 'Duration',
                i.description AS 'Description',
                f.name AS 'Family Name',
                f.student_fname AS 'Student First Name',
                f.student_lname 'Student Last Name',
                f.cohort_name 'Cohort Name',
                h.name AS 'House'
            FROM tb_interaction i
            JOIN tb_interaction_type it
                ON i.fk_pk_interaction_type_id = it.pk_interaction_type_id
            JOIN tb_interaction_type itp
                ON it.fk_pk_parent_interaction_type_id = itp.pk_interaction_type_id
            JOIN tb_user u
                ON i.fk_pk_user_id = u.pk_user_id
            JOIN tb_family f
                ON i.fk_pk_family_id = f.pk_family_id
            JOIN tb_house h
                ON f.fk_pk_house_id = h.pk_house_id
            WHERE i.is_active = 1
                AND i.is_deleted = 0
                AND i.is_active = 1
                AND i.datetime_created >= ?
                AND i.datetime_created <= ?
            ORDER BY i.datetime_created ASC
        ";

        $params = array($startDate, $endDate);
        $result = $this->db->query($sql, $params);
        return $result;
    }
}