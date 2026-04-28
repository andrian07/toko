<?php
 defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    date_default_timezone_set('Asia/Jakarta');
	
class Dashboard extends CI_Controller {
	
	
    public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('masterdata_model');
        $this->load->database(); 
		$this->load->helper(array('url', 'html'));
	}


    private function check_access()
    {
        if(!$this->session->userdata('user_id')) {
            redirect(base_url('Auth/loginpage'));
        }
    }


	public function index()
	{
		$this->check_access();
		$today_sales['today_sales'] = $this->masterdata_model->today_sales();
		$monthly_sales['monthly_sales'] = $this->masterdata_model->monthly_sales();
		$piutang_supplier['piutang_supplier'] = $this->masterdata_model->piutang_supplier();
		$hutang_customer['hutang_customer'] = $this->masterdata_model->hutang_customer();
		$last_transaction_dashboard['last_transaction_dashboard'] = $this->masterdata_model->last_transaction_dashboard();
		$last_transaction_dashboard_5['last_transaction_dashboard_5'] = $this->masterdata_model->last_transaction_dashboard_5();
		$last_supplier_debt_list['last_supplier_debt_list'] = $this->masterdata_model->last_supplier_debt_list();
		$data['data'] = array_merge($today_sales, $monthly_sales, $piutang_supplier, $hutang_customer, $last_transaction_dashboard, $last_transaction_dashboard_5, $last_supplier_debt_list);
		$this->load->view('dashboard', $data);
	}
}
