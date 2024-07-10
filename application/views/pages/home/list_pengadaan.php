<div class="breadcrumb-area">
    <div class="container h-100">
        <div class="row h-100 align-items-end">
            <div class="col-12">
                <div class="breadcumb--con">
                    <h2 class="title">Pengadaan Barang/Jasa</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i> Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengadaan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Curve -->
    <div class="breadcrumb-bg-curve">
        <img src="<?php echo base_url('assets/templates/home/img/core-img/curve-5.png') ?>" alt="">
    </div>
</div>

<div class="uza-blog-area section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <table style="width:'100%' !important;" id="list_pengadaan_table" class="table table-borderless">
                    <thead>
                        <tr>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        let site_url = '<?php echo site_url() ?>';
        let tableData = $('#list_pengadaan_table').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {
                no = 0;
            },
            "responsive": false,
            "select": false,
            "processing": true,
            "retrieve": true,
            "serverSide": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'procurement/get_data',
                "data": function(d) {
                    // d.f_status = 2;
                    d.f_project_type = 1;
                    d.f_showed_infront=1;
                },
                "dataSrc": "data"
            },
            'columns': [{
                render: function(data, type, full, meta) {


                    return `
                        <div class="single-blog-post" style="">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="<?php echo "https://waterfm.com/wp-content/uploads/purchase-order-illustration.jpg" ?>"/>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p style="text-align:left;font-size:14pt;font-weight:bold;">${full[4].length>34?full[4].substring(0,34)+'...':full[4]}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="timer" time="${full[6]}" style="text-align:right; font-size:15pt; color:#EE7B1D"></p>
                                            </div>
                                        </div>
                                        <div>
                                            <div id="strip_html" hidden>
                                            ${full[7]}
                                            </div>
                                            <div class="clean_html">
                                            </div>
                                        </div>
                                        <br/>
                                        <b>Waktu Pengadaan</b>: ${moment(full[5]).format('D MMM Y HH:mm')} s/d ${moment(full[6]).format('D MMM Y HH:mm')}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="text-align:right;">
                                <a href="<?php echo site_url('home/pengadaan/') ?>${full[0]}" class="read-more-btn">Selengkapnya <i class="arrow_carrot-2right"></i></a>
                            </div>
                        </div>`;
                }
            }],
            "drawCallback": function(settings) {

                let timers = [];
                let i = 0;
                $('.timer').each(function() {
                    let dataDate = $(this).attr('time');
                    timers[i] = setInterval(function() {
                        let now = new Date().getTime();
                        let distance = (new Date(dataDate).getTime()) - now;
                        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        let isActive = distance > 0;
                        let timeLeft = `<span class="">${isActive?`${days} hari ${hours} jam ${minutes} menit ${seconds} detik`:`Sisa Waktu 0 hari 0 jam 0 menit`}</span>`;
                        let textLeftTime = `${timeLeft}`;

                        $(this).html(textLeftTime);
                    }, 1000);

                    i++;
                });

                $('.clean_html').each(function() {
                    let plainText = $(this).parent('div').find('[id="strip_html"]').text();
                    $(this).html(plainText.length > 350 ? plainText.substring(0, 350) + '...' : plainText);
                });


            }
        });
    });
</script>