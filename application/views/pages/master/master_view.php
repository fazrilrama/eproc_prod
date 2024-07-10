<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="card" id="table-container">
    <div class="card-body">
        <div class="card-header mb-1">
            <h5 class="card-title"><?php echo $header_title ?>
                <?php if (!isset($action_add) || (isset($action_add) && $action_add == 'enabled')) { ?>
                    <button title="Add New" id="add-btn" style="margin-left:5px;border-radius:25px;" title="Add/Edit Data" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i>
                    </button>
                <?php } ?>

                <?php if (!isset($action_edit) || (isset($action_edit) && $action_edit == 'enabled')) { ?>
                    <button title="Edit Data" id="edit-btn" style="margin-left:5px;border-radius:25px;" title="Add/Edit Data" class="btn btn-sm btn-success">
                        <i class="fa fa-edit"></i>
                    </button>
                <?php } ?>

                <?php if (!isset($action_delete) || (isset($action_delete) && $action_delete == 'enabled')) { ?>
                    <button title="Hapus Data" id="delete-btn" style="margin-left:5px;border-radius:25px;" title="Add/Edit Data" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                <?php } ?>

                <?php echo isset($header_extra_content) ? $header_extra_content : null ?>
            </h5>
        </div>
        <div class="table-responsive">
            <label for="" id="selected_id"></label>
            <table id="table-data" style="max-width:80em" class="table table-striped table-bordered nowrap">
                <thead>
                    <tr>
                        <?php foreach ($table_header as $h) { ?>
                            <th><?php echo ucwords(str_replace('_', ' ', $h)) ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="main-card mb-3 card" id="form-container">
    <div class="card-header mb-1">
        <h5 class="card-title" id="form-label"><?php echo $header_title ?></h5>
    </div>

    <form id="form" method="post">
        <div class="card-body">
            <div>
                <input type="hidden" id="csrf_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="text" class="form-control" name="<?php echo $data_key ?>" id="<?php echo $data_key ?>" hidden>
                <input type="text" name="_table" id="_table" value="<?php echo $data_table ?>" hidden>
            </div>

            <?php
            if (isset($form)) {
                foreach ($form as $f) {
            ?>
                    <div class="form-group row">
                        <label id="" class="col-sm-2 col-form-label">
                            <?php echo $f['label']['text']; ?>
                        </label>
                        <div class="col-sm-10">
                            <?php echo $f['input_field']['html']; ?>
                        </div>
                    </div>

            <?php }
            } ?>

            <div class="form-group">
                <label class="col-form-label" style="color:#ff3333;">Required (*)</span>
                </label>
                <br />
                <br />
                <label for="">
                    <?php if (isset($form_note)) echo $form_note; ?></label>
            </div>
        </div>
        <div class="card-footer" style="display:block !important;">
            <?php if (isset($card_footer)) echo $card_footer; ?>
            <div class="row">
                <div class="col-md-12" style="text-align:right !important;">
                    <button type="button" id="cancel" class="btn btn-danger"> <i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary"> <i class="fa fa-paper-plane"></i> Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    /*Avaiable callbacks
        onSwitchView
        onResetParam
        onAdd
        onGetEdit
        onDelete
        onSubmit*/
    var callbacks = {
        onSwitchView: null,
        onResetParam: null,
        onAdd: null,
        onGetEdit: null,
        onDelete: null,
        onSubmit: null,
        onTableRedraw: null
    };

    var dataKey = '<?php echo $data_key ?>';
    var dataTableName = '<?php echo $data_table ?>';
    var selectedID = null;
    var isForVerification = ('<?php echo isset($is_for_verification) ? $is_for_verification : 'false'; ?>' == 'true');

    var dtTable = null;
    setSelectedID = function(id) {
        this.selectedID = id;
        if (dtTable != null) dtTable.rows('.selected').deselect();
    }.bind(this);

    $(document).ready(function() {
        var title = '<?php echo $header_title ?>';
        var isEdit = false;
        var formContainer = $('#form-container');
        var tableContainer = $('#table-container');
        var table = $('#table-data');
        var addURL = '<?php echo $add_url ?>';
        var getURL = '<?php echo $get_url ?>';
        var updateURL = '<?php echo $update_url ?>';
        var deleteURL = '<?php echo $delete_url ?>';
        var fields = '<?php echo $table_header_arr ?>';
        var columns = [];
        var modalLabel = $('#form-label');
        var form = $('#form');
        var textEditor = null;
        let full = null;
        var renderColumnModifier = <?php echo isset($render_column_modifier) ? $render_column_modifier : 'null'; ?>;
        // if (renderColumnModifier != '') renderColumnModifier = JSON.parse(renderColumnModifier);



        function doSubmit(form) {
            var url_form = (isEdit == false) ? site_url + addURL : site_url + updateURL
            var formData = new FormData(form[0]);

            // for (var p of formData) {
            //     console.log(p[0] + ":" + p[1]);
            // }

            $.ajax({
                url: url_form,
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data, text) {
                    var caption = (isEdit == false) ? 'input' : 'edit';
                    let failMsg = 'Failed ' + caption + ' data!';
                    if (data.success) {
                        swal(
                            'Saved!',
                            'Successful ' + caption + ' data!',
                            'success'
                        );
                        $('#table-data').DataTable().ajax.reload();
                        $('#cancel').click();

                    } else {
                        if (data.result != null) failMsg = data.result;
                        swal(
                            'Failed!',
                            failMsg,
                            'error'
                        );
                        $('#table-data').DataTable().ajax.reload();
                        $('#cancel').click();
                    }

                    if (callbacks.onSubmit != null) callbacks.onSubmit(true, data);

                    globalEvents.dispatch('formSubmit', {
                        success: true,
                        data: data
                    });
                    isEdit = false;
                },
                error: function(stat, res, err) {
                    // alert(err);
                    swal(
                            'Failed!',
                            'Something went wrong, please try again',
                            'error'
                        );
                    if (callbacks.onSubmit != null) callbacks.onSubmit(false, err);
                    globalEvents.dispatch('formSubmit', {
                        success: true,
                        data: {
                            err: err
                        }
                    });
                    isEdit = false;
                }
            });
        };

        if (fields.includes(',')) {
            var fieldArray = fields.split(',');
        } else {
            var fieldArray = [];
            fieldArray.push(fields);
        }
        fieldArray.forEach(function(e) {
            columns.push({
                render: function(data, type, full, meta) {
                    if (renderColumnModifier != null && renderColumnModifier[e] != null) {
                        if (full[e] != null) {
                            if (renderColumnModifier[e].condition != null) {
                                let tempVal = full[e].replace(' ', '_').toLowerCase();
                                Object.keys(renderColumnModifier[e].condition).forEach(function(key) {
                                    if (tempVal == key) {
                                        renderColumnModifier[e].render = renderColumnModifier[e].condition[key];
                                    }
                                });
                            }

                            if (typeof renderColumnModifier[e].render === "string") {
                                return renderColumnModifier[e].render.replace('{val}', full[e]);
                            } else {
                                return renderColumnModifier[e].render(full);
                            }
                        } else {
                            return '-';
                        }
                    } else {
                        return full[e];
                    }
                }
            });
        });

        var initiateModule = function() {

            tinymce.remove();
            tinymce.init({
                selector: '.text-editor-master',
                menubar: false,
                setup: function(ed) {
                    textEditor = ed;
                    ed.on('change', function() {
                        ed.save();
                    });
                },
                mobile: {
                    theme: 'silver'
                },
                branding: false,
                height: 300,
                plugins: 'preview powerpaste wordcount',
                image_advtab: true,
                force_br_newlines: true,
                force_p_newlines: false,
                forced_root_block: '',
                templates: [],
                fontsize_formats: "8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 36pt",
                content_css: [base_url + 'assets/vendor/tinymce/skins/content/default/content.min.css', base_url + 'assets/vendor/tinymce/skins/content/adinda/custom-content.css']
            });
            tinymce.init({
                selector: '.text-editor-master-full',
                menubar: true,
                setup: function(ed) {
                    textEditor = ed;
                    ed.on('change', function() {
                        ed.save();
                    });
                },
                mobile: {
                    theme: 'silver'
                },
                plugins: 'print powerpaste preview searchreplace autolink directionality visualblocks visualchars image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
                toolbar1: 'sizeselect | fontselect |  fontsizeselect | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
                branding: false,
                height: 400,
                image_advtab: true,
                force_br_newlines: true,
                force_p_newlines: false,
                forced_root_block: '',
                templates: [],
                fontsize_formats: "8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 36pt",
                content_css: [base_url + 'assets/vendor/tinymce/skins/content/default/content.min.css', base_url + 'assets/vendor/tinymce/skins/content/adinda/custom-content.css']
            });

            $.validate({
                form: '#form',
                validateOnBlur: false, // disable validation when input looses focus
                errorMessagePosition: 'top', // Instead of 'inline' which is default
                scrollToTopOnError: true, // Set this property to true on longer forms
                modules: 'location, date, security, file',
                onModulesLoaded: function() {},
                onError: function($form) {
                    event.preventDefault();
                },
                onSuccess: function($form) {
                    event.preventDefault();
                    doSubmit($form);
                    return true;
                }
            });

            $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            $('.select2').select2();
        };

        initiateModule();

        let isSSP = '<?php echo isset($ssp) ? $ssp : 'false' ?>' == 'true';
        let sspURL = '<?php echo isset($ssp_url) ? $ssp_url : null ?>';

        dtTable = table.DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {
                no = 0;
            },
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'excel',
                },
                // {
                //     extend: 'pdf',
                //     filename: function() `Rekap Lemburan ${$('#fwork_area :selected').text()} Tgl ${$('#fstart_date').val()} s/d ${$('#fend_date').val()}`
                // }
            ],
            "responsive": false,
            "select": "single",
            "processing": true,
            "retrieve": true,
            'ajax': {
                "type": "GET",
                "url": site_url + (isSSP ? sspURL : getURL),
                "data": function(d) {
                    d._table = dataTableName;
                },
                "dataSrc": isSSP ? 'data' : ''
            },
            'serverSide': isSSP,
            'columns': columns,
            "drawCallback": function(settings) {
                if (callbacks.onTableRedraw != null) callbacks.onTableRedraw(settings);
            }
        });

        dtTable.on('select', function(e, dt, type, indexes) {
            if (type === 'row') {
                var data = dtTable.rows({
                    selected: true
                }).data();
                selectedID = data[0][dataKey];
            }
        });

        dtTable.on('deselect', function(e, dt, type, indexes) {
            selectedID = null;
        });



        // Modal Section

        function switchView(animateTime = 0) {
            if (callbacks.onSwitchView != null) callbacks.onSwitchView(formContainer.is(':visible'));

            // dtTable.rows('.selected').deselect();
            $('.select2').val(null).trigger('change');
            if (textEditor != null) textEditor.setContent('');

            $('.form-control').each(function(index, eval) {
                var id = $(this).attr('id');
                var type = $(this).attr('type');
                if (id != null && id != '' &&
                    type != 'textarea' &&
                    type != 'select' &&
                    type != 'file') {
                    $(this).val(null);
                } else if (type == 'textarea') {
                    $(this).html(null);
                } else if (type == 'select') {
                    $(this).val(null).trigger('change');
                }
            });

            if (formContainer.is(':visible')) {
                formContainer.hide(animateTime);
                tableContainer.show(animateTime);
            } else {
                formContainer.show(animateTime);
                tableContainer.hide(animateTime);
            }

        }

        function resetParam() {
            isEdit = false;
            // selectedID=null;

            if (callbacks.onResetParam != null) callbacks.onResetParam();
        }

        switchView();
        $('#add-btn').click(function() {
            switchView();
            form[0].reset();
            modalLabel.html('<i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Add New ' + title);
            $('#back').click(function() {
                switchView();
                resetParam();
            });

            if (callbacks.onAdd != null) callbacks.onAdd();
        });

        $('#edit-btn').click(function() {
            if (selectedID != null) {
                switchView();
                modalLabel.html('<i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Edit ' + title);
                $('form')[0].reset();
                edit_data(selectedID);
                $('#back').click(function() {
                    switchView();
                    resetParam();
                });
            } else {
                swal({
                    title: "Edit Data",
                    text: "Please select at least 1 data!",
                    icon: "warning",
                    button: "OK",
                });
            }
        });

        $('#delete-btn').click(function() {
            if (selectedID != null) {
                delete_data(selectedID);
            } else {
                swal({
                    title: "Delete Data",
                    text: "Please select at least 1 data!",
                    icon: "warning",
                    button: "OK",
                });
            }
        });

        $('#cancel').click(function() {
            switchView();
            resetParam();
        });

        //Modal Section

        function edit_data(idData) {
            $.ajax({
                url: site_url + getURL,
                type: 'GET',
                dataType: 'json',
                data: JSON.parse('{"_key":"' + dataKey + '","_table":"' + dataTableName + '","' + dataKey + '":"' + idData + '"}'),
                success: function(data, text) {
                    if (data.length >= 1) {
                        isEdit = true;
                        $('#_table').val();
                        $('.form-control').each(function(index, eval) {
                            var id = $(this).attr('id');
                            var type = $(this).attr('type');
                            if (id != null && id != '' &&
                                type != 'textarea' &&
                                type != 'select' &&
                                type != 'password' &&
                                type != 'file' &&
                                type != 'datetime-local') {
                                $(this).val(null);
                                $(this).val(data[0][id]);
                            } else if (type == 'textarea') {
                                $(this).html(null);
                                $(this).html(data[0][id]);
                                $(this).val(data[0][id]);
                                if (textEditor != null) textEditor.setContent(data[0][id]);
                            } else if (type == 'select') {
                                $(this).val('').trigger('change');
                                $(this).val(data[0][id]).trigger('change');
                            } else if (type == 'datetime-local') {
                                $(this).val(moment(data[0][id]).format('Y-MM-DDThh:mm'));
                            }
                        });
                    }


                    if (callbacks.onGetEdit != null) callbacks.onGetEdit(selectedID, data);
                },
                error: function(stat, res, err) {
                    alert(err);
                }
            })
        }

        function delete_data(idData) {
            // console.log('clicked');
            swal({
                    title: "Are you sure?",
                    text: "This data will be deleted!",
                    icon: "warning",
                    buttons: ['Cancel', 'Yes, delete it.'],
                    dangerMode: true
                })
                .then(function(isDelete) {
                    if (isDelete) {
                        $.ajax({
                            url: site_url + deleteURL,
                            type: 'POST',
                            data: postDataWithCsrf.data(JSON.parse('{"_table":"' + dataTableName + '","' + dataKey + '":"' + idData + '", "_key":"' + dataKey + '"}')),
                            dataType: 'json',
                            async: false,
                            success: function(data) {
                                if (data.success) {
                                    swal(
                                        'Deleted!',
                                        'Successful delete data!',
                                        'success'
                                    );
                                    $('#table-data').DataTable().ajax.reload();
                                    resetParam();
                                    selectedID = null;
                                }

                                if (callbacks.onDelete != null) callbacks.onDelete(selectedID, data);


                                globalEvents.dispatch('formSubmit', {
                                    success: true,
                                    data: data
                                });
                            },
                            error: function(stat, res, err) {
                                $('#table-data').DataTable().ajax.reload();
                                resetParam();
                                selectedID = null;
                                alert(err);
                                //console.log(stat);
                            }
                        });
                    }
                });
        }

    });
</script>

<?php
if (isset($add_scripts)) {
    foreach ($add_scripts as $script) {
        echo '<script src="' . $script . '"></script>';
    }
}
?>