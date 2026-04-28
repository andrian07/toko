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
                    List Supplier
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
                                        <h5 class="text-lg font-bold">Tambah Supplier</h5>
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
                                                    <label for="supplier_code" style="font-size: 13px;">Kode Supplier</label>
                                                    <input id="supplier_code" type="text" class="form-input" value="AUTO" readonly/>
                                                </div>
                                                <div>
                                                    <label for="supplier_name" style="font-size: 13px;">Nama Supplier</label>
                                                    <input id="supplier_name" type="text" class="form-input" placeholder="Masukan Nama Supplier" />
                                                </div>
                                                <div>
                                                    <label for="supplier_phone" style="font-size: 13px;">No Telp</label>
                                                    <input id="supplier_phone" type="text" class="form-input" placeholder="Masukan No Telp Supplier" />
                                                </div>
                                                <div>
                                                    <label for="supplier_address" style="font-size: 13px;">Alamat</label>
                                                    <textarea id="supplier_address" class="form-input" placeholder="Masukan Alamat Supplier"></textarea>
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
                                            <button type="button" id="save-supplier" class="btn btn-primary ltr:ml-4 rtl:mr-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL ADD -->

                    <!-- MODAL EDIT -->
                    <div class="fixed inset-0 z-[999] hidden bg-black/60" :class="$store.supplier.openEdit && '!block'">
                        <div class="flex min-h-screen items-center justify-center px-4">
                            <div  class="panel animate__animated animate__slideInDown my-8 w-full max-w-lg overflow-hidden rounded-lg border-0 p-0" >

                                <div class="flex justify-between px-5 py-3 border-b">
                                    <h5 class="text-lg font-bold">Edit Supplier</h5>
                                    <button @click="$store.supplier.openEdit = false">✕</button>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <label>Kode Supplier</label>
                                        <input type="hidden" x-model="$store.supplier.form.supplier_id" class="form-input" id="supplier_id_edit">
                                        <input type="text" x-model="$store.supplier.form.supplier_code" class="form-input" id="supplier_code_edit" readonly/>
                                    </div>
                                    <div>
                                        <label>Nama Supplier</label>
                                        <input type="text" x-model="$store.supplier.form.supplier_name" class="form-input" id="supplier_name_edit" />
                                    </div>
                                    <div>
                                        <label>No Telp</label>
                                        <input type="text" x-model="$store.supplier.form.supplier_phone" class="form-input" id="supplier_phone_edit" />
                                    </div>
                                    <div>
                                        <label>Alamat</label>
                                        <textarea x-model="$store.supplier.form.supplier_address" class="form-input" id="supplier_address_edit"></textarea>
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
                                    <button class="btn btn-outline-danger" @click="$store.supplier.openEdit = false">Cancel</button>
                                    <button class="btn btn-primary" id="edit-supplier">Edit</button>
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

    window.editSupplier = function(id, code, name, phone, address) {
        console.log(code);
        Alpine.store('supplier').form.supplier_id = id;
        Alpine.store('supplier').form.supplier_code = code;
        Alpine.store('supplier').form.supplier_name = name;
        Alpine.store('supplier').form.supplier_phone = phone;
        Alpine.store('supplier').form.supplier_address = address;

        Alpine.store('supplier').openEdit = true;
        Alpine.nextTick(() => {
            Alpine.store('supplier').form.supplier_id = id;
            Alpine.store('supplier').form.supplier_code = code;
            Alpine.store('supplier').form.supplier_name = name;
            Alpine.store('supplier').form.supplier_phone = phone;
            Alpine.store('supplier').form.supplier_address = address;
        });
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            open: false,

            toggle() {
                this.open = !this.open;

                if (this.open) {
                    $('.error-message').hide();
                    $('.message-content').text('');
                    $('#supplier_code, #supplier_name, #supplier_address', '#supplier_phone').removeClass('input-error');
                }
            }
        }));
    });

    document.addEventListener('alpine:init', () => {
        Alpine.store('supplier', {
            openEdit: false,
            form: {
                supplier_id: '',
                supplier_code: '',
                supplier_name: '',
                supplier_phone: '',
                supplier_address: ''
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
                    let response = await fetch("<?= base_url('Masterdata/get_supplier') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    let res = await response.json();

                    let tableData = res.data.map((item, index) => [
                        index + 1,
                        item.supplier_code,
                        item.supplier_name,
                        item.supplier_phone,
                        item.supplier_address,
                        item.supplier_debt,
                        '',
                        item.supplier_id
                        ]);
                    this.datatable = new simpleDatatables.DataTable('#myTable', {
                        data: {
                            headings: ['No', 'Kode Supplier', 'Nama Supplier', 'Telp', 'Alamat', 'Hutang', 'Aksi', 'ID'],
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
                                <a href="javascript:;" x-tooltip="Edit" class="mr-2" onclick="window.editSupplier('${row.cells[7].data}', '${row.cells[1].data}', '${row.cells[2].data}', '${row.cells[3].data}', '${row.cells[4].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"  class="h-5 w-5 text-primary hover:scale-110 transition">
                                <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5"></path>
                                <path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5"></path>
                                </svg>
                                </a>
                                <!-- DELETE (kanan) -->
                                <a href="javascript:;" x-tooltip="Delete" onclick="window.deleteSupplier('${row.cells[7].data}')">
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
                            select: 7,
                            hidden: true
                        },
                        {
                            select: 5,
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


$('#save-supplier').on('click', function (e) {
    e.preventDefault();

    var supplier_code    = $("#supplier_code").val();
    var supplier_name    = $("#supplier_name").val();
    var supplier_phone   = $("#supplier_phone").val();
    var supplier_address = $("#supplier_address").val();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/save_supplier') ?>",
        dataType: "json",
        data: {
            supplier_code: supplier_code,
            supplier_name: supplier_name,
            supplier_phone: supplier_phone,
            supplier_address: supplier_address
        },
        success: function (data) {
            if (data.code == "200") {
                showNotif('success', data.message);
                // clear form inputs
                try{ clearSupplierForm(); }catch(e){}
                window.modalInstance.open = false;
                window.tableInstance.reloadTable();
            }else {
                $('.error-message').show();
                $('.message-content').text(data.message);
            }
        }
    });
});

function clearSupplierForm(){
    try{
        $('#supplier_name').val('');
        $('#supplier_phone').val('');
        $('#supplier_address').val('');
        // reset supplier_code to AUTO or empty as desired
        $('#supplier_code').val('AUTO');
        $('.error-message').hide();
        $('.message-content').text('');
        // remove any input-error classes
        $('#supplier_name, #supplier_phone, #supplier_address').removeClass('input-error');
    }catch(e){ }
}

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

$('#edit-supplier').on('click', function (e) {
    e.preventDefault();

    var supplier_id      = $("#supplier_id_edit").val();
    var supplier_name    = $("#supplier_name_edit").val();
    var supplier_phone   = $("#supplier_phone_edit").val();
    var supplier_address = $("#supplier_address_edit").val();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/edit_supplier') ?>",
        dataType: "json",
        data: {
            supplier_id: supplier_id,
            supplier_name: supplier_name,
            supplier_phone: supplier_phone,
            supplier_address: supplier_address
        },
        success: function (data) {
           if (data.code == "200") {
            showNotif('success', data.message);
            Alpine.store('supplier').openEdit = false;
            window.tableInstance.reloadTable();
        } else {
            $('.error-message').show();
            $('.message-content').text(data.message);
        }
    }
});
});


window.deleteSupplier = function(id, useSwal = true) {

    const doDelete = () => {
        $.ajax({
            type: "POST",
            url: "<?= base_url('Masterdata/delete_supplier') ?>",
            dataType: "json",
            data: { supplier_id: id },
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