<?php 
define('DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require DOC_ROOT_PATH . $this->config->item('header');
?>
<div class="animate__animated p-6" :class="[$store.app.animation]">
	<div x-data="multicolumn">

		<div class="panel mt-6">

			<div class="px-5">
				<!-- HEADER TABLE -->
				
				<div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
					<div>
						<label for="start_date">Tanggal Dari:</label>
						<input id="start_date" type="date" class="form-input" value="<?php echo date('Y-m-01'); ?>">
					</div>
					<div>
						<label for="end_date">Tanggal Sampai:</label>
						<input id="end_date" type="date" class="form-input" value="<?php echo date('Y-m-d'); ?>">
					</div>
					<div class="flex items-end">
						<button type="button" id="btnsearch" class="btn btn-primary">Cari</button>
					</div>
			</div>
		</div>
	</div>

	<div class="panel mt-6">
		<div class="px-5">
			<!-- HEADER TABLE -->
			<div class="flex items-center justify-between mb-3">

				<!-- TITLE -->
				<h5 class="text-lg font-semibold dark:text-white-light">
					Laporan Hutang Supplier
				</h5>
                
				<!-- BUTTON -->
				<div class="flex items-center gap-2">
					<a id="btndownloadexcell" class="btn btn-success gap-2" target="_blank">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-5 w-5">
							<path opacity="0.5" d="M17 9.00195C19.175 9.01406 20.3529 9.11051 21.1213 9.8789C22 10.7576 22 12.1718 22 15.0002V16.0002C22 18.8286 22 20.2429 21.1213 21.1215C20.2426 22.0002 18.8284 22.0002 16 22.0002H8C5.17157 22.0002 3.75736 22.0002 2.87868 21.1215C2 20.2429 2 18.8286 2 16.0002L2 15.0002C2 12.1718 2 10.7576 2.87868 9.87889C3.64706 9.11051 4.82497 9.01406 7 9.00195" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
							<path d="M12 2L12 15M12 15L9 11.5M12 15L15 11.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
						</svg>
						Download Excell
					</a>
				</div>
                
			</div>

			<hr class="mb-5 border-gray-200 dark:border-gray-700">
            <div id="report">
                <iframe id="preview" src="<?php echo base_url(); ?>Report/reportdebtpdf" width="100%" height="1000px"></iframe>
            </div>
            
		</div>
	</div>

</div>
</div>

<?php require DOC_ROOT_PATH . $this->config->item('footer'); ?>
<div id="notif-container"
style="position: fixed; top: 1rem; right: 1rem; left: auto; z-index: 999999"
class="flex flex-col gap-3">
</div>

<script>
// handle search and update iframe & download link


    $('#btnsearch').click(function(e) {
      e.preventDefault();
      let start_date       = $('#start_date').val();
      let end_date         = $('#end_date').val();

      let url = '<?php echo base_url(); ?>Report/reportdebtpdf?';
      url += 'start_date=' + start_date;
      url += '&end_date=' + end_date;
      $('#preview').attr('src', url);
    })


    $('#btndownloadexcell').click(function(e) {
      e.preventDefault();
      let start_date       = $('#start_date').val();
      let end_date         = $('#end_date').val();
      
      let url = '<?php echo base_url(); ?>Report/reportdebtexcell?';
      url += 'start_date=' + start_date;
      url += '&end_date=' + end_date;
      window.open(url, '_blank');
    })
</script>