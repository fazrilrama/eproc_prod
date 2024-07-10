<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="card">
    <div class="card-body">
        <h5>Budget Anda</h5>
        <hr>
        <div class="row">
            <div hidden class="col-md-4">
                <div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
                    <div class="widget-chat-wrapper-outer">
                        <div class="widget-chart-content">
                            <h6 class="widget-subheading">Operasional</h6>
                            <div class="widget-chart-flex">
                                <div class="widget-numbers mb-0 w-100">
                                    <div class="widget-chart-flex">
                                        <div class="fsize-4 text-success">
                                            <small class="opacity-5">Rp</small>
                                            <span id="budget_ops">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
                    <div class="widget-chat-wrapper-outer">
                        <div class="widget-chart-content">
                            <h6 class="widget-subheading">Budget Tersedia</h6>
                            <div class="widget-chart-flex">
                                <div class="widget-numbers mb-0 w-100">
                                    <div class="widget-chart-flex">
                                        <div class="fsize-4 text-danger">
                                            <small class="opacity-5">Rp</small>
                                            <span id="budget_nonops">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <div class="row">
            <div class="col-md-3">
                <div>
                    <div class="row">
                        <div class="col-md-12">
                            <b>Kata Kunci</b>
                            <input type="text" class="form-control" placeholder="Pencarian" name="search_term" id="search_term">
                        </div>
                        <div class="col-md-12">
                            <div>
                                <label for=""><b>Kategori</b></label>
                                <select class="form-control select2" name="f_category" id="f_category">
                                    <!-- <option value="">Pilih Semua</option> -->
                                    <option value="1">Barang</option>
                                    <!-- <option value="2">Jasa</option>
                                    <option value="3">Warung Pangan</option> -->
                                </select>
                            </div>
                            <div>
                                <label for=""><b>Kepemilikan Vendor</b></label>
                                <select class="form-control select2" name="f_company_owner" id="f_company_owner">
                                    <!-- <option value="">Pilih Semua</option>
                                    <?php
                                    foreach($this->db->where('deleted_at is null')->get('m_company')->result() as $d):
                                    ?>
                                        <option value="<?=$d->id?>"><?=$d->codename?></option>
                                    <?php endforeach;?> -->
                                    <?php if( $this->session->userdata('user')['id_usr_role']==1 ){ echo '<option value="">Semua</option>';} ?>
                                    <?php $data = $this->db->where('deleted_at is null')->get('m_company')->result();
                                    foreach ($data as $d) {
                                        if( $this->session->userdata('user')['id_usr_role']==1 ){
                                            echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                                        }
                                        else{
                                            if( $this->session->userdata('user')['id_company_owner']==$d->id){
                                                echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                                            }
                                        }
                                    } ?>
                                </select>

                                <label for=""><b>Kompetensi</b></label>
                                <select class="form-control select2" name="f_competency" id="f_competency">
                                    <option value="">Pilih Semua</option>
                                </select>

                                <label style="margin-top:5px;" for=""><b>Sub Kompetensi</b></label>
                                <select class="form-control select2" name="f_sub_competency" id="f_sub_competency">
                                    <option value="">Pilih Semua</option>
                                </select>

                                <label for="" style="margin-top:10px"><b>Lokasi</b></label>
                                <select style="width:100%" class="form-control select2" name="f_lokasi" id="f_lokasi">
                                    <option value="">Pilih Semua</option>
                                    <?php $data = $this->db
                                        ->where('capital_city', 1)
                                        ->get('m_city')
                                        ->result();

                                    foreach ($data as $d) {
                                        echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="collapse" id="filterCollapse">

                    </div> -->
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <button class="btn btn-lg btn-info" data-toggle="collapse" href="#filterCollapse"> <i class="fa fa-filter"></i> Filter</button> -->
                            <button id="do-search" class="btn btn-lg btn-success btn-block"> <i class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                </div>
                <div id="compare-container">
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Perbandingan Produk Terpilih:</h6>
                        </div>
                        <div class="col-md-12">
                            <div id="list-compare">
                            </div>
                            <button id="look-compare" class="btn btn-sm btn-info btn-block"><i class="fa fa-eye"></i> Lihat Perbandingan Selengkapnya</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-9">
                <div id="searching_container">
                    <center>
                        <img style="height:350px;" src="<?php echo base_url('assets/img/logo/searching_isometric.jpg') ?>" alt="">
                        <h5>Silahkan lakukan pencarian katalog vendor...</h5>
                    </center>
                </div>

                <div id="searching_not_found_container">
                    <center>
                        <img style="height:350px;" src="<?php echo base_url('assets/img/logo/search_not_found.png') ?>" alt="">
                        <h5>Maaf data yang Anda cari tidak ditemukan!</h5>
                    </center>
                </div>

                <div id="result_parent_container">
                    <div id="result_container" class="row" style="margin: 10px;">

                    </div>
                    <!-- <div id="load_more_container" style="margin-top:20px">
                        <center>
                            <button id="load_more" class="btn btn-success btn-lg"><i class="fa fa-arrow-down"></i> Muat Lebih Banyak</button>
                        </center>
                    </div> -->
                    <div id="pagination-container">
                        <ul id="pagination" class="pagination-sm"></ul>
                    </div>
                </div>

                <div id="compare_detail_container">
                </div>

                <div id="result_detail_container">
                </div>
            </div>
        </div>


    </div>
</div>


<script>
    $(document).ready(function() {
        if(!$('.fixed-sidebar').hasClass('closed-sidebar')){
            $('.fixed-sidebar').addClass('closed-sidebar');
            $('.close-sidebar-btn').addClass('is-active');
        }
        var loadMoreContainer = $('#load_more_container');
        var searchingContainer = $('#searching_container');
        var searchNotFoundContainer = $('#searching_not_found_container');
        var resultSearch = [];
        var resultContainer = $('#result_container');
        var lastOffset = 0;
        var resultParentContainer = $('#result_parent_container');
        var resultDetailContainer = $('#result_detail_container');
        var resultSearchPool = [];
        var comparePool = [];
        var maxCompare = 3;
        var budget = {
            ops: {
                available: 0
            },
            non_ops: {
                available: 0
            }
        };
        var compareDetailContainer = $('#compare_detail_container');
        var lookCompareDetail = $('#look-compare');

        lookCompareDetail.click(function() {
            if (comparePool.length >= 2) {
                resultParentContainer.hide();

                var compareDetailHtml = `
                <button id="to_result" class="btn btn-sm btn-danger"><i class="fa fa-arrow-left"></i> Kembali</button>
                <br/>
                <br/>
                <div class="row">`;
                comparePool.forEach(function(item, i) {
                    compareDetailHtml += `
                <div class="col-md-${(comparePool.length==2?'6':'4')}">
                
                <div id="${item.id}" class="card">
                        <div style="" class="card-body">
                            <center style="">
                                <span style="color:#2955c8;font-size:16px;font-weight:500;">${(item.product_name.length<=23)?item.product_name:item.product_name.substring(0,18)+'...'}</span>
                            </center>
                            <hr/>
                            <center style="padding:5px;overflow:hidden;">
                                ${(item.picture1!=null)?`<img onerror="this.onerror=null;this.src='<?php echo base_url('assets/img/logo/noimage.png') ?>';" style="width:auto;height:140px;" src="<?php echo base_url('upload/company/file/'); ?>${item.picture1}" />`:`<img style="width:100%;height:160px;" src="<?php echo base_url('assets/img/logo/noimage.png') ?>" />`}
                            </center>
                                <div>
                                    <center style="font-size:14px;color:darkgreen;"><b>${expandableText(item.price, 30, 'Harga Produk')}</b></center>
                                    <div class="table-responsive">
                                    <table style="font-size:13px;" class="table">
                                    `;

                    Object.keys(item).forEach(function(key) {
                        if (key != 'id' &&
                            key != 'id_company' &&
                            key != 'id_sub_competencies' &&
                            key != 'guarantee_file' &&
                            key != 'is_negotiable' &&
                            key != 'price_after_discount' &&
                            key != 'final_price' &&
                            !key.includes('picture') &&
                            key != 'created_at' &&
                            key != 'updated_at' &&
                            key != 'deleted_at' &&
                            key != 'images' &&
                            key != 'price' &&
                            key != 'active_date') {
                            compareDetailHtml += `
                        <tr>
                        <td><b>${key.replace(/_/g,' ').toUpperCase()}</b></td>
                        <td>${(key=='description'?item[key].replace(/(<([^>]+)>)/ig,""):item[key])}</td>
                        </tr>`;
                        }

                    });
                    compareDetailHtml += `
                                    </table>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12" style="margin:0px;padding:1px;">
                                        <button data-id="${item.id}" style="background-color:#EE7B1D !important;" class="catalog-card-shop btn btn-info btn-block"><i style="color:white" class="fa fa-shopping-cart"></i> Pesan Sekarang</button>
                                    </div>
                                </div>
                        </div>
                </div>
                </div>
                `;
                });
                compareDetailHtml += `</div>`;
                compareDetailContainer.html(compareDetailHtml);
                compareDetailContainer.show();

                $('.catalog-card-shop').click(function() {
                    var id = $(this).attr('data-id');
                    var item = comparePool.filter(function(item) {
                        return item.id == id
                    })[0];
                    var totalItem = 1;
                    basicModal({
                        title: `Pemesanan ${item.product_name}`,
                        body: function() {
                            var view = `<div>
                                            <table width="100%" class="table">
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Banyak Pemesanan
                                                    </td>
                                                    <td>
                                                        <button id="item-substract" class=" btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                                        <input style="font-size:16px;" id="item-count" value="${totalItem}" disabled type="text">
                                                        <button id="item-add" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Total Harga
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp<span id="item-total" style="display:none;"></span>
                                                        <span id="item-total-view"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Sisa Budget Anda
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp <span id="remain-budget"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Jenis Pembebanan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <select class="form-control" id="type">
                                                            <option value="1" hidden>Operasional</option>
                                                            <option value="2">Non Operasional</option>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Catatan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <textarea class="form-control" id="note" rows="5"></textarea>
                                                    </td>
                                                </tr>
                                                
                                            </table>
                                        </div>`;
                            return view;
                        },
                        footer: function() {
                            return `<button id="item-submit" class="btn btn-lg btn-success"><i class="fa fa-paper-plane"></i> Submit</button>`;
                        }
                    }).show(function() {

                        var maxTotal = 10000000;
                        var remainBudget = 0;
                        $('#remain-budget').html("0");
                        var updateRemainingBudget = function() {
                            var type = $('#type').val();
                            var textColor = "green";
                            switch (type) {
                                case '1': {
                                    remainBudget = budget.ops.available - parseInt($('#item-total').html());
                                    break;
                                }
                                case '2': {
                                    remainBudget = budget.non_ops.available - parseInt($('#item-total').html());
                                    break;
                                }
                            }

                            if (remainBudget <= 0) {
                                $('#item-submit').attr('disabled', 1);
                                textColor = "red";
                            } else {
                                $('#item-submit').removeAttr('disabled');
                                textColor = "green";
                            }

                            $('#remain-budget').html(remainBudget.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                            $('#remain-budget').css('color', textColor);
                        }

                        $('#item-total').html(totalItem * item.main_price);
                        $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                        $('#item-add').click(function() {
                            totalItem += 1;
                            $('#item-count').val(totalItem);
                            $('#item-total').html(totalItem * item.main_price);
                            $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                            updateRemainingBudget();

                        });

                        $('#item-substract').click(function() {
                            if (totalItem > 1) {
                                totalItem -= 1;
                                $('#item-count').val(totalItem);
                            } else {
                                $('#item-count').val(1);
                            }

                            $('#item-total').html(totalItem * item.main_price);
                            $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                            updateRemainingBudget();

                        });

                        $('#item-submit').click(function() {

                            if (totalItem * item.main_price > maxTotal) {
                                swal('Informasi', 'Mohon maaf permintaan pemesanan Anda tidak dapat diproses, karena total pemesanan Anda lebih dari 10 juta rupiah', 'warning');

                            } else {
                                var order_type = $('#type').val();
                                $.ajax({
                                    url: site_url + 'shopping/submit_order',
                                    type: 'post',
                                    data: postDataWithCsrf.data({
                                        product_id: item.id,
                                        buyer_id: user.id_user,
                                        qty: totalItem,
                                        note: $('#note').val(),
                                        order_type: order_type
                                    }),
                                    dataType: 'json',
                                    success: function(res) {
                                        if (res.success) {
                                            swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} berhasil, silahkan tunggu Approval GA Kantor Pusat.`, 'success')
                                                .then(function() {
                                                    refreshBudget((totalItem * parseInt(item.main_price)), order_type);
                                                    basicModal().close();
                                                });
                                        } else {

                                            swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} gagal!, silahkan coba lagi.`, 'error');
                                        }
                                    },
                                    error: function(err) {
                                        alert(err);
                                    }
                                });
                            }

                        });



                        $('#type').change(function(e) {
                            updateRemainingBudget();
                        });

                        updateRemainingBudget();

                    });
                });
                $('#to_result').click(function() {
                    compareDetailContainer.html('');
                    compareDetailContainer.hide();
                    resultParentContainer.show();
                });
            } else {
                swal('Minimal 2 Produk', 'Minimal 2 Produk untuk perbandingan.', 'warning');
            }

        });

        compareDetailContainer.hide();
        loadMoreContainer.hide();
        resultDetailContainer.hide();

        $('.select2').select2();


        //getBudget
        var refreshBudget = function(amount, type, budgetChange = {
            available: null
        }) {
            //console.log(`type ${type} : ${budgetChange.available}`);
            switch (parseInt(type)) {
                case 1: {
                    if (budgetChange.available != null) {
                        budget.ops.available = budgetChange.available;
                    }
                    budget.ops.available = budget.ops.available - amount;
                    $('#budget_ops').html(budget.ops.available.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."));
                    break;
                }
                case 2: {
                    if (budgetChange.available != null) {
                        budget.non_ops.available = budgetChange.available;
                    }
                    budget.non_ops.available = budget.non_ops.available - amount;
                    $('#budget_nonops').html(budget.non_ops.available.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."));
                    break;
                }
            }

        }
        var getBudget = function() {
            $.ajax({
                url: site_url + 'catalogue/get_budget',
                type: 'get',
                data: {
                    id_user: user.id_user,
                    interval_time: 1,
                    time: '<?php echo date("Y") ?>'
                },
                dataType: 'json',
                success: function(res) {
                    refreshBudget(0, 1, {
                        available: ((res.ops != null) ? res.ops.available : 0)
                    });
                    refreshBudget(0, 2, {
                        available: ((res.non_ops != null) ? res.non_ops.available : 0)
                    });
                }
            });
        }

        getBudget();

        // Components
        var CatalogCard = function(props = {
            id: null,
            image: null,
            images: [],
            title: null,
            description: null,
            competency: null,
            sub_competency: null,
            price: null,
            vendor: null,
            active_date: null,
        }) {
            return `
            <div class="col-md-4" style="margin-bottom:10px;">
                <div id="${props.id}" class="card" style="">
                    <div style="" class="card-body">
                        <center style="">
                            <span style="color:#2955c8;font-size:16px;font-weight:500;">${(props.title.length<=23)?props.title:props.title.substring(0,18)+'...'}</span>
                        </center>
                        <hr/>
                        <center style="padding:5px;overflow:hidden;">
                            ${(props.image!=null)?`<img onerror="this.onerror=null;this.src='<?php echo base_url('assets/img/logo/noimage.png') ?>';" style="width:auto;height:140px;" src="${props.image}" />`:`<img style="width:100%;height:160px;" src="<?php echo base_url('assets/img/logo/noimage.png') ?>" />`}
                        </center>
                        <div style="height:150px;">
                            <center style="font-size:14px;color:darkgreen;"><b>${expandableText(props.price, 30, 'Harga Produk')}</b></center>
                            <table style="font-size:13px;" class="table">
                                <tr>
                                    <td style="padding:2px !important;"><b>Penyedia</b></td>
                                    <td>${expandableText(props.vendor, 30, 'Ketegori Produk')}</td>
                                </tr>
                                <tr>
                                    <td style="padding:2px !important;"><b>Tersedia</b></td>
                                    <td>${expandableText(props.active_date, 30, 'Tgl Katalog Aktif')}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <input type="checkbox" data-id="${props.id}" class="catalog-add-to-compare"/> Bandingkan
                            </div>
                            <div class="col-md-2" style="margin:0px;padding:1px;">
                                <button data-id="${props.id}" style="width:100%" class="catalog-card-detail btn btn-primary"><i class="fa fa-info"></i></button>
                            </div>
                            <div class="col-md-3" style="margin:0px;padding:1px;">
                                <button data-id="${props.id}" style="background-color:#EE7B1D !important;" class="catalog-card-shop btn btn-warning btn-block"><i style="color:white" class="fa fa-shopping-cart"></i></button>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>`;
        };
        var singleDetailCatalog = function(item) {
            //console.log(item);
            return `
            <div class="card">
                        <div class="card-header-tab card-header">
                        <div style="cursor:pointer;" class="btn-detail-back card-header-title font-size-lg text-capitalize font-weight-normal"><i class="header-icon lnr-chevron-left icon-gradient bg-love-kiss"> </i><b>${item.product_name} dari ${item.company_name}</b></div>
                                            
                            <ul class="nav">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg5-0" class="nav-link show active"><i class="fa fa-info"></i> Detail</a></li>
                                <li data-id="${item.id}" class="nav-item add-to-cart"><a href="javascript:void(0)" class="nav-link"><i class="fa fa-shopping-cart"></i> Pemesanan</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane show active" id="tab-eg5-0" role="tabpanel">
                                    <div class="row">
                                                    <div class="col-md-6" style="width:100%">    
                                                        <div id="carouselExampleControls1" class="carousel slide" data-ride="carousel">
                                                            <div class="carousel-inner">
                                                                ${
                                                                    item.images.map(function(item,i){
                                                                        return (item!=null)? `<div class="carousel-item ${i==0?'active':null}">
                                                                            <img class="d-block w-100" src="<?php echo base_url() ?>/upload/company/file/${item}" alt="Picture">
                                                                        </div>`:null
                                                                    })
                                                                }
                                                            </div>
                                                            <a class="carousel-control-prev" href="#carouselExampleControls1" role="button" data-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a class="carousel-control-next" href="#carouselExampleControls1" role="button" data-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" style="width:100%">
                                                        <table class="table table-striped" style="width:100%">
                                                            <tbody>
                                                            
                                                                <tr>
                                                                    <td>
                                                                        <b>Harga Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td style="font-size:1.2em"><b>${item.price}</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Nama Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.product_name}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Kode Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.product_code}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Merek Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.product_brand}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Pemilik Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.company_name}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Harga Setelah Diskon</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>Rp ${item.price_after_discount.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Harga Final</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>Rp ${item.price_after_discount.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Min-Maks Pemesanan</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>Min ${item.min_order}, Maks ${item.max_order}</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>
                                                                        <b>Berat Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.product_weight}</td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <td>
                                                                        <b>Dimensi Produk</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>P ${item.dimension_long}(cm), L ${item.dimension_width}(cm), T ${item.dimension_height}(cm)</td>
                                                                </tr>

                                                                
                                                                <tr>
                                                                    <td>
                                                                        <b>Waktu Tersedia</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.active_date}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <b>Area Kerja</b>
                                                                    </td>
                                                                    <td>:</td>
                                                                    <td>${item.cities_networking}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                    </div>
                                    
                                    <br/>
                                                        <b>Deskripsi</b>
                                                        <br/>
                                                        ${item.description}
                                </div>
                            </div>
                        </div>
            </div>
            `;
        }

        searchNotFoundContainer.hide();

        var compareRefresh = function() {
            if (comparePool.length <= 0) {
                $('#compare-container').hide();
            } else {
                $('#compare-container').show();
                var list = `<div class="row">`;
                comparePool.forEach(function(item, i) {
                    list += `
                    <div style="margin:5px;" class="card col-md-12">
                        <div class="card-body">
                        <center>
                            ${(item.picture1!=null)
                                ?`<img style="width:auto;height:80px;" src="<?php echo base_url('upload/company/file/') ?>${item.picture1}" />`
                                :`<img style="width:100%;height:80px;" src="<?php echo base_url('assets/img/logo/noimage.png') ?>" />`}
                            <br>
                            <span style="font-weight:400">${item.product_name}</span>
                            <br>
                            <span style="font-weight:bold">Rp ${item.main_price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}</span>
                        </center>
                        <button class="compare-item-delete btn btn-sm btn-danger btn-block" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>`;
                });
                list += `</div> <hr/>`;
                $('#list-compare').html(list);
                $('.compare-item-delete').click(function() {
                    var dataID = $(this).attr('data-id');
                    comparePool = comparePool.filter(function(item, i) {
                        return item.id != dataID;
                    });
                    $(`input[data-id="${dataID}"]`).each(function() {
                        $(this).prop('checked', false);
                    });
                    compareRefresh();
                });
            }

            if (comparePool.length < 2) {
                compareDetailContainer.html('');
                compareDetailContainer.hide();
                resultParentContainer.show();
            } else {
                if (compareDetailContainer.is(':visible')) {
                    lookCompareDetail.click();
                }
            }
        }

        compareRefresh();

        var doSearch = function(limit = 10, offset = 0) {

            $.ajax({
                url: site_url + 'catalogue/search',
                type: 'POST',
                data: postDataWithCsrf.data({
                    group: $('#f_category').val(),
                    competency: $('#f_competency').val(),
                    sub_competency: $('#f_sub_competency').val(),
                    search_term: $('#search_term').val(),
                    f_lokasi: $('#f_lokasi').val(),
                    f_company_owner:$('#f_company_owner').val(),
                    limit: limit,
                    offset: offset
                }),
                dataType: 'json',
                success: function(res) {

                    loadMoreContainer.hide();
                    if (offset == 0 && res.result.length <= limit) {
                        resultSearch = [];
                        resultSearchPool = [];
                    }
                    if (offset == 0 && res.result.length == 0) {
                        resultSearch = [];
                        resultSearchPool = [];
                        searchNotFoundContainer.show();
                    } else {
                        searchNotFoundContainer.hide();
                        resultDetailContainer.show();
                    }

                    if (offset == 0) {
                        $('#pagination-container').html('');
                            
                        if(res.total_data>0){
                            $('#pagination-container').html('<ul id="pagination" class="pagination-sm"></ul>');
                            // if($('#pagination').data("twbs-pagination")){
                            //     $('#pagination').twbsPagination('destroy');
                            // }
                            $('#pagination').twbsPagination({
                                totalPages: Math.ceil(res.total_data / 12),
                                visiblePages: 12,
                                initiateStartPageClick: false,
                                onPageClick: function(e, p) {
                                    doSearch(12, (p - 1) * 12);
                                }
                            });
                        }
                    }

                    if (res.success) {
                        resultSearch = [];
                        if (res.result.length > 0 && res.result.length >= limit) loadMoreContainer.show();
                        res.result.map(function(item, i) {
                            resultSearch.push(CatalogCard({
                                image: `<?php echo base_url() ?>/upload/company/file/` + item.picture1,
                                images: [item.picture1, item.picture2, item.picture3, item.picture4, item.picture5],
                                id: item.id,
                                title: item.product_name,
                                description: item.description,
                                competency: item.kompetensi,
                                vendor: item.company_name,
                                active_date: moment(item.active_start_date, 'YYYY-MM-DD').format('D MMM Y') + ' s/d ' + moment(item.active_end_date, 'YYYY-MM-DD').format('D MMM Y'),
                                price: 'Rp' + item.main_price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + '/' + item.unit + (item.is_negotiable == '1' ? ' (Nego)' : '')
                            }));

                            item.images = [item.picture1, item.picture2, item.picture3, item.picture4, item.picture5];
                            item.price = 'Rp' + item.main_price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + '/' + item.unit + (item.is_negotiable == '1' ? ' (Nego)' : '');
                            item.active_date = moment(item.active_start_date, 'YYYY-MM-DD').format('D MMM Y') + ' s/d ' + moment(item.active_end_date, 'YYYY-MM-DD').format('D MMM Y');
                            resultSearchPool.push(item);
                        });

                        var result = '';
                        resultSearch.map(function(item, i) {
                            result += item;
                        });
                        resultContainer.html(result);

                        $('.catalog-card-shop').click(function() {
                            var id = $(this).attr('data-id');
                            var item = resultSearchPool.filter(function(item) {
                                return item.id == id
                            })[0];
                            var totalItem = 1;
                            basicModal({
                                title: `Pemesanan ${item.product_name}`,
                                body: function() {
                                    var view = `<div>
                                            <table width="100%" class="table">
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Banyak Pemesanan
                                                    </td>
                                                    <td>
                                                        <button id="item-substract" class=" btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                                        <input style="font-size:16px;" id="item-count" value="${totalItem}" disabled type="text">
                                                        <button id="item-add" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Total Harga
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp<span id="item-total" style="display:none;"></span>
                                                        <span id="item-total-view"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Sisa Budget Anda
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp <span id="remain-budget"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Jenis Pembebanan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <select class="form-control" id="type">
                                                            <!-- <option value="1" hidden>Operasional</option> -->
                                                            <option value="2">Non Operasional</option>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Catatan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <textarea class="form-control" id="note" rows="5"></textarea>
                                                    </td>
                                                </tr>
                                                
                                            </table>
                                        </div>`;
                                    return view;
                                },
                                footer: function() {
                                    return `<button id="item-submit" class="btn btn-lg btn-success"><i class="fa fa-paper-plane"></i> Submit</button>`;
                                }
                            }).show(function() {

                                var maxTotal = 10000000;
                                var remainBudget = 0;
                                $('#remain-budget').html("0");
                                var updateRemainingBudget = function() {
                                    var type = $('#type').val();
                                    var textColor = "green";
                                    switch (type) {
                                        case '1': {
                                            remainBudget = budget.ops.available - parseInt($('#item-total').html());
                                            break;
                                        }
                                        case '2': {
                                            remainBudget = budget.non_ops.available - parseInt($('#item-total').html());
                                            break;
                                        }
                                    }

                                    if (remainBudget <= 0) {
                                        $('#item-submit').attr('disabled', 1);
                                        textColor = "red";
                                    } else {
                                        $('#item-submit').removeAttr('disabled');
                                        textColor = "green";
                                    }

                                    $('#remain-budget').html(remainBudget.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                    $('#remain-budget').css('color', textColor);
                                }

                                $('#item-total').html(totalItem * item.main_price);
                                $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                $('#item-add').click(function() {
                                    totalItem += 1;
                                    $('#item-count').val(totalItem);
                                    $('#item-total').html(totalItem * item.main_price);
                                    $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                    updateRemainingBudget();

                                });

                                $('#item-substract').click(function() {
                                    if (totalItem > 1) {
                                        totalItem -= 1;
                                        $('#item-count').val(totalItem);
                                    } else {
                                        $('#item-count').val(1);
                                    }

                                    $('#item-total').html(totalItem * item.main_price);
                                    $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                    updateRemainingBudget();

                                });

                                $('#item-submit').click(function() {

                                    if (totalItem * item.main_price > maxTotal) {
                                        swal('Informasi', 'Mohon maaf permintaan pemesanan Anda tidak dapat diproses, karena total pemesanan Anda lebih dari 10 juta rupiah', 'warning');

                                    } else {
                                        var order_type = $('#type').val();
                                        $.ajax({
                                            url: site_url + 'shopping/submit_order',
                                            type: 'post',
                                            data: postDataWithCsrf.data({
                                                product_id: item.id,
                                                buyer_id: user.id_user,
                                                qty: totalItem,
                                                note: $('#note').val(),
                                                order_type: order_type
                                            }),
                                            dataType: 'json',
                                            success: function(res) {
                                                if (res.success) {
                                                    swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} berhasil, silahkan tunggu Approval GA Kantor Pusat.`, 'success')
                                                        .then(function() {
                                                            refreshBudget((totalItem * parseInt(item.main_price)), order_type);
                                                            basicModal().close();
                                                        });
                                                } else {

                                                    swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} gagal!, silahkan coba lagi.`, 'error');
                                                }
                                            },
                                            error: function(err) {
                                                alert(err);
                                            }
                                        });
                                    }

                                });



                                $('#type').change(function(e) {
                                    updateRemainingBudget();
                                });

                                updateRemainingBudget();

                            });
                        });

                        $('.catalog-card-detail').click(function() {
                            resultParentContainer.hide(300);
                            var id = $(this).attr('data-id');
                            item = resultSearchPool.filter(function(item) {
                                return item.id === id
                            })[0];
                            resultDetailContainer.html(singleDetailCatalog(item));
                            resultDetailContainer.show(100);

                            $('.add-to-cart').click(function() {
                                var id = $(this).attr('data-id');
                                var item = resultSearchPool.filter(function(item) {
                                    return item.id == id
                                })[0];
                                var totalItem = 1;
                                basicModal({
                                    title: `Pemesanan ${item.product_name}`,
                                    body: function() {
                                        var view = `<div>
                                            <table width="100%" class="table">
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Banyak Pemesanan
                                                    </td>
                                                    <td>
                                                        <button id="item-substract" class=" btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                                        <input style="font-size:16px;" id="item-count" value="${totalItem}" disabled type="text">
                                                        <button id="item-add" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Total Harga
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp<span id="item-total" style="display:none;"></span>
                                                        <span id="item-total-view"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Sisa Budget Anda
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        Rp <span id="remain-budget"></span>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Jenis Pembebanan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <select class="form-control" id="type">
                                                            <option value="1" hidden>Operasional</option>
                                                            <option value="2">Non Operasional</option>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-size:16px;">
                                                        Catatan
                                                    </td>
                                                    <td style="font-size:16px;font-weight:400;">
                                                        <textarea class="form-control" id="note" rows="5"></textarea>
                                                    </td>
                                                </tr>
                                                
                                            </table>
                                        </div>`;
                                        return view;
                                    },
                                    footer: function() {
                                        return `<button id="item-submit" class="btn btn-lg btn-success"><i class="fa fa-paper-plane"></i> Submit</button>`;
                                    }
                                }).show(function() {

                                    var maxTotal = 10000000;
                                    var remainBudget = 0;
                                    $('#remain-budget').html("0");
                                    var updateRemainingBudget = function() {
                                        var type = $('#type').val();
                                        var textColor = "green";
                                        switch (type) {
                                            case '1': {
                                                remainBudget = budget.ops.available - parseInt($('#item-total').html());
                                                break;
                                            }
                                            case '2': {
                                                remainBudget = budget.non_ops.available - parseInt($('#item-total').html());
                                                break;
                                            }
                                        }

                                        if (remainBudget <= 0) {
                                            $('#item-submit').attr('disabled', 1);
                                            textColor = "red";
                                        } else {
                                            $('#item-submit').removeAttr('disabled');
                                            textColor = "green";
                                        }

                                        $('#remain-budget').html(remainBudget.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                        $('#remain-budget').css('color', textColor);
                                    }

                                    $('#item-total').html(totalItem * item.main_price);
                                    $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                    $('#item-add').click(function() {
                                        totalItem += 1;
                                        $('#item-count').val(totalItem);
                                        $('#item-total').html(totalItem * item.main_price);
                                        $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                        updateRemainingBudget();

                                    });

                                    $('#item-substract').click(function() {
                                        if (totalItem > 1) {
                                            totalItem -= 1;
                                            $('#item-count').val(totalItem);
                                        } else {
                                            $('#item-count').val(1);
                                        }

                                        $('#item-total').html(totalItem * item.main_price);
                                        $('#item-total-view').html((totalItem * item.main_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                                        updateRemainingBudget();

                                    });

                                    $('#item-submit').click(function() {

                                        if (totalItem * item.main_price > maxTotal) {
                                            swal('Informasi', 'Mohon maaf permintaan pemesanan Anda tidak dapat diproses, karena total pemesanan Anda lebih dari 10 juta rupiah', 'warning');

                                        } else {
                                            var order_type = $('#type').val();
                                            $.ajax({
                                                url: site_url + 'shopping/submit_order',
                                                type: 'post',
                                                data: postDataWithCsrf.data({
                                                    product_id: item.id,
                                                    buyer_id: user.id_user,
                                                    qty: totalItem,
                                                    note: $('#note').val(),
                                                    order_type: order_type
                                                }),
                                                dataType: 'json',
                                                success: function(res) {
                                                    if (res.success) {
                                                        swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} berhasil, silahkan tunggu Approval GA Kantor Pusat.`, 'success')
                                                            .then(function() {
                                                                refreshBudget((totalItem * parseInt(item.main_price)), order_type);
                                                                basicModal().close();
                                                            });
                                                    } else {

                                                        swal('Pemesanan', `Pemesanan ${item.product_name} sebanyak ${totalItem} gagal!, silahkan coba lagi.`, 'error');
                                                    }
                                                },
                                                error: function(err) {
                                                    alert(err);
                                                }
                                            });
                                        }

                                    });



                                    $('#type').change(function(e) {
                                        updateRemainingBudget();
                                    });

                                    updateRemainingBudget();

                                });
                            });

                            $('.btn-detail-back').click(function() {
                                resultParentContainer.show(100);
                                resultDetailContainer.hide(100);
                                resultDetailContainer.html('');
                            });
                        });

                        comparePool.forEach(function(item, i) {
                            $(`input[data-id="${item.id}"]`).each(function() {
                                $(this).attr('checked', true);
                            });
                        });

                        $('.catalog-add-to-compare').change(function() {
                            var isChecked = this.checked;
                            var dataID = $(this).attr('data-id');
                            if (isChecked) {
                                var selectedData = null;
                                var resultData = resultSearchPool.filter(function(item) {
                                    return item.id == dataID
                                });
                                if (resultData.length > 0) selectedData = resultData[0];
                                if (selectedData != null && comparePool.length < maxCompare) {
                                    comparePool.push(selectedData);
                                } else {
                                    swal('Info', 'Maaf, maksimal 3 produk untuk dibandingkan', 'warning');
                                    this.checked = false;
                                }
                            } else {
                                if (comparePool.length > 0) {
                                    var resultData = comparePool.filter(function(item) {
                                        return item.id == dataID
                                    });
                                    if (resultData.length > 0) {
                                        comparePool = comparePool.filter(function(item) {
                                            return item.id != dataID;
                                        });
                                    }
                                }
                            }

                            compareRefresh();


                        });

                    } else {
                        resultParentContainer.html('Opsss, pencarian Anda tidak ditemukan, silahkan coba lagi...');
                    }



                    $('#pagination').twbsPagination({
                        totalPages: 1000,
                        visiblePages: 12
                    });


                },
                error: function(err) {
                    alert(err);
                }
            });
        }

        function isInArray(value, array) {
            return array.indexOf(value) > -1;
        }

        $.ajax({
            url: site_url + 'master/get_company_competency',
            type: 'get',
            dataType: 'json',
            success: function(res) {
                var opt = `<option value="">Pilih Semua</option>`;
                var exception=[1,2,4,5,6,7,13];
                res.map(function(item) {
                    if(!isInArray(Number(item.id), exception)){
                        opt += `<option value="${item.id}">${item.name}</option>`;
                
                    }
                });
                $('#f_competency').html(opt);
            },
            error: function(err) {
                alert(err);
            }

        });
        $('#f_competency').html()

        $('#f_competency').change(function() {
            var value = $(this).val();
            getMasterData.getCompanySubCompetency({
                    id_company_competency: (value != '' ? value : -1)
                },
                function(stat, data) {
                    if (stat) {
                        var opt = '<option value="">Pilih</option>';
                        data.forEach(function(i) {
                            opt += '<option value="' + i.id + '">' + i.name + '</option>';
                        });
                        $('#f_sub_competency').html(opt);
                    }
                });
        });

        $('#do-search').click(function() {
            searchingContainer.hide();
            // resultDetailContainer.hide();
            resultParentContainer.show();
            resultDetailContainer.hide();
            resultDetailContainer.html('');

            doSearch(12, 0);
            lastOffset = 0;
        });

        $('#load_more').click(function() {
            lastOffset = lastOffset == 0 ? 12 : lastOffset += 12;
            doSearch(12, lastOffset);
        });
    });
</script>