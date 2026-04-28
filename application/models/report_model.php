<?php
class Report_model extends CI_Model {
    public function get_income_report($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('transaction');
        $this->db->join('ms_customer', 'transaction.customer_id = ms_customer.customer_id', 'left');
        $this->db->join('ms_payment', 'transaction.payment_id = ms_payment.payment_id', 'left');
        $this->db->where('transaction_date >=', $start_date);
        $this->db->where('transaction_date <=', $end_date);
        $this->db->where('transaction_status', 'Success');
        return $this->db->get()->result();
    }

    public function get_income_report_total($start_date, $end_date)
    {
        $this->db->select('sum(transaction_total) as total_income, count(*) as total_row');
        $this->db->from('transaction');
        $this->db->where('transaction_date >=', $start_date);
        $this->db->where('transaction_date <=', $end_date);
        $this->db->where('transaction_status', 'Success');
        return $this->db->get()->result();
    }

    public function get_receivable($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('customer_receivable');
        $this->db->join('ms_customer', 'customer_receivable.customer_id_receivable = ms_customer.customer_id', 'left');
        $this->db->where('customer_receivable_created >=', $start_date);
        $this->db->where('customer_receivable_created <=', $end_date);
        $this->db->where('customer_receivable_remaining >', 0);
        $this->db->where('customer_receivable_status', 'Success');
        $this->db->order_by('customer_name', 'ASC');
        return $this->db->get()->result();
    }

    public function get_debt($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('supplier_debt');
        $this->db->join('ms_supplier', 'supplier_debt.supplier_id_debt = ms_supplier.supplier_id', 'left');
        $this->db->where('supplier_debt_created >=', $start_date);
        $this->db->where('supplier_debt_created <=', $end_date);
        $this->db->where('supplier_debt_remaining >', 0);
        $this->db->where('supplier_debt_status', 'Success');
        $this->db->order_by('supplier_name', 'ASC');
        return $this->db->get()->result();
    }
}