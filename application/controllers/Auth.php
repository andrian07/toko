<?php
 defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    date_default_timezone_set('Asia/Jakarta');

class Auth extends CI_Controller {
    public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
        $this->load->database(); 
		$this->load->model('auth_model');
		$this->load->helper(array('url', 'html'));
	}

	public function index()
	{
		if($this->session->userdata('user_id'))
		{
			redirect(base_url('Dashboard'));
		}else{
			redirect(base_url('Auth/loginpage'));
		}
	}

	public function loginpage()
	{
		$this->load->view('login');
	}

	public function login_process()
	{
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));

		if($username == '') {
			echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Username"
            ]);
            return;
		}
		if($password == '') {
			echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Silahkan Isi Password"
            ]);
            return;
		}

		$check_user = $this->auth_model->check_user($username, $password);
		if ($check_user ->num_rows() > 0) {
			$user = $check_user->row();
			$this->session->set_userdata('user_id', $user->user_id);
			$this->session->set_userdata('user_name', $user->user_name);
			$this->session->set_userdata('user_role', $user->user_role);
			echo json_encode([
				"code" => 200,
				"status" => "success",
				"message" => "Login Berhasil"	
			]);
			return;
		}else{
			echo json_encode([
                "code" => 0,
                "status" => "error",
                "message" => "Username atau Password Salah"
            ]);
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url('Auth'));
	}
}
