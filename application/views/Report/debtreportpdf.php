<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Hutang Supplier</title>
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
        <h2 class="headline">Laporan Hutang Supplier</h2>
        <p style="text-align: center; margin-top: 5px; margin-bottom: 5px;">Periode: <?php echo $data['start_date_formatted']; ?> - <?php echo $data['end_date_formatted']; ?></p>
    </div>
    <div class="container">
        <table>
            <tr>
                <th>No</th>
                <th>No Nota</th>
                <th>Supplier</th>
                <th>Total Hutang</th>
                <th>Sudah Bayar</th>
                <th>Sisa Hutang</th>
            </tr>
            <?php $no = 1; foreach($data['get_debt_report'] as $row) { ?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td><?php echo $row->supplier_debt_invoice; ?></td>
                <td><?php echo $row->supplier_name; ?></td>
                <td><?php echo 'Rp. ' . number_format($row->supplier_debt_nominal, 0, ',', '.'); ?></td>
                <td><?php echo 'Rp. ' . number_format($row->supplier_debt_nominal - $row->supplier_debt_remaining, 0, ',', '.'); ?></td>
                <td><?php echo 'Rp. ' . number_format($row->supplier_debt_remaining, 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </table>
</body>
</html>