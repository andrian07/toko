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
					<?php foreach ($data['get_supplier_data'] as $row) { ?>
						<div>
							<label for="supplier_name">Nama Supplier:</label>
							<input id="supplier_name" type="text" id="supplier_name" class="form-input" readonly value="<?= $row->supplier_name ?>">
						</div>
						
						<div>
							<label for="debt_nominal">Total Hutang:</label>
							<input id="debt_nominal" type="text" class="form-input" readonly value="<?= 'Rp '. number_format($row->supplier_debt_total) ?>">
						</div>

                        <div>
							<label for="total_nota">Total Nota:</label>
							<input id="total_nota" type="text" class="form-input" readonly value="<?= number_format($row->supplier_debt_count) ?>">
						</div>
						
					</div>      
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="panel mt-6">
		<div class="px-5">
			<!-- HEADER TABLE -->
			<div class="flex items-center justify-between mb-3">

				<!-- TITLE -->
				<h5 class="text-lg font-semibold dark:text-white-light">
					List Hutang
				</h5>
                
				<!-- BUTTON -->
				<div class="flex items-center gap-2">
					<button type="button" class="btn btn-secondary gap-2" @click="location.reload()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-5 w-5 shrink-0 ltr:mr-1.5 rtl:ml-1.5">

                            <path d="M21 12A9 9 0 1 1 18 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M21 3V7H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>

                        </svg>
                        Refresh
                    </button>
                    <!-- MODAL ADD -->
                    <div x-data="modal" x-init="window.modalInstance = $data">
                        <a href="#" class="btn btn-primary gap-2" @change="$store.app.toggleAnimation()" @click="toggle">
                            <svg width="24px"  height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" >
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add New
                        </a>

                        <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60" :class="open && '!block'">
                            <div class="flex min-h-screen items-start justify-center px-4">
                                <div class="panel animate__animated animate__slideInDown my-8 w-full max-w-lg overflow-hidden rounded-lg border-0 p-0" >
                                    <div class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3 dark:bg-[#121c2c]">
                                        <h5 class="text-lg font-bold">Tambah Hutang Supplier</h5>
                                        <button type="button" class="text-white-dark hover:text-dark" @click="toggle">
                                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-5">
                                        <div class="text-base font-medium text-[#1f2937] dark:text-white-dark/70">
                                            <form class="space-y-5">
                                                <div>
                                                    <label for="debt_invoice_add" style="font-size: 13px;">No Invoice Pembelian</label>
                                                    <input id="debt_supplier_id_add" type="hidden" class="form-input"/>
                                                    <input id="debt_invoice_add" type="text" class="form-input" placeholder="Masukan No Invoice Pembelian"/>
                                                </div>
                                                <div>
                                                    <label for="debt_supplier_name_add" style="font-size: 13px;">Nama Supplier</label>
                                                    <input id="debt_supplier_name_add" type="text" class="form-input" placeholder="Masukan Nama Supplier" readonly/>
                                                </div>
                                                <div>
                                                    <label for="debt_nominal_add" style="font-size: 13px;">Nominal</label>
                                                    <input id="debt_nominal_add" type="text" class="form-input" placeholder="Masukan Nominal Hutang" value="0"/>
                                                </div>
                                            </form>
                                            <div class="flex items-center rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light  error-message">
                                                <span class="notif-box">
                                                    <strong class="title">Warning!</strong>
                                                    <span class="message-content"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-8 flex items-center justify-end">
                                            <button type="button" class="btn btn-outline-danger" @click="toggle">Cancel</button>
                                            <button type="button" id="save-debt" class="btn btn-primary ltr:ml-4 rtl:mr-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL ADD -->
                         <!-- MODAL EDIT -->
                    <div class="fixed inset-0 z-[999] hidden bg-black/60" :class="$store.paymentdebt.openEdit && '!block'">
                        <div class="flex min-h-screen items-center justify-center px-4">
                            <div  class="panel animate__animated animate__slideInDown my-8 w-full max-w-lg overflow-hidden rounded-lg border-0 p-0" >

                                <div class="flex justify-between px-5 py-3 border-b">
                                    <h5 class="text-lg font-bold">Pembayaran Hutang</h5>
                                    <button @click="$store.paymentdebt.openEdit = false">✕</button>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <label>Supplier</label>
                                        <input type="text" x-model="$store.paymentdebt.form.supplier_name" class="form-input" id="supplier_name_payment" readonly/>
                                    </div>
                                    <div>
                                        <label>Nota</label>
                                        <input type="hidden" x-model="$store.paymentdebt.form.debt_id" class="form-input" id="debt_id_payment" readonly/>
                                        <input type="text" x-model="$store.paymentdebt.form.nota" class="form-input" id="nota_payment" readonly/>
                                    </div>
                                    <div>
                                        <label>Hutang</label>
                                        <input type="text" x-model="$store.paymentdebt.form.debt_amount" class="form-input" id="debt_amount_payment" value="0" readonly/>
                                    </div>
                                    <div>
                                        <label>Nominal Bayar</label>
                                        <input type="text" x-model="$store.paymentdebt.form.payment_amount" class="form-input" id="payment_amount_payment" value="0"/>
                                    </div>
                                    <div>
                                        <label>Sisa Hutang</label>
                                        <input type="text" x-model="$store.paymentdebt.form.remaining_debt" class="form-input" id="remaining_debt_new" value="0" readonly/>
                                    </div>
                                    <div class="flex items-center rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light  error-message">
                                        <span class="notif-box">
                                            <strong class="title">Warning!</strong>
                                            <span class="message-content"></span>
                                        </span>
                                    </div>
                                </div>
                                <!-- FOOTER -->
                                <div class="flex justify-end gap-2 p-5 border-t">
                                    <button class="btn btn-outline-danger" @click="$store.paymentdebt.openEdit = false">Cancel</button>
                                    <button class="btn btn-primary" id="save-paymentdebt">Bayar</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- END MODAL EDIT -->
				</div>
                
			</div>

			<hr class="mb-5 border-gray-200 dark:border-gray-700">

			<table id="myTable" class="table-bordered"></table>

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

    let debt_amount_payment_val = new AutoNumeric('#debt_amount_payment', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        decimalPlacesShownOnFocus: 0,
        digitGroupSeparator : '.',
    });

    let payment_amount_payment_val = new AutoNumeric('#payment_amount_payment', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });

    let remaining_debt_new_val = new AutoNumeric('#remaining_debt_new', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });

    let debt_nominal_add_val = new AutoNumeric('#debt_nominal_add', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });


    window.Payment = function(id, nominal, nota) {
        let supplierName = document.getElementById('supplier_name').value;
        Alpine.store('paymentdebt').form.supplier_name = '';
        Alpine.store('paymentdebt').form.debt_id = '';
        debt_amount_payment_val.set(0);
        payment_amount_payment_val.set(0);
        remaining_debt_new_val.set(0);
        Alpine.store('paymentdebt').form.nota = '';

        Alpine.store('paymentdebt').openEdit = true;
        Alpine.nextTick(() => {
            Alpine.store('paymentdebt').form.supplier_name = supplierName;
            Alpine.store('paymentdebt').form.debt_id = id;
            debt_amount_payment_val.set(nominal);
            remaining_debt_new_val.set(nominal);
            Alpine.store('paymentdebt').form.nota = nota;
        });
    }

    // update remaining value when payment amount changes
    try{
        document.getElementById('payment_amount_payment').addEventListener('input', function(){
            const paid = payment_amount_payment_val.getNumber() || 0;
            const debt = debt_amount_payment_val.getNumber() || 0;
            const remaining = Math.max(0, debt - paid);
            remaining_debt_new_val.set(remaining);
        });
    }catch(e){}

	document.addEventListener('alpine:init', () => {
		// initialize paymentdebt store to be used by window.Payment and modals
		Alpine.store('paymentdebt', {
			openEdit: false,
			form: {
				debt_id: '',
				debt_amount: '',
				supplier_name: '',
				nota: '',
				payment_amount: '',
				remaining_debt: ''
			}
		});
	});

	document.addEventListener('alpine:init', () => {
		Alpine.data('multicolumn', () => ({
			datatable: null,

			async init() {
				window.tableInstance = this;
				await this.loadData();
			},

			async loadData() {
				document.querySelector('#myTable').innerHTML = '';

				try {
					// get supplier id from querystring (set by link)
					let supplierId = '<?= isset($_GET['id']) ? $_GET['id'] : '' ?>';

					let response = await fetch("<?= base_url('Payment/get_debt_detail') ?>", {
						method: "POST",
						headers: {
							"Content-Type": "application/json"
						},
						body: JSON.stringify({ supplier_id: supplierId })
					});

					let res = await response.json();

					let tableData = res.data.map((item, index) => [
						index + 1,
                        item.supplier_debt_invoice,
                        'Rp ' + item.supplier_debt_nominal,
                        'Rp ' + item.supplier_debt_remaining,
						'',
						item.supplier_id,
                        item.supplier_debt_id,
                        item.supplier_debt_remaining_no_format
						]);
					this.datatable = new simpleDatatables.DataTable('#myTable', {
						data: {
							headings: ['No', 'No Invoice', 'Total Hutang', 'Sisa Hutang', 'Aksi', 'ID', 'Debt Id', 'Last Hutang'],
							data: tableData
						},
						searchable: true,
						perPage: 10,
						perPageSelect: [10, 20, 30, 50, 100],
						columns: [
						{
							select: 4,
							sortable: false,
							cellClass: 'text-right',
							headerClass: 'text-center',
							width: '20%',
							render: (data, cell, row) => {
                                return `
                                <div class="flex items-center w-full px-2">
                                <a href="javascript:;" x-tooltip="Bayar Hutang" class="mr-2" onclick="window.Payment('${row.cells[6].data}', '${row.cells[7].data}', '${row.cells[1].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"  class="h-5 w-5 text-primary hover:scale-110 transition">
                                <path opacity="0.5" d="M22 10.5V12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2H13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                <path d="M17.3009 2.80624L16.652 3.45506L10.6872 9.41993C10.2832 9.82394 10.0812 10.0259 9.90743 10.2487C9.70249 10.5114 9.52679 10.7957 9.38344 11.0965C9.26191 11.3515 9.17157 11.6225 8.99089 12.1646L8.41242 13.9L8.03811 15.0229C7.9492 15.2897 8.01862 15.5837 8.21744 15.7826C8.41626 15.9814 8.71035 16.0508 8.97709 15.9619L10.1 15.5876L11.8354 15.0091C12.3775 14.8284 12.6485 14.7381 12.9035 14.6166C13.2043 14.4732 13.4886 14.2975 13.7513 14.0926C13.9741 13.9188 14.1761 13.7168 14.5801 13.3128L20.5449 7.34795L21.1938 6.69914C22.2687 5.62415 22.2687 3.88124 21.1938 2.80624C20.1188 1.73125 18.3759 1.73125 17.3009 2.80624Z" stroke="currentColor" stroke-width="1.5"></path>
                                <path opacity="0.5" d="M16.6522 3.45508C16.6522 3.45508 16.7333 4.83381 17.9499 6.05034C19.1664 7.26687 20.5451 7.34797 20.5451 7.34797M10.1002 15.5876L8.4126 13.9" stroke="currentColor" stroke-width="1.5"></path>
                                </svg>
                                </a>
                                <!-- DELETE (kanan) -->
                                 <a href="javascript:;" x-tooltip="Delete" onclick="window.deleteDebt('${row.cells[6].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                class="h-7 w-7 text-danger hover:scale-110 transition">
                                <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M14.5 9.5L9.5 14.5M9.5 9.5L14.5 14.5"
                                stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"/>
                                </svg>
                                </a>
                                </div>`;
							},
						},
						{
							select: 5,
							hidden: true
						},
                        {
							select: 6,
							hidden: true
						},
                         {
							select: 7,
							hidden: true
						}
						],
					});

				} catch (error) {
					console.error("Error load data:", error);
				}
			},

			reloadTable() {
				if (this.datatable) {
					this.datatable.destroy();
					this.datatable = null;
				}

				this.loadData();
			},

			deleteRow() {
				alert('Fitur delete belum diimplementasikan');
			}
		}));    
	});


    
    $('#save-debt').on('click', function (e) {
        e.preventDefault();

        let debt_supplier_id_add    = $("#debt_supplier_id_add").val();
        let debt_invoice_add      = $("#debt_invoice_add").val();
        let debt_nominal_add      = debt_nominal_add_val.get();

        $.ajax({
            type: "POST",
            url: "<?= base_url('Payment/save_debt') ?>",
            dataType: "json",
            data: {
                debt_supplier_id_add: debt_supplier_id_add,
                debt_invoice_add: debt_invoice_add,
                debt_nominal_add: debt_nominal_add
            },
            success: function (data) {
                if (data.code == "200") {
                    showNotif('success', data.message);
                    try{ Alpine.store('paymentdebt').openEdit = false; }catch(e){}
                    try{ if(window.modalInstance) window.modalInstance.open = false; }catch(e){}
                    window.tableInstance.reloadTable();
                }else {
                    $('.error-message').show();
                    $('.message-content').text(data.message);
                }
                    // clear add modal inputs
                    try{
                        document.getElementById('debt_invoice_add').value = '';
                        debt_nominal_add_val.set(0);
                        var sel = document.getElementById('debt_supplier_id_add');
                        if(sel){
                            try{ $(sel).val('').trigger('change'); }catch(e){ sel.value = ''; }
                        }
                        $('.error-message').hide();
                    }catch(e){console.warn('clear add modal error', e)}
            }
        });
    });
    
    $('#save-paymentdebt').on('click', function (e) {
        e.preventDefault();

        let debt_id_payment             = $("#debt_id_payment").val();
        let nota_payment                = $("#nota_payment").val();
        let payment_amount_payment      = payment_amount_payment_val.get();
        let remaining_debt_new          = remaining_debt_new_val.get();
        let debt_amount_payment         = debt_amount_payment_val.get();

        $.ajax({
            type: "POST",
            url: "<?= base_url('Payment/save_payment_debt') ?>",
            dataType: "json",
            data: {
                debt_id_payment: debt_id_payment,
                nota_payment: nota_payment,
                payment_amount_payment: payment_amount_payment,
                remaining_debt_new: remaining_debt_new,
                debt_amount_payment: debt_amount_payment
            },
            success: function (data) {
                if (data.code == "200") {
                    showNotif('success', data.message);
                    // close Alpine payment modal
                    try{ Alpine.store('paymentdebt').openEdit = false; }catch(e){}
                    window.tableInstance.reloadTable();
                }else {
                    $('.error-message').show();
                    $('.message-content').text(data.message);
                }
            }
        });
    });

    function showNotif(type = 'success', message = '') {
    const container = document.getElementById('notif-container');

    const config = {
        success: {
            wrapper: 'border border-success bg-success-light text-success',
            title: 'Success!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        },
        error: {
            wrapper: 'border border-danger bg-danger-light text-danger',
            title: 'Error!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        },
        warning: {
            wrapper: 'border border-warning bg-warning-light text-warning',
            title: 'Warning!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        }
    };
}

// implement simple toast insertion using the config above
function showNotif(type = 'success', message = '') {
    const container = document.getElementById('notif-container');

    const config = {
        success: {
            wrapper: 'border border-success bg-success-light text-success',
            title: 'Success!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        },
        error: {
            wrapper: 'border border-danger bg-danger-light text-danger',
            title: 'Error!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        },
        warning: {
            wrapper: 'border border-warning bg-warning-light text-warning',
            title: 'Warning!',
            icon: `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.5" d="M5.3 10.7C8.2 5.5 9.6 3 12 3C14.3 3 15.7 5.5 18.6 10.7L19 11.4C21.4 15.7 22.6 17.8 21.5 19.4C20.4 21 17.7 21 12.3 21H11.6C6.2 21 3.5 21 2.4 19.4C1.3 17.8 2.5 15.7 4.9 11.4L5.3 10.7Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
            </svg>`
        }
    };

    const item = document.createElement('div');

    item.className = `
    relative flex items-center rounded p-3.5
    ltr:border-l-[64px] rtl:border-r-[64px]
    shadow-md
    animate__animated animate__fadeInRight
    ${config[type].wrapper}
    `;

    item.innerHTML = `
    <span class="absolute inset-y-0 m-auto h-6 w-6 text-white ltr:-left-11 rtl:-right-11">
    ${config[type].icon || ''}
    </span>

    <span class="ltr:pr-2 rtl:pl-2 text-sm">
    <strong class="mr-1">${config[type].title}</strong>
    ${message}
    </span>

    <button type="button" class="ml-auto hover:opacity-70" onclick="this.parentElement.remove()">
    ✕
    </button>
    `;

    container.appendChild(item);

    setTimeout(() => {
        item.classList.add('animate__fadeOutRight');
        setTimeout(() => item.remove(), 300);
    }, 3000);
}


window.deleteDebt = function(id, useSwal = true) {

    const doDelete = () => {
        $.ajax({
            type: "POST",
            url: "<?= base_url('Payment/delete_debt') ?>",
            dataType: "json",
            data: { debt_id: id },
            success: function (data) {
                if (data.code == "200") {
                    showNotif('success', data.message);
                    window.tableInstance.reloadTable();
                } else {
                    showNotif('error', data.message);
                }
            }
        });
    };

    if (useSwal && typeof Swal !== "undefined") {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                doDelete();
            }
        });
    } else {
        doDelete();
    }
}

</script>

<script>
// set debt_supplier_id_add from querystring if available
try{
    document.getElementById('debt_supplier_id_add').value = '<?= isset($_GET['id']) ? $_GET['id'] : '' ?>';
    // also set supplier name for add modal from header supplier_name field if present
    try{
        var supplierNameHeader = document.getElementById('supplier_name');
        if(supplierNameHeader && document.getElementById('debt_supplier_name_add')){
            document.getElementById('debt_supplier_name_add').value = supplierNameHeader.value || '';
        }
    }catch(e){}
}catch(e){}
</script>