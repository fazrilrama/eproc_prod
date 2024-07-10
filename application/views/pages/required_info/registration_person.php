<link rel="stylesheet" href="<?php echo base_url('assets/vendor/timer/default.css') ?>">
<script src="<?php echo base_url('assets/vendor/timer/jquery.syotimer.min.js') ?>"></script>

<div id="timer-countdown"></div>

<form id="form_input" style="padding:10px;">
    <input type="hidden" name="id_user" value="<?php echo $this->session->userdata('user')['id_user']; ?>">
    <input type="hidden" id="action_profile" name="action_profile" value="">
    <input type="hidden" id="action_contact" name="action_contact" value="">
    <input type="hidden" id="action_pic" name="action_pic" value="">
    <input type="hidden" id="action_npwp" name="action_npwp" value="">
    <input type="hidden" id="action_siup" name="action_siup" value="">
    <input type="hidden" id="action_tdp" name="action_tdp" value="">
    <input type="hidden" id="action_nib" name="action_nib" value="">
    <input type="hidden" id="action_akta" name="action_akta" value="">
    <input type="hidden" id="action_bank" name="action_bank" value="">
    <input type="hidden" id="action_ktp" name="action_ktp" value="">
    <input type="hidden" id="action_imb" name="action_imb" value="">
    
    <input type="hidden" id="action_fin_report_1y" name="action_fin_report_1y" value="">

    <input type="hidden" id="action_kebijakan_k3" name="action_kebijakan_k3" value="">
    <input type="hidden" id="action_tanggap_darurat" name="action_tanggap_darurat" value="">
    <input type="hidden" id="action_iso_450001" name="action_iso_450001" value="">
    <input type="hidden" id="action_stuktur_organisasi_k3" name="action_stuktur_organisasi_k3" value="">
    <input type="hidden" id="action_peralatan_k3" name="action_peralatan_k3" value="">

    <input type="hidden" id="action_tdg" name="action_tdg" value="">
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
                    <!-- <div id="company_name_container" class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '5' : '5') ?>">
                        <input maxlength="40" v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" placeholder="Nama Depan" class="form-control personal">
                        
                    </div> -->
                    <div class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '10' : '10') ?>">
                        <input maxlength="0" v-model="form.name.val" data-validation="required" data-validation-error-msg="Nama tidak valid!." type="text" name="name" id="name" placeholder="<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'Nama' : 'Nama') ?>" class="form-control">
                        <small style="color:red;" id="system-name-note"></small>
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
                        <select type="select" onchange="ocCountry(this.value);" id="id_country" name="id_country" class="form-control" data-validation="required" data-validation-error-msg="Negara tidak valid!">
                            <?php
                            foreach ($country as $c) {
                                echo '<option value="' . $c->id . '">' . $c->name . '</option>';
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
                        <select type="select" onchange="ocProvince(this.value);" id="id_country_province" name="id_country_province" class="form-control select2" data-validation="required" data-validation-error-msg="Provinsi tidak valid!">
                            <option value="">Pilih</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label id="" class="col-sm-2 col-form-label">
                        Kota<span style="color:red;">*</span></label>
                    <div class="col-sm-10">
                        <select type="select" id="id_city" name="id_city" class="form-control select2" data-validation="required" data-validation-error-msg="Kota tidak valid!">
                            <option value="">Pilih</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label id="" class="col-sm-2 col-form-label">
                        Alamat<span style="color:red;">*</span>
                    </label>
                    <div class="col-sm-10">
                        <textarea type="textarea" id="address" maxlength="60" class="form-control" placeholder="Ex: Jl. Kalibesar Timur No. 5-7" name="address" data-validation="required length" data-validation-length="max60" data-validation-error-msg="Alamat tidak valid!"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label id="" class="col-sm-2 col-form-label">
                        Kode Pos<span style="color:red;">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" id="pos_code" data-validation-error-msg="Kode Pos tidak valid!." class="form-control input-mask-trigger" placeholder="Ex: 11110" name="pos_code" data-validation="required number length" data-validation-length="max10" data-inputmask="'mask': '99999'" inputmode="verbatim">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Area Kerja<span style="color:red;">*</span></label></label>
                    </div>
                    <div class="col-md-10">
                        <?php $area = $this->db->where('deleted_at is null')->where('capital_city', '1')->get(App_Model::TBL_CITY)->result(); ?>
                        <select data-validation="required" data-validation-error-msg="Area Kerja tidak valid!." multiple name="work_area[]" id="work_area" class="form-control select2">
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

                <div class="form-group row">
                    <label id="" class="col-sm-2 col-form-label">
                        Nilai Proyek Tertinggi
                    </label>
                    <div class="col-sm-10">
                        <input type="text" value="0" id="highest_project_value" class="form-control input-mask-trigger" placeholder="Ex: 1000000000" name="highest_project_value" data-validation="required" data-inputmask-regex="^[0-9]{1,1000}$" inputmode="verbatim" data-validation-error-msg="Nilai Proyek Tertinggi tidak valid!">
                    </div>
                </div>
                <!-- Company Profile
                <div class="row" style="margin-bottom:0px;">
                    <div class="col-md-2">
                        <label for="">Upload Company Profile<span id="req_file_compro" style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <input is-mandatory="true" data-validation-error-msg="File Company Profile tidak valid!" type="file" id="company_profile" name="company_profile" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf" />
                        <span style="color:red">File pdf Maksimal 50MB</span>
                        <a href="" style="display:none;" id="download_file_compro" target="_blank" class="btn btn-sm btn-info">Lihat File</a>

                    </div>
                </div>
 		-->

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
                    <input type="text" id="no_npwp" class="form-control input-mask-trigger" placeholder="NPWP" name="no_npwp" data-validation="required" data-inputmask="'mask': '99.999.999.9-999.999'" inputmode="verbatim">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Upload NPWP<span id="span_req_npwp" style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_npwp" name="attachment_npwp" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_npwp" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_npwp_old" id="attachment_npwp_old" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Upload KTP<span id="span_req_ktp" style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_ktp" name="attachment_ktp" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_ktp" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_ktp_old" id="attachment_ktp_old" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Upload IMB<span id="span_req_imb" style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_imb" name="attachment_imb" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_imb" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_imb_old" id="attachment_imb_old" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Upload TDG<span id="span_req_tdg" style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_tdg" name="attachment_tdg" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_tdg" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_tdg_old" id="attachment_tdg_old" value="">
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
                    <input type="text" id="no_rekening" class="form-control" data-inputmask-regex="^[0-9]{1,15}$" placeholder="No.Rekening" name="no_rekening" data-validation="required" data-validation-error-msg="No.Rekening tidak valid!">
                </div>
            </div>

            <div class="form-group row">
                <label id="" class="col-sm-2 col-form-label">
                    Nama Nasabah<span style="color:red;">*</span>
                </label>
                <div class="col-sm-10">
                    <input type="text" maxlength="40" id="owner" class="form-control" placeholder="Nama Nasabah" name="owner" data-validation="required length" data-validation-length="max40" data-validation-error-msg="Nama Nasabah tidak valid!">
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
                            echo '<option value="' . $b->name . '">' . $b->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div class="row" style="margin-bottom:0px;">
                <div class="col-md-2">
                    <label for="">Buku Tabungan<span id="span_req_bank" style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_bank" name="attachment_bank" class="form-control" data-validation="required mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_bank" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_bank_old" id="attachment_bank_old" value="">
                </div>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
            <div class="card-header">
                <h5 class="card-title">
                    Finansial Report
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Laporan Keuangan 1 Tahun Terakhir <!-- <span id="span_req_fin_report_1y" style="color:red;">*</span>--></label>
                    </div>
                    <div class="col-md-10">
                        <input is-mandatory="false" type="file" id="attachment_fin_report_1y" name="attachment_fin_report_1y" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                        <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                        <a href="" style="display:none;" id="download_file_fin_report_1y" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                        <input type="hidden" name="attachment_fin_report_1y_old" id="attachment_fin_report_1y_old" value="">
                    </div>
                </div>
            </div>
        </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <h5 class="card-title">
                Aspek K3LL
            </h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Kebijakan/Komitmen</label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_kebijakan_k3" name="attachment_kebijakan_k3" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_kebijakan_k3" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_kebijakan_k3_old" id="attachment_kebijakan_k3_old" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Sistem Tanggap Darurat</label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_tanggap_darurat" name="attachment_tanggap_darurat" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_tanggap_darurat" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_tanggap_darurat_old" id="attachment_tanggap_darurat_old" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Sertifikasi ISO 45001/9001 atau dokumen yang relevan lainnya</label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_iso_450001" name="attachment_iso_450001" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_iso_450001" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_iso_450001_old" id="attachment_iso_450001_old" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Struktur Organisasi K3</label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_stuktur_organisasi_k3" name="attachment_stuktur_organisasi_k3" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_stuktur_organisasi_k3" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_stuktur_organisasi_k3_old" id="attachment_stuktur_organisasi_k3_old" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="">Peralatan K3</label>
                </div>
                <div class="col-md-10">
                    <input is-mandatory="true" type="file" id="attachment_peralatan_k3" name="attachment_peralatan_k3" class="form-control" data-validation="mime size" data-validation-max-size="50M" data-validation-allowing="pdf, png, jpeg, jpg" />
                    <span style="color:red">File pdf, png, jpeg, jpg, Maksimal 50MB</span>
                    <a href="" style="display:none;" id="download_file_peralatan_k3" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                    <input type="hidden" name="attachment_peralatan_k3_old" id="attachment_peralatan_k3_old" value="">
                </div>
            </div>
        </div>
                
    </div>

    <div class="card" id="note_verification_container">
        <div class="card-header">
            <h5 class="card-title">
                Catatan Verifikasi
            </h5>
        </div>
        <div class="card-body" id="note_verification">

        </div>
    </div>

    <div class="main-card mb-3 card" id="persetujuan_registrasi">
        <div class="card-header">
            <h5 class="card-title">
                Persetujuan Registrasi
            </h5>
        </div>
        <div class="card-body">
            <ol style="font-size: 1.2em">
                <li>Pastikan data Anda benar dan dapat dipertanggung jawabkan.</li>
                <li><input type="checkbox" id="aggreement" required>Jika data yang Anda masukan adalah tidak benar secara hukum,
                    maka Penyelengara dalam hal ini <b>PT. BGR Logistik Indonesia (Persero)</b> dapat <span style="color: red;">memblokir/menghapus/melakukan tindakan hukum
                        menurut Perundang-undangan yang berlaku.</span> </li>
            </ol>
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Submit</button>
        </div>
    </div>

</form>

<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>
<script>
    var isViewOnly = '<?php echo $this->input->get('viewOnly') ?>' == 'true';
    $(document).ready(function() {
        $("#id_country").trigger('change');
        var idUser = <?php echo isset($id_user) ? $id_user : $this->session->userdata('user')['id_user'] ?>;
        getCompanyProfile(idUser);

        $('#note_verification_container').hide();

        $('#name').keyup(function() {
            var val = $(this).val();
            var currentName = `${$('#name').val()}`;
            var maxLength = 40 - currentName.length;
            $('#name').attr('maxlength', 40);
            $('#system-name-note').html(`Karakter Tersedia ${maxLength} karakter.`);
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
                formData = new FormData($form[0]);
                formData.append('_table', 'company_profile');
                formData.append('id_usr_role', 6);
                var formURL = site_url;
                formURL += 'required_info/edit_data_profile';

                $.ajax({
                    url: formURL,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data, text) {
                        if (data.success) {
                            swal(
                                'Saved!',
                                'Data berhasil disimpan!',
                                'success'
                            );

                        } else {
                            swal(
                                'Saved!',
                                'Data gagal disimpan!',
                                'error'
                            );
                        }
                        globalEvents.dispatch('formSubmit', {
                            success: true,
                            data: data
                        });
                    },
                    error: function(stat, res, err) {
                        globalEvents.dispatch('formSubmit', {
                            success: true,
                            data: {
                                err: err
                            }
                        });
                        alert(err);
                        $.unblockUI();
                    }
                });

                return false;
            }
        });

        getMasterData.getCompanyType(function(stat, res) {
            var type_option = "";
            $.each(res, function(i) {
                type_option += "<option value='" + res[i].id + "'>" + res[i].name + "</option>";
            });
            $("#type").html(type_option);
        });

        if (!isViewOnly) $('.select2').select2();
    });

    function ocPositionType(el) {
        if (el.value == "1") {
            $('#attachment_surat_kuasa').attr('data-validation', 'mime size');
            $('#span_req_surat_kuasa').css('display', 'none');
        } else if (el.value == "2") {
            $('#attachment_surat_kuasa').attr('data-validation', 'required mime size');
            $('#span_req_surat_kuasa').css('display', '');
        }
    }

    function ocCountry(value, item_selected = null) {
        getMasterData.getProvince({
                id_country: (value != '' ? value : -1)
            },
            function(stat, data) {
                if (stat) {
                    var opt = '<option value="">Pilih</option>';
                    data.forEach(function(i) {
                        opt += '<option ' + (item_selected == i.id ? "selected" : "") + ' value="' + i.id + '">' + i.name + '</option>';
                    });
                    $('#id_country_province').html(opt);
                }
            });
    }

    function ocProvince(value, item_selected = null) {
        getMasterData.getCity({
                id_country_province: (value != '' ? value : -1)
            },
            function(stat, data) {
                if (stat) {
                    var opt = '<option value="">Pilih</option>';
                    data.forEach(function(i) {
                        opt += '<option ' + (item_selected == i.id ? "selected" : "") + ' value="' + i.id + '">' + i.name + '</option>';
                    });
                    $('#id_city').html(opt);
                }
            });

    }

    $('#type').change(function() {
        var val = $(this).val();

        if (val.includes('7')) {
            $('#attachment_imb').attr('data-validation', 'required mime size').parent('div').parent('div').show();
            $('#attachment_tdg').attr('data-validation', 'required mime size').parent('div').parent('div').show();
            $('#span_req_imb').parent('label').parent('div').parent('div').show();
            $('#span_req_tdg').parent('label').parent('div').parent('div').show();
        } else {
            $('#attachment_imb').attr('data-validation', '').parent('div').parent('div').hide();
            $('#attachment_tdg').attr('data-validation', '').parent('div').parent('div').hide();
            $('#span_req_imb').parent('label').parent('div').parent('div').hide();
            $('#span_req_tdg').parent('label').parent('div').parent('div').hide();
        }
    });



    function getCompanyProfile(idUser) {
        $.ajax({
            url: site_url + 'profile/get_company_profile',
            type: 'get',
            dataType: 'json',
            data: {
                id_user: idUser
            },
            success: function(res) {
                var profile = res.profile;
                var contact = res.contact;
                var pic = res.pic;
                var npwp = res.npwp;
                var siup = res.siup;
                var tdp = res.tdp;
                var nib = res.nib;
                var akta = res.akta;
                var bank = res.bank;
                var ktp = res.ktp;
                var imb = res.imb;
                var tdg = res.tdg;
                var fin_report_1y = res.fin_report_1y;

                var kebijakan_k3 = res.kebijakan_k3;
                var tanggap_darurat = res.tanggap_darurat;
                var iso_450001 = res.iso_450001;
                var stuktur_organisasi_k3 = res.stuktur_organisasi_k3;
                var peralatan_k3 = res.peralatan_k3;


                var company_types = [];
                res.type.forEach(function(e) {
                    company_types.push(e.id_company_type);
                });
                $('#type').val(company_types).trigger('change');

                if (profile != null && Object.keys(profile).length > 0) {
                    $("#action_profile").val("edit");

                    $("#name").val((profile.prefix_name ? profile.prefix_name + ' ' : '') + profile.name);
                    // $("#prefix_name").val(profile.prefix_name);
                    $("#highest_project_value").val(profile.highest_project_value);
                    $("#id_group").val(profile.id_group);
                    if (profile.company_profile != null) {
                        $('#company_profile').attr('data-validation', 'mime size');
                        $('#req_file_compro').css('display', 'none');
                        $("#download_file_compro").attr('href', site_url + '/upload/company/file/' + profile.company_profile);
                        $("#download_file_compro").css('display', '');
                    } else {
                        $('#company_profile').attr('data-validation', 'required mime size');
                        $('#req_file_compro').css('display', '');
                        $("#download_file_compro").css('display', 'none');
                    }
                } else {
                    $("#action_profile").val("add");
                }

                if (contact != null && Object.keys(contact).length > 0) {
                    $("#action_contact").val("edit");

                    $("#id_country").val(contact.id_country);
                    ocCountry(contact.id_country, contact.id_country_province);
                    ocProvince(contact.id_country_province, contact.id_city);
                    $("#address").val(contact.address);
                    $("#pos_code").val(contact.pos_code);
                    $("#phone").val(contact.phone.replace(/\D/g,''));
                    $("#email").val(contact.email);
                } else {
                    $("#action_contact").val("add");
                }

                if (pic != null && Object.keys(pic).length > 0) {
                    $("#action_pic").val("edit");

                    $("#name_pic").val(pic.name);
                    $("#position_type").val(pic.position_type);
                    $("#position").val(pic.position);
                    $("#mobile_phone").val(pic.mobile_phone.replace(/\D/g,''));
                    $("#email_pic").val(pic.email);
                    if (pic.attachment != null) {
                        $('#attachment_surat_kuasa').attr('data-validation', 'mime size');
                        $('#span_req_surat_kuasa').css('display', 'none');
                        $('#attachment_surat_kuasa_old').val(pic.attachment);
                        $("#download_file_surat_kuasa").attr('href', site_url + '/upload/company/file/' + pic.attachment);
                        $("#download_file_surat_kuasa").css('display', '');
                    } else {
                        $('#attachment_surat_kuasa').attr('data-validation', 'required mime size');
                        $('#span_req_surat_kuasa').css('display', '');
                        $('#attachment_surat_kuasa_old').val('');
                        $("#download_file_surat_kuasa").css('display', 'none');
                    }
                } else {
                    $("#action_pic").val("add");
                }

                if (npwp != null && Object.keys(npwp).length > 0) {
                    $("#action_npwp").val("edit");

                    $("#no_npwp").val(npwp.no);
                    if (npwp.attachment != null) {
                        $('#attachment_npwp').attr('data-validation', 'mime size');
                        // $('#span_req_npwp').css('display', 'none');
                        $('#attachment_npwp_old').val(npwp.attachment);
                        $("#download_file_npwp").attr('href', site_url + '/upload/company/file/' + npwp.attachment);
                        $("#download_file_npwp").css('display', '');
                    } else {
                        $('#attachment_npwp').attr('data-validation', 'required mime size');
                        // $('#span_req_npwp').css('display', '');
                        $('#attachment_npwp_old').val('');
                        $("#download_file_npwp").css('display', 'none');
                    }
                } else {
                    $("#action_npwp").val("add");
                }

                if (siup != null && Object.keys(siup).length > 0) {
                    $("#action_siup").val("edit");
                    if (siup.attachment != null) {
                        $('#attachment_siup').attr('data-validation', 'mime size');
                        $('#span_req_siup').css('display', 'none');
                        $('#attachment_siup_old').val(siup.attachment);
                        $("#download_file_siup").attr('href', site_url + '/upload/company/file/' + siup.attachment);
                        $("#download_file_siup").css('display', '');
                    } else {
                        $('#attachment_siup').attr('data-validation', 'required mime size');
                        $('#span_req_siup').css('display', '');
                        $('#attachment_siup_old').val('');
                        $("#download_file_siup").css('display', 'none');
                    }
                } else {
                    $("#action_siup").val("add");
                }


                if (tdp != null && Object.keys(tdp).length > 0) {
                    $("#action_tdp").val("edit");
                    if (tdp.attachment != null) {
                        $('#attachment_tdp').attr('data-validation', 'mime size');
                        $('#span_req_tdp').css('display', 'none');
                        $('#attachment_tdp_old').val(tdp.attachment);
                        $("#download_file_tdp").attr('href', site_url + '/upload/company/file/' + tdp.attachment);
                        $("#download_file_tdp").css('display', '');
                    } else {
                        $('#attachment_tdp').attr('data-validation', 'required mime size');
                        $('#span_req_tdp').css('display', '');
                        $('#attachment_tdp_old').val('');
                        $("#download_file_tdp").css('display', 'none');
                    }
                } else {
                    $("#action_tdp").val("add");
                }



                if (nib != null && Object.keys(nib).length > 0) {
                    $("#action_nib").val("edit");
                    if (nib.attachment != null) {
                        $('#attachment_nib').attr('data-validation', 'mime size');
                        $('#span_req_nib').css('display', 'none');
                        $('#attachment_nib_old').val(nib.attachment);
                        $("#download_file_nib").attr('href', site_url + '/upload/company/file/' + nib.attachment);
                        $("#download_file_nib").css('display', '');
                    } else {
                        $('#attachment_nib').attr('data-validation', 'required mime size');
                        $('#span_req_nib').css('display', '');
                        $('#attachment_nib_old').val('');
                        $("#download_file_nib").css('display', 'none');
                    }
                } else {
                    $("#action_nib").val("add");
                }

                if (akta != null && Object.keys(akta).length > 0) {
                    $("#action_akta").val("edit");
                    if (akta.attachment != null) {
                        $('#attachment_akta').attr('data-validation', 'mime size');
                        $('#span_req_akta').css('display', 'none');
                        $('#attachment_akta_old').val(akta.attachment);
                        $("#download_file_akta").attr('href', site_url + '/upload/company/file/' + akta.attachment);
                        $("#download_file_akta").css('display', '');
                    } else {
                        $('#attachment_akta').attr('data-validation', 'required mime size');
                        $('#span_req_akta').css('display', '');
                        $('#attachment_akta_old').val('');
                        $("#download_file_akta").css('display', 'none');
                    }
                } else {
                    $("#action_akta").val("add");
                }

                //KTP
                if (ktp != null && Object.keys(ktp).length > 0) {
                    $("#action_ktp").val("edit");
                    if (ktp.attachment != null) {
                        $('#attachment_ktp').attr('data-validation', 'mime size');
                        $('#span_req_ktp').css('display', 'none');
                        $('#attachment_ktp_old').val(ktp.attachment);
                        $("#download_file_ktp").attr('href', site_url + '/upload/company/file/' + ktp.attachment);
                        $("#download_file_ktp").css('display', '');
                    } else {
                        $('#attachment_ktp').attr('data-validation', 'required mime size');
                        $('#span_req_ktp').css('display', '');
                        $('#attachment_ktp_old').val('');
                        $("#download_file_ktp").css('display', 'none');
                    }
                } else {
                    $("#action_ktp").val("add");
                }

                //FIN_Report_1y
                if (fin_report_1y != null && Object.keys(fin_report_1y).length > 0) {
                    $("#action_fin_report_1y").val("edit");
                    if (fin_report_1y.attachment != null) {
                        $('#attachment_fin_report_1y').attr('data-validation', 'mime size');
                        $('#span_req_fin_report_1y').css('display', 'none');
                        $('#attachment_fin_report_1y_old').val(fin_report_1y.attachment);
                        $("#download_file_fin_report_1y").attr('href', site_url + '/upload/company/file/' + fin_report_1y.attachment);
                        $("#download_file_fin_report_1y").css('display', '');
                    } else {
                        $('#attachment_fin_report_1y').attr('data-validation', 'mime size');
                        $('#span_req_fin_report_1y').css('display', '');
                        $('#attachment_fin_report_1y_old').val('');
                        $("#download_file_fin_report_1y").css('display', 'none');
                    }
                } else {
                    $("#action_fin_report_1y").val("add");
                }

                //kebijakan_k3
                if (kebijakan_k3 != null && Object.keys(kebijakan_k3).length > 0) {
                    $("#action_kebijakan_k3").val("edit");
                    if (kebijakan_k3.attachment != null && kebijakan_k3.attachment != "") {
                        $('#attachment_kebijakan_k3').attr('data-validation', 'mime size');
                        $('#attachment_kebijakan_k3_old').val(kebijakan_k3.attachment);
                        $("#download_file_kebijakan_k3").attr('href', site_url + '/upload/company/file/' + kebijakan_k3.attachment);
                        $("#download_file_kebijakan_k3").css('display', '');
                    } else {
                        $('#attachment_kebijakan_k3').attr('data-validation', 'mime size');
                        $('#attachment_kebijakan_k3_old').val('');
                        $("#download_file_kebijakan_k3").css('display', 'none');
                    }
                } else {
                    $("#action_kebijakan_k3").val("add");
                }

                //tanggap_darurat
                if (tanggap_darurat != null && Object.keys(tanggap_darurat).length > 0) {
                    $("#action_tanggap_darurat").val("edit");
                    if (tanggap_darurat.attachment != null && tanggap_darurat.attachment != "") {
                        $('#attachment_tanggap_darurat').attr('data-validation', 'mime size');
                        $('#attachment_tanggap_darurat_old').val(tanggap_darurat.attachment);
                        $("#download_file_tanggap_darurat").attr('href', site_url + '/upload/company/file/' + tanggap_darurat.attachment);
                        $("#download_file_tanggap_darurat").css('display', '');
                    } else {
                        $('#attachment_tanggap_darurat').attr('data-validation', 'mime size');
                        $('#attachment_tanggap_darurat_old').val('');
                        $("#download_file_tanggap_darurat").css('display', 'none');
                    }
                } else {
                    $("#action_tanggap_darurat").val("add");
                }

                //iso_450001
                if (iso_450001 != null && Object.keys(iso_450001).length > 0) {
                    $("#action_iso_450001").val("edit");
                    if (iso_450001.attachment != null && iso_450001.attachment != "") {
                        $('#attachment_iso_450001').attr('data-validation', 'mime size');
                        $('#attachment_iso_450001_old').val(iso_450001.attachment);
                        $("#download_file_iso_450001").attr('href', site_url + '/upload/company/file/' + iso_450001.attachment);
                        $("#download_file_iso_450001").css('display', '');
                    } else {
                        $('#attachment_iso_450001').attr('data-validation', 'mime size');
                        $('#attachment_iso_450001_old').val('');
                        $("#download_file_iso_450001").css('display', 'none');
                    }
                } else {
                    $("#action_iso_450001").val("add");
                }

                //stuktur_organisasi_k3
                if (stuktur_organisasi_k3 != null && Object.keys(stuktur_organisasi_k3).length > 0) {
                    $("#action_stuktur_organisasi_k3").val("edit");
                    if (stuktur_organisasi_k3.attachment != null && stuktur_organisasi_k3.attachment != "") {
                        $('#attachment_stuktur_organisasi_k3').attr('data-validation', 'mime size');
                        $('#attachment_stuktur_organisasi_k3_old').val(stuktur_organisasi_k3.attachment);
                        $("#download_file_stuktur_organisasi_k3").attr('href', site_url + '/upload/company/file/' + stuktur_organisasi_k3.attachment);
                        $("#download_file_stuktur_organisasi_k3").css('display', '');
                    } else {
                        $('#attachment_stuktur_organisasi_k3').attr('data-validation', 'mime size');
                        $('#attachment_stuktur_organisasi_k3_old').val('');
                        $("#download_file_stuktur_organisasi_k3").css('display', 'none');
                    }
                } else {
                    $("#action_stuktur_organisasi_k3").val("add");
                }

                //peralatan_k3
                if (peralatan_k3 != null && Object.keys(peralatan_k3).length > 0) {
                    $("#action_peralatan_k3").val("edit");
                    if (peralatan_k3.attachment != null && peralatan_k3.attachment != "") {
                        $('#attachment_peralatan_k3').attr('data-validation', 'mime size');
                        $('#attachment_peralatan_k3_old').val(peralatan_k3.attachment);
                        $("#download_file_peralatan_k3").attr('href', site_url + '/upload/company/file/' + peralatan_k3.attachment);
                        $("#download_file_peralatan_k3").css('display', '');
                    } else {
                        $('#attachment_peralatan_k3').attr('data-validation', 'mime size');
                        $('#attachment_peralatan_k3_old').val('');
                        $("#download_file_peralatan_k3").css('display', 'none');
                    }
                } else {
                    $("#action_peralatan_k3").val("add");
                }

                //IMB
                if (imb != null && Object.keys(imb).length > 0) {
                    $("#action_imb").val("edit");
                    if (imb.attachment != null) {
                        $('#attachment_imb').attr('data-validation', 'mime size');
                        $('#span_req_imb').css('display', 'none');
                        $('#attachment_imb_old').val(imb.attachment);
                        $("#download_file_imb").attr('href', site_url + '/upload/company/file/' + imb.attachment);
                        $("#download_file_imb").css('display', '');
                    } else {
                        $('#attachment_imb').attr('data-validation', 'required mime size');
                        $('#span_req_imb').css('display', '');
                        $('#attachment_imb_old').val('');
                        $("#download_file_imb").css('display', 'none');
                    }
                } else {
                    $("#action_imb").val("add");
                }

                //TDG
                if (tdg != null && Object.keys(tdg).length > 0) {
                    $("#action_tdg").val("edit");
                    if (tdg.attachment != null) {
                        $('#attachment_tdg').attr('data-validation', 'mime size');
                        $('#span_req_tdg').css('display', 'none');
                        $('#attachment_tdg_old').val(tdg.attachment);
                        $("#download_file_tdg").attr('href', site_url + '/upload/company/file/' + tdg.attachment);
                        $("#download_file_tdg").css('display', '');
                    } else {
                        $('#attachment_tdg').attr('data-validation', 'required mime size');
                        $('#span_req_tdg').css('display', '');
                        $('#attachment_tdg_old').val('');
                        $("#download_file_tdg").css('display', 'none');
                    }
                } else {
                    $("#action_tdg").val("add");
                }



                if (bank != null && Object.keys(bank).length > 0) {
                    $("#action_bank").val("edit");

                    $("#no_rekening").val(bank.no);
                    $("#owner").val(bank.owner);
                    $("#bank_name").val(bank.bank_name);

                    if (bank.attachment != null) {
                        $('#attachment_bank').attr('data-validation', 'mime size');
                        $('#span_req_bank').css('display', 'none');
                        $('#attachment_bank_old').val(bank.attachment);
                        $("#download_file_bank").attr('href', site_url + '/upload/company/file/' + bank.attachment);
                        $("#download_file_bank").css('display', '');
                    } else {
                        $('#attachment_bank').attr('data-validation', 'required mime size');
                        $('#span_req_bank').css('display', '');
                        $('#attachment_bank_old').val('');
                        $("#download_file_bank").css('display', 'none');
                    }
                } else {
                    $("#action_bank").val("add");
                }

                var work_areas = [];
                if (res.work_area != null) {
                    res.work_area.forEach(function(e) {
                        work_areas.push(e.id_city);
                    });
                }
                $('#work_area').val(work_areas).trigger('change');


                var verification_status = `<?php $data = $this->db
                                                ->where('id_user', $this->session->userdata('user')['id_user'])
                                                ->get(App_Model::TBL_COMPANY_PROFILE)
                                                ->row();
                                            echo ($data != null) ? $data->verification_status : ""; ?>`;
                if (verification_status != null && verification_status == 'Rejected') {
                    $('#note_verification_container').show();
                    $('#note_verification').append(`<div>
                        Status Registarasi : <b style="color:red"> ${verification_status} </b>
                        <br/>
                        Catatan Verifikator : <?php $id_company = $this->db
                                                    ->where('id_user', $this->session->userdata('user')['id_user'])
                                                    ->get(App_Model::TBL_COMPANY_PROFILE)
                                                    ->row();

                                                $data = null;
                                                if ($id_company != null) {
                                                    $id_company = $id_company->id;
                                                    $data = $this->db->where('data_id', $id_company)
                                                        ->order_by('created_at desc')
                                                        ->limit(1)
                                                        ->get('verification_history')
                                                        ->row();
                                                }
                                                echo ($data != null) ? $data->verification_note : '-'; ?>
                        <br/>
                    </div>`);
                }

            },
            error: function(xhr, res, err) {

            }
        });
    }
</script>