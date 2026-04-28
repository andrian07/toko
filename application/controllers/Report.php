    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    date_default_timezone_set('Asia/Jakarta');
    require 'vendor/autoload.php';

    use Dompdf\Dompdf;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class Report extends CI_Controller {
        
        public function __construct(){
            parent::__construct();
            $this->load->helper('url');
            $this->load->library('session');
            $this->load->database(); 
            $this->load->model('report_model');
            $this->load->helper(array('url', 'html'));
        }
        public function index()
        {
            echo "Report Controller"; die();
        }

        private function check_access()
        {
            if(!$this->session->userdata('user_id')) {
                redirect(base_url('Auth/loginpage'));
            }
        }
            // start report sales //
        public function income()
        {
            $this->check_access();
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $data['data'] = $this->report_model->get_income_report($start_date, $end_date);
            $this->load->view('report/incomereport', $data);
        }


    
        public function reportincomepdf()
        {
            	$start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_income_report['get_income_report'] = $this->report_model->get_income_report($start_date, $end_date);
                $get_income_report_total['get_income_report_total'] = $this->report_model->get_income_report_total($start_date, $end_date);
                $start_date_formatted['start_date_formatted'] = date('d M Y', strtotime($start_date));
                $end_date_formatted['end_date_formatted'] = date('d M Y', strtotime($end_date));
                $data['data'] = array_merge($get_income_report, $get_income_report_total, $start_date_formatted, $end_date_formatted);
                $htmlView   = $this->load->view('report/incomereportpdf', $data, true);
                $dompdf = new Dompdf();
                $dompdf->loadHtml($htmlView);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $dompdf->stream('laporanpenjualan.pdf', array("Attachment" => false));
                exit();
        }

        public function reportincomeexcell()
        {
                $start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_income_report = $this->report_model->get_income_report($start_date, $end_date);
                $get_income_report_total = $this->report_model->get_income_report_total($start_date, $end_date);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', "Laporan Penjualan"); 
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A3:F3')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');
                
                // Set header
                $sheet->setCellValue('A4', 'No');
                $sheet->setCellValue('B4', 'Invoice');
                $sheet->setCellValue('C4', 'Pelanggan');
                $sheet->setCellValue('D4', 'Metode Pembayaran');
                $sheet->setCellValue('E4', 'Tanggal');
                $sheet->setCellValue('F4', 'Total');

                // Set data
                $rowNumber = 5;
                foreach ($get_income_report as $index => $row) {
                    $sheet->setCellValue('A' . $rowNumber, $index + 1);
                    $sheet->setCellValue('B' . $rowNumber, $row->transaction_inv);
                    $sheet->setCellValue('C' . $rowNumber, $row->customer_name);
                    $sheet->setCellValue('D' . $rowNumber, $row->payment_name);
                    $sheet->setCellValue('E' . $rowNumber, date('d M Y', strtotime($row->transaction_date)));
                    $sheet->setCellValue('F' . $rowNumber, $row->transaction_total);
                    $rowNumber++;
                }

                $sheet->setCellValue('E' . $rowNumber, 'Total Pendapatan:');
                $sheet->setCellValue('F' . $rowNumber, $get_income_report_total[0]->total_income);
                $sheet->getStyle('E' . $rowNumber)->getFont()->setBold(true);
                $sheet->getStyle('F' . $rowNumber)->getFont()->setBold(true);
                
                $sheet->getColumnDimension('A')->setWidth(10); 
                $sheet->getColumnDimension('B')->setWidth(40); 
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(25);

                
                
                $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');	
                // Output to browser
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="laporanpenjualan.xlsx"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                ob_end_clean(); // Clear output buffer to avoid corrupting the Excel file
                $writer->save('php://output');
        }

        // end  report sales //


        // start report receivable //

        public function reportreceivable()
        {
            $this->check_access();
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $data['data'] = $this->report_model->get_receivable($start_date, $end_date);
            $this->load->view('report/receivablereport', $data);
        }

        public function reportreceivablepdf()
        {
            	$start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_receivable_report['get_receivable_report'] = $this->report_model->get_receivable($start_date, $end_date);
                $start_date_formatted['start_date_formatted'] = date('d M Y', strtotime($start_date));
                $end_date_formatted['end_date_formatted'] = date('d M Y', strtotime($end_date));
                $data['data'] = array_merge($get_receivable_report, $start_date_formatted, $end_date_formatted);
                $htmlView   = $this->load->view('report/receivablereportpdf', $data, true);
                $dompdf = new Dompdf();
                $dompdf->loadHtml($htmlView);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $dompdf->stream('laporanpiutang.pdf', array("Attachment" => false));
                exit();
        }

         public function reportreceivableexcell()
         {
                $start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_receivable_report = $this->report_model->get_receivable($start_date, $end_date);
                // Similar implementation as reportincomeexcell but with receivable data
                // You can customize the columns and data as needed 
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();  
                $sheet->setCellValue('A1', "Laporan Piutang Pelanggan");
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A3:F3')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');
                // Set header
                $sheet->setCellValue('A4', 'No');
                $sheet->setCellValue('B4', 'No Nota');
                $sheet->setCellValue('C4', 'Pelanggan');
                $sheet->setCellValue('D4', 'Total Hutang');
                $sheet->setCellValue('E4', 'Sudah Bayar');
                $sheet->setCellValue('F4', 'Sisa Hutang');
                // Set data
                $rowNumber = 5;
                foreach ($get_receivable_report as $index => $row) {
                    $sheet->setCellValue('A' . $rowNumber, $index + 1);
                    $sheet->setCellValue('B' . $rowNumber, $row->customer_receivable_invoice);
                    $sheet->setCellValue('C' . $rowNumber, $row->customer_name);
                    $sheet->setCellValue('D' . $rowNumber, $row->customer_receivable_nominal);
                    $sheet->setCellValue('E' . $rowNumber, $row->customer_receivable_nominal - $row->customer_receivable_remaining);
                    $sheet->setCellValue('F' . $rowNumber, $row->customer_receivable_remaining);
                    $rowNumber++;
                }
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(25);
                $sheet->getStyle('D')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('E')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');
                // Output to browser
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="laporanpiutang.xlsx"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                ob_end_clean(); // Clear output buffer to avoid corrupting the Excel file
                $writer->save('php://output');
         }
        // end report receivable //


        // start report debt //

        public function reportdebt()
        {
            $this->check_access();
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $data['data'] = $this->report_model->get_debt($start_date, $end_date);
            $this->load->view('report/debtreport', $data);
        }

         public function reportdebtpdf()
        {
            	$start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_debt_report['get_debt_report'] = $this->report_model->get_debt($start_date, $end_date);
                $start_date_formatted['start_date_formatted'] = date('d M Y', strtotime($start_date));
                $end_date_formatted['end_date_formatted'] = date('d M Y', strtotime($end_date));
                $data['data'] = array_merge($get_debt_report, $start_date_formatted, $end_date_formatted);
                $htmlView   = $this->load->view('report/debtreportpdf', $data, true);
                $dompdf = new Dompdf();
                $dompdf->loadHtml($htmlView);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $dompdf->stream('laporandept.pdf', array("Attachment" => false));
                exit();
        }

         public function reportdebtexcell()
         {
                $start_date       = $this->input->get('start_date');
                $end_date 	      = $this->input->get('end_date');

                if($start_date == null || $end_date == null) {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-d');
                }
                $get_debt_report = $this->report_model->get_debt($start_date, $end_date);
                // Similar implementation as reportincomeexcell but with debt data
                // You can customize the columns and data as needed 
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();  
                $sheet->setCellValue('A1', "Laporan Hutang Supplier");
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A3:F3')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');
                // Set header
                $sheet->setCellValue('A4', 'No');
                $sheet->setCellValue('B4', 'No Nota');
                $sheet->setCellValue('C4', 'Supplier');
                $sheet->setCellValue('D4', 'Total Hutang');
                $sheet->setCellValue('E4', 'Sudah Bayar');
                $sheet->setCellValue('F4', 'Sisa Hutang');
                // Set data
                $rowNumber = 5;
                foreach ($get_debt_report as $index => $row) {
                    $sheet->setCellValue('A' . $rowNumber, $index + 1);
                    $sheet->setCellValue('B' . $rowNumber, $row->supplier_debt_invoice);
                    $sheet->setCellValue('C' . $rowNumber, $row->supplier_name);
                    $sheet->setCellValue('D' . $rowNumber, $row->supplier_debt_nominal);
                    $sheet->setCellValue('E' . $rowNumber, $row->supplier_debt_nominal - $row->supplier_debt_remaining);
                    $sheet->setCellValue('F' . $rowNumber, $row->supplier_debt_remaining);
                    $rowNumber++;
                }
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(25);
                $sheet->getStyle('D')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('E')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');
                // Output to browser
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="laporanhutang.xlsx"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                ob_end_clean(); // Clear output buffer to avoid corrupting the Excel file
                $writer->save('php://output');
         }



    }
