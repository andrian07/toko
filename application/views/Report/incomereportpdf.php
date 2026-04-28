<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Penjualan</title>
  <style type="text/css">
    body{
        margin: 0 !important;
        padding: 0 !important;
    }
    .headline{
        text-align: center;
        border-bottom: double;
    }

    table, td, th {  
        border: 1px solid #000;
        text-align: left;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 5px;
    }

    th{
        background-color: #D3D0C8;
        text-align: center;
    }

    td{
       font-size: 14px;
   }

   @page { margin: 10px; }
   body { margin: 10px; }

</style>
</head>
<body>
    <div class="container">
        <h2 class="headline">Laporan Penjualan</h2>
        <p style="text-align: center; margin-top: 5px; margin-bottom: 5px;">Periode: <?php echo $data['start_date_formatted']; ?> - <?php echo $data['end_date_formatted']; ?></p>
    </div>
    <div class="container">
        <table>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th>Metode Pembayaran</th>
                <th>Tanggal</th>
                <th>Total</th>
            </tr>
            <?php $no = 1; foreach($data['get_income_report'] as $row) { ?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td><?php echo $row->transaction_inv; ?></td>
                <td><?php echo $row->customer_name; ?></td>
                <td><?php echo $row->payment_name; ?></td>
                <td style="text-align: center;"><?php echo date('d M Y', strtotime($row->transaction_date)); ?></td>
                <td style="text-align: right;"><?php echo number_format($row->transaction_total, 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <th colspan="5" style="text-align: right;">Total Pendapatan:</th>
                <th style="text-align: right;"><?php echo number_format($data['get_income_report_total'][0]->total_income, 0, ',', '.'); ?></th>
            </tr>
          
        </table>
</body>
</html>