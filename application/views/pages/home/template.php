<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="uza - Model Agency HTML5 Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>E-Procurement | BGR Logistik Indonesia</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('assets/') ?>img/icon/fav_icon.png">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="<?php echo base_url('assets/templates/home/') ?>style.css">

    
	<!-- Datatables -->
	<link href="<?php echo base_url('assets/vendor/datatables/css/datatables.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/vendor/datatables/css/select.datatables.min.css'); ?>" rel="stylesheet">
    
    <style>
        .classy-nav-container .classy-navbar {
            height: 70px;
            padding: 0 3%;
        }
    </style>
</head>

<body>
    
    <!-- jQuery js -->
    <script src="<?php echo base_url('assets/templates/home/') ?>js/jquery.min.js"></script>
    <!-- Preloader -->
    <div id="preloader">
        <div class="wrapper">
            <div class="cssload-loader"></div>
        </div>
    </div>

    <!-- ***** Top Search Area Start ***** -->
    <div class="top-search-area">
        <!-- Search Modal -->
        <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <!-- Close Button -->
                        <button type="button" class="btn close-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                        <!-- Form -->
                        <form action="index.html" method="post">
                            <input type="search" name="top-search-bar" class="form-control" placeholder="Search and hit enter...">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Top Search Area End ***** -->

    <!-- ***** Header Area Start ***** -->
    <header class="header-area">
        <!-- Main Header Start -->
        <div class="main-header-area">
            <div class="classy-nav-container breakpoint-off">
                <!-- Classy Menu -->
                <nav class="classy-navbar justify-content-between" id="uzaNav">

                    <!-- Logo -->
                    <a class="nav-brand" href="<?php echo base_url() ?>">
                        <img style="width:250px;height:90px; object-fit:contain;" src="<?php echo base_url('assets/') ?>img/logo/company_logo_header.png" alt="">
                        <!-- <span style="font-size:0.5em;margin-top:15px;font-family:Arial, Helvetica, sans-serif;">E-PROCUREMENT</span>-->
                        <?php if(!$this->agent->is_mobile()):?>
                            <span style="font-size:0.5em;"> | </span><span class="timeWidget badge badge-info" style="font-size:0.5em;"></span> 
                        <?php endif;?>
                    </a>

                    <!-- Navbar Toggler -->
                    <div class="classy-navbar-toggler">
                        <span class="navbarToggler"><span></span><span></span><span></span></span>
                    </div>

                    <!-- Menu -->
                    <div class="classy-menu">
                        <!-- Menu Close Button -->
                        <div class="classycloseIcon">
                            <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                        </div>

                        <!-- Nav Start -->
                        <div class="classynav">
                            <ul id="nav">
                                <li class="current-item"><a href="<?php echo base_url()?>">Beranda</a></li>
                                <li><a href="<?php echo site_url('home/list_pengadaan') ?>">Pengadaan</a></li>
                                <li hidden><a target="_blank" href="https://www.bgrlogistik.id/">Tentang Kami</a></li>
                                <li><a href="<?php echo site_url('auth/register') ?>">Syarat & Ketentuan</a></li>
                            </ul>

                            <!-- Get A Quote -->
                            <div hidden class="get-a-quote ml-4 mr-3">
                                <a href="<?php echo site_url('auth/register') ?>" class="btn uza-btn">Register</a>
                            </div>

                            <!-- Login / Register -->
                            <div hidden class="login-register-btn mx-3">
                                <a href="<?php echo site_url('auth/login') ?>">Login</a>
                            </div>

                            <!-- Search Icon -->
                            <!-- <div class="search-icon" data-toggle="modal" data-target="#searchModal">
                                <i class="icon_search"></i>
                            </div> -->
                        </div>
                        <!-- Nav End -->

                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- Content -->
    <?php echo $_contents; ?>

    <!-- ***** Footer Area Start ***** -->
    <footer class="footer-area section-padding-80-0">
        <div class="container">
            <div class="row justify-content-between">

                <!-- Single Footer Widget -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget mb-80">
                        <!-- Widget Title -->
                        <h4 class="widget-title">Halo BGR</h4>

                        <!-- Footer Content -->
                        <div class="footer-content mb-15">
                            <img width="70%" src="https://www.bgrlogistik.id/bgr/img/call_center.png" alt="" srcset="">
                        </div>
                    </div>
                </div>

                <!-- Single Footer Widget -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget mb-80">
                        <!-- Widget Title -->
                        <h4 class="widget-title">Pintasan</h4>

                        <!-- Nav -->
                        <nav>
                            <ul class="our-link">
                                <li><a href="https://www.bgrlogistik.id/id/about">Tentang Kami</a></li>
                                <li><a href="<?php echo site_url('home/list_pengadaan')?>">Pengadaan</a></li>
                                <li><a href="https://www.bgrlogistik.id/">Website Utama</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Single Footer Widget -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget mb-80">
                        <!-- Widget Title -->
                        <h4 class="widget-title">Kontak</h4>

                        <!-- Nav -->
                        <nav>
                            <ul class="our-link">
                                <li><a href="#"><i class="fa fa-phone"></i> +6221 691 6666</a></li>
                                <li><a href="#"><i class="fa fa-fax"></i> +6221 690 3162</a></li>
                                <li><a href="#"><i class="fa fa-envelope"></i> info@bgrlogistics.id</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Single Footer Widget -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-footer-widget mb-80">
                        <!-- Widget Title -->
                        <h4 class="widget-title">Sosial Media</h4>
                        <!-- Social Info -->
                        <div class="footer-social-info">
                            <a href="https://www.bgrlogistik.id/bgr/img/putih/Twitter.png" class="twitter" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter"></i></a>
                            <a href="https://www.instagram.com/bgrlogistik.id/" class="instagram" data-toggle="tooltip" data-placement="top" title="Instagram"><i class="fa fa-instagram"></i></a>
                            <a href="https://www.youtube.com/channel/UCIOTEaDjA53S8Ir4eDbszAA" class="youtube" data-toggle="tooltip" data-placement="top" title="YouTube"><i class="fa fa-youtube-play"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div style="margin-bottom: 30px;">

                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                Powered By <a target="_blank" style="font-size:inherit !important;" 
                href="https://www.bgrlogistik.id/">BGR Access </a><br>Copyright&copy; <?= date('Y')?> All Rights Reserved
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </div>

        </div>
    </footer>
    <!-- ***** Footer Area End ***** -->

    <!-- ******* All JS Files ******* -->
    <!-- Popper js -->
    <script src="<?php echo base_url('assets/templates/home/') ?>js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="<?php echo base_url('assets/templates/home/') ?>js/bootstrap.min.js"></script>
    <!-- All js -->
    <script src="<?php echo base_url('assets/templates/home/') ?>js/uza.bundle.js"></script>
    <!-- Active js -->
    <script src="<?php echo base_url('assets/templates/home/') ?>js/default-assets/active.js"></script>

    <!-- Datatables -->
    <script src="<?php echo base_url('assets/vendor/datatables/js/pdfmake.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/datatables/js/vfs_fonts.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/datatables/js/datatables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/datatables/js/select.datatables.min.js'); ?>"></script>
    
    <script src="<?php echo base_url('assets/vendor/moment/moment-with-locales.js'); ?>"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>

    <script>
        $(document).ready(function(){
            setInterval(function(){
                $('.timeWidget').html(moment().tz('Asia/Jakarta').format('ddd, DD MMM YYYY HH:mm:ss z'));
            }, 1000);
        });
    </script>
    
</body>

</html>