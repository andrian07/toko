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
                    List Produk
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
                                        <h5 class="text-lg font-bold">Tambah Produk</h5>
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
                                                    <label for="product_code" style="font-size: 13px;">Kode Produk</label>
                                                    <input id="product_code" type="text" class="form-input" placeholder="Masukan Kode Produk" />
                                                    
                                                </div>
                                                <div>
                                                    <label for="product_name" style="font-size: 13px;">Nama Produk</label>
                                                    <input id="product_name" type="text" class="form-input" placeholder="Masukan Nama Produk" />
                                                </div>
                                                <div>
                                                    <label for="product_unit" style="font-size: 13px;">Satuan</label>
                                                    <select class="form-select form-select-lg text-white-dark" id="product_unit">
                                                        <option value="">--Pilih Satuan--</option>
                                                        <?php foreach($data['unit_list'] as $row){ ?>
                                                            <option value="<?php echo $row->unit_id; ?>"><?php echo $row->unit_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="product_category" style="font-size: 13px;">Kategori</label>
                                                    <select class="form-select form-select-lg text-white-dark" id="product_category">
                                                        <option value="">--Pilih Kategori--</option>
                                                        <?php foreach($data['category_list'] as $row){ ?>
                                                            <option value="<?php echo $row->category_id; ?>"><?php echo $row->category_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="product_cogs" style="font-size: 13px;">Harga Modal Produk</label>
                                                    <input id="product_cogs" type="text" class="form-input" placeholder="Masukan Harga Modal Produk" value="0"/>
                                                </div>

                                                <div>
                                                    <label for="product_price" style="font-size: 13px;">Harga Jual Produk</label>
                                                    <input id="product_price" type="text" class="form-input" placeholder="Masukan Harga Produk" value="0"/>
                                                </div>

                                                <div>
                                                    <label for="product_description" style="font-size: 13px;">Keterangan</label>
                                                    <input id="product_description" type="text" class="form-input" placeholder="Masukan Keterangan Produk" />
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
                                            <button type="button" id="save-category" class="btn btn-primary ltr:ml-4 rtl:mr-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL ADD -->

                    <!-- MODAL EDIT -->
                    <div class="fixed inset-0 z-[999] hidden bg-black/60" :class="$store.product.openEdit && '!block'">
                        <div class="flex min-h-screen items-center justify-center px-4">
                            <div  class="panel animate__animated animate__slideInDown my-8 w-full max-w-lg overflow-hidden rounded-lg border-0 p-0" >

                                <div class="flex justify-between px-5 py-3 border-b">
                                    <h5 class="text-lg font-bold">Edit Produk</h5>
                                    <button @click="$store.product.openEdit = false">✕</button>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <label>Kode Kategori</label>
                                        <input type="hidden" x-model="$store.product.form.product_id" class="form-input" id="product_id_edit">
                                        <input type="text" x-model="$store.product.form.product_code" class="form-input" id="product_code_edit">
                                    </div>
                                    <div>
                                        <label>Nama Kategori</label>
                                        <input type="text" x-model="$store.product.form.product_name" class="form-input" id="product_name_edit">
                                    </div>
                                    <div>
                                        <label for="product_unit_edit" style="font-size: 13px;">Satuan</label>
                                        <select class="form-select form-select-lg text-white-dark"  x-model="$store.product.form.product_unit"  id="product_unit_edit">
                                            <option value="">--Pilih Satuan--</option>
                                            <?php foreach($data['unit_list'] as $row){ ?>
                                                <option value="<?php echo $row->unit_id; ?>"><?php echo $row->unit_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="product_category_edit" style="font-size: 13px;">Kategori</label>
                                        <select class="form-select form-select-lg text-white-dark" id="product_category_edit"  x-model="$store.product.form.product_category" >
                                            <option value="">--Pilih Kategori--</option>
                                            <?php foreach($data['category_list'] as $row){ ?>
                                                <option value="<?php echo $row->category_id; ?>"><?php echo $row->category_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="product_cogs_edit" style="font-size: 13px;">Harga Modal Produk</label>
                                        <input id="product_cogs_edit" type="text" class="form-input" placeholder="Masukan Harga Modal Produk" value="0"/>
                                    </div>


                                    <div>
                                        <label for="product_price_edit" style="font-size: 13px;">Harga Jual Produk</label>
                                        <input id="product_price_edit"  type="text" class="form-input" placeholder="Masukan Harga Produk" value="0"/>
                                    </div>

                                    <div>
                                        <label for="product_description_edit" style="font-size: 13px;">Keterangan</label>
                                        <input id="product_description_edit" type="text" class="form-input" placeholder="Masukan Keterangan Produk" />
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
                                    <button class="btn btn-outline-danger" @click="$store.product.openEdit = false">Cancel</button>
                                    <button class="btn btn-primary" id="edit-product">Edit</button>
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


    let product_price_val = new AutoNumeric('#product_price', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });

    let product_cogs_val = new AutoNumeric('#product_cogs', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });

    let product_price_edit = new AutoNumeric('#product_price_edit', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });

    let product_cogs_edit = new AutoNumeric('#product_cogs_edit', {
        currencySymbol : 'Rp. ',
        decimalCharacter : ',',
        decimalPlaces: 0,
        digitGroupSeparator : '.',
    });



    window.editProduct = function(id) {
        
        $.ajax({
            type: "POST",
            url: "<?= base_url('Masterdata/get_product_by_id') ?>",
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                if (data.code == "200") {
                   let res = data.data;
                    Alpine.store('product').form.product_id = res.product_id;
                    Alpine.store('product').form.product_code = res.product_code;
                    Alpine.store('product').form.product_name = res.product_name;
                    Alpine.store('product').form.product_unit = res.unit_id;
                    Alpine.store('product').form.product_category = res.category_id;
                    Alpine.store('product').openEdit = true;
                   product_price_edit.set(res.product_price)
                }
            }
        });

       /*Alpine.store('product').form.product_id = id;
        Alpine.store('product').form.product_code = code;
        Alpine.store('product').form.product_name = name;

        Alpine.store('product').openEdit = true;
        console.log(satuan, category);
        Alpine.nextTick(() => {
            Alpine.store('product').form.product_unit = satuan;
            Alpine.store('product').form.product_category = category;
        });

        let cleanHarga = harga.toString().replace(/[^0-9]/g, '');
        product_price_edit.set(cleanHarga);*/
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            open: false,

            toggle() {
                this.open = !this.open;

                if (this.open) {
                    $('.error-message').hide();
                    $('.message-content').text('');
                    $('#category_code, #category_name').removeClass('input-error');
                }
            }
        }));
    });

    document.addEventListener('alpine:init', () => {
        Alpine.store('product', {
            openEdit: false,
            form: {
                product_id: '',
                product_code: '',
                product_name: '',
                product_unit: '',
                product_category: '',
                product_cogs: '',
                product_price: ''
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
                    let response = await fetch("<?= base_url('Masterdata/get_product') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    let res = await response.json();

                    let tableData = res.data.map((item, index) => [
                        index + 1,
                        item.product_code,
                        item.product_name,
                        item.unit_name,
                        item.category_name,
                        item.product_cogs,
                        item.product_price,
                        item.product_details,
                        '',
                        item.product_id
                        ]);
                    this.datatable = new simpleDatatables.DataTable('#myTable', {
                        data: {
                            headings: ['No', 'Kode Item', 'Nama Item', 'Satuan', 'Kategori', 'Harga Beli', 'Harga Jual', 'Keterangan', 'Aksi', 'ID'],
                            data: tableData
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [
                        {
                            select: 8,
                            sortable: false,
                            cellClass: 'text-right',
                            headerClass: 'text-center',
                            width: '20%',
                            render: (data, cell, row) => {
                                return `
                                <div class="flex items-center w-full px-2">
                                <a href="javascript:;" x-tooltip="Edit" class="mr-2" onclick="window.editProduct('${row.cells[9].data}')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"  class="h-5 w-5 text-primary hover:scale-110 transition">
                                <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5"></path>
                                <path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5"></path>
                                </svg>
                                </a>
                                <!-- DELETE (kanan) -->
                                <a href="javascript:;" x-tooltip="Delete" onclick="window.deleteProduct('${row.cells[9].data}')">
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
                            select: 9,
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


$('#save-category').on('click', function (e) {
    e.preventDefault();

    var product_code     = $("#product_code").val();
    var product_name     = $("#product_name").val();
    var product_unit     = $("#product_unit").val();
    var product_category = $("#product_category").val();
    var product_price    = product_price_val.get();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/save_product') ?>",
        dataType: "json",
        data: {
            product_code: product_code,
            product_name: product_name,
            product_unit: product_unit,
            product_name: product_name,
            product_category: product_category,
            product_price: product_price
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

$('#edit-product').on('click', function (e) {
    e.preventDefault();

    var product_id       = $("#product_id_edit").val();
    var product_code     = $("#product_code_edit").val();
    var product_name     = $("#product_name_edit").val();
    var product_unit     = $("#product_unit_edit").val();
    var product_category = $("#product_category_edit").val();
    var product_price    = product_price_edit.get();

    $.ajax({
        type: "POST",
        url: "<?= base_url('Masterdata/edit_product') ?>",
        dataType: "json",
        data: {
            product_id: product_id,
            product_code: product_code,
            product_name: product_name,
            product_unit: product_unit,
            product_category: product_category,
            product_price: product_price
        },
        success: function (data) {
           if (data.code == "200") {
            showNotif('success', data.message);
            Alpine.store('product').openEdit = false;
            window.tableInstance.reloadTable();
        } else {
            $('.error-message').show();
            $('.message-content').text(data.message);
        }
    }
});
});


window.deleteProduct = function(id, useSwal = true) {

    const doDelete = () => {
        $.ajax({
            type: "POST",
            url: "<?= base_url('Masterdata/delete_product') ?>",
            dataType: "json",
            data: { product_id: id },
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