<?php
class Pos_model extends CI_Model {

    public function insert_transaction($data)
    {
        $this->db->trans_start();
        $this->db->insert('transaction', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id;
    }

    public function last_transaction_code()
    {
        $this->db->select('transaction_inv');
        $this->db->from('transaction');
        $this->db->order_by('transaction_id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function get_temp_cart($user_id)
    {
        $this->db->select('*');
        $this->db->from('temp_cart');
        $this->db->where('user_id', $user_id);
        return $this->db->get()->result();
    }
    public function insert_detal($data_detail)
    {
        $this->db->insert('transaction_detail', $data_detail);
    }

    public function clear_temp_cart($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('temp_cart');
    }

    public function get_pos_list($search, $limit, $start)
    {
        $this->db->select('t.transaction_id, t.transaction_inv, c.customer_name, p.payment_name, t.transaction_total, t.transaction_date, t.transaction_status');
        $this->db->from('transaction t');
        $this->db->join('ms_customer c', 't.customer_id = c.customer_id', 'left');
        $this->db->join('ms_payment p', 't.payment_id = p.payment_id', 'left');
        if ($search) {
            $this->db->like('t.transaction_inv', $search);
            $this->db->or_like('c.customer_name', $search);
            $this->db->or_like('p.payment_name', $search);
        }
        $this->db->order_by('t.transaction_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function delete_transaction($transaction_id){
        $this->db->set('transaction_status', 'Cancel');
        $this->db->where('transaction_id', $transaction_id);
        $this->db->update('transaction');   
    }

    public function get_transaction_by_id($transaction_id){
        $this->db->select('t.*, c.customer_name, p.payment_name');
        $this->db->from('transaction t');
        $this->db->join('ms_customer c', 't.customer_id = c.customer_id', 'left');
        $this->db->join('ms_payment p', 't.payment_id = p.payment_id', 'left');
        $this->db->where('t.transaction_id', $transaction_id);
        return $this->db->get()->row();
    }

    public function get_transaction_details($transaction_id){
        $this->db->select('td.*, pr.product_name');
        $this->db->from('transaction_detail td');
        $this->db->join('ms_product pr', 'pr.product_id = td.item_id', 'left');
        $this->db->where('td.transaction_id', $transaction_id);
        return $this->db->get()->result();
    }
}