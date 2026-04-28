<?php
class Auth_model extends CI_Model {

    public function check_user($username, $password)
    {
        $this->db->select('*');
        $this->db->from('ms_user');
        $this->db->where('user_name', $username);
        $this->db->where('user_pass', $password);
        $this->db->where('user_active', 'Y');
        $query = $this->db->get();
        return $query;
    }
}