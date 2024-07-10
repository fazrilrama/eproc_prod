<!-- ***** Blog Area Start ***** -->
<section class="uza-blog-area">
    <!-- Background Curve -->
    <div class="blog-bg-curve">
        <img src="<?php echo base_url('assets/templates/home/') ?>./img/core-img/curve-4.png" alt="">
    </div>

    <div class="container">
        <div class="row">
            <!-- Section Heading -->
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>Pengadaan Terbaru</h2>
                    <p>Berikut daftar Pengadaan Terbaru yang tersedia</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            $data = $this->db
                ->select('b.name as user_name,a.*,( now()>a.end_date ) is_expired ')
                ->join('sys_user' . ' b', 'b.id_user=a.id_user')
                ->where('a.deleted_at is null')
                // ->where("a.end_date >= '" . date('Y-m-d H:i:s') . "'")
                // ->where('a.status=2')
                ->where('showed_infront',1)
                ->where('a.project_type=1')
                ->order_by('a.start_date desc')
                ->limit(6)
                ->from('project' . ' a')
                ->get()
                ->result();
            if(count($data)>0){
                foreach ($data as $d) {
                echo '<div class="col-12 col-lg-4">
                <div style="height:400px;" class="single-blog-post bg-img mb-80" style="background-image: url(' . base_url("assets/") . 'img/icon/fav_icon.png);">
                    <!-- Post Content -->
                    <div class="post-content">
                        <span class="post-date"><span>' . date_format(date_create($d->start_date), 'd') . '</span> ' . date_format(date_create($d->start_date), 'M') . ', ' . date_format(date_create($d->start_date), 'Y') . '</span>
                        <a href="#" class="post-title">' . $d->name . '</a>
                        <p>' . substr(strip_tags($d->description), 0, 100) . '...</p>
                        <a href="' . site_url('home/pengadaan/' . $d->id) . '" class="read-more-btn">Selengkapnya <i class="arrow_carrot-2right"></i></a>
                    </div>
                </div>
                </div>';
                }
                echo '<div class="col-md-12"><center><a href="'.site_url('home/list_pengadaan').'" style="font-size:18pt;">Tampilkan Lebih Banyak...</a></center></div>';
            }
            else{
                echo '<div class="col-md-12 col-lg-12">
                <center><img style="height:50vh;" src="'.base_url('assets/img/illustration/proc_process.png').'"/>
                <br/>
                <h3>Maaf Belum Terdapat Pengadaan Saat Ini...</h3>
                
                </center>
                </div>';
            }

            
            ?>

        </div>
    </div>
</section>
<!-- ***** Blog Area End ***** -->