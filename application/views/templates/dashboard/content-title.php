<div class="app-page-title">
	<div class="page-title-wrapper">
		<div class="page-title-heading">
			<div class="page-title-icon">
				<i class="<?php echo $page_title_icon; ?> icon-gradient bg-mean-fruit">
				</i>
			</div>
			<div><?php echo $page_title_label; ?>
				<div class="page-title-subheading">
					<div class="row">
						<div class="col-md-12">
							<?php
							if (count($page_title_breadcumb) > 0) {
								echo '<nav class="" aria-label="breadcrumb">
													<ol class="breadcrumb">';
								foreach ($page_title_breadcumb as $bc) {
									$active = isset($bc['active']) ? 'active' : '';
									$link = isset($bc['link']) ? $bc['link'] : 'javascript:void(0)';
									$icon = isset($bc['icon']) ? $bc['icon'] : '';
									echo '<li class="' . $active . ' breadcrumb-item">';
									echo '<a class="rylinx-nav-bc" href="' . $link . '">';
									echo $icon . '' . $bc['label'];
									echo '</a>';
									echo '</li>';
								}
								echo '</ol>
													</nav>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('.rylinx-nav-bc').click(function() {
		if ($(this).attr('href') != 'javascript:void(0)' && $(this).attr('href') != '#') {
			$('#loading_content').show();
			RyLinx.to($(this).attr('href'), function() {
				$('#loading_content').hide();
			});
		}
	});
</script>