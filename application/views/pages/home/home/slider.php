<?php
$listSlider=$this->db
->where('deleted_at is null')
->where('id_company_owner',1)
->order_by('sort','asc')->get('home_slider')->result();
?>

<section>
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">

            <?php 
        $i=0;
        foreach($listSlider as $slider):?>
            <li data-target="#carouselExampleIndicators" data-slide-to="<?= $i?>" class="<?= $i==0?'active':''?>"></li>
            <?php $i++; endforeach;?>
        </ol>
        <div class="carousel-inner">

            <?php 
        $i=0;
        foreach($listSlider as $slider):?>
            <div class="carousel-item <?= $i==0?'active':''?>"
                onclick="<?= $slider->link!=null?'window.open(\''.$slider->link.'\',\'_blank\')':'javascript:void(0)'?>">
                <img style="width:100%;height: 80vh; object-fit:cover;" class="d-block w-100"
                    src="<?= (strpos($slider->image,'http')!==FALSE? $slider->image:base_url('upload/home_content').'/'.$slider->image) ?>"
                    alt="First slide">
                <div class="carousel-caption d-md-block" style="background-color: rgba(0, 0, 0, 0.5); right:0;left:0;bottom:0;">
                    <?php if($slider->title!=null):?>
                    <h3 style="color:white;"><?= $slider->title?></h3>
                    <?php endif;?>
                    <?php if($slider->subtitle):?>
                    <p style="color:whitesmoke;"><?= $slider->subtitle?></p>
                    <?php endif;?>
                </div>
            </div>

            <?php $i++; endforeach;?>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</section>