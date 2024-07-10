<!-- Modal -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.71.1/dist/L.Control.Locate.min.css" />
<!-- Location Control -->
<script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.71.1/dist/L.Control.Locate.min.js" charset="utf-8"></script>
<?php
$comp=$this->input->get('comp');
$comp=$comp==null?'BLI':$comp;
if($comp!=null){
    $compAttr=$this->db->where('codename',$comp)->get('m_company')->row();
}
?>
<div data-backdrop="static" class="modal fade" id="modalRegisterHome" tabindex="-1" role="dialog" aria-labelledby="modalRegisterHomeTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRegisterHomeTitle">DAFTAR MENJADI MITRA <?=$compAttr->name ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="registerFormHome">
          <div style="display: none;" id="registerFormHomeAlert" class="alert alert-warning alert-dismissible fade show" role="alert">
            <div id="registerFormHomeAlertMsg"></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <input hidden type="text" name="kode_perusahaan" id="kode_perusahaan" value="<?=$compAttr->id?>" >

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>JENIS USAHA</b></div>
            <div class="col-md-10">
              <select required name="bidang_usaha" id="bidang_usaha" class="form-control" style="color: black;">
                <option value="">Pilih</option>
                <?php foreach($this->db->order_by('name','asc')->where('is_for_elmira',1)->get('m_company_type')->result() as $d): ?>
                  <option value="<?=$d->id?>"><?=$d->name?></option>
                <?php endforeach;?>
              </select>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>JENIS KEMITRAAN</b></div>
            <div class="col-md-10">
              <select required name="id_usr_role" id="id_usr_role" class="form-control" style="color: black;">
                <option value="">Pilih</option>
                <option value="2">Perusahaan</option>
                <option value="6">Perseorangan</option>
              </select>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>NAMA</b></div>
            <div class="col-md-10">
              <input required type="text" name="name" id="name" class="form-control" placeholder="Nama">
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>NO.HP</b></div>
            <div class="col-md-10">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><b>62</b></span>
                </div>
                <input class="form-control" type="number" name="no_hp" id="no_hp"
                  placeholder="No.HP">
              </div>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>KATA SANDI</b></div>
            <div class="col-md-10">
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                      <i toggle='.login_regist_pwd' class="fa fa-eye toggle-password"></i>
                    </span>
                  </div>
                  <input class="form-control login_regist_pwd" id="password" type="password" name="password"
                    placeholder="Kata Sandi">
              </div>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>KONFIRMASI KATA SANDI</b></div>
            <div class="col-md-10">
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                      <i toggle='.login_regist_pwd_confirm' class="fa fa-eye toggle-password"></i>
                    </span>
                  </div>
                  <input class="form-control login_regist_pwd_confirm" id="password_confirmation" type="password" name="password_confirmation"
                    placeholder="Konfirmasi Kata Sandi">
              </div>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>PROVINSI</b></div>
            <div class="col-md-10">
              <select required name="id_province" id="id_province" class="form-control" style="color: black;">
                <option value="">Pilih</option>
                <?php foreach($this->db->order_by('name','asc')->get('provinces')->result() as $d): ?>
                  <option value="<?=$d->id?>"><?=$d->name?></option>
                <?php endforeach;?>
              </select>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>KABUPATEN/KOTA</b></div>
            <div class="col-md-10">
              <select required name="id_city" id="id_city" class="form-control" style="color: black;">
                <option value="">Pilih</option>
              </select>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>KECAMATAN</b></div>
            <div class="col-md-10">
              <select required name="id_kec" id="id_kec" class="form-control" style="color: black;">
                <option value="">Pilih</option>
              </select>
            </div>
          </div>

          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-2" style="display: flex;align-items:center;"><b>ALAMAT</b></div>
            <div class="col-md-10">
              <textarea class="form-control" name="address" id="address"></textarea>
              <input type="checkbox" id="getAddressMap"> Alamat dari Peta
            </div>
          </div>

          <input type="text" id="latitude" name="latitude" hidden required>
          <input type="text" id="longitude" name="longitude" hidden required>

          <br>
          <div id="map" style="height : 300px"></div>

          <button hidden type="submit">submit</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submitRegisterFormHome" type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    var mapCenter = [-0.789275, 113.9213257];
    var map = L.map('map', {
            center: mapCenter,
            zoom: 3,
            attributionControl:false,
        });
    var address = $('#address');
    var latitude = $('#latitude');
    var longitude = $('#longitude');
    var currentLocMarker = L.marker(mapCenter).addTo(map);
    var lc = L.control.locate({
      strings: {
        title: "Dapatkan Lokasi Terkini"
      },
      initialZoomLevel: 10,
      locateOptions: {
        enableHighAccuracy: true
      }
    }).addTo(map);
    latitude.val(mapCenter[0]);
    longitude.val(mapCenter[1]);

    var updateCurrentLoc = function(lat, lng, withAddress = false) {
            currentLocMarker.setLatLng([lat, lng]).bindPopup('Lokasi Anda');
            map.setView([lat, lng], 18);
            latitude.val(lat);
            longitude.val(lng);
            if (withAddress) getAddress(lat, lng);
    };
    var getAddress = function(lat, lng) {
            let url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
            address.val('Sedang mengambil data...');
            setTimeout(() => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function(request) {},
                    success: function(res) {
                        if (res.error == null && res.display_name != null) {
                            address.val(res.display_name);
                        } else {
                            address.val(null);
                        }
                    },
                    error: function(xhr, stat, err) {
                        console.log(err);
                        address.val(null);
                    }
                });
            }, 1000);
    }
    var onLocationFound=function(e) {
      var radius = e.accuracy;
      updateCurrentLoc(e.latlng.lat, e.latlng.lng,$('#getAddressMap').is(':checked'));
    }
    var onLocationError=function(e) {
      alert(e.message);
    }
    var getCity=function(idProv=null){
      $('#id_city').attr('disabled',1);
      $('#id_city').html('<option value="">Sedang memuat...</option>');
      $.ajax({
        url:'<?=site_url('home/getCity')?>',
        dataType:'json',
        type:'get',
        data:{idProv},
        success:function(res){
          var opt=`<option value="">Pilih</option>`;
          res.forEach(function(ele){
            opt+=`<option value="${ele.id}">${ele.name}</option>`;
          });
          $('#id_city').html(opt);
        },
        error:function(xhr,stat,err){
          alert(err);
        },
        complete:function(){
          $('#id_city').removeAttr('disabled');
        }
      });
    }
    var getKec=function(idKab=null){
      $('#id_kec').attr('disabled',1);
      $('#id_kec').html('<option value="">Sedang memuat...</option>');
      $.ajax({
        url:'<?=site_url('home/getKecamatan')?>',
        dataType:'json',
        type:'get',
        data:{idKab},
        success:function(res){
          var opt=`<option value="">Pilih</option>`;
          res.forEach(function(ele){
            opt+=`<option value="${ele.id_kec}">${ele.nama}</option>`;
          });
          $('#id_kec').html(opt);
        },
        error:function(xhr,stat,err){
          alert(err);
        },
        complete:function(){
          $('#id_kec').removeAttr('disabled');
        }
      });
    }

    $('#modalRegisterHome').on('shown.bs.modal', function(){
        setTimeout(function() {
            map.invalidateSize();
        }, 1);
    });
    $('#modalRegisterHome').on('hidden.bs.modal', function(){
      $('#registerFormHome')[0].reset();
    });

    $('#id_province').change(function(){
      getCity($(this).val());
      getKec(-1);
    });

    $('#id_city').change(function(){
      getKec($(this).val());
    });

    map.on('locationerror', onLocationError);
    map.on('locationfound', onLocationFound);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: 'Map data &copy; OpenStreetMap contributors',
      noWrap: true
    }).addTo(map);
    map.on('click', function(e) {
        updateCurrentLoc(e.latlng.lat, e.latlng.lng,$('#getAddressMap').is(':checked'));
    });

    $('#registerFormHome').submit(function(e){
      e.preventDefault();
      var fd=new FormData($(this)[0]);
      // for(var f of fd.entries()){
      //   console.log(`${f[0]}:${f[1]}`);
      // }
      fd.append('no_hp','62'+$('#no_hp').val());
      $.ajax({
        url:`<?=site_url('home/addMitra')?>`,
        type:'post',
        dataType:'json',
        data:fd,
        processData:false,
        contentType:false,
        success:function(res){
          $('#registerFormHomeAlert').show();
          if(res.success){
            $('#modalRegisterHome').modal('hide');
            $('#registerFormHomeAlert').hide();
            alert(res.message);
          }else{
            $('#registerFormHomeAlert').show();
            $('#registerFormHomeAlert').attr('class',`alert alert-${res.success?'success':'warning'} alert-dismissible fade show`);
            $('#registerFormHomeAlertMsg').html(res.message);
          }


        },
        error:function(xhr,stat,err){
          alert(err);
        }
      })
    });

    $('#submitRegisterFormHome').click(function(){
      $('#registerFormHome').find('button[type="submit"]').click();
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

  });
</script>