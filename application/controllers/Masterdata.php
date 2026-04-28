    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    date_default_timezone_set('Asia/Jakarta');

    class Masterdata extends CI_Controller {
        
        public function __construct(){
            parent::__construct();
            $this->load->helper('url');
            $this->load->library('session');
            $this->load->database(); 
            $this->load->model('masterdata_model');
            $this->load->model('utility_model');
            $this->load->helper(array('url', 'html'));
        }
        public function index()
        {
            echo "Masterdata Controller"; die();
        }

        private function check_access()
        {
            if(!$this->session->userdata('user_id')) {
                redirect(base_url('Auth/loginpage'));
            }
        }


        // start product //
        public function product()
        {
            $this->check_access();
            $unit_list['unit_list'] = $this->utility_model->get_unit_normal();
            $category_list['category_list'] = $this->utility_model->get_category_normal();
            $data['data'] = array_merge($unit_list, $category_list);
            $this->load->view('Masterdata/product', $data);
        }

        public function get_product()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->masterdata_model->get_product($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "product_id"     => $row->product_id,
                    "product_code"   => $row->product_code,
                    "product_name"   => $row->product_name,
                    "unit_name"      => $row->unit_name,
                    "category_name"  => $row->category_name,
                    "product_cogs"   => number_format($row->product_cogs),
                    "product_price"  => number_format($row->product_price),
                    "product_details" => $row->product_details,
                    "unit_id"        => $row->unit_id,
                    "category_id"    => $row->category_id,
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function get_product_by_id()
        {
            $this->check_access();
            $product_id = $this->input->post('id');
            if (empty($product_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Produk Tidak Di Temukan"
                ]);
                return;
            }
            $result = $this->masterdata_model->get_product_by_id($product_id);
            if (!$result) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Produk Tidak Di Temukan"
                ]);
                return;
            }
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "data" => [
                    "product_id"     => $result->product_id,
                    "product_code"   => $result->product_code,
                    "product_name"   => $result->product_name,
                    "unit_id"        => $result->unit_id,
                    "category_id"    => $result->category_id,
                    "product_cogs"   => $result->product_cogs,
                    "product_price"  => $result->product_price,
                    "product_details" => $result->product_details
                ]
            ]);
        }


        public function save_product()
        {
            $this->check_access();
            $product_code     = $this->input->post('product_code');
            $product_name     = $this->input->post('product_name');
            $product_unit     = $this->input->post('product_unit');
            $product_category = $this->input->post('product_category');
            $product_price    = $this->input->post('product_price');
            $product_price    = str_replace('.', '', $product_price);
            $product_cogs     = $this->input->post('product_cogs');
            $product_cogs     = str_replace('.', '', $product_cogs);
            $product_description = $this->input->post('product_description');


            if (empty($product_code)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Kode Produk"
                ]);
                return;
            }
            if (empty($product_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Produk"
                ]);
                return;
            }
            if (empty($product_unit)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Unit Produk"
                ]);
                return;
            }
            if (empty($product_category)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Kategori Produk"
                ]);
                return;
            }
            if ($product_price < 1) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Harga Tidak Boleh 0"
                ]);
                return;
            }

            if ($product_cogs < 1) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Harga ModalTidak Boleh 0"
                ]);
                return;
            }
            $data = [
                "product_code"      => $product_code,
                "product_name"      => $product_name,
                "unit_id"           => $product_unit,
                "product_price"     => $product_price,
                "product_cogs"      => $product_cogs,
                "product_details"   => $product_description,
                "category_id"       => $product_category
            ];

            $this->masterdata_model->save_product($data);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Unit Berhasil Di Simpan"
            ]);
        }

        public function edit_product()
        {
            $this->check_access();
            $product_id       = $this->input->post('product_id');
            $product_code     = $this->input->post('product_code');
            $product_name     = $this->input->post('product_name');
            $product_unit     = $this->input->post('product_unit');
            $product_category = $this->input->post('product_category');
            $product_price    = $this->input->post('product_price');
            $product_price    = str_replace('.', '', $product_price);
            $product_cogs     = $this->input->post('product_cogs');
            $product_cogs     = str_replace('.', '', $product_cogs);
            $product_description = $this->input->post('product_description');

            if (empty($product_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Produk Tidak Di Temukan"
                ]);
                return;
            }
            if (empty($product_code)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Kode Produk"
                ]);
                return;
            }
            if (empty($product_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Produk"
                ]);
                return;
            }
            if (empty($product_unit)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Unit Produk"
                ]);
                return;
            }
            if (empty($product_category)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Kategori Produk"
                ]);
                return;
            }
            if ($product_price < 1) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Harga Tidak Boleh 0"
                ]);
                return;
            }

            if ($product_cogs < 1) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Harga ModalTidak Boleh 0"
                ]);
                return;
            }

            $data = [
                "product_code"      => $product_code,
                "product_name"      => $product_name,
                "unit_id"           => $product_unit,
                "product_price"     => $product_price,
                "category_id"       => $product_category,
                "product_cogs"      => $product_cogs,
                "product_details"   => $product_description
            ];
            $this->masterdata_model->edit_product($data, $product_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Unit Berhasil Di Simpan"
            ]);
        }

        public function delete_product()
        {
            $this->check_access();
            $product_id = $this->input->post('product_id');
            if (empty($product_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Produk Tidak Di Temukan"
                ]);
                return;
            }
            $data = [
                "product_active" => 'N'
            ];
            $this->masterdata_model->edit_product($data, $product_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Produk Berhasil Di Hapus"
            ]);
        }   
        // End product //


        // start customer //
        public function customer()
        {
            $this->check_access();
            $this->load->view('Masterdata/customer');
        }

        public function get_customer()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->masterdata_model->get_customer($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "customer_id"    => $row->customer_id,
                    "customer_code"  => $row->customer_code,
                    "customer_name"   => $row->customer_name,
                    "customer_phone" => $row->customer_phone,
                    "customer_address" => $row->customer_address,
                    "customer_debt" => number_format($row->customer_debt)
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function save_customer()
        {
            $this->check_access();
            $customer_name    = $this->input->post('customer_name');
            $customer_phone   = $this->input->post('customer_phone');
            $customer_address = $this->input->post('customer_address');
            
            $maxCode  = $this->masterdata_model->last_customer_code();

            if ($maxCode == NULL) {
                $last_code = 'C-000001';
            } else {
                $maxCode   = $maxCode->customer_code;
                $last_code = substr($maxCode, -6);
                $last_code = 'C-' . substr('000000' . strval(floatval($last_code) + 1), -6);
            }

            if (empty($customer_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Pelanggan"
                ]);
                return;
            }
            $data = [
                "customer_code"    => $last_code,
                "customer_name"    => $customer_name,
                "customer_phone"   => $customer_phone,
                "customer_address" => $customer_address
            ];
            $this->masterdata_model->save_customer($data);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Pelanggan Berhasil Di Simpan"
            ]);
        }   

        public function edit_customer()
        {
            $this->check_access();
            $customer_id      = $this->input->post('customer_id');
            $customer_name    = $this->input->post('customer_name');
            $customer_phone   = $this->input->post('customer_phone');
            $customer_address = $this->input->post('customer_address');
            
            if (empty($customer_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Pelanggan Tidak Di Temukan"
                ]);
                return;
            }
            if (empty($customer_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Pelanggan"
                ]);
                return;
            }
            $data = [
                "customer_name"    => $customer_name,
                "customer_phone"   => $customer_phone,
                "customer_address" => $customer_address
            ];
            $this->masterdata_model->edit_customer($data, $customer_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Pelanggan Berhasil Di Simpan"
            ]);
        }

        public function delete_customer()
        {
            $this->check_access();
            $customer_id = $this->input->post('customer_id');
            if (empty($customer_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Pelanggan Tidak Di Temukan"
                ]);
                return;
            }
            $data = [
                "customer_active" => 'N'
            ];
            $this->masterdata_model->edit_customer($data, $customer_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Pelanggan Berhasil Di Hapus"
            ]);
        }
        // end product //


        // start supplier //

        public function supplier()
        {
            $this->check_access();
            $this->load->view('Masterdata/supplier');
        }

        public function get_supplier()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->masterdata_model->get_supplier($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "supplier_id"    => $row->supplier_id,
                    "supplier_code"  => $row->supplier_code,
                    "supplier_name"   => $row->supplier_name,
                    "supplier_phone" => $row->supplier_phone,
                    "supplier_address" => $row->supplier_address,
                    "supplier_debt" => number_format($row->supplier_debt)
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function save_supplier()
        {
            $this->check_access();
            $supplier_name    = $this->input->post('supplier_name');
            $supplier_phone   = $this->input->post('supplier_phone');
            $supplier_address = $this->input->post('supplier_address');
            
            $maxCode  = $this->masterdata_model->last_supplier_code();

            if ($maxCode == NULL) {
                $last_code = 'S-000001';
            } else {
                $maxCode   = $maxCode->supplier_code;
                $last_code = substr($maxCode, -6);
                $last_code = 'S-' . substr('000000' . strval(floatval($last_code) + 1), -6);
            }

            if (empty($supplier_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Supplier"
                ]);
                return;
            }
            $data = [
                "supplier_code"    => $last_code,
                "supplier_name"    => $supplier_name,
                "supplier_phone"   => $supplier_phone,
                "supplier_address" => $supplier_address
            ];
            $this->masterdata_model->save_supplier($data);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Supplier Berhasil Di Simpan"
            ]);
        }

        public function edit_supplier()
        {
            $this->check_access();
            $supplier_id      = $this->input->post('supplier_id');
            $supplier_name    = $this->input->post('supplier_name');
            $supplier_phone   = $this->input->post('supplier_phone');
            $supplier_address = $this->input->post('supplier_address');
            
            if (empty($supplier_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Supplier Tidak Di Temukan"
                ]);
                return;
            }
            if (empty($supplier_name)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Silahkan Isi Nama Supplier"
                ]);
                return;
            }
            $data = [
                "supplier_name"    => $supplier_name,
                "supplier_phone"   => $supplier_phone,
                "supplier_address" => $supplier_address
            ];
            $this->masterdata_model->edit_supplier($data, $supplier_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Supplier Berhasil Di Simpan"
            ]);
        }

        public function delete_supplier()
        {
            $this->check_access();
            $supplier_id = $this->input->post('supplier_id');
            if (empty($supplier_id)) {
                echo json_encode([
                    "code" => 0,
                    "status" => "error",
                    "message" => "Supplier Tidak Di Temukan"
                ]);
                return;
            }
            $data = [
                "supplier_active" => 'N'
            ];
            $this->masterdata_model->edit_supplier($data, $supplier_id);
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Supplier Berhasil Di Hapus"
            ]);
        }

    }
