<div class="breadcrumb-area">
    <div class="container h-100">
        <div class="row h-100 align-items-end">
            <div class="col-12">
                <div class="breadcumb--con">
                    <h2 class="title">Detail Pengadaan</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i> Beranda</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail Pengadaan</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $detail->name ?></li>
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

<section class="blog-details-area section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="blog-details-content">
                    <!-- Post Details Text -->
                    <div class="post-details-text">

                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-10">
                                <center>
                                    <div id="timer" time="<?php echo $detail->end_date ?>"></div>
                                </center>
                                <div class="post-content text-center mb-50">
                                    <h3><?php echo $detail->name ?></h3>
                                    <h6><?php echo date_format(date_create($detail->start_date), 'd M Y H:i') ?> s/d <?php echo date_format(date_create($detail->end_date), 'd M Y H:i') ?></h6>
                                </div>
                            </div>
                            <div class="col-12">
                                <center>
                                    <img width="80%" src="https://www.bgrlogistik.id/storage/banner-homes/February2019/cIMZFokgq9rTv2g5T5y3.png" alt="">
                                </center>
                                <br />
                                <?php echo $detail->description ?>
                                <hr />
                                <div <?= $detail->is_expired?'hidden':'' ?> class="row">
                                    <div class="col-md-12" style="text-align:right;">
                                        <a href="<?php echo site_url('auth') ?>" class="btn btn-md btn-success">Daftar Pengadaan</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let dataDate = $('#timer').attr('time');
    setInterval(function() {
        let now = new Date().getTime();
        let distance = (new Date(dataDate).getTime()) - now;
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        let isActive = distance > 0;
        let timeLeft = `<h3 style="color:#EE7B1D">${isActive?`${days} hari ${hours} jam ${minutes} menit ${seconds} detik`:`Sisa Waktu 0 hari 0 jam 0 menit`}</h3>`;
        let textLeftTime = `${timeLeft}`;

        $('#timer').html(textLeftTime);
    }, 1000);
</script>