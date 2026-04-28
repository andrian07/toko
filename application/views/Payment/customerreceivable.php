<?php 
define('DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require DOC_ROOT_PATH . $this->config->item('header');
?>
<div class="animate__animated p-6" :class="[$store.app.animation]">
  <div x-data="multicolumn">

    <div class="panel mt-6">
        <div class="px-5">
            <!-- HEADER TABLE -->
            <div class="flex items-center justify-between mb-3">

                <!-- TITLE -->
                <h5 class="text-lg font-semibold dark:text-white-light">
                    List Piutang Pelanggan
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
                                        <h5 class="text-lg font-bold">Tambah Piutang customer</h5>
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
                                                    <label for="receivable_invoice_add" style="font-size: 13px;">No Invoice Penjualan</label>
                                                    <input id="receivable_invoice_add" type="text" class="form-input" placeholder="Masukan No Invoice Penjualan"/>
                                                </div>
                                                <div>
                                                    <label for="receivable_customer_name_add" style="font-size: 13px;">Nama Pelanggan</label>
                                                    <select id="receivable_customer_id_add" class="form-input">
                                                        <option value="">Pilih Pelanggan</option>
                                                        <?php foreach($customer_list as $customer): ?>
                                                            <option value="<?= $customer->customer_id ?>"><?= $customer->customer_name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="receivable_nominal_add" style="font-size: 13px;">Nominal</label>
                                                    <input id="receivable_nominal_add" type="text" class="form-input" placeholder="Masukan Nominal Piutang" value="0"/>
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
                                            <button type="button" id="save-receivable" class="btn btn-primary ltr:ml-4 rtl:mr-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL ADD -->
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
    
      let receivable_nominal_add_val = new AutoNumeric('#receivable_nominal_add', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
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
                    let response = await fetch("<?= base_url('Payment/get_receivable') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    let res = await response.json();

                    let tableData = res.data.map((item, index) => [
                        index + 1,
                        item.customer_code,
                        item.customer_name,
                        item.customer_phone,
                        item.customer_receivable_total,
                        item.customer_receivable_count,
                        '',
                        item.customer_id
                        ]);
                    this.datatable = new simpleDatatables.DataTable('#myTable', {
                        data: {
                            headings: ['No', 'Kode Pelanggan', 'Nama Pelanggan', 'Telp', 'Total Piutang', 'Jumlah Nota', 'Aksi', 'ID'],
                            data: tableData
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [
                        {
                            select: 6,
                            sortable: false,
                            cellClass: 'text-right',
                            headerClass: 'text-center',
                            width: '20%',
                            render: (data, cell, row) => {
                                return `
                                <div class="flex items-center w-full px-2">
                                <a href="<?= base_url('Payment/detailcustomerreceivable?id=') ?>${row.cells[7].data}" x-tooltip="List" class="mr-2" onclick="window.editCustomer('${row.cells[7].data}', '${row.cells[1].data}', '${row.cells[2].data}', '${row.cells[3].data}', '${row.cells[4].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"  class="h-5 w-5 text-primary hover:scale-110 transition">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.46447 3.46447C2 4.92893 2 7.28595 2 12C2 16.714 2 19.0711 3.46447 20.5355C4.92893 22 7.28595 22 12 22C16.714 22 19.0711 22 20.5355 20.5355C22 19.0711 22 16.714 22 12C22 7.28595 22 4.92893 20.5355 3.46447C19.0711 2 16.714 2 12 2C7.28595 2 4.92893 2 3.46447 3.46447ZM10.5431 7.51724C10.8288 7.2173 10.8172 6.74256 10.5172 6.4569C10.2173 6.17123 9.74256 6.18281 9.4569 6.48276L7.14286 8.9125L6.5431 8.28276C6.25744 7.98281 5.78271 7.97123 5.48276 8.2569C5.18281 8.54256 5.17123 9.01729 5.4569 9.31724L6.59976 10.5172C6.74131 10.6659 6.9376 10.75 7.14286 10.75C7.34812 10.75 7.5444 10.6659 7.68596 10.5172L10.5431 7.51724ZM13 8.25C12.5858 8.25 12.25 8.58579 12.25 9C12.25 9.41422 12.5858 9.75 13 9.75H18C18.4142 9.75 18.75 9.41422 18.75 9C18.75 8.58579 18.4142 8.25 18 8.25H13ZM10.5431 14.5172C10.8288 14.2173 10.8172 13.7426 10.5172 13.4569C10.2173 13.1712 9.74256 13.1828 9.4569 13.4828L7.14286 15.9125L6.5431 15.2828C6.25744 14.9828 5.78271 14.9712 5.48276 15.2569C5.18281 15.5426 5.17123 16.0173 5.4569 16.3172L6.59976 17.5172C6.74131 17.6659 6.9376 17.75 7.14286 17.75C7.34812 17.75 7.5444 17.6659 7.68596 17.5172L10.5431 14.5172ZM13 15.25C12.5858 15.25 12.25 15.5858 12.25 16C12.25 16.4142 12.5858 16.75 13 16.75H18C18.4142 16.75 18.75 16.4142 18.75 16C18.75 15.5858 18.4142 15.25 18 15.25H13Z" fill="currentColor"></path>
                                </svg>
                                </a>
                                </div>`;
                            },
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

try{
    (function(){
        function initSelect2(){
            try{
                if(typeof $ === 'undefined') return;
                $('#receivable_customer_id_add').select2({
                    placeholder: 'Pilih Customer',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(document.body)
                });
            }catch(e){console.warn('select2 init error', e)}
        }

        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
            $(initSelect2);
            return;
        }

        // dynamically load Select2 CSS and JS from CDN then init
        var cssHref = '<?php echo base_url("assets/css/select2.min.css"); ?>';
        if(!document.querySelector('link[href="'+cssHref+'"]')){
            var l = document.createElement('link'); l.rel='stylesheet'; l.href=cssHref; document.head.appendChild(l);
        }

        var scriptSrc = '<?php echo base_url("assets/js/select2.js"); ?>';
        if(!document.querySelector('script[src="'+scriptSrc+'"]')){
            var s = document.createElement('script'); s.src = scriptSrc; s.onload = function(){ $(initSelect2); }; document.body.appendChild(s);
        } else {
            var sExisting = document.querySelector('script[src="'+scriptSrc+'"]');
            sExisting.addEventListener('load', function(){ $(initSelect2); });
            setTimeout(function(){ if(window.jQuery && $.fn && $.fn.select2) $(initSelect2); }, 500);
        }
    })();
}catch(e){console.warn('Select2 init skipped', e)}

$('#save-receivable').on('click', function (e) {
        e.preventDefault();

        let receivable_customer_id_add    = $("#receivable_customer_id_add").val();
        let receivable_invoice_add      = $("#receivable_invoice_add").val();
        let receivable_nominal_add      = receivable_nominal_add_val.get();

        $.ajax({
            type: "POST",
            url: "<?= base_url('Payment/save_receivable') ?>",
            dataType: "json",
            data: {
                receivable_customer_id_add: receivable_customer_id_add,
                receivable_invoice_add: receivable_invoice_add,
                receivable_nominal_add: receivable_nominal_add
            },
            success: function (data) {
                if (data.code == "200") {
                    showNotif('success', data.message);
                    // close Alpine payment modal
                    try{ Alpine.store('paymentreceivable').openEdit = false; }catch(e){}
                    // also close the Add modal (window.modalInstance) if present
                    try{ if(window.modalInstance) window.modalInstance.open = false; }catch(e){}
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
</script>