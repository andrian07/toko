<?php
class Payment_model extends CI_Model {


    // start debt //
    public function get_debt($search, $limit, $start)
    {
        $this->db->select('*, sum(supplier_debt_remaining) as supplier_debt_total, count(supplier_debt_id) as supplier_debt_count');
        $this->db->from('supplier_debt');
        $this->db->join('ms_supplier', 'supplier_debt.supplier_id_debt = ms_supplier.supplier_id');
        if ($search) {
            $this->db->like('supplier_code', $search);
            $this->db->or_like('supplier_name', $search);
        }
        $this->db->where('supplier_debt_remaining >', 0);
        $this->db->order_by('supplier_debt_id', 'DESC');
        $this->db->order_by('ms_supplier.supplier_name','DESC');
        $this->db->group_by('ms_supplier.supplier_id');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_debt_by_supplier($supplier_id)
    {
        $this->db->select('*');
        $this->db->from('supplier_debt');
        $this->db->where('supplier_id_debt', $supplier_id);
        $this->db->where('supplier_debt_remaining >', 0);
        $this->db->order_by('supplier_debt_id', 'DESC');
        return $this->db->get()->result();
    }   

    public function get_supplier_data($supplier_id)
    {
        $this->db->select('*, sum(supplier_debt_remaining) as supplier_debt_total, count(supplier_debt_id) as supplier_debt_count');
        $this->db->from('ms_supplier');
        $this->db->join('supplier_debt', 'supplier_debt.supplier_id_debt = ms_supplier.supplier_id', 'left');
        $this->db->where('ms_supplier.supplier_id', $supplier_id); 
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('supplier_debt_status', 'Success');
        $this->db->where('supplier_debt_remaining >', 0);
        $this->db->group_by('ms_supplier.supplier_id');
        return $this->db->get()->result();
    }

    public function get_debt_detail($supplier_id, $search, $limit, $start)
    {
        $this->db->select('*');
        $this->db->from('supplier_debt');
        if ($search) {
            $this->db->like('supplier_debt_invoice', $search);
        }
        $this->db->where('supplier_debt_remaining >', 0);
        $this->db->where('supplier_debt_status', 'Success');
        $this->db->where('supplier_id_debt', $supplier_id);
        $this->db->order_by('supplier_debt_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function insert_payment_debt($data)
    {
        $this->db->trans_start();
        $this->db->insert('supplier_debt_pay', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id;
    }

     public function insert_debt($data)
    {
        $this->db->trans_start();
        $this->db->insert('supplier_debt', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id;
    }

    public function update_remaining_debt($debt_id_payment, $remaining_debt_new)
    {
        $this->db->set('supplier_debt_remaining', $remaining_debt_new);
        $this->db->where('supplier_debt_id', $debt_id_payment);
        $this->db->update('supplier_debt');
    }

    public function delete_debt($debt_id)
    {
        $this->db->set('supplier_debt_status', 'Cancel');
        $this->db->where('supplier_debt_id', $debt_id);
        $this->db->update('supplier_debt');
    }
    
    // end debt //

    // start receivable //

    public function get_receivable($search, $limit, $start)
    {
        $this->db->select('*, sum(customer_receivable_remaining) as customer_receivable_total, count(customer_receivable_id) as customer_receivable_count');
        $this->db->from('customer_receivable');
        $this->db->join('ms_customer', 'customer_receivable.customer_id_receivable = ms_customer.customer_id');
        if ($search) {
            $this->db->like('customer_code', $search);
            $this->db->or_like('customer_name', $search);
        }
        $this->db->where('customer_receivable_remaining >', 0);
        $this->db->order_by('customer_receivable_id', 'DESC');
        $this->db->order_by('ms_customer.customer_name','DESC');
        $this->db->group_by('ms_customer.customer_id');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_receivable_by_customer($customer_id)
    {
        $this->db->select('*');
        $this->db->from('customer_receivable');
        $this->db->where('customer_id_receivable', $customer_id);
        $this->db->where('customer_receivable_remaining >', 0);
        $this->db->order_by('customer_receivable_id', 'DESC');
        return $this->db->get()->result();
    }   

    public function insert_payment_receivable($data)
    {
        $this->db->trans_start();
        $this->db->insert('customer_receivable_pay', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id; 
    }

    public function get_customer_data($customer_id)
    {
        $this->db->select('*, sum(customer_receivable_remaining) as customer_receivable_total, count(customer_receivable_id) as customer_receivable_count');
        $this->db->from('ms_customer');
        $this->db->join('customer_receivable', 'customer_receivable.customer_id_receivable = ms_customer.customer_id', 'left');
        $this->db->where('ms_customer.customer_id', $customer_id); 
        $this->db->where('customer_id', $customer_id);
        $this->db->where('customer_receivable_status', 'Success');
        $this->db->where('customer_receivable_remaining >', 0);
        $this->db->group_by('ms_customer.customer_id');
        return $this->db->get()->result();
    }

    public function get_receivable_detail($customer_id, $search, $limit, $start)
    {
        $this->db->select('*');
        $this->db->from('customer_receivable');
        if ($search) {
            $this->db->like('customer_receivable_invoice', $search);
        }
        $this->db->where('customer_receivable_remaining >', 0);
        $this->db->where('customer_receivable_status', 'Success');
        $this->db->where('customer_id_receivable', $customer_id);
        $this->db->order_by('customer_receivable_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function insert_receivable($data)
    {
        $this->db->trans_start();
        $this->db->insert('customer_receivable', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id;
    }

    public function update_remaining_receivable($receivable_id_payment, $remaining_receivable_new)
    {
        $this->db->set('customer_receivable_remaining', $remaining_receivable_new);
        $this->db->where('customer_receivable_id', $receivable_id_payment);
        $this->db->update('customer_receivable');
    }

    // end receivable //


}