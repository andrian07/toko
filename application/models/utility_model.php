<?php
class Utility_model extends CI_Model {


    // start unit //
    public function get_unit_normal()
    {
        $this->db->select('*');
        $this->db->from('ms_units');
        $this->db->where('unit_active', 'Y');
        $this->db->order_by('unit_id', 'ASC');
        return $this->db->get()->result();
    }
    public function get_unit($search = '', $limit = 100, $start = 0)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 100;
        }
        if ($limit > 400) {
            $limit = 400;
        }
        $start = (int)$start;
        if ($start < 0) {
            $start = 0;
        }
        $this->db->select('unit_id, unit_code, unit_name');
        $this->db->from('ms_units');
        $this->db->where('unit_active', 'Y');
        if (!empty($search)) {
            $this->db->like('unit_name', $search);
        }
        $this->db->order_by('unit_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function save_unit($data)
    {
        $this->db->insert('ms_units', $data);
    }

    public function edit_unit($unit_id, $data)
    {
        $this->db->where('unit_id', $unit_id);
        $this->db->update('ms_units', $data);
    }

    public function delete_unit($unit_id)
    {
        $this->db->where('unit_id', $unit_id);
        $this->db->update('ms_units', ['unit_active' => 'N']);
    }

    // end unit //

    // start category //


    public function get_category_normal()
    {
        $this->db->select('*');
        $this->db->from('ms_category');
        $this->db->where('category_active', 'Y');
        $this->db->order_by('category_id', 'DESC');
        return $this->db->get()->result();
    }
    public function get_category($search = '', $limit = 100, $start = 0)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 100;
        }
        if ($limit > 400) {
            $limit = 400;
        }
        $start = (int)$start;
        if ($start < 0) {
            $start = 0;
        }
        $this->db->select('category_id, category_code, category_name');
        $this->db->from('ms_category');
        $this->db->where('category_active', 'Y');
        if (!empty($search)) {
            $this->db->like('category_name', $search);
        }
        $this->db->order_by('category_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function save_category($data)
    {
        $this->db->insert('ms_category', $data);
    }

    public function edit_category($category_id, $data)
    {
        $this->db->where('category_id', $category_id);
        $this->db->update('ms_category', $data);
    }

    public function delete_category($category_id)
    {
        $this->db->where('category_id', $category_id);
        $this->db->update('ms_category', ['category_active' => 'N']);
    }

    // end category //
}