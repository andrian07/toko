<?php 
define('DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require DOC_ROOT_PATH . $this->config->item('header');
?>
<style>
/* POS improvements */
.pos-search { display:flex; gap:8px; align-items:center }
.search-input { border:1px solid #e5e7eb; padding:10px 12px; border-radius:8px; width:100%; box-shadow:0 1px 2px rgba(0,0,0,0.03); }
.product-card { transition: transform .12s ease, box-shadow .12s ease; }
.product-card:hover { transform: translateY(-4px); box-shadow:0 8px 20px rgba(16,24,40,0.08); }
.product-image { border-bottom:1px solid #f3f4f6 }
.price-badge { background:linear-gradient(90deg,#06b6d4,#3b82f6); color:white; padding:6px 8px; border-radius:6px; font-weight:700 }
.small-muted { font-size:12px; color:#6b7280 }
</style>
<style>
.cart-small-btn { background:#f3f4f6; border-radius:6px; padding:6px 8px }
.category-bar { display:flex; gap:8px; overflow-x:auto; padding:8px 0 12px }
.category-btn { padding:6px 10px; border-radius:999px; background:#f3f4f6; border:1px solid #eee; cursor:pointer; white-space:nowrap }
.category-btn.active { background:#3b82f6; color:#fff; border-color:transparent }
/* cart row font sizes */
#cart-table tbody td:first-child { font-size:11px; padding-right:8px; }
#cart-table tbody td:nth-child(3) { font-size:11px; }
/* make save button more prominent */
#btn-save-sale { font-size:15px; padding:10px 18px; border-radius:10px; width: 100%;;}
</style>
</style>
<div class="animate__animated p-6" :class="[$store.app.animation]">
    <!-- start main content section -->
    <div>
        <div class="relative flex h-full gap-5 sm:h-[calc(100vh_-_150px)] sm:min-h-0">
            <!-- LEFT: Product list with barcode scan -->
            <div class="panel flex-1 p-0" id="list-item" style="height:620px">
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-4 pos-search">
                        <div style="flex:1;position:relative">
                            <input id="search-barcode" placeholder="Scan barcode atau cari produk... (enter untuk tambah)" class="search-input" autocomplete="off" />
                            <button id="clear-search" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:#fff;border:0;padding:6px;border-radius:6px;cursor:pointer">✕</button>
                        </div>
                    </div>

                    <div class="category-bar" id="category-bar">
                        <button class="category-btn active" data-cat="">Semua</button>
                        <?php
                            $cats = [];
                            if (!empty($products)){
                                foreach ($products as $pc){
                                    $k = trim($pc->category_name ?? 'Lainnya');
                                    if ($k === '') $k = 'Lainnya';
                                    if (!isset($cats[$k])) $cats[$k] = $k;
                                }
                            }
                            foreach ($cats as $cat): ?>
                                <button class="category-btn" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" style="max-height:480px;overflow:auto;">
                        <?php if (!empty($products)): foreach ($products as $p): ?>
                            <div class="product-card border rounded overflow-hidden flex flex-col shadow-sm bg-white h-full p-0 cursor-pointer" 
                                data-id="<?= $p->product_id ?>"
                                data-barcode="<?= htmlspecialchars($p->product_code ?? $p->product_id) ?>"
                                data-name="<?= htmlspecialchars($p->product_name) ?>"
                                data-price="<?= $p->product_price ?>">
                                <div class="p-3 flex-1 flex flex-col justify-between" data-category="<?= htmlspecialchars($p->category_name ?? 'Lainnya') ?>">
                                    <div>
                                        <div class="text-sm font-semibold" style="font-size:12px;"><?= htmlspecialchars($p->product_name) ?></div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="font-bold" style="font-size:12px;">Rp. <?= number_format($p->product_price,0,',','.') ?></div>
                                        <button class="btn-add-to-cart bg-blue-600 text-white px-3 py-1 rounded">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="col-span-4 text-center text-gray-500">Tidak ada produk</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Cart / Payment -->
            <div class="panel w-full max-w-md" id="list-payment" style="min-height: 620px; padding:5px;">
                <div class="p-4 flex flex-col h-full">
                    <h3 class="text-lg font-semibold mb-3">ITEM</h3>
                    <div id="cart-loading" style="display:none;" class="text-sm text-gray-500 mb-2">Memuat keranjang...</div>

                    <div class="flex-1 overflow-auto mb-3">
                        <table class="w-full text-sm" id="cart-table">
                            <thead>
                                <tr class="text-left">
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th class="text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="space-y-3">

                        <div class="flex items-center justify-between">
                            <div>Subtotal</div>
                            <div id="summary-subtotal">Rp 0</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>Tax</div>
                            <div id="summary-tax">Rp 0</div>
                        </div>
                        <div class="flex items-center justify-between text-lg font-semibold">
                            <div>Total</div>
                            <div id="summary-total">Rp 0</div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button id="btn-save-sale" class="btn btn-primary bg-green-600 text-white px-4 py-2 rounded">BAYAR</button>
                        </div>

                        <div id="save-result" class="text-sm"></div>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
    <!-- end main content section -->
</div>
<?php require DOC_ROOT_PATH . $this->config->item('footer'); ?>
<div id="notif-container"
style="position: fixed; top: 1rem; right: 1rem; left: auto; z-index: 999999"
class="flex flex-col gap-3">
</div>


<script>
$(document).ready(function() {
       
});

// SweetAlert2 helper: show alert with fallback to native alert if Swal not available
async function showError(msgerror) {
    if (window.Swal) {
        try{
            new window.Swal({
                icon: 'error',
                title: msgerror
            });
            return;
        }catch(e){ /* fallthrough to native alert */ }
    }
}
// clear/reset all inputs inside payment modal (if exists)
function clearPaymentModal(){
    var m = document.getElementById('payment-modal');
    if (!m) return;
    // customer select
    var cust = m.querySelector('#pm-customer');
    if (cust){
        try{
            if (window.jQuery && jQuery.fn && jQuery.fn.select2){ jQuery(cust).val('').trigger('change'); }
            else cust.value = '';
        }catch(e){ cust.value = ''; }
    }
    // payment type
    var ptype = m.querySelector('#pm-payment-type'); if (ptype) ptype.value = '';
    // paid input (AutoNumeric aware)
    var paidEl = m.querySelector('#pm-paid');
    if (m._pmPaidAN && typeof m._pmPaidAN.set === 'function'){
        try{ m._pmPaidAN.set(0); }catch(e){ if (paidEl) paidEl.value = 0; }
    } else if (paidEl){ paidEl.value = 0; }
    if (paidEl) paidEl.setAttribute('disabled','disabled');
    // change display
    var change = m.querySelector('#pm-change'); if (change) change.textContent = formatRupiah(0);
    // hide error
    var err = m.querySelector('#pm-error'); if (err) err.style.display = 'none';
    var errText = m.querySelector('#pm-error-text'); if (errText) errText.textContent = '';
    // total inputs
    var totalInput = m.querySelector('#pm-total-input'); if (totalInput) totalInput.value = 0;
    var totalEl = m.querySelector('#pm-total'); if (totalEl) totalEl.textContent = formatRupiah(0);
}

// SweetAlert2 success helper with fallback
async function showSuccess(msg) {
    if (window.Swal) {
        try{
            new window.Swal({
                icon: 'success',
                title: msg,
                padding: '2em',
            });
            return;
        }catch(e){}
    }
    var el = document.getElementById('save-result');
    if (el) el.innerHTML = '<span style="color:green">'+('Tersimpan')+'</span>';
}

document.addEventListener('DOMContentLoaded', function(){
    var cart = {};

    function showCartLoading(){ var el = document.getElementById('cart-loading'); if (el) el.style.display = 'block'; }
    function hideCartLoading(){ var el = document.getElementById('cart-loading'); if (el) el.style.display = 'none'; }

    function formatRupiah(v){
        return Number(v || 0).toLocaleString('id-ID');
    }

    function addToCart(item){
        // fetch product details from DB first, then insert to temp_cart
        // now optimized: use the add_temp_item response to update local cart without fetching full cart
        showCartLoading();
        fetch('<?= base_url('Pos/get_product_item') ?>', {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: item.id })
        }).then(function(res){ return res.json(); }).then(function(prod){
            if (!prod || prod.status === 'error') throw new Error('Product not found');
            var price = prod.product_price || item.price || 0;
            return fetch('<?= base_url('Pos/add_temp_item') ?>', {
                method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: prod.product_id, product_price: price, product_qty: 1 })
            }).then(function(r){ return r.json(); }).then(function(res){
                if (!(res && res.status === 'success')) throw new Error('Failed to add temp');
                // update local cart using returned row data
                var d = res.data || {};
                var pid = String(d.product_id || item.id);
                var name = item.name || d.product_name || ('Produk ' + pid);
                var price2 = parseFloat(d.product_price || item.price || 0) || 0;
                var qty = parseInt(d.product_qty || 1, 10) || 1;
                cart[pid] = { id: pid, name: name, price: price2, qty: qty };
                renderCart();
            });
        }).catch(function(err){ console.error(err); renderCart(); }).finally(function(){ hideCartLoading(); });
    }

    function renderCart(){
        var $tbody = document.querySelector('#cart-table tbody');
        $tbody.innerHTML = '';
        var subtotal = 0;
        Object.values(cart).forEach(function(it){
                var row = document.createElement('tr');
                // include editable price field
                row.innerHTML = '<td>'+escapeHtml(it.name)+'</td>'+
                    '<td><input class="cart-qty" data-id="'+it.id+'" type="number" value="'+it.qty+'" min="1" style="width:40px"></td>'+
                    '<td><input class="cart-price text-right" data-id="'+it.id+'" type="number" value="'+(it.price||0)+'" step="0.01" style="width:70px;text-align:right" /></td>'+
                    '<td class="text-right">'+formatRupiah(it.qty * it.price)+'</td>'+
                '<td>' +
                    '<button class="btn-remove" data-id="'+it.id+'" aria-label="Hapus" style="background:none;border:0;padding:4px;display:inline-flex;align-items:center;">' +
                        '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="h-7 w-7 text-danger" style="transition:transform .12s;">' +
                            '<circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>' +
                            '<path d="M14.5 9.5L9.5 14.5M9.5 9.5L14.5 14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>' +
                        '</svg>' +
                    '</button>' +
                '</td>';
            $tbody.appendChild(row);
            subtotal += it.qty * it.price;
        });

        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        // make total equal to subtotal (ignore discounts/tax for now)
        var total = subtotal;
        document.getElementById('summary-total').textContent = formatRupiah(total);
    }

    // update totals only (used while editing qty to avoid re-rendering inputs)
    function updateTotals(){
        var subtotal = 0;
        Object.values(cart).forEach(function(it){ subtotal += (parseFloat(it.price)||0) * (parseInt(it.qty,10)||0); });
        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        // keep total same as subtotal
        var total = subtotal;
        document.getElementById('summary-total').textContent = formatRupiah(total);
    }

    // escape helper
    function escapeHtml(text){ var map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;" }; return String(text).replace(/[&<>"']/g,function(m){return map[m];}); }

    // Product click / add button
    document.getElementById('product-grid').addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.btn-add-to-cart');
        var card = e.target.closest && e.target.closest('.product-card');
        if (btn){
            var cardEl = btn.closest('.product-card');
            var item = { id: cardEl.dataset.id, name: cardEl.dataset.name, price: cardEl.dataset.price };
            addToCart(item);
            return;
        }
        // clicking card adds too
        if (card && !e.target.classList.contains('btn-add-to-cart')){
            var item2 = { id: card.dataset.id, name: card.dataset.name, price: card.dataset.price };
            addToCart(item2);
        }
    });

    // Unified search + barcode input
    var searchInput = document.getElementById('search-barcode');
    var activeCategory = '';
    function filterProducts(){
        var q = (searchInput.value||'').trim().toLowerCase();
        document.querySelectorAll('#product-grid .product-card').forEach(function(card){
            var name = (card.dataset.name||'').toLowerCase();
            var barcode = (card.dataset.barcode||'').toLowerCase();
            var cat = (card.querySelector('[data-category]')&&card.querySelector('[data-category]').dataset.category||'').toLowerCase();
            var matchesSearch = !q || name.indexOf(q) !== -1 || barcode.indexOf(q) !== -1;
            var matchesCategory = !activeCategory || cat === activeCategory.toLowerCase();
            card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }
    searchInput.addEventListener('input', function(){ filterProducts(); });
    document.getElementById('clear-search').addEventListener('click', function(){ searchInput.value=''; filterProducts(); searchInput.focus(); });

    searchInput.addEventListener('keydown', function(e){
        if (e.key === 'Enter'){
            var v = this.value.trim(); if (!v) return;
            // try exact barcode or id match
            var card = document.querySelector('.product-card[data-barcode="'+CSS.escape(v)+'"]') || document.querySelector('.product-card[data-id="'+CSS.escape(v)+'"]');
            if (card){ addToCart({ id: card.dataset.id, name: card.dataset.name, price: card.dataset.price }); this.value=''; filterProducts(); return; }

            // fallback: if search narrows to one visible product, add it
            var visible = Array.from(document.querySelectorAll('#product-grid .product-card')).filter(function(c){ return c.style.display !== 'none'; });
            if (visible.length === 1){ var c = visible[0]; addToCart({ id: c.dataset.id, name: c.dataset.name, price: c.dataset.price }); this.value=''; filterProducts(); return; }

            alert('Produk tidak ditemukan untuk: ' + v);
        }
    });

    // Category click handling
    var categoryBar = document.getElementById('category-bar');
    if (categoryBar){
        categoryBar.addEventListener('click', function(e){
            var btn = e.target.closest && e.target.closest('.category-btn');
            if (!btn) return;
            // toggle active
            Array.from(categoryBar.querySelectorAll('.category-btn')).forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            activeCategory = btn.dataset.cat || '';
            filterProducts();
        });
    }

    // Cart qty/price change & remove
    // Allow backspace/editing: only commit qty when the input has a non-empty numeric value.
    var cartTable = document.getElementById('cart-table');
    cartTable.addEventListener('input', function(e){
        var el = e.target;
        // qty editing
        if (el.classList && el.classList.contains('cart-qty')){
            var id = el.dataset.id;
            var val = el.value.trim();
            // allow editing (empty while typing) — only update cart when there's a numeric value
            if (val === ''){
                // temporarily treat as 0 for totals calculation
                if (cart[id]){ cart[id].qty = 0; updateTotals(); }
                return;
            }
            var v = parseInt(val, 10);
            if (isNaN(v) || v < 0) v = 0;
            if (cart[id]){ cart[id].qty = v; updateTotals(); }

            // debounce save to server to avoid too many requests while typing
            if (!window._qtySaveTimers) window._qtySaveTimers = {};
            if (window._qtySaveTimers[id]) clearTimeout(window._qtySaveTimers[id]);
            window._qtySaveTimers[id] = setTimeout(function(){
                // ensure minimum 1
                var toSave = parseInt(el.value,10);
                if (isNaN(toSave) || toSave < 1) toSave = 1;
                saveQtyToServer(id, toSave).catch(function(){ /* ignore */ });
            }, 700);
            return;
        }

        // price editing
        if (el.classList && el.classList.contains('cart-price')){
            var id = el.dataset.id;
            var val = el.value.trim();
            if (val === ''){
                if (cart[id]){ cart[id].price = 0; updateTotals();
                    var tr0 = el.closest('tr'); if (tr0){ var cells0 = tr0.querySelectorAll('td'); if (cells0 && cells0.length >= 4) cells0[3].textContent = formatRupiah(0); }
                }
                return;
            }
            var v = parseFloat(val);
            if (isNaN(v) || v < 0) v = 0;
            if (cart[id]){
                cart[id].price = v; updateTotals();
                var tr = el.closest('tr');
                if (tr){
                    var cells = tr.querySelectorAll('td');
                    if (cells && cells.length >= 4){
                        cells[3].textContent = formatRupiah((parseFloat(cart[id].price)||0) * (parseInt(cart[id].qty,10)||0));
                    }
                }
            }

            // debounce save to server
            if (!window._priceSaveTimers) window._priceSaveTimers = {};
            if (window._priceSaveTimers[id]) clearTimeout(window._priceSaveTimers[id]);
            window._priceSaveTimers[id] = setTimeout(function(){
                savePriceToServer(id, v).catch(function(){ /* ignore */ });
            }, 700);
        }
    });

    // On blur (when leaving the input), ensure there's at least qty=1 and re-render rows and save immediately
    cartTable.addEventListener('blur', function(e){
        var el = e.target;
        if (el.classList && el.classList.contains('cart-qty')){
            var id = el.dataset.id;
            var val = el.value.trim();
            var v = parseInt(val, 10);
            if (isNaN(v) || v < 1) v = 1;
            if (cart[id]){ cart[id].qty = v; renderCart(); }
            saveQtyToServer(id, v).catch(function(){ /* ignore */ });
        }
    }, true);

    cartTable.addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.btn-remove');
        if (btn){ 
            var id = btn.dataset.id; 
            // call server to delete, then update UI
            fetch('<?= base_url('Pos/delete_temp_item') ?>', {
                method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: id })
            }).then(function(r){ return r.json(); }).then(function(res){
                if (res && res.status === 'success'){
                    delete cart[id]; renderCart();
                } else {
                    alert('Gagal menghapus item');
                }
            }).catch(function(){ alert('Error menghapus item'); });
        }
    });

    // helper: save qty to server
    function saveQtyToServer(product_id, qty){
        return fetch('<?= base_url('Pos/update_temp_item') ?>', {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: product_id, product_qty: qty })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (!(res && res.status === 'success')) throw new Error('update failed');
            return res;
        });
    }

    // helper: save price to server
    function savePriceToServer(product_id, price){
        return fetch('<?= base_url('Pos/update_temp_item') ?>', {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ product_id: product_id, product_price: price })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (!(res && res.status === 'success')) throw new Error('update failed');
            return res;
        });
    }
    

    // show payment modal helper (creates modal if not exists)
    function showPaymentModal(total, onConfirm){
        var existing = document.getElementById('payment-modal');
        if (!existing){
            var modal = document.createElement('div');
            modal.id = 'payment-modal';
            modal.style = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100000;align-items:center;justify-content:center;';
                modal.innerHTML = '<div class="fixed inset-0 z-[999] overflow-y-auto bg-[black]/60"><div class="flex min-h-screen items-start justify-center px-4"><div class="panel animate__animated animate__slideInDown my-8 w-full max-w-lg overflow-hidden rounded-lg border-0 p-0 bg-white"><div class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3"><h5 class="text-lg font-bold">Konfirmasi Pembayaran</h5><button type="button" id="pm-close" class="text-white-dark hover:text-dark"><svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button></div><div class="p-5"><div class="text-base font-medium text-[#1f2937]"><form class="space-y-5"><div><label for="pm-customer" style="font-size:13px;">Nama Pelanggan (opsional)</label><select id="pm-customer" class="form-input"><option value="">--Pilih Pelanggan--</option><?php if (!empty($customers)) : foreach ($customers as $c) : ?><option value="<?= $c->customer_id ?>"><?= htmlspecialchars($c->customer_name) ?></option><?php endforeach; endif; ?></select></div><div><label for="pm-payment-type" style="font-size:13px;">Cara Bayar</label><select id="pm-payment-type" class="form-input"><option value="">--Pilih Pembayaran--</option><?php if (!empty($payment)): foreach ($payment as $pm): ?><option value="<?= htmlspecialchars($pm->payment_id) ?>"><?= htmlspecialchars($pm->payment_name ?? ($pm->payment_code ?? $pm->payment_id)) ?></option><?php endforeach; endif; ?></select></div><div><label for="pm-paid" style="font-size:13px;">Jumlah Dibayar</label><div style="display:flex;gap:8px;align-items:center"><input id="pm-paid" type="text" value="0" class="form-input" style="flex:1" /><button type="button" id="pm-set-total" class="btn btn-outline" style="white-space:nowrap;padding:6px 10px;margin-left:6px">Uang Pas</button></div></div><div><label style="font-size:13px;">Total</label><div id="pm-total" class="font-bold">'+formatRupiah(total)+'</div><input type="hidden" id="pm-total-input" name="total" value="'+ total +'" /></div><div id="pm-change-row" style="display:none"><label style="font-size:13px;">Kembali</label><div id="pm-change" class="font-bold">Rp 0</div></div></form><div class="flex items-center rounded bg-danger-light p-3.5 text-danger mt-3" style="display:none" id="pm-error"><span class="notif-box"><strong class="title">Warning!</strong><span class="message-content" id="pm-error-text"></span></span></span></div></div><div class="mt-8 flex items-center justify-end"><button type="button" id="pm-cancel" class="btn btn-outline-danger">Cancel</button><button type="button" id="pm-ok" class="btn btn-primary ltr:ml-4 rtl:mr-4">SIMPAN</button></div></div></div></div>';
            document.body.appendChild(modal);
            existing = modal;

            // initialize Select2-like searchable select: try Select2 (jQuery) else fall back to NiceSelect
            var custSelect = existing.querySelector('#pm-customer');
            function initSelect2(){
                try{
                    if (window.jQuery && jQuery.fn && jQuery.fn.select2){
                        // ensure dropdown is appended inside modal so it is clickable/visible
                        jQuery(custSelect).select2({ placeholder: '--Pilih Pelanggan--', allowClear: true, width: '100%', dropdownParent: jQuery(existing) });
                        return true;
                    }
                }catch(e){}
                return false;
            }
            if (!initSelect2()){
                // attempt to load Select2 JS dynamically then init
                if (typeof window.jQuery !== 'undefined'){
                    var s = document.createElement('script');
                    s.src = '<?php echo base_url('assets/js/select2.js'); ?>';
                    s.onload = function(){ initSelect2(); };
                    document.head.appendChild(s);
                    // also load css from local assets
                    var l = document.createElement('link'); l.rel='stylesheet'; l.href='<?php echo base_url('assets/css/select2.min.css'); ?>'; document.head.appendChild(l);
                } else if (window.NiceSelect && custSelect){
                    try{ NiceSelect.bind(custSelect, { searchable: true }); }catch(e){ /* ignore */ }
                }
            }

            // handlers
            var paidEl = existing.querySelector('#pm-paid');
            // initialize AutoNumeric if available
            var pmPaidAN = null;
            if (window.AutoNumeric){
                try{ pmPaidAN = new AutoNumeric('#pm-paid', { currencySymbol: 'Rp. ', decimalCharacter: ',', decimalPlaces: 0, digitGroupSeparator: '.' }); }catch(e){ pmPaidAN = null; }
            }
            // store instance on modal element so we can reuse/reset it
            existing._pmPaidAN = pmPaidAN;
            // set-total button handler (sets paid = total)
            var setTotalBtn = existing.querySelector('#pm-set-total');
            if (setTotalBtn){
                setTotalBtn.addEventListener('click', function(){
                    try{
                        if (pmPaidAN && typeof pmPaidAN.set === 'function') pmPaidAN.set(total);
                        else if (paidEl) paidEl.value = total;
                    }catch(e){ if (paidEl) paidEl.value = total; }
                    try{ updateChange(); }catch(e){}
                });
            }
            // by default disable paid input until payment type selected
            if (paidEl) paidEl.setAttribute('disabled','disabled');

            existing.querySelector('#pm-payment-type').addEventListener('change', function(){
                var v = this.value;
                // enable/disable paid input depending on selection
                if (paidEl){
                    if (v){
                        paidEl.removeAttribute('disabled');
                        // if selected payment is not cash, set paid = total automatically
                        if (v !== '1'){
                            try{ if (pmPaidAN && typeof pmPaidAN.set === 'function') pmPaidAN.set(total); else paidEl.value = total; }catch(e){ if (paidEl) paidEl.value = total; }
                        } else {
                            // reset to 0 for cash
                            try{ if (pmPaidAN && typeof pmPaidAN.set === 'function') pmPaidAN.set(0); else paidEl.value = 0; }catch(e){ if (paidEl) paidEl.value = 0; }
                        }
                        // focus the input (AutoNumeric has focus method)
                        setTimeout(function(){ try{ if (pmPaidAN && typeof pmPaidAN.focus === 'function') pmPaidAN.focus(); else paidEl.focus(); }catch(e){} }, 10);
                    } else {
                        paidEl.setAttribute('disabled','disabled');
                    }
                }
                // show change row only for cash
                if (v === '1'){
                    existing.querySelector('#pm-change-row').style.display = 'block';
                } else {
                    existing.querySelector('#pm-change-row').style.display = 'none';
                }
                updateChange();
            });
            // listen to input events (AutoNumeric also triggers input)
            paidEl.addEventListener('input', updateChange);
            function updateChange(){
                var paid = 0;
                if (pmPaidAN && typeof pmPaidAN.getNumber === 'function'){
                    paid = pmPaidAN.getNumber();
                } else {
                    paid = parseFloat(paidEl.value) || 0;
                }
                var diff = paid - total;
                var changeEl = existing.querySelector('#pm-change');
                var okBtn = existing.querySelector('#pm-ok');
                var err = existing.querySelector('#pm-error');
                var errText = existing.querySelector('#pm-error-text');
                if (diff >= 0){
                    changeEl.textContent = formatRupiah(diff);
                    changeEl.style.color = '';
                    if (err) err.style.display = 'none';
                    if (errText) errText.textContent = '';
                    if (okBtn) okBtn.removeAttribute('disabled');
                } else {
                    changeEl.textContent = 'Kurang ' + formatRupiah(Math.abs(diff));
                    changeEl.style.color = 'red';
                    if (err){ err.style.display = 'block'; if (errText) errText.textContent = 'Pembayaran belum cukup'; }
                    if (okBtn) okBtn.setAttribute('disabled','disabled');
                }
            }

            existing.querySelector('#pm-cancel').addEventListener('click', function(){ existing.style.display = 'none'; });

            // OK / Save handler: gather values and call onConfirm callback
            var okBtnEl = existing.querySelector('#pm-ok');
            if (okBtnEl){
                okBtnEl.addEventListener('click', function(e){
                    e.preventDefault();
                    // gather values
                    var customer = (existing.querySelector('#pm-customer')||{}).value || '';
                    var payment_type = (existing.querySelector('#pm-payment-type')||{}).value || '';
                    var paid = 0;
                    if (existing._pmPaidAN && typeof existing._pmPaidAN.getNumber === 'function'){
                        try{ paid = existing._pmPaidAN.getNumber(); }catch(e){ paid = parseFloat((existing.querySelector('#pm-paid')||{}).value) || 0; }
                    } else {
                        paid = parseFloat((existing.querySelector('#pm-paid')||{}).value) || 0;
                    }
                    // call provided callback
                    try{ if (typeof onConfirm === 'function') onConfirm({ customer: customer, payment_type: payment_type, paid: paid }); }catch(e){ console.error(e); }
                    existing.style.display = 'none';
                });
            }
        } else {
            existing.querySelector('#pm-total').textContent = formatRupiah(total);
            var tinput = existing.querySelector('#pm-total-input'); if (tinput) tinput.value = total;
        }
        existing.style.display = 'flex';
            // ensure change updated
            var paidInput = existing.querySelector('#pm-paid');
            var an = existing._pmPaidAN;
            if (an && typeof an.set === 'function'){ try{ an.set(0); }catch(e){ if (paidInput) paidInput.value = 0; } } else if (paidInput){ paidInput.value = 0; }
            if (existing.querySelector('#pm-change')) existing.querySelector('#pm-change').textContent = formatRupiah(0);
            // disable paid input if payment type not selected
            var curPay = existing.querySelector('#pm-payment-type').value || '';
            if (paidInput){ 
                if (!curPay) {
                    paidInput.setAttribute('disabled','disabled');
                } else {
                    paidInput.removeAttribute('disabled');
                    // if preselected non-cash, set paid to total
                    try{ if (curPay !== '1' && existing._pmPaidAN && typeof existing._pmPaidAN.set === 'function') existing._pmPaidAN.set(total);
                          else if (curPay !== '1') paidInput.value = total;
                          else if (existing._pmPaidAN && typeof existing._pmPaidAN.set === 'function') existing._pmPaidAN.set(0);
                          else paidInput.value = 0;
                    }catch(e){ if (curPay !== '1') paidInput.value = total; }
                }
            }
            var tinput = existing.querySelector('#pm-total-input'); if (tinput) tinput.value = total;
            // update change & button state
            updateChange();
    }

    // attach save button to open modal
    var saveBtn = document.getElementById('btn-save-sale');
    if (saveBtn){
        saveBtn.addEventListener('click', function(){
            var items = Object.values(cart).map(function(it){ return { product_id: it.id, qty: it.qty, price: it.price }; });
            if (items.length === 0){ let msgerror = 'Item Tidak Boleh Kosong'; showError(msgerror); return; }
            var total = 0; items.forEach(function(it){ total += (parseFloat(it.price)||0) * (parseInt(it.qty,10)||0); });
            showPaymentModal(total, function(result){
                // send to save_sale
                console.log(result);
                var payload = {
                    customer_id: result.customer,
                    payment_type: result.payment_type,
                    transaction_total: result.paid
                };
                fetch('<?= base_url('Pos/save_sale') ?>', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) })
                .then(function(r){ return r.json(); }).then(function(res){
                    if (res && res.code == 200){
                        let msg = res.message;
                        showSuccess(msg);
                        try{ clearPaymentModal(); }catch(e){}
                        cart = {}; renderCart();
                    } else {
                        let msgerror = res.message || 'Gagal Simpan Transaksi';
                        showError(msgerror);
                    }
                }).catch(function(){ 
                    let msgerror = 'Error menyimpan';
                    showError(msgerror);
                });
            });
        });
    }


    // initial render: fetch cart from server with loading indicator
    showCartLoading();
    fetch('<?= base_url('Pos/get_temp_cart') ?>').then(function(r){ return r.json(); }).then(function(rows){
        cart = {};
        rows.forEach(function(r){
            var id = String(r.product_id);
            var price = parseFloat(r.product_price || r.master_price || 0) || 0;
            var qty = parseInt(r.product_qty,10) || 0;
            var name = r.product_name || ('Produk '+id);
            cart[id] = { id: id, name: name, price: price, qty: qty };
        });
        renderCart();
    }).catch(function(){ renderCart(); }).finally(function(){ hideCartLoading(); });
});


// pm-ok handler attached inside showPaymentModal when modal is created
</script>