    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    date_default_timezone_set('Asia/Jakarta');

    class Pos extends CI_Controller {
        
        public function __construct(){
            parent::__construct();
            $this->load->helper('url');
            $this->load->library('session');
            $this->load->database(); 
            $this->load->model('pos_model');
            $this->load->model('masterdata_model');
            $this->load->helper(array('url', 'html'));
        }


        private function check_access()
        {
            if(!$this->session->userdata('user_id')) {
                redirect(base_url('Auth/loginpage'));
            }
        }

        // start pos //
        public function index()
        {
            $this->check_access();
            // load products and customers to be used in the POS view
            $products = $this->masterdata_model->get_product_list();
            $customers = $this->masterdata_model->get_customer_list();
            $payment = $this->masterdata_model->get_payment_list();
            $data = [
                'products' => $products,
                'customers' => $customers,
                'payment' => $payment,
            ];
            $this->load->view('Pos/pos', $data);
        }

        public function save_sale()
        {
            $this->check_access();
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) {
                $payload = $this->input->post();
            }
            $customer_id = isset($payload['customer_id']) ? $payload['customer_id'] : null;
            $payment_type = isset($payload['payment_type']) ? $payload['payment_type'] : null;
            $transaction_total = isset($payload['transaction_total']) ? $payload['transaction_total'] : null;
            $user_id = $this->session->userdata('user_id');

            if($payment_type == null){
                echo json_encode(['code' => '0', 'message' => 'Silahkan Isi Jenis Pembayaran']);
            }
            if($transaction_total < 1){
                echo json_encode(['code' => '0', 'message' => 'Silahkan Isi Transaksi']);
            }
            
            $maxCode  = $this->pos_model->last_transaction_code();
            $date = date('Y-m-d');
            if ($maxCode == NULL) {
                $last_code = 'INV.' . date('Ymd') . '.000000001';
            } else {
                $maxCode   = $maxCode->transaction_inv;
                $last_code = substr($maxCode, -9);
                $last_code = 'INV.' . date('Ymd') . '.' . substr('000000000' . strval(floatval($last_code) + 1), -9);
            }

            $data = [
                'transaction_inv' => $last_code,
                'customer_id'     => $customer_id,
                'payment_id'      => $payment_type,
                'transaction_subtotal' => $transaction_total,
                'transaction_total' => $transaction_total,  
                'transaction_user_id' => $this->session->userdata('user_id')
            ];

            $save_transaction = $this->pos_model->insert_transaction($data);

            $get_temp_cart = $this->pos_model->get_temp_cart($user_id);
            foreach ($get_temp_cart as $item) {
                $data_detail = [
                    'transaction_id' => $save_transaction,
                    'item_id' => $item->product_id,
                    'price' => $item->product_price,
                    'qty' => $item->product_qty,
                ];
                $this->pos_model->insert_detal($data_detail);
            }

            $this->pos_model->clear_temp_cart($user_id);

            echo json_encode(['code' => '200', 'message' => 'Transaksi Sukses Di Simpan']);
        }

        public function add_temp_item()
        {
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) {
                $payload = $this->input->post();
            }

            $product_id = isset($payload['product_id']) ? $payload['product_id'] : null;
            $product_price = isset($payload['product_price']) ? $payload['product_price'] : null;
            $product_qty = isset($payload['product_qty']) ? $payload['product_qty'] : 1;

            if (empty($product_id) || $product_price === null) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['status'=>'error','message'=>'Invalid input']));
            }

            // Prefer logged-in user_id; fall back to PHP session id so cart persists across refresh
            $user_id = $this->session->userdata('user_id');
            if (empty($user_id)) {
                if (session_id() == '') @session_start();
                $user_id = session_id();
            }

            // if same product exists for this user/session, increment qty instead of inserting duplicate
            $this->db->where('user_id', $user_id);
            $this->db->where('product_id', $product_id);
            $existing = $this->db->get('temp_cart')->row();

            if ($existing) {
                $new_qty = (int)$existing->product_qty + (int)$product_qty;
                $this->db->where('product_id', $existing->product_id);
                $this->db->update('temp_cart', ['product_qty' => $new_qty, 'product_price' => $product_price]);
                $insert_id = $existing->product_id; // keep existing ID for updates
                $data = ['product_id'=>$product_id,'product_price'=>$product_price,'product_qty'=>$new_qty,'user_id'=>$user_id];
            } else {
                $data = [
                    'product_id' => $product_id,
                    'product_price' => $product_price,
                    'product_qty' => $product_qty,
                    'user_id' => $user_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('temp_cart', $data);
                $insert_id = $this->db->insert_id();
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status'=>'success','id'=>$insert_id,'data'=>$data]));
        }

        // Return a single product's basic info (id, name, price)
        // Public API: product info can be fetched without login
        public function get_product_item()
        {
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) {
                $payload = $this->input->get();
            }
            $product_id = isset($payload['product_id']) ? $payload['product_id'] : null;
            if (empty($product_id)) {
                return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status'=>'error','message'=>'product_id required']));
            }

            $this->db->select('product_id, product_name, product_price');
            $this->db->from('ms_product');
            $this->db->where('product_id', $product_id);
            $row = $this->db->get()->row();
            if (!$row) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'not found']));
            }
            return $this->output->set_content_type('application/json')->set_output(json_encode($row));
        }

        // Return current user's temp cart items as JSON
        // Public API: allow anonymous session-based carts
        public function get_temp_cart()
        {
            $user_id = $this->session->userdata('user_id');
            if (empty($user_id)) { if (session_id() == '') @session_start(); $user_id = session_id(); }

            $this->db->select('tc.*, p.product_name, p.product_price as master_price');
            $this->db->from('temp_cart tc');
            $this->db->join('ms_product p', 'p.product_id = tc.product_id', 'left');
            $this->db->where('tc.user_id', $user_id);
            $rows = $this->db->get()->result();

            return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
        }

        // Update qty for a temp_cart item (expects id or product_id)
        // Public API for session carts
        public function update_temp_item()
        {
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) $payload = $this->input->post();

            $user_id = $this->session->userdata('user_id');
            if (empty($user_id)) { if (session_id() == '') @session_start(); $user_id = session_id(); }
            $product_id = isset($payload['product_id']) ? $payload['product_id'] : null;
            $qty = isset($payload['product_qty']) ? (int)$payload['product_qty'] : null;
            $price = isset($payload['product_price']) ? $payload['product_price'] : null;

            // require at least product_id and either qty or price to update
            if (!$user_id || !$product_id || ($qty === null && $price === null)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error']));
            }

            $updateData = [];
            // include qty only when provided
            if ($qty !== null) $updateData['product_qty'] = $qty;
            // allow updating price as well
            if (isset($payload['product_price'])) {
                $updateData['product_price'] = $payload['product_price'];
            }

            $this->db->where('user_id', $user_id);
            $this->db->where('product_id', $product_id);
            $this->db->update('temp_cart', $updateData);

            return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'success']));
        }

        // Delete a temp_cart item by product_id
        // Public API for session carts
        public function delete_temp_item()
        {
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!$payload) $payload = $this->input->post();

            $user_id = $this->session->userdata('user_id');
            if (empty($user_id)) { if (session_id() == '') @session_start(); $user_id = session_id(); }
            $product_id = isset($payload['product_id']) ? $payload['product_id'] : null;

            if (!$user_id || !$product_id) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error']));
            }

            $this->db->where('user_id', $user_id);
            $this->db->where('product_id', $product_id);
            $this->db->delete('temp_cart');

            return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'success']));
        }

        // Migrate old temp_cart rows that have NULL user_id to the current PHP session id
        // WARNING: this will assign all rows with NULL user_id to this session.
        public function migrate_temp_cart()
        {
            // do not enforce login here because we may migrate anonymous rows
            if (session_id() == '') @session_start();
            $sid = session_id();

            // update all rows where user_id IS NULL to current session id
            $this->db->where('user_id IS NULL', null, false);
            $this->db->update('temp_cart', ['user_id' => $sid]);
            $affected = $this->db->affected_rows();

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success', 'migrated' => (int)$affected, 'session_id' => $sid
            ]));
        }

        // Migrate temp_cart rows (NULL or session_id) to the logged-in user_id
        public function migrate_temp_cart_to_user()
        {
            $this->check_access(); // require login
            $user_id = $this->session->userdata('user_id');
            if (!$user_id) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'Not logged in']));
            }

            if (session_id() == '') @session_start();
            $sid = session_id();

            // update rows where user_id IS NULL or equals this session id
            $this->db->where('user_id IS NULL', null, false);
            $this->db->or_where('user_id', $sid);
            $this->db->update('temp_cart', ['user_id' => $user_id]);
            $affected = $this->db->affected_rows();

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success', 'migrated' => (int)$affected, 'user_id' => $user_id
            ]));
        }

        // end pos //


        // start pos list //

        public function transactionlist()
        {
            $this->check_access();
            $this->load->view('Pos/transactionlist');
        }

        public function get_pos_list()
        {
            $this->check_access();
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $search = $input['search'] ?? '';
            $limit  = $input['limit'] ?? 10000;
            $start  = $input['start'] ?? 0;
            $result = $this->pos_model->get_pos_list($search, $limit, $start);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    "transaction_id"     => $row->transaction_id,
                    "transaction_inv"   => $row->transaction_inv,
                    "customer_name"   => $row->customer_name,
                    "payment_name"      => $row->payment_name,
                    "transaction_total"  => number_format($row->transaction_total),
                    "transaction_date"  => $row->transaction_date,
                    "transaction_status"  => $row->transaction_status,
                ];
            }
            echo json_encode([
                "data" => $data
            ]);
        }

        public function get_pos_detail()
        {
            $this->check_access();
            $transaction_id = $this->input->post('transaction_id');
            if (!$transaction_id) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'transaction_id required']));
            }
            $row = $this->pos_model->get_transaction_by_id($transaction_id);
            if (!$row){
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'error','message'=>'not found']));
            }
            $details = $this->pos_model->get_transaction_details($transaction_id);
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status'=>'success','data'=>['transaction'=>$row,'details'=>$details]]));
        }

        public function delete_transaction()
        {
            $this->check_access();
            $transaction_id = $this->input->post('transaction_id');
            if (!$transaction_id) {
                echo json_encode(['code' => '0', 'message' => 'Transaksi Tidak Ditemukan']);
                return;
            }
            $this->pos_model->delete_transaction($transaction_id);
            echo json_encode(['code' => '200', 'message' => 'Transaksi Berhasil Dihapus']);
            return;
        }
        // end pos list //


    }
