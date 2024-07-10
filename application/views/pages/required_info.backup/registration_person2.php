<link rel="stylesheet" href="<?php echo base_url('assets/vendor/timer/default.css') ?>">
<script src="<?php echo base_url('assets/vendor/timer/jquery.syotimer.min.js') ?>"></script>

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-user text-info">
                </i>
            </div>
            <div>Form Informasi Wajib
                <div class="page-title-subheading">Mohon isi dan submit informasi yang wajib Anda isi.
                </div>
            </div>
        </div>
    </div>
</div>

<div id="timer-countdown"></div>

<form id="form_input" style="padding:10px;">


<div class="main-card mb-3 card">
    <div class="card-header">
        <h5 class="card-title">
            PROFIL PERUSAHAAN
        </h5>
    </div>
    <div class="card-body">
            <div id="form-fields">
                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Nama Lengkap<span style="color:red;">*</span></label>
                    </div>
                    <div id="company_name_container" class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '5' : '5') ?>">
                        <input v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" placeholder="Nama Depan" class="form-control personal">
                    </div>
                    <div class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '5' : '5') ?>">
                        <input v-model="form.name.val" data-validation="required" data-validation-error-msg="Nama tidak valid!." type="text" name="name" id="name" placeholder="<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'Nama Belakang' : 'Nama Perusahaan') ?>" id="prefix_name" class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Bidang Usaha<span style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <select data-validation="required" data-validation-error-msg="Bidang Usaha tidak valid!." multiple name="type[]" id="type" class="form-control select2">
                        </select>
                    </div>
                </div>


                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Kategori Perusahaan<span style="color:red;">*</span></label></label>
                    </div>
                    <div class="col-md-10">
                        <?php $groups = $this->db->where('deleted_at is null')->get(App_Model::TBL_GROUP_VENDOR)->result(); ?>
                        <select v-model="form.id_group.val" class="form-control" name="id_group" id="id_group" cols="3" rows="3">
                            <?php
                            foreach ($groups as $g) {
                                echo '<option value="' . $g->id . '">' . str_replace('BP ', '', $g->description) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Negara<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                        <?php $country = $this->db->where('deleted_at is null')->get(App_Model::TBL_COUNTRY)->result(); ?>
                            <select type="select" id="id_country" name="id_country" class="form-control" data-validation="required" data-validation-error-msg="Negara tidak valid!">
                            <option value="">Pilih</option>
                            <?php
                            foreach ($country as $c) {
                                echo '<option value="' . $c->id . '">'.$c->name.'</option>';
                            }
                            ?>                           
                            </select>                        
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Provinsi<span style="color:red;">*</span>                        
                        </label>
                        <div class="col-sm-10">
                            <select type="select" id="id_country_province" name="id_country_province" class="form-control select2" data-validation="required" data-validation-error-msg="Provinsi tidak valid!"><option value="">Pilih</option></select>                        
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Kota<span style="color:red;">*</span></label>
                        <div class="col-sm-10">
                            <select type="select" id="id_city" name="id_city" class="form-control select2" data-validation="required" data-validation-error-msg="Kota tidak valid!"><option value="">Pilih</option></select>                        
                        </div>
                    </div>

                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Alamat<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                            <textarea type="textarea" id="address" class="form-control" placeholder="Ex: Jl. Kalibesar Timur No. 5-7" name="address" data-validation="required length" data-validation-length="max60" data-validation-error-msg="Alamat tidak valid!"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Kode Pos<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" id="pos_code" class="form-control input-mask-trigger" placeholder="Ex: 11110" name="pos_code" data-validation="required number length" data-validation-length="max10" data-inputmask="'mask': '99999'" inputmode="verbatim">
                        </div>
                    </div>




                    <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Area Kerja<span style="color:red;">*</span></label></label>
                    </div>
                    <div class="col-md-10">
                        <?php $area = $this->db->where('deleted_at is null')->where('capital_city','1')->get(App_Model::TBL_CITY)->result(); ?>
                        <select data-validation="required" data-validation-error-msg="Area Kerja tidak valid!." multiple name="area_kerja[]" id="area_kerja" class="form-control select2">
                            <?php
                            foreach ($area as $area) {
                                echo '<option value="' . $area->id . '">' . $area->name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            No. Telepon<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" id="phone" class="form-control input-mask-trigger" placeholder="Ex: 02123456789" name="phone" data-validation="required" data-inputmask-regex="^[0-9]{1,15}$" inputmode="verbatim" data-validation-error-msg="No Telepon Perusahaan tidak valid!">
                        </div>
                    </div>
                <!-- Company Profile -->
                <div class="row" style="margin-bottom:0px;">
                    <div class="col-md-2">
                        <label for="">Upload Company Profile<span style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <div id="singleupload1">
                        </div>
                        <span style="color:red">File pdf,jpg,jpeg,jpg, Ukuran Maksimal 50MB</span>
                        <!-- <div v-for="company_profile in form.company_profile.val">
                            <a target="blank" :href="company_profile.download_link"><i class="fa fa-download"></i> Uploaded Company Profile File</a>
                        </div> -->
                    </div>
                </div>

            </div>
    </div>
</div>

<div class="main-card mb-3 card">
    <div class="card-header">
        <h5 class="card-title">
            LEGALITAS
        </h5>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label id="" class="col-sm-2 col-form-label">
                No NPWP<span style="color:red;">*</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="no" class="form-control input-mask-trigger" placeholder="NPWP" name="no" data-validation="required" data-inputmask="'mask': '99.999.999.9-999.999'" inputmode="verbatim">
            </div>
        </div>
        
                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Upload NPWP<span style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                        data-validation="required mime size"
                        data-validation-max-size="50M"
                        data-validation-allowing="pdf, png, jpeg, jpg"  />
                        <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    </div>
                </div>
    </div>
</div>

<div class="main-card mb-3 card">
    <div class="card-header">
        <h5 class="card-title">
            AKUN BANK
        </h5>
    </div>
    <div class="card-body">
                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            No.Rekening<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" id="no" class="form-control" placeholder="No.Rekening" name="no" data-validation="required" data-validation-error-msg="No.Rekening tidak valid!">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Nama Nasabah<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" id="owner" class="form-control" placeholder="Nama Nasabah" name="owner" data-validation="required" data-validation-error-msg="Nama Nasabah tidak valid!">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            Nama Bank<span style="color:red;">*</span>
                        </label>
                        <div class="col-sm-10">
                        <?php $bank = $this->db->where('deleted_at is null')->get(App_Model::TBL_BANK)->result(); ?>
                            <select type="select" id="bank_name" name="bank_name" class="form-control" data-validation="required" data-validation-error-msg="Bank tidak valid!">
                                <option value="">Pilih</option>
                                <?php
                                foreach ($bank as $b) {
                                    echo '<option value="' . $b->id . '">'.$b->name.'</option>';
                                }
                                ?>                           
                            </select>
                        </div>
                    </div> 

        
                    <div class="row" style="margin-bottom:0px;">
                        <div class="col-md-2">
                            <label for="">Buku Tabungan<span style="color:red;">*</span></label>
                        </div>
                        <div class="col-md-10">
                        <input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                        data-validation="required mime size"
                        data-validation-max-size="50M"
                        data-validation-allowing="pdf, png, jpeg, jpg"  />
                        <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                        </div>
                    </div>                   
    </div>
</div>

<div class="main-card mb-3 card">
    <div class="card-header">
        <h5 class="card-title">
            Persetujuan Registrasi
        </h5>
    </div>
    <div class="card-body">
                        <ol style="font-size: 1.2em">
                            <li>Pastikan data Anda benar dan dapat dipertanggung jawabkan.</li>
                            <li>Jika data yang Anda masukan adalah tidak benar secara hukum,
                                maka Penyelengara dalam hal ini <b>PT. BGR Logistik Indonesia (Persero)</b> dapat <span style="color: red;">memblokir/menghapus/melakukan tindakan hukum
                                    menurut Perundang-undangan yang berlaku.</span> </li>
                        </ol>

                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Simpan</button>
    </div>
</div>

</form>

<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>
<script src="<?php echo base_url('assets/js/page/company_legal_domicile.js') ?>"></script>
<script>
$(document).ready(function(){
            var uploadObj1 = $("#singleupload1").uploadFile({
                url: site_url + 'file_manage/upload/company_file',
                dynamicFormData: function() {
                    return postDataWithCsrf.data({
                        fieldName: 'company_profile'
                    });
                },
                multiple: false,
                autoSubmit: false,
                showDownload: true,
                showDelete: true,
                showError: true,
                showProgress: true,
                dragDrop: true,
                maxFileCount: 1,
                fileName: "company_profile",
                allowedTypes: "pdf,png,jpg,jpeg",
                uploadStr: "<i class='fa fa-upload'></i> Upload Profile File",
                onSuccess: function(files, data, xhr, pd) {
                    data = JSON.parse(data);
                    if (data[0] != null) {
                        let file_name = data[0].file_name;
                        formData.append('company_profile', file_name);
                        context.doSubmit(formData);
                    }
                    uploadObj1.reset();
                    $('#id_user').trigger('change');
                },
                downloadCallback: function(files, pd) {
                    event.preventDefault();
                    files = JSON.parse(files);
                    let file = files[0];
                    url = file.full_path.replace(fc_path, base_url);
                    window.open(url);
                },
                deleteCallback: function(data, pd) {
                    event.preventDefault();
                    data = JSON.parse(data);
                    let file = data[0];
                    context.deleteFile(file.file_name, (res) => {
                        //console.log(res);
                    });
                }
            });

            $.validate({
                form: '#form_input',
                modules: 'location, date, security, file',
                onModulesLoaded: function() {},
                onError: function($form) {},
                validateOnBlur: false, // disable validation when input looses focus
                errorMessagePosition: 'top', // Instead of 'inline' which is default
                scrollToTopOnError: true, // Set this property to true on longer forms
                onSuccess: function($form) {
                    event.preventDefault();
                    if (context.form.company_profile.val.length <= 0 && uploadObj1.getFileCount() <= 0) {
                        alert('File Profil Wajib Diisi!');
                        return true;
                    } else {

                        formData = new FormData($form[0]);
                        if (uploadObj.getFileCount() > 0) {
                            uploadObj.startUpload();
                        } else if (uploadObj1.getFileCount() > 0) {
                            uploadObj1.startUpload();
                        } else {
                            context.doSubmit(formData);
                        }
                        return false;
                    }
                }
            });

            getMasterData.getCompanyType((stat, res) => {
                var type_option = "";
                $.each(res, function(i) {
                    // console.log(res[i]);
                    type_option+= "<option value='"+res[i].id+"'>"+res[i].name+"</option>";
                });
                $("#type").html(type_option);
            });


    $('#id_country_province').change(function () {
        let value = $(this).val();
        getMasterData.getCity({
            id_country_province: (value != '' ? value : -1)
        },
            (stat, data) => {
                if (stat) {
                    let opt = '<option value="">Pilih</option>';
                    data.forEach((i) => {
                        opt += '<option value="' + i.id + '">' + i.name + '</option>';
                    });
                    $('#id_city').html(opt);
                }
            });
    });

    $('.select2').select2();
});

function ocPositionType(el){
    if (el.value=="1"){
        $('#attachment_surat_kuasa').attr('data-validation', 'mime size');
        $('#span_req_surat_kuasa').css('display','none');
    }else if (el.value=="2"){
        $('#attachment_surat_kuasa').attr('data-validation', 'required mime size');
        $('#span_req_surat_kuasa').css('display','');
    }
}
</script>