<?php $this->load->view('templates/dashboard/content-title'); ?>

<style>
#table_data tr th{
    min-width: 150px;
}
#form-add label{
    font-size: 10pt;
    font-weight: bold;
}
.form-control textarea{
    width: 100%;
}
</style>

<div class="card" id="form-container" style="display:none;">
    <div class="card-body">
        <h5>Form Upload</h5>
        <form action="post" id="form-add">
            
        <label for="">Ambil Data Project Dari Project Selesai</label>
            <select name="project_id" id="project_id" class="form-control" style="width: 100%;"></select>
            <small style="color:red;">
                Catatan:
                <ul>
                    <li>Data vendor pada project mungkin belum terdaftar di PADI UMKM.</li>
                    <li>Kami sarankan untuk mendaftarkan terlebih dahulu vendor pemenang project di <a href="<?=site_url()?>app#padi_umkm/list_umkm">PADI UMKM > Data UMKM.</a></li>
                    <li>
                        <b>Mohon cek kembali data yang terisi otomatis!.</b>
                        <br>
                        <span style="color:black;">Data terisi otomatis hanya mencakup</span>
                        <div class="row" style="color:black;">
                            <div class="col-md-3">
                                <ul>
                                    <li>Tanggal Transaksi</li>
                                    <li>Tanggal Konfirmasi</li>
                                    <li>Transaksi ID</li>
                                    <li>Nama Project</li>
                                    <li>Total Nilai Project</li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <ul>
                                    <li>Tanggal Order Placement</li>
                                    <li>Deskripsi Projek</li>
                                    <li>UID UMKM</li>
                                    <li>Nama UMKM</li>
                                    <li>Kategori UMKM(Jika Sudah Terdaftar PADI UMKM)</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </small>

            <label for="">Ambil Data UMKM Dari PADI UMKM</label>
            <select name="vendor_sap_id" id="vendor_sap_id" class="form-control" style="width: 100%;"></select>
            <small style="color:red;">
                Catatan:
                <ul>
                    <li>Data UMKM adalah data yg terdaftar di <a href="<?=site_url()?>app#padi_umkm/list_umkm">PADI UMKM > Data UMKM.</a></li>
                    <li>
                        <b>Mohon cek kembali data yang terisi otomatis!.</b>
                        <br>
                        <span style="color:black;">Data terisi otomatis hanya mencakup</span>
                        <div class="row" style="color:black;">
                            <div class="col-md-3">
                                <ul>
                                    <li>UID UMKM</li>
                                    <li>Nama UMKM</li>
                                    <li>Kategori UMKM</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </small>


            <hr>
            <div>
                <label for="">Tanggal Transaksi<span style="color:red">*</span></label>
                <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Transaksi ID<span style="color:red">*</span></label>
                <input type="text" class="form-control" id="transaksi_id" name="transaksi_id" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Nama Project <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="nama_project" name="nama_project" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Kategori Project <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="kategori_project" name="kategori_project" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($this->db->get('padiumkm_kategori_project')->result() as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>
            <div>
                <label for="">Total Nilai Project <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="total_nilai_project" name="total_nilai_project" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Tipe Nilai Project <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="tipe_nilai_project" name="tipe_nilai_project" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <option value="CAPEX">CAPEX</option>
                 <option value="OPEX">OPEX</option>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>
            <div>
                <label for="">Kategori UMKM <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="kategori_umkm" name="kategori_umkm" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($kat_usaha as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>
            <div>
                <label for="">UID UMKM <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="uid_umkm" name="uid_umkm" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Nama UMKM <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="nama_umkm" name="nama_umkm" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Target Penyelesaian(hari) <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="target_penyelesaian" name="target_penyelesaian" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Tanggal Order Placement <span style="color:red">*</span></label>
                <input type="date" class="form-control" id="tanggal_order_placement" name="tanggal_order_placement" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Tanggal Confirmation <span style="color:red">*</span></label>
                <input type="date" class="form-control" id="tanggal_confirmation" name="tanggal_confirmation" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Tanggal Delivery <span style="color:red">*</span></label>
                <input type="date" class="form-control" id="tanggal_delivery" name="tanggal_delivery" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Tanggal Invoice <span style="color:red">*</span></label>
                <input type="date" class="form-control" id="tannggal_invoice" name="tannggal_invoice" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Total Cycle Time(hari) <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="total_cycle_time" name="total_cycle_time" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Kategori Delivery Time <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="kategori_delivery_time" name="kategori_delivery_time" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($this->db->get('padiumkm_delivery_time')->result() as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>
            <div>
                <label for="">Rating <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="rating" name="rating" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($this->db->get('padiumkm_rating')->result() as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>
            <div>
                <label for="">Feedback <span style="color:red">*</span></label>
                <textarea type="text" class="form-control" id="feedback" name="feedback" placeholder="Type here" required></textarea>
                <small style="color:red;"></small>
            </div>
            <div>
                <label for="">Deskripsi Project <span style="color:red">*</span></label>
                <textarea type="text" class="form-control" id="deskripsi_project" name="deskripsi_project" placeholder="Type here" required></textarea>
                <small style="color:red;"></small>
            </div>


            <hr>
            Note:
            <ul>
            <li><span style="color:red">*</span> : Required field</li>
            </ul>
            <button class="btn btn-lg btn-danger cancel" type="button"><i class="fa fa-close"></i> Cancel</button>
            <button class="btn btn-lg btn-info" type="reset"><i class="fa fa-retweet"></i> Reset</button>  
            <button class="btn btn-lg btn-success" type="submit"><i class="fa fa-send"></i> Submit</button>

        </form>
    </div>
</div>

<div class="card" id="upload-container" style="display:none;">
    <div class="card-body">
        <h5>Form Upload Batch </h5>
        <form action="post" id="form-add-excel">
            <div>
                <label for="">File Excel (.xlsx)</label>
                <input type="file" class="form-control" id="file" name="file" placeholder="Type here" required/>
                <small style="color:red;">
                Pastikan Anda mengikuti format terlampir!, baris 1 format upload adalah contoh, silahkan lanjutkan pada baris ke 2.
                <br>
                <a href="<?=site_url('padi_umkm/getFormatDataTransaksi')?>" target="_blank" ><i class="fa fa-download"></i> Unduh Format Upload</a></small>
            </div>
            <hr>
            <button class="btn btn-lg btn-danger cancel" type="button"><i class="fa fa-close"></i> Cancel</button>
            <button class="btn btn-lg btn-info" type="reset"><i class="fa fa-retweet"></i> Reset</button>  
            <button class="btn btn-lg btn-success" type="submit"><i class="fa fa-send"></i> Submit</button>           

        </form>
    </div>
</div>

<div class="card" id="list-container">
    <div class="card-header">
        PADI UMKM - Data Transaksi
    </div>
    <div class="card-body">
        <button id="btn-add-excel" class="btn btn-md btn-primary"><i class="fa fa-file"></i> Add From Excel</button>
        <button id="btn-add" class="btn btn-md btn-primary"><i class="fa fa-plus"></i> Add New</button>
        <button hidden id="export-excel" class=" btn btn-md btn-success"><i class="fa fa-file-excel"></i> Export To Excel</button>
        <hr>
        <div class="row">
            <div hidden class="col-md-4">
                <?php 
                $f_data_source=isset($_GET['f_data_source'])?$_GET['f_data_source']:1;
                $f_data_source=in_array($f_data_source,[1,2])?$f_data_source:1;
                ?>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-4 col-md-4 col-form-label">Data Source</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="f_data_source" id="f_data_source" class="form-control filter">
                            <option <?= $f_data_source==1?'selected':'' ?> value="1">Internal Database</option>
                            <option <?= $f_data_source==2?'selected':'' ?> value="2">Direct PADI API</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="table_data" class="table table-hover table-borderless table-striped">
                <thead>
                    <tr>
                        <th>BUMN ID</th>
                        <th>TGL TRANSAKSI</th>
                        <th>TRANSAKSI ID</th>
                        <th>NAMA PROJECT</th>
                        <th>KATEGORI PROJECT</th>
                        <th>TOTAL NILAI PROJECT</th>
                        <th>TIPE NILAI PROJECT</th>
                        <th>KATEGORI UMKM</th>
                        <th>UID UMKM</th>
                        <th>NAMA UMKM</th>
                        <th>TARGET PENYELESAIAN</th>
                        <th>TGL ORDER PLACEMENT</th>
                        <th>TGL KONFIRMASI</th>
                        <th>TGL DELIVERY</th>
                        <th>TGL INVOICE</th>
                        <th>TOTAL CYCLE TIME</th>
                        <th>KATEGORI DELIVERY TIME</th>
                        <th>RATING</th>
                        <th>FEEDBACK</th>
                        <th>DESKRIPSI PROJECT</th>
                        <th>TIMESTAMP</th>
                        <th>USER</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){

        var filter={
            dataSource:$('#f_data_source').val()
        };
        var getURL=site_url+'padi_umkm/'+(filter.dataSource==1?'getListTransaksiDB':'getListTransaksi');
        $('#f_data_source').change(function(){
            window.location.href=site_url+'app#padi_umkm/list_transaksi?f_data_source='+$(this).val();
            window.location.reload();
        });
        
        $('.sel2').select2();
        //Start Add Form
        var selectedVendor=[];
        var selectedProject=[];
        $('#btn-add').click(function(){
            selectedVendor=[];
            selectedProject=[];
            $('.sel2').val(null).trigger('change');
            $('#form-add')[0].reset();
            $('#vendor_sap_id').html(null);
            $('#project_id').html(null);
            $('#form-container').show();
            $('#list-container').hide();
        });
        $('#form-add .cancel').click(function(){
            selectedVendor=[];
            selectedProject=[];
            $('.sel2').val(null).trigger('change');
            $('#form-add')[0].reset();
            $('#vendor_sap_id').html(null);
            $('#project_id').html(null);
            $('#form-container').hide();
            $('#list-container').show();
            $('#table_data').DataTable().ajax.reload();
        });
        $('#form-add').submit(function(e){
            e.preventDefault();
            var formData=new FormData(this);
            $.ajax({
                url:site_url+'padi_umkm/postDataTransaksi',
                type:'post',
                dataType:'json',
                data:formData,
                processData:false,
                contentType:false,
                success:function(res){
                    if(res.status!='success'){
                        alert('Something went wrong, please try again');
                    }else{
                        alert('Success!');
                        $('#form-add .cancel').click();
                    }
                }
                ,error:function(xhr,stat,err){
                    //console.log(err);
                    alert('Something went wrong, please try again');
                }
            });
        });

        $('#vendor_sap_id').select2({
            minimumInputLength:3,
            placeholder:'Cari No SAP Vendor/Nama/Email',
            ajax: {
                url: site_url+'/padi_umkm/searchVendorUMKM',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        limit: 10
                    }
                    return query;
                },
                processResults: function (data) {
                    data.map(function(item){
                        item.id=item.id_sap;
                        item.text=`VENDOR ID ${item.id_sap} | ${item.vendor_name} | ${item.email} `;
                        return item;
                    });
                    selectedVendor=data;
                    return {
                        results: data
                    };
                },
                delay:300
            }
        });

        $('#vendor_sap_id').change(function(){
           var id=$(this).val();
           var selectedData=selectedVendor.filter(function(item){
               return item.id_sap==id;
           });
           if(selectedData.length>0){
               selectedData=selectedData[0];
           }
           else{
               selectedData=null;
           }

           if(selectedData!=null){
               $('#uid_umkm').val(selectedData.id_sap);
               $('#nama_umkm').val(selectedData.vendor_name);
               $('#kategori_umkm').val(JSON.parse(selectedData.padiumkm_data).kategori_usaha).trigger('change');
           }

        });

        $('#project_id').select2({
            minimumInputLength:3,
            placeholder:'Cari No Kontrak/Nama/Deskripsi Proyek',
            ajax: {
                url: site_url+'/padi_umkm/searchProject',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        limit: 10
                    }
                    return query;
                },
                processResults: function (data) {
                    data.map(function(item){
                        item.id=item.id;
                        item.text=`${item.contract_no} | ${item.name.length>80?item.name.substring(0,80)
                        +'...':item.name} | ${item.vendor_name}(${item.id_sap}) `;
                        return item;
                    });
                    selectedProject=data;
                    return {
                        results: data
                    };
                },
                delay:300
            }
        });

        $('#project_id').change(function(){
           var id=$(this).val();
           var selectedData=selectedProject.filter(function(item){
               return item.id==id;
           });
           if(selectedData.length>0){
               selectedData=selectedData[0];
           }
           else{
               selectedData=null;
           }
           $('#form-add')[0].reset();
           $('.sel2').val(null).trigger('change');
           $('#vendor_sap_id').html(null);

           if(selectedData!=null){
               $('#tanggal_transaksi').val(selectedData.end_date);
               $('#tanggal_confirmation').val(selectedData.end_date);
               $('#transaksi_id').val(selectedData.id);
               $('#nama_project').val(selectedData.name);
               $('#total_nilai_project').val(selectedData.final_price);
               $('#tanggal_order_placement').val(selectedData.start_date);
               $('#deskripsi_project').val(selectedData.description);
               $('#uid_umkm').val(selectedData.id_sap);
               $('#nama_umkm').val(selectedData.vendor_name);
               if(selectedData.padiumkm_data!=null){
                   $('#kategori_umkm').val(JSON.parse(selectedData.padiumkm_data).kategori_usaha).trigger('change');
               }
               
           }

        });
        //End Add Form

        //Start Upload Excel
        $('#btn-add-excel').click(function(){
            $('#upload-container').show();
            $('#list-container').hide();
        });
        $('#form-add-excel .cancel').click(function(){
            $('#form-add-excel')[0].reset();
            $('#upload-container').hide();
            $('#list-container').show();
            $('#table_data').DataTable().ajax.reload();
        });
        $('#form-add-excel').submit(function(e){
            e.preventDefault();
            var formData=new FormData(this);
            $.ajax({
                url:site_url+'padi_umkm/readDataTransaksiFromExcel',
                type:'post',
                dataType:'json',
                data:formData,
                processData:false,
                contentType:false,
                success:function(res){
                    alert(res.message);
                    if(res.status=='success'){
                        $('#form-add-excel .cancel').click();
                    }
                }
                ,error:function(xhr,stat,err){
                    //console.log(err);
                    alert('Something went wrong, please try again');
                }
            });
        });
        //End Upload Excel

        var dtTable = $('#table_data').DataTable({
            "aaSorting": [],
            "retrieve": true,
            "processing": true,
            "serverSide":true,
            "searching":filter.dataSource==1,
            "dom":"lBfrtip",
            buttons: [
                {
                    extend: 'excel',
                },
            ],
            'ajax': {
                "type": "POST",
                "url": getURL,
                "data": function(d) {
                    var info = $('#table_data').DataTable().page.info();
                    d.page=info.page+1;
                },
                "dataSrc": function(res){
                    return res.data;
                }
            },
            'columns': [
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.bumn_id}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tanggal_transaksi}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.transaksi_id_text}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.nama_project}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.kategori_project}`;
                        var view = `${full.kat_project_name}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `Rp${Number(full.total_nilai_project).toLocaleString('id')}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tipe_nilai_project}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.kategori_umkm}`;
                        var view = `${full.kat_usaha_name}`;
                        return view;
                    },
                    sortable:false
                },  
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.uid_umkm}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.nama_umkm}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.target_penyelesaian} Hari`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tanggal_order_placement}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tanggal_confirmation}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tanggal_delivery}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.tannggal_invoice}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.total_cycle_time} Hari`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.kategori_delivery_time}`;
                        var view = `${full.delivery_time_name}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.rating}`;
                        var view = `${full.rating_name}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.feedback}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.deskripsi_project}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.timestamp}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.user}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `<button data-id="${full.transaksi_id}" class="btn btn-md btn-danger btn-delete">
                        <i class="fa fa-trash"></i></button>`;
                        return view;
                    },
                    sortable:false
                },

            ],
            "responsive": false,
            drawCallback: function(setting) {
                $('.btn-delete').off('click').click(function(){
                    if(confirm('Are your sure?')){
                        var uid=$(this).attr('data-id');
                        $.ajax({
                            url:site_url+'padi_umkm/deleteDataTransaksi',
                            type:'post',
                            dataType:'json',
                            data:{
                                uid:uid
                            }
                            ,success:function(res){
                                if(res.status!='success'){
                                    alert('Something went wrong, please try again');
                                }else{
                                    alert('Success!');
                                    $('#table_data').DataTable().ajax.reload();
                                }
                            }
                            ,error:function(xhr,stat,err){
                                //console.log(err);
                                alert('Something went wrong, please try again');
                            }
                        })
                    }
                });
            },
        });
    });
</script>
