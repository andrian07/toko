<?php
class Masterdata_model extends CI_Model {


    // start product //

    public function get_product_list($search = '', $limit = 10000, $start = 0)
    {
        return $this->get_product($search, $limit, $start);
    }   
    public function get_product($search = '', $limit = 10000, $start = 0)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 100;
        }
        $start = (int)$start;
        if ($start < 0) {
            $start = 0;
        }
        $this->db->select('*');
        $this->db->from('ms_product');
        $this->db->join('ms_units', 'ms_product.unit_id = ms_units.unit_id');
        $this->db->join('ms_category', 'ms_product.category_id = ms_category.category_id');
        $this->db->where('product_active', 'Y');
        if (!empty($search)) {
            $this->db->like('unit_name', $search);
        }
        $this->db->order_by('product_id', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function save_product($data)
    {
         $this->db->insert('ms_product', $data);
    }

    public function edit_product($data, $product_id)
    {
         $this->db->where('product_id', $product_id);
         $this->db->update('ms_product', $data);
    }

    public function get_product_by_id($product_id)
    {
        $this->db->select('*');
        $this->db->from('ms_product');
        $this->db->join('ms_units', 'ms_product.unit_id = ms_units.unit_id');
        $this->db->join('ms_category', 'ms_product.category_id = ms_category.category_id');
        $this->db->where('product_id', $product_id);
        $this->db->where('product_active', 'Y');
        return $this->db->get()->row();
    }
    // end product //

    // start customer //

    public function get_customer_list_all()
    {
        $this->db->select('*');
        $this->db->from('ms_customer');
        return $this->db->get()->result();
    }   
    
    public function get_customer_list()
    {
        return $this->get_customer();
    }   
    public function get_customer($search = '', $limit = 10000, $start = 0)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 100;
        }

        $start = (int)$start;
        if ($start < 0) {
            $start = 0;
        }

        $this->db->select('*');
        $this->db->from('ms_customer');
        $this->db->where('customer_active', 'Y');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('customer_name', $search);
            $this->db->or_like('customer_code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('customer_id', 'DESC');
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    public function save_customer($data)
    {
         $this->db->insert('ms_customer', $data);
    }

    public function last_customer_code()
    {
        $this->db->select('customer_code');
        $this->db->from('ms_customer');
        $this->db->order_by('customer_id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }
    
    public function edit_customer($data, $customer_id)
    {
         $this->db->where('customer_id', $customer_id);
         $this->db->update('ms_customer', $data);
    }
    
    // end customer //

    // start supplier //

    public function get_supplier_list()
    {
        $this->db->select('*');
        $this->db->from('ms_supplier');
        $this->db->where('supplier_active', 'Y');
        return $this->db->get()->result();
    }
    public function get_supplier($search = '', $limit = 10000, $start = 0)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 100;
        }

        $start = (int)$start;
        if ($start < 0) {
            $start = 0;
        }

        $this->db->select('*');
        $this->db->from('ms_supplier');
        $this->db->where('supplier_active', 'Y');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('supplier_name', $search);
            $this->db->or_like('supplier_code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('supplier_id', 'DESC');
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }
    public function save_supplier($data)
    {
         $this->db->insert('ms_supplier', $data);
    }   

    public function last_supplier_code()
    {
        $this->db->select('supplier_code');
        $this->db->from('ms_supplier');
        $this->db->order_by('supplier_id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function edit_supplier($data, $supplier_id)
    {
         $this->db->where('supplier_id', $supplier_id);
         $this->db->update('ms_supplier', $data);
    }

    public function delete_supplier($supplier_id)
    {
        $this->db->where('supplier_id', $supplier_id);
        $this->db->update('ms_supplier', ['supplier_active' => 'N']);
    }
    // end supplier //

    public function get_payment_list()
    {
        $this->db->select('*');
        $this->db->from('ms_payment');
        $this->db->where('payment_active', 'Y');
        return $this->db->get()->result();
    }


    // start dashboard //
    public function today_sales()
    {
        $this->db->select('SUM(transaction_total) as total_today_sales');
        $this->db->from('transaction');
        $this->db->where("DATE(transaction_date) = CURDATE()", null, false);
        return $this->db->get()->row();
    }

    public function monthly_sales()
    {
        $this->db->select('SUM(transaction_total) as total_monthly_sales');
        $this->db->from('transaction');
        $this->db->where('MONTH(transaction_date)', date('m'));
        $this->db->where('YEAR(transaction_date)', date('Y'));
         return $this->db->get()->row();
    }

    public function piutang_supplier()
    {
        $this->db->select('SUM(supplier_debt_remaining) as total_debt, count(supplier_debt_id) as qty_nota_debt');
        $this->db->from('supplier_debt');
        return $this->db->get()->row();
    }

    public function hutang_customer()
    {
        $this->db->select('SUM(customer_receivable_remaining) as total_receivable, count(customer_receivable_id) as qty_nota_receivable');
        $this->db->from('customer_receivable');
        return $this->db->get()->row();
    }
    public function last_transaction_dashboard()
    {
        $this->db->select('count(*) as total_item, transaction_total, transaction_inv, transaction_date, customer_name');
        $this->db->from('transaction');
        $this->db->join('transaction_detail', 'transaction.transaction_id = transaction_detail.transaction_id');
        $this->db->join('ms_customer', 'transaction.customer_id = ms_customer.customer_id', 'left');
        $this->db->order_by('transaction.transaction_id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->result();
    }

    public function last_transaction_dashboard_5()
    {
        // include created timestamp and return multiple rows
        $this->db->select('transaction_total, transaction_inv, transaction_date, transaction_created_at, customer_name');
        $this->db->from('transaction');
        $this->db->join('ms_customer', 'transaction.customer_id = ms_customer.customer_id', 'left');
        $this->db->order_by('transaction.transaction_id', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
     }

     public function last_supplier_debt_list()
     {
         $this->db->select('supplier_debt_id, supplier_debt_invoice, supplier_debt_nominal, supplier_debt_remaining, supplier_name, supplier_debt_created');
         $this->db->from('supplier_debt');
         $this->db->join('ms_supplier', 'supplier_debt.supplier_id_debt = ms_supplier.supplier_id');
         $this->db->where('supplier_debt_remaining >', 0);
         $this->db->order_by('supplier_debt_id', 'DESC');
         $this->db->limit(5);
         return $this->db->get()->result();
      }
    // end dashboard //


}