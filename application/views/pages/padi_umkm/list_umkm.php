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
            <label for="">Ambil Data Dari Vendor Terdaftar</label>
            <select name="vendor_sap_id" id="vendor_sap_id" class="form-control" style="width: 100%;"></select>
            <small style="color:red;">
                <b>Mohon cek kembali data yang terisi otomatis!</b>
                <br>
                <span style="color:black;">Data terisi otomatis hanya mencakup</span>
                <div class="row" style="color:black;">
                    <div class="col-md-3">
                        <ul>
                            <li>UID</li>
                            <li>Nama UMKM</li>
                            <li>Alamat</li>
                            <li>Kode POS</li>
                            <li>Kota</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <ul>
                            <li>Provinsi</li>
                            <li>No Telp</li>
                            <li>No HP</li>
                            <li>Email</li>
                            <li>NPWP</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <ul>
                            <li>Nama Bank</li>
                            <li>Country Bank</li>
                            <li>No Rekening</li>
                            <li>Nama Pemilik Rekening</li>
                        </ul>
                    </div>
                </div>
            </small>
            <hr>
            <div>
                <label for="">UID(Vendor ID) <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="uid" name="uid" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Nama UMKM <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="nama_umkm" name="nama_umkm" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Alamat <span style="color:red">*</span></label>
                <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Type here" required></textarea>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Blok No Kav <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="blok_no_kav" name="blok_no_kav" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Kode POS <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Kota <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="kota" name="kota" placeholder="Type here" required/>
            </div>

            <div>
                <label for="">Provinsi <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">No Telp <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="no_telp" name="no_telp" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">No HP <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="hp" name="hp" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Email <span style="color:red">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Kategori Usaha <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="kategori_usaha" name="kategori_usaha" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($kat_usaha as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>

            <div>
                <label for="">Kegiatan Usaha <span style="color:red">*</span></label>
                <select style="width: 100%;" class="form-control sel2" id="jenis_kegiatan_usaha" name="jenis_kegiatan_usaha" placeholder="Type here" required>
                 <option value="">Pilih</option>
                 <?php foreach($keg_usaha as $v):?>
                    <option value="<?=$v->id?>"><?=$v->name?></option>
                 <?php endforeach;?>
                </select>
                <small style="color:red;">Daftar pilihan berasal dari PADI UMKM</small>
            </div>

            <div>
                <label for="">NPWP <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="npwp" name="npwp" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Nama Bank <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="nama_bank" name="nama_bank" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Country Bank <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="country_bank" name="country_bank" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">No. Rekening <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="no_rekening" name="no_rekening" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Nama Pemeilik Rekening <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="nama_pemilik_rekening" name="nama_pemilik_rekening" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Longitude <span style="color:red">*</span></label>
                <input step="any" type="number" class="form-control" id="longitute" name="longitute" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">Latitude <span style="color:red">*</span></label>
                <input step="any" type="number" class="form-control" id="latitute" name="latitute" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            
            <div>
                <label for="">Total Project(Rp) <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="total_project" name="total_project" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            
            <div>
                <label for="">Total Revenue(Rp) <span style="color:red">*</span></label>
                <input type="number" min="0" class="form-control" id="total_revenue" name="total_revenue" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>

            <div>
                <label for="">On Time Rate(0-100%) <span style="color:red">*</span></label>
                <input min="0" max="100" type="number" class="form-control" id="ontime_rate" name="ontime_rate" placeholder="Type here" required/>
                <small style="color:red;"></small>
            </div>
            
            <div>
                <label for="">Average Rating(0-100%) <span style="color:red">*</span></label>
                <input min="0" max="100" type="number" class="form-control" id="average_rating" name="average_rating" placeholder="Type here" required/>
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
                <a href="<?=site_url('padi_umkm/getFormatDataUMKM')?>" target="_blank" ><i class="fa fa-download"></i> Unduh Format Upload</a></small>
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
        PADI UMKM - Data UMKM
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
                        <th>UID</th>
                        <th>NAMA UMKM</th>
                        <th>ALAMAt</th>
                        <th>BLOK NO KAV</th>
                        <th>KODE POS</th>
                        <th>KOTA</th>
                        <th>PROVINSI</th>
                        <th>NO TELP</th>
                        <th>HP</th>
                        <th>EMAIL</th>
                        <th>KATEGORI USAHA</th>
                        <th>JENIS KEGIATAN USAHA</th>
                        <th>NPWP</th>
                        <th>NAMA BANK</th>
                        <th>COUNTRY BANK</th>
                        <th>NO REKENING</th>
                        <th>NAMA PEMILIK REKENING</th>
                        <th>LONGITUDE</th>
                        <th>LATITTUDE</th>
                        <th>TOTAL PROJECT</th>
                        <th>TOTAL REVENUE</th>
                        <th>ONTIME RATE</th>
                        <th>AVERAGE RATING</th>
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
        var getURL=site_url+'/padi_umkm/'+(filter.dataSource==1?'getListUMKMDB':'getListUMKM');
        $('#f_data_source').change(function(){
            window.location.href=site_url+'app#padi_umkm/list_umkm?f_data_source='+$(this).val();
            window.location.reload();
        });

        //Start Add Form
        var selectedVendor=[];
        $('#btn-add').click(function(){
            selectedVendor=[];
            $('.sel2').val(null).trigger('change');
            $('#form-add')[0].reset();
            $('#vendor_sap_id').html(null);
            $('#form-container').show();
            $('#list-container').hide();
        });
        $('#form-add .cancel').click(function(){
            selectedVendor=[];
            $('.sel2').val(null).trigger('change');
            $('#form-add')[0].reset();
            $('#vendor_sap_id').html(null);
            $('#form-container').hide();
            $('#list-container').show();
            $('#table_data').DataTable().ajax.reload();
        });
        $('#form-add').submit(function(e){
            e.preventDefault();
            var formData=new FormData(this);
            $.ajax({
                url:site_url+'padi_umkm/postDataUMKM',
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
                    //(err);
                    alert('Something went wrong, please try again');
                }
            });
        });

        $('#vendor_sap_id').select2({
            minimumInputLength:3,
            placeholder:'Cari No SAP Vendor/Nama/Email',
            ajax: {
                url: site_url+'/padi_umkm/searchVendor',
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

        $('.sel2').select2();

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
           $('#form-add')[0].reset();
           $('.sel2').val(null).trigger('change');

           if(selectedData!=null){
               $('#uid').val(selectedData.id_sap);
               $('#nama_umkm').val(selectedData.vendor_name);
               $('#alamat').val(selectedData.address);
               $('#blok_no_kac').val(null);
               $('#kode_pos').val(selectedData.pos_code);
               $('#kota').val(selectedData.city_name);
               $('#provinsi').val(selectedData.prov_name);
               $('#no_telp').val(selectedData.phone);
               $('#hp').val(selectedData.phone);
               $('#email').val(selectedData.email);
               $('#npwp').val(selectedData.npwp.replace(/\D/g,''));
               $('#nama_bank').val(selectedData.bank_name);
               $('#country_bank').val('ID');
               $('#no_rekening').val(selectedData.bank_no);
               $('#nama_pemilik_rekening').val(selectedData.bank_owner);
           }

        });

        $('#do-filter').click(function(){
            $('#table_data').DataTable().ajax.reload();
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
                url:site_url+'padi_umkm/readDataUMKMFromExcel',
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
                        var view = `${full.uid}`;
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
                        var view = `${full.alamat}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.blok_no_kav}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.kode_pos??'-'}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.kota}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.provinsi}`;
                        return view;
                    },
                    sortable:false
                },  
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.no_telp??'-'}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.hp??'-'}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.email}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.kategori_usaha}`;
                        var view = `${full.kat_usaha_name}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        // var view = `${full.jenis_kegiatan_usaha}`;
                        var view = `${full.keg_usaha_name}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.npwp}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.nama_bank}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.country_bank}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.no_rekening}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.nama_pemilik_rekening}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.longitute}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.latitute}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `Rp${Number(full.total_project).toLocaleString('id')}`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `Rp${Number(full.total_revenue).toLocaleString('id')}`;;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.ontime_rate}%`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view = `${full.average_rating}%`;
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
                        var view = `<button data-id="${full.uid}" class="btn btn-md btn-danger btn-delete">
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
                            url:site_url+'padi_umkm/deleteDataUMKM',
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
