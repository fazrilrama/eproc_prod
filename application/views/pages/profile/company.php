<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="main-card mb-3 card" id="main_content">
    <div class="card-header">
        <h5 class="card-title"><?php echo $page_title_label ?>
        </h5>
    </div>
    <form id="form_input" style="padding:10px;">
        <div class="card-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="form-group row" id="id_user_container">
                <div class="col-md-2">
                    <label for="">Customer/Vendor<span style="color:red;">*</span></label>
                </div>
                <div class="col-md-10">
                    <select data-validation="required" data-validation-error-msg="User tidak valid!." name="id_user" id="id_user" class="form-control select2">
                        <option value="">Pilih</option>
                        <option v-for="u in form.id_user.data" v-bind:value="u.id_user">{{u.name}} - {{u.role_name}}</option>
                    </select>
                </div>
            </div>

            <div id="form-fields">
                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Nama Perusahaan<span style="color:red;">*</span></label>
                    </div>
                    <div id="company_name_container" class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '5' : '5') ?>">
                        <?php if ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL) { ?>
                            <input v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" placeholder="Nama Depan" class="form-control personal">
                        <?php } else { ?>
                            <select v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" class="form-control non_personal">
                                <option value="">Pilih</option>
                                <option value="Perseorangan">Perusahaan perseorangan</option>
                                <option value="Firma">Firma</option>
                                <option value="CV">CV (Persekutuan Komanditer)</option>
                                <option value="PT">PT (Perseroan Terbatas)</option>
                                <option value="Persero">Persero (Perseroan Terbatas Negara)</option>
                                <option value="PD">PD (Perusahaan Daerah)</option>
                                <option value="Perum">Perum (Perusahaan Negara Umum)</option>
                                <option value="Perjan">Perjan (Perusahaan Negara Jawatan)</option>
                                <option value="Koperasi">Koperasi</option>
                                <option value="Yayasan">Yayasan</option>
                            </select>
                        <?php } ?>

                    </div>
                    <div class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '5' : '5') ?>">
                        <input v-model="form.name.val" data-validation="required" data-validation-error-msg="Nama tidak valid!." type="text" name="name" id="name" placeholder="<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'Nama Belakang' : 'Nama Perusahaan') ?>" id="prefix_name" class="form-control">
                    </div>
                    <!-- <div class="col-md-<?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? '0' : '2') ?>" <?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL || $this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR ? 'hidden' : null) ?>>
                        <input v-model="form.postfix_name.val" type="text" name="postfix_name" id="postfix_name" placeholder="Nama Belakang" class="form-control">
                    </div> -->
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Bidang Usaha<span style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <select data-validation="required" data-validation-error-msg="Tipe perusahaan tidak valid!." multiple name="type[]" id="type" class="form-control select2">
                            <option v-for="u in form.company_type.data" :value="u.id">{{u.name}}</option>
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

                <div class="form-group row" <?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'hidden' : null) ?>>
                    <div class="col-md-2">
                        <label for="">Modal Dasar</label>
                    </div>
                    <div class="col-md-10">
                        <input data-inputmask="'alias': 'numeric', 'groupSeparator': '.', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': 'Rp ', 'placeholder': '0'" class="form-control input-mask-trigger" type="text" id="authorized_capital" name="authorized_capital" data-validation="required" data-validation-error-msg="Modal perusahaan wajib diisi!.">
                    </div>
                </div>

                <div class="form-group row" <?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'hidden' : null) ?>>
                    <div class="col-md-2">
                        <label for="">Modal Dibayar</label>
                    </div>
                    <div class="col-md-10">
                        <input data-inputmask="'alias': 'numeric', 'groupSeparator': '.', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': 'Rp ', 'placeholder': '0'" class="form-control input-mask-trigger" type="text" id="paid_up_capital" name="paid_up_capital" data-validation="required" data-validation-error-msg="Modal dibayar wajib diisi!.">
                    </div>
                </div>


                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Deskripsi Singkat</label>
                    </div>
                    <div class="col-md-10">
                        <textarea v-model="form.description.val" class="form-control" name="description" id="description" cols="3" rows="3"></textarea>
                    </div>
                </div>

                <!-- Logo -->
                <div class="row" style="margin-bottom:0px;" <?php echo ($this->session->userdata('user')['id_usr_role'] == App_Model::ROLE_VENDOR_PERSONAL ? 'hidden' : 'hidden') ?>>
                    <div class="col-md-2">
                        <label for="">Logo</label>
                    </div>
                    <div class="col-md-10">
                        <div id="singleupload">
                        </div>

                        <div v-for="logo in form.logo.val">
                            <img :src="logo.download_link" alt="" max-width="40%" srcset="">
                        </div>
                    </div>
                </div>
                <br>
                <!-- Company Profile -->
                <div class="row" style="margin-bottom:0px;">
                    <div class="col-md-2">
                        <label for="">File Profil Perusahaan<span style="color:red;">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <div id="singleupload1">
                        </div>
                        <span style="color:red">File pdf,jpg,jpeg,jpg, Ukuran Maksimal 50MB</span>
                        <div v-for="company_profile in form.company_profile.val">
                            <a target="blank" :href="company_profile.download_link"><i class="fa fa-download"></i> Uploaded Vendor Profile File</a>
                        </div>
                    </div>
                </div>
                <br>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Status Verifikasi<span style="color:red;">*</span></label>
                    </div>

                    <div v-if="user.role_id==1 || user.role_id==5" class="col-md-10">
                        <select v-model="form.verification_status.val" class="form-control" name="verification_status" id="verification_status">
                            <option value="Pending Verification">Pending Verification</option>
                            <option value="Verified">Verfied</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div v-else class="col-md-10">
                        <input type="text" disabled v-model="form.verification_status.val" class="form-control" name="verification_status" id="verification_status" />
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="">Catatan Verifikasi<span style="color:red;">*</span></label>
                    </div>
                    <div v-if="user.role_id==1 || user.role_id==5" class="col-md-10">
                        <textarea v-model="form.verification_note.val" class="form-control" id="verification_note" name="verification_note"></textarea>
                    </div>
                    <div v-else class="col-md-10">
                        <textarea v-model="form.verification_note.val" readonly class="form-control" id="verification_note" name="verification_note"></textarea>
                    </div>
                </div>

            </div>



        </div>

        <div class="card-footer">
            <div class="text-right">
                <button class="btn btn-info" type="reset"><i class="fa fa-retweet"></i> Reset</button>
                <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </div>
    </form>
