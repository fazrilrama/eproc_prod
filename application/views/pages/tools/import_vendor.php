<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="card">
    <div class="card-header">
        <h5>Import Vendor</h5>
    </div>
    <div class="card-body">
        <?= form_open(null,['id'=>'form','method'=>'post'])?>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <b for="">Import Dari</b>
                    <select name="import_from" id="import_from" class="form-control">
                        <option value="excel">Microsoft Excel</option>
                    </select>
                </div>
            </div>
            <div class="col-md-8" style="background-color: #fafafa;">
                <p>
                    <b>Pastikan Anda mengikuti petunjuk berikut</b>
                    <ul>
                        <li>Gunakan format yang telah diberikan</li>
                        <li>Untuk membatasi overload sistem, per sesi import hanya diperbolehkan maksimal 1000 data vendor</li>
                        <li>Format dapat didownload <a id="format_import" href="<?=base_url('assets/file/FormatInputBatchDRMKlasterPangan.xlsx?v=1.0.0')?>">DISINI</a></li>
                    </ul>
                </p>
                    <input type="file" class="form-control" name="file_upload" id="file_upload" required>
                    <small style="color:red">Allowed file extension .xlsx with max size 20MB</small>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-lg btn-primary btn-block"><a class="fa fa-download"></a> Import</button>
            </div>
        </div>
        <?= form_close()?>
        
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#form').submit(function(e){
            e.preventDefault();
            var fd=new FormData($(this)[0]);
            var btnSubmit=$(this).find('button[type="submit"]');
            btnSubmit.attr('disabled',1);
            $.ajax({
                url:site_url+'/tools/import_vendor',
                type:'post',
                dataType:'json',
                data:fd,
                processData:false,
                contentType:false,
                success:function(res){
                    alert(res.message);
                    btnSubmit.removeAttr('disabled');
                },
                error:function(xhr,stat,err){
                    alert(err);
                    btnSubmit.removeAttr('disabled');
                }
            });
        });
    });
</script>