<?php 
define('DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require DOC_ROOT_PATH . $this->config->item('header');
?>


<div class="animate__animated p-6" :class="[$store.app.animation]">
	<div x-data="finance">
		<ul class="flex space-x-2 rtl:space-x-reverse">
			
			<li>
				<span>DASHBOARD TOKO</span>
			</li>
		</ul>
		<div class="pt-5">
			<div class="mb-6 grid grid-cols-1 gap-6 text-white sm:grid-cols-2 xl:grid-cols-4">
				<!-- Users Visit -->
				<div class="panel bg-gradient-to-r from-cyan-500 to-cyan-400">
					<div class="flex justify-between">
						<div class="text-md font-semibold">Penjualan Hari Ini</div>
					</div>
					<div class="mt-5 flex items-center">
						<div class="text-2xl font-bold ltr:mr-3 rtl:ml-3">Rp. <?= number_format($data['today_sales']->total_today_sales ?? 0, 0, ',', '.') ?></div>
					</div>
				</div>

				<!-- Sessions -->
				<div class="panel bg-gradient-to-r from-violet-500 to-violet-400">
					<div class="flex justify-between">
						<div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Penjualan Bulan Ini</div>
						
					</div>
					<div class="mt-5 flex items-center">
						<div class="text-2xl font-bold">Rp. <?= number_format($data['monthly_sales']->total_monthly_sales ?? 0, 0, ',', '.') ?></div>
					</div>
				</div>

				<!-- Time On-Site -->
				<div class="panel bg-gradient-to-r from-blue-500 to-blue-400">
					<div class="flex justify-between">
						<div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Piutang Customer Jatuh Tempo</div>
						<div x-data="dropdown" @click.outside="open = false" class="dropdown">
						</div>
					</div>
					<div class="mt-5 flex items-center">
						<div class="text-1xl font-bold ltr:mr-3 rtl:ml-3">Rp. <?= number_format($data['piutang_supplier']->total_debt ?? 0, 0, ',', '.') ?></div>
						<div class="badge bg-white/30"><?= $data['piutang_supplier']->qty_nota_debt ?> Nota</div>
					</div>
					<div class="mt-5 flex items-center font-semibold">
						<svg
						width="24"
						height="24"
						viewBox="0 0 24 24"
						fill="none"
						
						class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2"
						>
						<path
						opacity="0.5"
						d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
						stroke="currentColor"
						stroke-width="1.5"
						></path>
						<path
						d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
						stroke="currentColor"
						stroke-width="1.5"
						></path>
					</svg>
					Tanggal 01 - <?= date('d') ?>
				</div>
			</div>

			<!-- Bounce Rate -->
			<div class="panel bg-gradient-to-r from-fuchsia-500 to-fuchsia-400">
				<div class="flex justify-between">
					<div class="text-md font-semibold ltr:mr-1 rtl:ml-1">Hutang Customer Jatuh Tempo</div>
				</div>
				<div class="mt-5 flex items-center">
					<div class="text-1xl font-bold ltr:mr-3 rtl:ml-3">Rp. <?= number_format($data['hutang_customer']->total_receivable ?? 0, 0, ',', '.') ?></div>
					<div class="badge bg-white/30"><?= $data['hutang_customer']->qty_nota_receivable ?> Nota</div>
				</div>
				<div class="mt-5 flex items-center font-semibold">
					<svg
					width="24"
					height="24"
					viewBox="0 0 24 24"
					fill="none"
					
					class="h-5 w-5 shrink-0 ltr:mr-2 rtl:ml-2"
					>
					<path
					opacity="0.5"
					d="M3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C4.97196 6.49956 7.81811 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957Z"
					stroke="currentColor"
					stroke-width="1.5"
					></path>
					<path
					d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
					stroke="currentColor"
					stroke-width="1.5"
					></path>
				</svg>
				Tanggal 01 - <?= date('d') ?>
			</div>
		</div>
	</div>


	<div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
		<div class="grid gap-6 xl:grid-flow-row">
			<!-- Previous Statement -->
			<div class="panel overflow-hidden">
				<div class="flex items-center justify-between">
					<div>
						<div class="text-lg font-bold">Penjualan Terakhir</div>
						<div class="text-success">Trx #<?= $data['last_transaction_dashboard'][0]->transaction_inv ?> - <?= date('F d, Y', strtotime($data['last_transaction_dashboard'][0]->transaction_date)) ?></div>
					</div>
				</div>
				<div class="relative mt-10">
					<div class="grid grid-cols-2 gap-6 md:grid-cols-3">
						<div>
							<div class="text-primary">Total</div>
							<div class="mt-2 text-2xl font-semibold">Rp. <?= number_format($data['last_transaction_dashboard'][0]->transaction_total, 0, ',', '.') ?></div>
						</div>
						<div>
							<div class="text-primary">Item</div>
							<div class="mt-2 text-2xl font-semibold"><?= $data['last_transaction_dashboard'][0]->total_item ?> Item</div>
						</div>
						<div>
							<div class="text-primary">Pelanggan</div>
							<div class="mt-2 text-2xl font-semibold text-success"><?= $data['last_transaction_dashboard'][0]->customer_name ?></div>
						</div>
					</div>
				</div>
			</div>

			<!-- Current Statement -->
			<div class="panel h-full">
				<div class="mb-5 flex items-center justify-between dark:text-white-light">
					<h5 class="text-lg font-semibold">List Penjualan Terakhir</h5>
				</div>
				<div>
					<div class="space-y-6">
						<?php foreach($data['last_transaction_dashboard_5'] as $trx): ?>
							<div class="flex">
								<span class="grid h-9 w-9 shrink-0 place-content-center rounded-md bg-warning-light text-warning dark:bg-warning dark:text-warning-light">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-6 w-6">
										<path
										d="M2 10C2 7.17157 2 5.75736 2.87868 4.87868C3.75736 4 5.17157 4 8 4H13C15.8284 4 17.2426 4 18.1213 4.87868C19 5.75736 19 7.17157 19 10C19 12.8284 19 14.2426 18.1213 15.1213C17.2426 16 15.8284 16 13 16H8C5.17157 16 3.75736 16 2.87868 15.1213C2 14.2426 2 12.8284 2 10Z"
										stroke="currentColor"
										stroke-width="1.5"
										/>
										<path
										opacity="0.5"
										d="M19.0003 7.07617C19.9754 7.17208 20.6317 7.38885 21.1216 7.87873C22.0003 8.75741 22.0003 10.1716 22.0003 13.0001C22.0003 15.8285 22.0003 17.2427 21.1216 18.1214C20.2429 19.0001 18.8287 19.0001 16.0003 19.0001H11.0003C8.17187 19.0001 6.75766 19.0001 5.87898 18.1214C5.38909 17.6315 5.17233 16.9751 5.07642 16"
										stroke="currentColor"
										stroke-width="1.5"
										/>
										<path
										d="M13 10C13 11.3807 11.8807 12.5 10.5 12.5C9.11929 12.5 8 11.3807 8 10C8 8.61929 9.11929 7.5 10.5 7.5C11.8807 7.5 13 8.61929 13 10Z"
										stroke="currentColor"
										stroke-width="1.5"
										/>
										<path opacity="0.5" d="M16 12L16 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
										<path opacity="0.5" d="M5 12L5 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
									</svg>
								</span>
								<div class="flex-1 px-3">
									<div><?php echo $trx->transaction_inv; ?></div>
									<div class="text-xs text-white-dark dark:text-gray-500"><?php echo $trx->transaction_created_at; ?></div>
								</div>
								<span class="whitespace-pre px-1 text-base text-success ltr:ml-auto rtl:mr-auto">Rp. <?= number_format($trx->transaction_total, 0, ',', '.') ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Recent Transactions -->
		<div class="panel" style="height: 400px">
			<div class="mb-5 text-lg font-bold">List Hutang Supplier</div>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th class="ltr:rounded-l-md rtl:rounded-r-md">No</th>
							<th>SUPPLIER</th>
							<th>TOTAL</th>
							<th>SISA</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; foreach($data['last_supplier_debt_list'] as $debt): ?>
						<tr>
							<td class="font-semibold"># <?= $no++ ?></td>
							<td class="whitespace-nowrap"><?= $debt->supplier_name ?></td>
							<td class="whitespace-nowrap"><span class="badge rounded-full bg-success/20 text-success hover:top-0">RP. <?= number_format($debt->supplier_debt_nominal, 0, ',', '.') ?></span></td>
							<td class="whitespace-nowrap"><span class="badge rounded-full bg-success/20 text-danger hover:top-0">RP. <?= number_format($debt->supplier_debt_remaining, 0, ',', '.') ?></span></td>
						</tr>
						<?php endforeach; ?>
						<tr>
							<td colspan="4"><a href="<?php echo base_url('Payment/supplier_debt') ?>"><button type="button" class="btn btn-primary w-full">Lihat Semua</button></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</div>

<?php require DOC_ROOT_PATH . $this->config->item('footer'); ?>
