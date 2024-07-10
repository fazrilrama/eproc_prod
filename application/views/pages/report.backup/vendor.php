
<div class="card">
    <div class="card-header">
        Data Status Vendor
    </div>
    <div class="card-body">
        <b>Filter</b>
        <div class="row">
            <div class="col-md-4">
                <b for="">Cari</b>
                <input placeholder="Kata kunci.." class="form-control filter" type="text" name="search[value]" id="search[value]">
            </div>
            <div class="col-md-4">
                <b for="">Status</b>
                <select name="f_status" id="f_status" class="form-control select2 filter">
                    <option value="">Semua</option>
                    <option value="1">Blacklisted</option>
                    <option value="0">Whitelisted</option>
                </select>
            </div>
            <div class="col-md-4">
                <b for="">Jenis Vendor</b>
                <select name="f_jenis_vendor" id="f_jenis_vendor" class="form-control select2 filter">
                    <option value="">Semua</option>
                    <option value="2">Perusahaan</option>
                    <option value="6">Perseorangan</option>
                </select>
            </div>

            <div class="col-md-4" style="margin-bottom: 5px;">
                <b>Wilayah Kerja</b>
                <select class="form-control select2 filter" name="f_branch" id="f_branch">
                    <?php echo '<option value="">Semua</option>'; ?>
                    <?php
                        $data_bidang = $this->db->where('deleted_at is null')->get('m_branch_code')->result();
                        foreach ($data_bidang as $d) {
                            if( $this->session->userdata('user')['id_usr_role']==1 ){
                                echo '<option value="' . $d->official_code . '">' . $d->name . '</option>';
                            }else{
                                if( $this->session->userdata('user')['id_company_owner']==$d->id_company_owner){
                                    echo '<option value="' . $d->official_code . '">' . $d->name . '</option>';
                                }
                            }
                        }
                    ?>
                </select>
            </div>


            <div class="col-md-4">
                    <b>Bidang Usaha</b>
                        <select class="form-control select2 filter" name="f_bidang" id="f_bidang">
                            <option value="">Semua</option>
                            <?php
                            $data_bidang = $this->db->where('deleted_at is null')->get('m_company_type')->result();
                            foreach ($data_bidang as $d) {
                                echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                            }
                            ?>
                            <option value="">Semua</option>
                        </select>
                </div>

                <?php if( $this->session->userdata('user')['id_company_owner']==1 ):?>
                    <div class="col-md-4" style="margin-bottom: 5px;">
                        <b>Wilayah Kerja</b>
                        <select class="form-control select2 filter" name="f_branch" id="f_branch">
                            <?php
                            $data_bidang = $this->db->where('deleted_at is null')->get('m_branch_code')->result();
                            foreach ($data_bidang as $d) {
                                echo '<option value="' . $d->official_code . '">' . $d->name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                <?php endif;?>

                <div class="col-md-4" style="margin-bottom: 5px;">
                    <b>Kabupaten/Kota</b>
                    <select class="form-control select2 filter" name="f_area_kerja" id="f_area_kerja">
                        <option value="">Semua</option>
                        <?php
                        $data = $this->db->where('deleted_at is null')->get('m_city')->result();
                        foreach ($data as $d) {
                            echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4" style="margin-bottom: 5px;">
                    <b>Kompetensi</b>
                    <select class="form-control select2 filter" name="f_kompetensi" id="f_kompetensi">
                        <option value="">Semua</option>
                        <?php $data = $this->db->where('deleted_at is null')->get(App_Model::TBL_COMPANY_COMPETENCY)->result();
                        foreach ($data as $d) {
                            echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                        } ?>
                    </select>
                </div>


                <div class="col-md-4" style="margin-bottom: 5px;">
                    <b>Sub Kompetensi</b>
                    <select class="form-control select2 filter" name="f_sub_kompetensi" id="f_sub_kompetensi">
                        <option value="">Semua</option>
                    </select>
                </div>

                <div class="col-md-4" style="margin-bottom: 5px;">
                    <b>Pemilik Vendor</b>
                    <select class="form-control filter" name="f_company_owner" id="f_company_owner">
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
                </div>

        </div>

        <hr>
        
        <button id="do-filter" class=" btn btn-md btn-primary">
            <i class="fa fa-filter"></i> Filter
        </button>
        <button id="export-excel" class=" btn btn-md btn-success"><i class="fa fa-file-excel"></i> Export To Excel</button>
        <hr>

        <div class="table-responsive">
            <table id="table_data" class="table table-hover table-borderless table-striped">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th style="min-width:150px;">Nama Vendor</th>
                        <th style="min-width:100px;">Vendor No</th>
                        <th style="min-width:100px;">Pemilik Vendor</th>
                        <th style="min-width:50px;">Grup</th>
                        <th style="min-width:100px;">Email</th>
                        <th style="min-width:100px;">No.Telp</th>
                        <th style="min-width:100px;">NPWP No</th>
                        <th style="min-width:100px">Bidang Usaha</th>
                        <th style="min-width:150px;">Area Kerja</th>
                        <th style="min-width:150px;">Kompetensi</th>
                        <th style="min-width:150px;">Sub Kompetensi</th>
                        <th style="min-width:150px;">Tgl Akun Dibuat</th>
                        <th style="min-width:150px;">Note</th>
                        <th style="min-width:150px;">Nama PIC</th>
                        <th style="min-width:150px;">Telp PIC</th>
                        <th style="min-width:150px;">Alamat</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>

        var doChangeStatus=function(id,currentStatus,note=''){
            $.ajax({
                url:site_url+'data_status_vendor/changeStatus',
                type:'post',
                data:{id,currentStatus,note},
                dataType:'json',
                success:function(res){
                    if(res.success){
                        swal("Information", res.message, "success");
                        $('#table_data').DataTable().ajax.reload();
                    }
                    else{
                        swal("Error", res.message, "error");
                    }
                },
                error:function(xhr,stat,err){
                    swal("Error", err, "error");
                }
            });
            
        }
        var changeStatus=function(id,currentStatus){
            swal({
                title: "Apa Anda Yakin?",
                text: "Jika vendor ter blacklist maka tidak akan muncul pada report dan pemilihan vendor saat pengadaan.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willChange) => {
            if (willChange) {
                swal({
                    text: `Harap tuliskan alasan Vendor di Blacklist/Whitelist`,
                    content: "input",
                    dangerMode: true,
                    button: {
                        text: (currentStatus==1?'Whitelist':'Blacklist')+' Vendor',
                        closeModal: true,
                    },
                })
                .then(note => {
                        if (note){
                            doChangeStatus(id,currentStatus,note);
                        }
                });
            }
            });

            
        }
    $(document).ready(function(){

        let fKompetensi = $('#f_kompetensi');
        fKompetensi.change(function() {
            $.ajax({
                url: site_url + '/master/get_data_sub_competency',
                type: 'GET',
                data: {
                    id_competency: $('#f_kompetensi').val() != "" ? $('#f_kompetensi').val() : '-1'
                },
                dataType: 'json',
                success: function(res) {
                    let opt = `<option value="">Semua</option>`;
                    res.forEach(function(i) {
                        opt += `<option value="${i.id}">${i.name}</option>`;
                    });

                    $('#f_sub_kompetensi').html(opt);
                },
                error: function(res, stat, err) {
                    //console.log(err);
                }
            });
        });

        let fBranch = $('#f_branch');
        fBranch.change(function() {
            let val = $(this).val();
            $.ajax({
                url: site_url + 'report/get_city_per_branch',
                type: 'get',
                dataType: 'json',
                data: {
                    branch: $('#f_branch').val()
                },
                success: function(res) {
                    let opt = `<option value="">Semua</option>`;
                    res.forEach(function(item) {
                        opt += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#f_area_kerja').html(opt);
                },
                error: function(xhr, res, err) {
                    //console.log('error');
                }
            });
        });

        $('.select2').select2();
        $('#do-filter').click(function(){
            $('#table_data').DataTable().ajax.reload();
        });

        $('#export-excel').click(function(){
			var filterData={};
			$('.filter').each(function(){
				filterData[$(this).attr('id')]=$(this).val();
			});
			var filenameMailType=$('#fMailType').val()=='0'?'Mail':$('#fMailType :selected').text();
			var filename=`Report Vendor Eprocurement <?php echo date('Y-m-d H:i')?>`;
			filterData['fileName']=filename;
            filterData['order[0][column]']=$('#table_data').dataTable().fnSettings().aaSorting[0]!=null?$('#table_data').dataTable().fnSettings().aaSorting[0][0]:'-1';
			filterData['order[0][dir]']=$('#table_data').dataTable().fnSettings().aaSorting[0]!=null?$('#table_data').dataTable().fnSettings().aaSorting[0][1]:'desc';
			
			$.ajax({
				url:site_url+'/data_status_vendor/exportExcel',
				dataType:'json',
				data:filterData,
				success:function(res){
					var $a = $("<a>");
					$a.attr("href",res.file);
					$("body").append($a);
					$a.attr("download",`${filename}.xlsx`);
					$a[0].click();
					$a.remove();
				},
				error:function(xhr,stat,err){
					alert(err);
				}
			});
		});

        let dtTable = $('#table_data').DataTable({
            "aaSorting": [],
            "order": [[ 11, "desc" ]],
            "retrieve": true,
            "processing": true,
            "serverSide":true,
            "searching":false,
            'ajax': {
                "type": "GET",
                "url": site_url + 'data_status_vendor/get_data',
                "data": function(d) {
                    $('.filter').each(function(){
                        d[$(this).attr('id')]=$(this).val();
                    });
                },
                "dataSrc": "data"
            },
            'columns': [
                {
                    render: function(data, type, full, meta) {
                        let view = `<span class="badge badge-${full.is_blacklisted==1?'danger':'success'}">${full.blacklist_status_name}</span>`;
                        return view;
                    },
                    sortable:true
                }
                ,{
                    render: function(data, type, full, meta) {
                        let view = `<a href="javascript:void()" id-user="${full.id_user}" data-id='${full.id_company}' class="view_detail" >${full.name + (full.prefix_name?', '+full.prefix_name:'') }</a>`;
                        return view;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        return full.id_sap;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        return full.company_owner_name;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        return full.group_desc.replace('BP ', '');
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        return `Email Kontak:${full.email}<br>Email Login:${full.login_email}`;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        return full.phone;
                    },
                    sortable:true
                }, {
                    render: function(data, type, full, meta) {
                        return full.no_npwp;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        var view=`-`;
                        if(full.company_types!=null){
                            view=`<ul>`;
                            full.company_types.split('||').forEach(function(item){
                                view+=`<li>${item}</li>`;
                            });
                            view+=`</ul>`;
                        }
                        return `<div style="max-width:200px;max-height:150px;overflow:scroll;">${view}</div>`;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        var view=`-`;
                        if(full.work_area!=null){
                            view=`<ul>`;
                            full.work_area.split('||').forEach(function(item){
                                view+=`<li>${item}</li>`;
                            });
                            view+=`</ul>`;
                        }
                        return `<div style="max-width:200px;max-height:150px;overflow:scroll;">${view}</div>`;
                    },
                    sortable:false
                },

                {
                    render: function(data, type, full, meta) {
                        var view=`-`;
                        if(full.competencies_name!=null){
                            view=`<ul>`;
                            full.competencies_name.split('||').forEach(function(item){
                                view+=`<li>${item}</li>`;
                            });
                            view+=`</ul>`;
                        }
                        return `<div style="max-width:200px;max-height:150px;overflow:scroll;">${view}</div>`;
                    },
                    sortable:false
                }, {
                    render: function(data, type, full, meta) {
                        var view=`-`;
                        if(full.sub_competencies_name!=null){
                            view=`<ul>`;
                            full.sub_competencies_name.split('||').forEach(function(item){
                                view+=`<li>${item}</li>`;
                            });
                            view+=`</ul>`;
                        }
                        return `<div style="max-width:200px;max-height:150px;overflow:scroll;">${view}</div>`;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        let view = full.created_at;
                        return view;
                    },
                    sortable:true
                },
                {
                    render: function(data, type, full, meta) {
                        let view = `<div style="max-width:150px;max-height:150px;overflow:scroll;">${full.blacklist_note==null || full.blacklist_note=="null"?'-':full.blacklist_note}</div>`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        let view = `<div style="max-width:150px;max-height:150px;overflow:scroll;">${full.pic_name==null || full.pic_name=="null"?`${full.name + (full.prefix_name?', '+full.prefix_name:'') }`:full.pic_name}</div>`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        let view = `<div style="max-width:150px;max-height:150px;overflow:scroll;">${full.pic_mobile_phone==null || full.pic_mobile_phone=="null"?'-':full.pic_mobile_phone}</div>`;
                        return view;
                    },
                    sortable:false
                },
                {
                    render: function(data, type, full, meta) {
                        let view = `<div style="max-width:150px;max-height:150px;overflow:scroll;">${full.address==null || full.address=="null"?'-':full.address}</div>`;
                        return view;
                    },
                    sortable:false
                }

            ],
            "responsive": false,
            drawCallback: function(setting) {
                $('.view_detail').click(function() {
                    let id = $(this).attr('data-id');
                    let id_user = $(this).attr('id-user');
                    viewDetailVendor(id, id_user);
                });
            },
            dom: 'Bflrtip',
            buttons: [],
        });
    });
</script>