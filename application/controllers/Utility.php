<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
date_default_timezone_set('Asia/Jakarta');

class Utility extends CI_Controller {
	
    public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
        $this->load->database(); 
		$this->load->model('utility_model');
		$this->load->helper(array('url', 'html'));
	}
	public function index()
	{
		echo "Utility Controller"; die();
	}

    private function check_access()
    {
        if(!$this->session->userdata('user_id')) {
            redirect(base_url('Auth/loginpage'));
        }
    }


    // start unit //
    public function unit()
    {
        $this->check_access();
        $this->load->view('Utility/unit');
    }

    public function get_unit()
    {
        $this->check_access();
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        $search = $input['search'] ?? '';
        $limit  = $input['limit'] ?? 100;
        $start  = $input['start'] ?? 0;
        $result = $this->utility_model->get_unit($search, $limit, $start);
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                "unit_id"  => $row->unit_id,
                "unit_code"  => $row->unit_code,
                "unit_name"  => $row->unit_name,
            ];
        }
        echo json_encode([
            "data" => $data
        ]);
    }

    public function save_unit()
    {
        $this->check_access();
        $unit_code = $this->input->post('unit_code');
        $unit_name = $this->input->post('unit_name');
        if (empty($unit_code)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Kode Unit"
            ]);
            return;
        }
        if (empty($unit_name)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Nama Unit"
            ]);
            return;
        }
        $data = [
            "unit_code" => $unit_code,
            "unit_name" => $unit_name
        ];
        $this->utility_model->save_unit($data);
        echo json_encode([
             "code" => 200,
            "status" => "success",
            "message" => "Unit Berhasil Di Simpan"
        ]);
    }

    public function edit_unit()
    {
        $this->check_access();
        $unit_id = $this->input->post('unit_id');
        $unit_code = $this->input->post('unit_code');
        $unit_name = $this->input->post('unit_name');
        if (empty($unit_id)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Unit Tidak Di Temukan"
            ]);
            return;
        }
        if (empty($unit_code)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Kode Unit"
            ]);
            return;
        }
        if (empty($unit_name)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Nama Unit"
            ]);
            return;
        }
        $data = [
            "unit_code" => $unit_code,
            "unit_name" => $unit_name
        ];
        $this->utility_model->edit_unit($unit_id, $data);
        echo json_encode([
            "code" => 200,
            "status" => "success",
            "message" => "Unit updated successfully"
        ]);
    }

    public function delete_unit()
    {
        $this->check_access();
        $unit_id = $this->input->post('unit_id');
        if (empty($unit_id)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Unit Tidak Di Temukan"
            ]);
            return;
        }
        $this->utility_model->delete_unit($unit_id);
        echo json_encode([
            "code" => 200,
            "status" => "success",
            "message" => "Unit deleted successfully"
        ]);
    }

    // End start unit //


    // start category //
    public function category()
    {
        $this->check_access();
        $this->load->view('Utility/category');
    }

    public function get_category()
    {
        $this->check_access();
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        $search = $input['search'] ?? '';
        $limit  = $input['limit'] ?? 100;
        $start  = $input['start'] ?? 0;
        $result = $this->utility_model->get_category($search, $limit, $start);
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                "category_id"  => $row->category_id,
                "category_code"  => $row->category_code,
                "category_name"  => $row->category_name,
            ];
        }
        echo json_encode([
            "data" => $data
        ]);
    }

    public function save_category()
    {
        $this->check_access();
        $category_code = $this->input->post('category_code');
        $category_name = $this->input->post('category_name');
        if (empty($category_code)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Kode Kategori"
            ]);
            return;
        }
        if (empty($category_name)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Nama Kategori"
            ]);
            return;
        }
        $data = [
            "category_code" => $category_code,
            "category_name" => $category_name
        ];
        $this->utility_model->save_category($data);
        echo json_encode([
             "code" => 200,
            "status" => "success",
            "message" => "Kategori Berhasil Di Simpan"
        ]);
    }

    public function edit_category()
    {
        $this->check_access();
        $category_id = $this->input->post('category_id');
        $category_code = $this->input->post('category_code');
        $category_name = $this->input->post('category_name');
        if (empty($category_id)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Kategori Tidak Di Temukan"
            ]);
            return;
        }
        if (empty($category_code)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Kode Kategori"
            ]);
            return;
        }
        if (empty($category_name)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Nama Kategori"
            ]);
            return;
        }
        $data = [
            "category_code" => $category_code,
            "category_name" => $category_name
        ];
        $this->utility_model->edit_category($category_id, $data);
        echo json_encode([
            "code" => 200,
            "status" => "success",
            "message" => "Kategori updated successfully"
        ]);
    }

    public function delete_category()
    {
        $this->check_access();
        $category_id = $this->input->post('category_id');
        if (empty($category_id)) {
            echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Kategori Tidak Di Temukan"
            ]);
            return;
        }
        $this->utility_model->delete_category($category_id);
        echo json_encode([
            "code" => 200,
            "status" => "success",
            "message" => "Kategori deleted successfully"
        ]);
    }

    // End category //


}
