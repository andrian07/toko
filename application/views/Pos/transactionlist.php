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
                    List Transaksi
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


    document.addEventListener('alpine:init', () => {
        Alpine.store('transaction_list', {
            openEdit: false,
            form: {
                transaction_id: '',
                customer_name: '',
                payment_name: '',
                subtotal: '',
                tax: '',
                total: '',
                date: '',
                transaction_status: ''
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

            // format date string to dd-mm-yyyy
            formatDateToDDMMYYYY(dateStr){
                if (!dateStr) return '';
                var d = new Date(dateStr);
                if (isNaN(d)){
                    // try parse from possible PHP datetime format
                    var parts = String(dateStr).split(/[- :T]/).filter(Boolean);
                    if (parts.length >= 3){
                        // assume YYYY-MM-DD or similar
                        var y = parts[0], m = parts[1], day = parts[2];
                        return (String(day).padStart(2,'0')) + '-' + (String(m).padStart(2,'0')) + '-' + y;
                    }
                    return dateStr;
                }
                var dd = String(d.getDate()).padStart(2,'0');
                var mm = String(d.getMonth()+1).padStart(2,'0');
                var yyyy = d.getFullYear();
                return dd + '-' + mm + '-' + yyyy;
            },

            async loadData() {
                document.querySelector('#myTable').innerHTML = '';

                try {
                    let response = await fetch("<?= base_url('Pos/get_pos_list') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    let res = await response.json();

                        let tableData = res.data.map((item, index) => {
                            // render status as colored badge
                            var status = (item.transaction_status || '').toString().toLowerCase();
                            var statusHtml = '';
                            if (status === 'cancel'){
                                statusHtml = '<span class="badge bg-danger">Cancel</span>';
                            }else if(status === 'success' || status === 'success' ){
                                statusHtml = '<span class="badge bg-success">Success</span>';
                            }
                            return [
                                index + 1,
                                item.transaction_inv,
                                item.customer_name,
                                item.payment_name,
                                item.transaction_total,
                                this.formatDateToDDMMYYYY(item.transaction_date),
                                statusHtml,
                                '',
                                item.transaction_id
                            ];
                        });
                    this.datatable = new simpleDatatables.DataTable('#myTable', {
                        data: {
                            headings: ['No', 'Kode Transaksi', 'Nama Pelanggan', 'Metode Pembayaran', 'Total Transaksi', 'Tanggal Transaksi', 'Status', 'Aksi', 'ID'],
                            data: tableData
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [
                        {
                            select: 7,
                            sortable: false,
                            cellClass: 'text-right',
                            headerClass: 'text-center',
                            width: '20%',
                            render: (data, cell, row) => {
                                return `
                                <!-- DETAIL -->
                                <div class="flex items-center w-full px-2">
                                <a href="javascript:;" x-tooltip="View" class="mr-2" onclick="window.viewTransaction('${row.cells[8].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-primary hover:scale-110 transition">
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" fill="currentColor"></circle>
                                </svg>
                                </a>
                                <!-- DELETE (kanan) -->
                                <a href="javascript:;" x-tooltip="Delete" onclick="window.deleteTransaction('${row.cells[8].data}')">
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
                            select: 8,
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


$('#save-customer').on('click', function (e) {
    e.preventDefault();

    var customer_code    = $("#customer_code").val();
    var customer_name    = $("#customer_name").val();
    var customer_phone   = $("#customer_phone").val();
    var customer_address = $("#customer_address").val();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/save_customer') ?>",
        dataType: "json",
        data: {
            customer_code: customer_code,
            customer_name: customer_name,
            customer_phone: customer_phone,
            customer_address: customer_address
        },
        success: function (data) {
            if (data.code == "200") {
                showNotif('success', data.message);
                window.modalInstance.open = false;
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

$('#edit-customer').on('click', function (e) {
    e.preventDefault();

    var customer_id      = $("#customer_id_edit").val();
    var customer_name    = $("#customer_name_edit").val();
    var customer_phone   = $("#customer_phone_edit").val();
    var customer_address = $("#customer_address_edit").val();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/edit_customer') ?>",
        dataType: "json",
        data: {
            customer_id: customer_id,
            customer_name: customer_name,
            customer_phone: customer_phone,
            customer_address: customer_address
        },
        success: function (data) {
           if (data.code == "200") {
            showNotif('success', data.message);
            Alpine.store('customer').openEdit = false;
            window.tableInstance.reloadTable();
        } else {
            $('.error-message').show();
            $('.message-content').text(data.message);
        }
    }
});
});


window.deleteTransaction = function(id, useSwal = true) {

    const doDelete = () => {
        $.ajax({
            type: "POST",
            url: "<?= base_url('Pos/delete_transaction') ?>",
            dataType: "json",
            data: { transaction_id: id },
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

window.viewTransaction = function(id){
    if (!id) return;
    fetch('<?= base_url('Pos/get_pos_detail') ?>', {
        method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'transaction_id='+encodeURIComponent(id)
    }).then(function(r){ return r.json(); }).then(function(res){
        if (!(res && res.status === 'success')){
            showNotif('error', res.message || 'Detail tidak ditemukan');
            return;
        }
        var t = res.data.transaction;
        var details = res.data.details || [];
        var html = '<div style="max-height:300px;overflow:auto">';
        html += '<p><strong>Invoice:</strong> '+(t.transaction_inv||'')+'</p>';
        html += '<p><strong>Pelanggan:</strong> '+(t.customer_name||'')+'</p>';
        html += '<p><strong>Metode:</strong> '+(t.payment_name||'')+'</p>';
        html += '<p><strong>Total:</strong> '+(t.transaction_total||'')+'</p>';
        html += '<hr/>';
        html += '<table style="width:100%;font-size:13px"><thead><tr><th>Produk</th><th>Qty</th><th>Harga</th></tr></thead><tbody>';
        function formatNumber(v){
            var n = Number(v);
            if (isNaN(n)) return v || '';
            return n.toLocaleString('id-ID');
        }
        details.forEach(function(d){
            var name = d.product_name || d.item_id || '';
            var qty = d.qty || d.product_qty || '';
            var price = formatNumber(d.price || d.product_price || 0);
            html += '<tr><td>'+ name +'</td><td>'+ qty +'</td><td>'+ price +'</td></tr>'; 
        });
        html += '</tbody></table>';
        html += '</div>';
        if (typeof Swal !== 'undefined'){
            Swal.fire({ title: 'Detail Transaksi', html: html, width: 700 });
        } else {
            // fallback: open a small window
            var w = window.open('', '_blank', 'width=600,height=400');
            w.document.write(html);
        }
    }).catch(function(err){ console.error(err); showNotif('error','Error memuat detail'); });
}
</script>