</div>

<script>
    new Vue({
        el: '#main_content',
        data: {
            user: user,
            form: {
                isEdit: false,
                id_user: {
                    val: '',
                    data: []
                },
                prefix_name: {
                    val: ''
                },
                name: {
                    val: ''
                },
                postfix_name: {
                    val: ''
                },
                company_type: {
                    val: [],
                    data: []
                },
                description: {
                    val: ''
                },
                id_group: {
                    val: 4
                },
                logo: {
                    val: []
                },
                company_profile: {
                    val: []
                },
                verification_status: {
                    val: 'Pending Verification'
                },
                verification_note: {
                    val: null
                },
                authorized_capital: 0,
                paid_up_capital: 0
            }
        },
        mounted: function() {
            let formData = new FormData();
            $('.select2').select2();
            var context = this;
            getMasterData.getUser(function(stat, res) {
                context.form.id_user.data = res.result;
            });
            getMasterData.getCompanyType(function(stat, res) {
                context.form.company_type.data = res;
            });

            var uploadObj = $("#singleupload").uploadFile({
                url: site_url + 'file_manage/upload/logo',
                dynamicFormData: function() {
                    return postDataWithCsrf.data({
                        fieldName: 'logo'
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
                fileName: "logo",
                allowedTypes: "png,jpg,jpeg",
                uploadStr: "<i class='fa fa-upload'></i> Upload Logo",
                onSuccess: function(files, data, xhr, pd) {
                    data = JSON.parse(data);
                    if (data[0] != null) {
                        let file_name = data[0].file_name;
                        formData.append('logo', file_name);
                        context.doSubmit(formData);
                    }
                    uploadObj.reset();
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
                    context.deleteLogo(file.file_name, function(res) {
                        //console.log(res);
                    });
                }
            });
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
                    context.deleteFile(file.file_name, function(res) {
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

            $('#form-fields').hide();
            $('#id_user').change(function() {
                let val = $(this).val();
                if (val != "" && val != null) {
                    $('#form-fields').show();
                } else {
                    $('#form-fields').hide();
                }
                context.getDataByID($(this).val(),
                    function(res) {
                        context.form.isEdit = false;
                        if (res.profile != null) {
                            context.form.isEdit = true;
                            context.form.prefix_name.val = res.profile.prefix_name;
                            context.form.name.val = res.profile.name;
                            context.form.postfix_name.val = res.profile.postfix_name;
                            context.form.description.val = res.profile.description;
                            context.form.id_group.val = res.profile.id_group;
                            context.form.logo.val = [];
                            if (res.profile.logo != null) context.form.logo.val.push(res.profile.logo);
                            context.form.authorized_capital = res.profile.authorized_capital;
                            context.form.paid_up_capital = res.profile.paid_up_capital;
                            context.form.verification_status.val = res.profile.verification_status;
                            context.form.company_profile.val = [];
                            if (res.profile.company_profile != null) context.form.company_profile.val.push(res.profile.company_profile);
                            context.form.verification_note.val = res.profile.verification_note;
                            $('#authorized_capital').val(res.profile.authorized_capital);
                            $('#paid_up_capital').val(res.profile.paid_up_capital);

                            if (user.role_id == 1) {

                                if (res.profile.role_name == 'Vendor (Perseorangan)') {
                                    $('#company_name_container').html(`<input v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" placeholder="Nama Depan" class="form-control personal">`);
                                } else {
                                    $('#company_name_container').html(`
                                <select value="" v-model="form.prefix_name.val" data-validation="required" data-validation-error-msg="Prefix tidak valid!." type="text" name="prefix_name" id="prefix_name" class="form-control non_personal">
                                    <option value="">Pilih</option>
                                    <option value="Perseorangan">Perusahaan perseorangan</option>
                                    <option value="Firma">Firma</option>
                                    <option value="CV">CV (Persekutuan Komanditer)</option>
                                    <option value="PT">PT (Perseroan Terbatas)</option>
                                    <option value="Persero">Persero (Perseroan Terbatas Negara)</option>
                                    <option value="PD">PD (Perusahaan Daerah)</option>
                                    <option value="Perum">Perum (Perusahaan Negara Umum)</option>
                                    <option value="Perjan">Perjan (Perusahaan Negara Jawatan)</option>
                                    <option value="Koperasi">Koperasi</option>
                                    <option value="Yayasan">Yayasan</option>
                                </select>
                                `);
                                }
                                context.form.prefix_name.val = res.profile.prefix_name;
                                $('#prefix_name').val(res.profile.prefix_name);
                            }

                        } else {
                            context.form.prefix_name.val = null;
                            context.form.name.val = null;
                            context.form.postfix_name.val = null;
                            context.form.logo.val = [];
                            context.form.description.val = null;
                            context.form.id_group.val = 4;
                            context.form.authorized_capital = 0;
                            context.form.paid_up_capital = 0;
                            context.form.verification_status.val = 'Pending Verification';
                            context.form.company_profile.val = [];
                            context.form.verification_note.val = null;
                            $('#authorized_capital').val(0);
                            $('#paid_up_capital').val(0);
                        }

                        let company_types = [];
                        res.type.forEach(function(e) {
                            company_types.push(e.id_company_type);
                        });
                        context.form.company_type.val = company_types;
                        $('#type').val(company_types).trigger('change');
                    });
            });

            this.$nextTick(function() {
                if (user.role_id != 1 && user.role_id != 5) {
                    $('#id_user').val(user.id_user).trigger('change');
                    $('#id_user_container').hide();
                } else if (user.role_id == 5) {
                    $('input').attr('disabled', 'disabled');
                    $('textarea').attr('disabled', 'disabled');
                    $('select').attr('disabled', 'disabled');
                    $('#id_user').removeAttr('disabled');
                    $('#verification_status').removeAttr('disabled');
                    $('#verification_note').removeAttr('disabled');
                    $('.ajax-file-upload-container').remove();
                    $('#singleupload1').remove();
                    $('#singleupload').remove();
                }

            });



        },
        methods: {
            doSubmit: function(form) {

                form.append('_table', 'company_profile');
                // for (var p of form) {
                //     console.log(p[0] + ":" + p[1]);
                // }

                let context = this;
                let formURL = site_url;
                formURL += (this.form.isEdit == false) ? 'profile/add_data_profile' : 'profile/edit_data_profile';

                $.ajax({
                    url: formURL,
                    type: 'post',
                    data: form,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data, text) {
                        var caption = (context.form.isEdit == false) ? 'input' : 'edit';
                        if (data.success) {
                            swal(
                                'Saved!',
                                'Successful ' + caption + ' data!',
                                'success'
                            );

                        } else {
                            swal(
                                'Saved!',
                                'Failed ' + caption + ' data!',
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
                    }
                });
            },
            getDataByID: function(id, onSuccess, onError) {
                $.ajax({
                    url: site_url + 'profile/get_company_profile',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        id_user: id
                    },
                    success: function(res) {
                        if (onSuccess != null) onSuccess(res);
                    },
                    error: function(xhr, res, err) {
                        if (onError != null) onError(xhr, res, err);
                    }
                })
            },
            deleteLogo: function(file_name, onSuccess = null, onError = null) {
                $.ajax({
                    url: site_url + 'file_manage/delete/logo',
                    type: 'post',
                    data: postDataWithCsrf.data({
                        filename: file_name
                    }),
                    dataType: 'json',
                    success: function(res) {
                        if (onSuccess != null) onSuccess(res);
                    },
                    error: function(err) {
                        if (onError != null) onError(res);
                    }
                })
            },
            deleteFile: function(file_name, onSuccess = null, onError = null) {
                $.ajax({
                    url: site_url + 'file_manage/delete/file',
                    type: 'post',
                    data: postDataWithCsrf.data({
                        filename: file_name
                    }),
                    dataType: 'json',
                    success: function(res) {
                        if (onSuccess != null) onSuccess(res);
                    },
                    error: function(err) {
                        if (onError != null) onError(res);
                    }
                })
            }
        }
    });
</script>