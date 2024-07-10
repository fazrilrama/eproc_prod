<!-- ***** Welcome Area Start ***** -->
<section hidden class="welcome-area">
    <div class="welcome-slides owl-carousel">

        <!-- Single Welcome Slide -->
        <div class="single-welcome-slide">
            <!-- Background Curve -->
            <div class="background-curve">
                <img src="<?php echo base_url('assets/templates/home/') ?>./img/core-img/curve-1.png" alt="">
            </div>

            <!-- Welcome Content -->
            <div class="welcome-content h-100">
                <div class="container h-100">
                    <div class="row h-100 align-items-center">
                        <!-- Welcome Text -->
                        <div class="col-12 col-md-6">
                            <div hidden class="welcome-text">
                                <h2 data-animation="fadeInUp" data-delay="100ms">Daftar Menjadi<br> <span>Vendor</span></h2>
                                <h5 data-animation="fadeInUp" data-delay="400ms">Kami menyediakan pengadaan barang/jasa yang cocok untuk Bisnis Anda.</h5>
                                <a href="<?php echo site_url('auth/register') ?>" class="btn uza-btn btn-2" data-animation="fadeInUp" data-delay="700ms">Register Sekarang</a>
                            </div>
                        </div>
                        <!-- Welcome Thumbnail -->
                        <div class="col-12 col-md-6">
                            <div class="welcome-thumbnail">
                                <img style="border-radius:10px;height:500px;object-fit:contain;" src="<?= base_url('assets/img/system/kerjasama.jpg')?>" alt="" data-animation="slideInRight" data-delay="400ms">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section style="margin-top: 10vh;"></section>
<?php $this->load->view('pages/home/home/slider')?>
<?php $this->load->view('pages/home/home/login_home')?>
<?php $this->load->view('pages/home/home/procurement')?>

