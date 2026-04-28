    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");

    class Payment extends CI_Controller {
        
        public function __construct(){
            parent::__construct();
            $this->load->helper('url');
            $this->load->library('session');
            $this->load->database(); 
            $this->load->model('payment_model');
            $this->load->model('masterdata_model');
            $this->load->helper(array('url', 'html'));
        }
        public function index()
        {
            echo "Payment Controller"; die();
        }

        private function check_access()
        {
            if(!$this->session->userdata('user_id')) {
                redirect(base_url('Auth/loginpage'));
            }
        }


        // start debt //
        public function supplier_debt()
        {
            $this->check_access();
            $supplier_list['supplier_list'] = $this->masterdata_model->get_supplier_list();
            $this->load->view('Payment/supplierdebt', $supplier_list);
        }

        public function get_debt()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->payment_model->get_debt($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "supplier_id"           => $row->supplier_id,  
                    "supplier_code"         => $row->supplier_code,
                    "supplier_name"         => $row->supplier_name,
                    "supplier_phone"        => $row->supplier_phone,
                    "supplier_debt_total"   => 'Rp '. number_format($row->supplier_debt_total),
                    "supplier_debt_count"   => number_format($row->supplier_debt_count)
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }   

        public function detailsupplierdebt()
        {
            $this->check_access();
            $supplier_id = $this->input->get('id');
            $get_supplier_data['get_supplier_data'] = $this->payment_model->get_supplier_data($supplier_id);
            $get_debt_by_supplier['get_debt_by_supplier'] = $this->payment_model->get_debt_by_supplier($supplier_id);
            $data['data'] = array_merge($get_supplier_data, $get_debt_by_supplier);
         
            $this->load->view('Payment/detailsupplierdebt', $data);
        }

        public function get_debt_detail()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $supplier_id = $input['supplier_id'] ?? '';
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->payment_model->get_debt_detail($supplier_id, $search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "supplier_debt_id"                     => $row->supplier_debt_id,
                    "supplier_debt_invoice"                => $row->supplier_debt_invoice,  
                    "supplier_debt_nominal"                => number_format($row->supplier_debt_nominal),
                    "supplier_debt_remaining"              => number_format($row->supplier_debt_remaining),
                    "supplier_debt_created"                => $row->supplier_debt_created,
                    "supplier_debt_remaining_no_format"    => $row->supplier_debt_remaining,
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function save_payment_debt()
        {
            $this->check_access();
            $debt_id_payment = $this->input->post('debt_id_payment');
            $nota_payment = $this->input->post('nota_payment');
            $payment_amount_payment = $this->input->post('payment_amount_payment');
            $remaining_debt_new = $this->input->post('remaining_debt_new');
            $debt_amount_payment = $this->input->post('debt_amount_payment');

            if($payment_amount_payment > $debt_amount_payment) {
                echo json_encode(['code' => 400, 'message' => 'Nominal Pembayaran tidak boleh lebih besar dari sisa hutang']);
                return;
            }
            
            $data = [
                'supplier_debt_id' => $debt_id_payment,
                'supplier_debt_invoice' => $nota_payment,
                'supplier_debt_nominal' => $payment_amount_payment
            ];
            
            $this->payment_model->insert_payment_debt($data);
            $this->payment_model->update_remaining_debt($debt_id_payment, $remaining_debt_new);
            echo json_encode(['code' => 200, 'message' => 'Payment saved successfully']);
        }

        public function save_debt()
        {
            $this->check_access();
            $debt_supplier_id_add = $this->input->post('debt_supplier_id_add');
            $debt_invoice_add = $this->input->post('debt_invoice_add');
            $debt_nominal_add = $this->input->post('debt_nominal_add');

            if($debt_nominal_add <= 0) {
                echo json_encode(['code' => 400, 'message' => 'Nominal Hutang harus lebih besar dari 0']);
                return;
            }

            $data = [
                'supplier_id_debt' => $debt_supplier_id_add,
                'supplier_debt_invoice' => $debt_invoice_add,
                'supplier_debt_nominal' => $debt_nominal_add,
                'supplier_debt_remaining' => $debt_nominal_add
            ];
            
            $this->payment_model->insert_debt($data);
            echo json_encode(['code' => 200, 'message' => 'Debt saved successfully']);
        }

        public function delete_debt()
        {
            $this->check_access();
            $debt_id = $this->input->post('debt_id');
            $this->payment_model->delete_debt($debt_id);
            echo json_encode(['code' => 200, 'message' => 'Debt deleted successfully']);
        }
         // end debt //


         // start receivable //

        public function customer_receivable()
        {
            $this->check_access();
            $customer_list['customer_list'] = $this->masterdata_model->get_customer_list_all();
            $this->load->view('Payment/customerreceivable', $customer_list);
        }

        public function get_receivable()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->payment_model->get_receivable($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "customer_id"           => $row->customer_id,
                    "customer_code"         => $row->customer_code,
                    "customer_name"         => $row->customer_name,
                    "customer_phone"        => $row->customer_phone,
                    "customer_receivable_total"   => 'Rp '. number_format($row->customer_receivable_total),
                    "customer_receivable_count"   => number_format($row->customer_receivable_count)
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function detailcustomerreceivable()
        {
            $this->check_access();
            $customer_id = $this->input->get('id');
            $get_customer_data['get_customer_data'] = $this->payment_model->get_customer_data($customer_id);
            $get_receivable_by_customer['get_receivable_by_customer'] = $this->payment_model->get_receivable_by_customer($customer_id);
            $data['data'] = array_merge($get_customer_data, $get_receivable_by_customer);   
            $this->load->view('Payment/detailcustomerreceivable', $data);
        }

        public function get_receivable_detail()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $customer_id = $input['customer_id'] ?? '';
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->payment_model->get_receivable_detail($customer_id, $search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "customer_receivable_id"                     => $row->customer_receivable_id,
                    "customer_receivable_invoice"                => $row->customer_receivable_invoice,
                    "customer_receivable_nominal"                => number_format($row->customer_receivable_nominal),
                    "customer_receivable_remaining"              => number_format($row->customer_receivable_remaining),
                    "customer_receivable_created"                => $row->customer_receivable_created,
                    "customer_receivable_remaining_no_format"    => $row->customer_receivable_remaining,
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function save_receivable()
        {
            $this->check_access();
            $customer_id = $this->input->post('receivable_customer_id_add');
            $invoice = $this->input->post('receivable_invoice_add');
            $nominal = $this->input->post('receivable_nominal_add');
            if($nominal <= 0) {
                echo json_encode(['code' => 400, 'message' => 'Nominal Piutang harus lebih besar dari 0']);
                return;
            }
            $data = [
                'customer_id_receivable' => $customer_id,
                'customer_receivable_invoice' => $invoice,
                'customer_receivable_nominal' => $nominal,
                'customer_receivable_remaining' => $nominal
            ];

            $this->payment_model->insert_receivable($data);
            echo json_encode(['code' => 200, 'message' => 'Receivable saved successfully']);
        }

        public function save_payment_receivable()
        {
            $this->check_access();
            $receivable_id_payment = $this->input->post('receivable_id_payment');
            $nota_payment = $this->input->post('nota_payment');
            $payment_amount_payment = $this->input->post('payment_amount_payment');
            $remaining_receivable_new = $this->input->post('remaining_receivable_new');
            $receivable_amount_payment = $this->input->post('receivable_amount_payment');

            if($payment_amount_payment > $receivable_amount_payment) {
                echo json_encode(['code' => 400, 'message' => 'Nominal Pembayaran tidak boleh lebih besar dari sisa piutang']);
                return;
            }

            $data = [
                'customer_receivable_id' => $receivable_id_payment,
                'customer_receivable_invoice' => $nota_payment,
                'customer_receivable_nominal' => $payment_amount_payment
            ];
            $this->payment_model->insert_payment_receivable($data);
            $this->payment_model->update_remaining_receivable($receivable_id_payment, $remaining_receivable_new);
            echo json_encode(['code' => 200, 'message' => 'Payment saved successfully']);
        }

         // end receivable //

    }
