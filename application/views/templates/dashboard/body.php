<?php include('header.php'); ?>

<body>
	<div id="loading_content" class="loading">
		<!-- <center>
			<img class="loading-image" src="<?php echo base_url('assets/img/loading.gif'); ?>" alt="" srcset="">
			<br><span class="loading-label">Loading...</span>
		</center> -->
	</div>
	<?php include('scripts-body.php'); ?>

	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<div class="app-header header-shadow">
			<div class="app-header__logo">
				<a href="<?=site_url('/app')?>" class="logo-src">
					<img width=" 70px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/company_logo.png" alt="">
					<img width=" 110px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/app_logo.png" alt="">
				</a>
				<div class="header__pane ml-auto">
					<div>
						<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
							<span class="hamburger-box">
								<span class="hamburger-inner"></span>
							</span>
						</button>
					</div>
				</div>
			</div>
			<div class="app-header__mobile-menu">
				<div>
					<button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</div>
			</div>
			<div class="app-header__menu">
				<span>
					<button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
						<span class="btn-icon-wrapper">
							<i class="fa fa-ellipsis-v fa-w-6"></i>
						</span>
					</button>
				</span>
			</div>
			<div class="app-header__content">
				<div hidden class="app-header-left">
					<div class="search-wrapper">
						<div class="input-holder">
							<input type="text" class="search-input" placeholder="Type to search">
							<button class="search-icon"><span></span></button>
						</div>
						<button class="close"></button>
					</div>
				</div>
				<div class="app-header-right">
					<div class="header-dots">
						<div class="dropdown">
							<button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
								<span class="icon-wrapper icon-wrapper-alt rounded-circle">
									<span class="icon-wrapper-bg bg-danger"></span>
									<i id="notification_header_anim" class="icon text-danger icon-anim-pulse fa fa-bell"></i>
									<span id="notification_header_dot" class="badge badge-dot badge-dot-sm badge-danger">Notifikasi</span>
								</span>
							</button>
							<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right">
								<div class="dropdown-menu-header mb-0">
									<div class="dropdown-menu-header-inner bg-deep-blue">
										<div class="menu-header-image opacity-1"></div>
										<div class="menu-header-content text-dark">
											<h5 class="menu-header-title">Notifikasi</h5>
											<h6 class="menu-header-subtitle">Anda memiliki <b class="notification_unread_counter">0</b> notifikasi belum terbaca</h6>
										</div>
									</div>
								</div>
								<ul class="tabs-animated-shadow tabs-animated nav nav-justified tabs-shadow-bordered p-3">
									<li class="nav-item">
										<a role="tab" class="nav-link active" data-toggle="tab" href="#tab-events-header">
											<span>Notifikasi</span>
										</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab-events-header" role="tabpanel">
										<div class="scroll-area-sm">
											<div class="scrollbar-container ps">
												<div class="p-3">
													<div id="notification_header_container" class="vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column">

													</div>
												</div>
												<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
													<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
												</div>
												<div class="ps__rail-y" style="top: 0px; right: 0px;">
													<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<ul class="nav flex-column">
									<li class="nav-item-divider nav-item"></li>
									<li class="nav-item-btn text-center nav-item">
										<a href="<?=site_url('/app#notification')?>" class="btn-shadow btn-wide btn-pill btn btn-focus btn-sm">Selengkapnya...</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<div class="header-btn-lg pr-0">
						<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left">
									<div class="btn-group">
										<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
											<img width="42" class="rounded-circle" src="<?php echo base_url('upload/user_photo/') . $this->session->userdata('user')['photo'] ?>" alt="">
											<i class="fa fa-angle-down ml-2 opacity-8"></i>
										</a>
										<div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-292px, 44px, 0px);">
											<div class="dropdown-menu-header">
												<div class="dropdown-menu-header-inner bg-info">
													<div class="menu-header-image opacity-2" style="background-image: url('<?php echo base_url('assets/img/system/city3.jpg') ?>');"></div>
													<div class="menu-header-content text-left">
														<div class="widget-content p-0">
															<div class="widget-content-wrapper">
																<div class="widget-content-left mr-3">
																	<img width="42" class="rounded-circle" src="<?php echo base_url('upload/user_photo/') . $this->session->userdata('user')['photo'] ?>" alt="">
																</div>
																<div class="widget-content-left">
																	<div class="widget-heading" style="color:white;"> <?php echo $this->session->userdata('user')['name']; ?>
																	</div>
																	<div class="widget-subheading opacity-8" style="color:white;font-style:italic;"><?php echo $this->session->userdata('user')['email']; ?>
																	</div>
																	<div class="widget-subheading opacity-8" style="color:white;"><?php echo $this->session->userdata('user')['role_name']; ?>
																	</div>
																</div>
																<div class="widget-content-right mr-2">
																	<!-- <button class="btn-pill btn-shadow btn-shine btn btn-focus">Logout
																	</button> -->
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="scroll-area-xs" style="height: 150px;">
												<div class="scrollbar-container ps">
													<ul class="nav flex-column">
														<li class="nav-item-header nav-item">
															Akun Saya
														</li>
														<li class="nav-item">
															<a href="#notification" class="nav-link rylinx-nav">Notifikasi
																<div class="ml-auto badge badge-warning notification_unread_counter">
																</div>
															</a>
														</li>
													</ul>
													<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
														<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
													</div>
													<div class="ps__rail-y" style="top: 0px; right: 0px;">
														<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
													</div>
												</div>
											</div>
											<ul class="nav flex-column">
												<li class="nav-item-divider mb-0 nav-item"></li>
											</ul>
											<div class="grid-menu grid-menu-2col">
												<div class="no-gutters row">
													<div class="col-sm-6">
														<a href="#user/manage_account" class="rylinx-nav btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-warning">
															<i class="pe-7s-user icon-gradient bg-amy-crisp btn-icon-wrapper mb-2"></i>
															Pengaturan Akun
														</a>
													</div>
													<div class="col-sm-6">
														<a href="<?php echo base_url('auth/logout') ?>" class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-danger">
															<i class="pe-7s-power icon-gradient bg-love-kiss btn-icon-wrapper mb-2"></i>
															<b>Logout</b>
														</a>
													</div>
												</div>
											</div>

										</div>
									</div>
								</div>

								<div class="widget-content-left  ml-3 header-user-info">
									<div class="widget-heading">
										<?php echo $this->session->userdata('user')['name']; ?>
									</div>
									<div class="widget-subheading">
										<?php echo $this->session->userdata('user')['role_name'] ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="ui-theme-settings" hidden>
			<button type="button" id="TooltipDemo" class="btn-open-options btn btn-warning">
				<i class="fa fa-cog fa-w-16 fa-spin fa-2x"></i>
			</button>
			<div class="theme-settings__inner">
				<div class="scrollbar-container ps ps--active-y">
					<div class="theme-settings__options-wrapper">
						<h3 class="themeoptions-heading">Layout Options
						</h3>
						<div class="p-3">
							<ul class="list-group">
								<li class="list-group-item">
									<div class="widget-content p-0">
										<div class="widget-content-wrapper">
											<div class="widget-content-left mr-3">
												<div class="switch has-switch switch-container-class" data-class="fixed-header">
													<div class="switch-animate switch-on">
														<input type="checkbox" checked="" data-toggle="toggle" data-onstyle="success">
													</div>
												</div>
											</div>
											<div class="widget-content-left">
												<div class="widget-heading">Fixed Header
												</div>
												<div class="widget-subheading">Makes the header top fixed, always
													visible!
												</div>
											</div>
										</div>
									</div>
								</li>
								<li class="list-group-item">
									<div class="widget-content p-0">
										<div class="widget-content-wrapper">
											<div class="widget-content-left mr-3">
												<div class="switch has-switch switch-container-class active" data-class="fixed-sidebar">
													<div class="switch-animate switch-on">
														<input type="checkbox" checked="" data-toggle="toggle" data-onstyle="success">
													</div>
												</div>
											</div>
											<div class="widget-content-left">
												<div class="widget-heading">Fixed Sidebar
												</div>
												<div class="widget-subheading">Makes the sidebar left fixed, always
													visible!
												</div>
											</div>
										</div>
									</div>
								</li>
								<li class="list-group-item">
									<div class="widget-content p-0">
										<div class="widget-content-wrapper">
											<div class="widget-content-left mr-3">
												<div class="switch has-switch switch-container-class" data-class="fixed-footer">
													<div class="switch-animate switch-off">
														<input type="checkbox" data-toggle="toggle" data-onstyle="success">
													</div>
												</div>
											</div>
											<div class="widget-content-left">
												<div class="widget-heading">Fixed Footer
												</div>
												<div class="widget-subheading">Makes the app footer bottom fixed, always
													visible!
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul>
						</div>
						<h3 class="themeoptions-heading">
							<div>
								Header Options
							</div>
							<button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-header-cs-class" data-class="">
								Restore Default
							</button>
						</h3>
						<div class="p-3">
							<ul class="list-group">
								<li class="list-group-item">
									<h5 class="pb-2">Choose Color Scheme
									</h5>
									<div class="theme-settings-swatches">
										<div class="swatch-holder bg-primary switch-header-cs-class" data-class="bg-primary header-text-light">
										</div>
										<div class="swatch-holder bg-secondary switch-header-cs-class" data-class="bg-secondary header-text-light">
										</div>
										<div class="swatch-holder bg-success switch-header-cs-class" data-class="bg-success header-text-dark">
										</div>
										<div class="swatch-holder bg-info switch-header-cs-class" data-class="bg-info header-text-dark">
										</div>
										<div class="swatch-holder bg-warning switch-header-cs-class" data-class="bg-warning header-text-dark">
										</div>
										<div class="swatch-holder bg-danger switch-header-cs-class" data-class="bg-danger header-text-light">
										</div>
										<div class="swatch-holder bg-light switch-header-cs-class" data-class="bg-light header-text-dark">
										</div>
										<div class="swatch-holder bg-dark switch-header-cs-class" data-class="bg-dark header-text-light">
										</div>
										<div class="swatch-holder bg-focus switch-header-cs-class" data-class="bg-focus header-text-light">
										</div>
										<div class="swatch-holder bg-alternate switch-header-cs-class" data-class="bg-alternate header-text-light">
										</div>
										<div class="divider">
										</div>
										<div class="swatch-holder bg-vicious-stance switch-header-cs-class" data-class="bg-vicious-stance header-text-light">
										</div>
										<div class="swatch-holder bg-midnight-bloom switch-header-cs-class" data-class="bg-midnight-bloom header-text-light">
										</div>
										<div class="swatch-holder bg-night-sky switch-header-cs-class" data-class="bg-night-sky header-text-light">
										</div>
										<div class="swatch-holder bg-slick-carbon switch-header-cs-class" data-class="bg-slick-carbon header-text-light">
										</div>
										<div class="swatch-holder bg-asteroid switch-header-cs-class" data-class="bg-asteroid header-text-light">
										</div>
										<div class="swatch-holder bg-royal switch-header-cs-class" data-class="bg-royal header-text-light">
										</div>
										<div class="swatch-holder bg-warm-flame switch-header-cs-class" data-class="bg-warm-flame header-text-dark">
										</div>
										<div class="swatch-holder bg-night-fade switch-header-cs-class" data-class="bg-night-fade header-text-dark">
										</div>
										<div class="swatch-holder bg-sunny-morning switch-header-cs-class" data-class="bg-sunny-morning header-text-dark">
										</div>
										<div class="swatch-holder bg-tempting-azure switch-header-cs-class" data-class="bg-tempting-azure header-text-dark">
										</div>
										<div class="swatch-holder bg-amy-crisp switch-header-cs-class" data-class="bg-amy-crisp header-text-dark">
										</div>
										<div class="swatch-holder bg-heavy-rain switch-header-cs-class" data-class="bg-heavy-rain header-text-dark">
										</div>
										<div class="swatch-holder bg-mean-fruit switch-header-cs-class" data-class="bg-mean-fruit header-text-dark">
										</div>
										<div class="swatch-holder bg-malibu-beach switch-header-cs-class" data-class="bg-malibu-beach header-text-light">
										</div>
										<div class="swatch-holder bg-deep-blue switch-header-cs-class" data-class="bg-deep-blue header-text-dark">
										</div>
										<div class="swatch-holder bg-ripe-malin switch-header-cs-class" data-class="bg-ripe-malin header-text-light">
										</div>
										<div class="swatch-holder bg-arielle-smile switch-header-cs-class" data-class="bg-arielle-smile header-text-light">
										</div>
										<div class="swatch-holder bg-plum-plate switch-header-cs-class" data-class="bg-plum-plate header-text-light">
										</div>
										<div class="swatch-holder bg-happy-fisher switch-header-cs-class" data-class="bg-happy-fisher header-text-dark">
										</div>
										<div class="swatch-holder bg-happy-itmeo switch-header-cs-class" data-class="bg-happy-itmeo header-text-light">
										</div>
										<div class="swatch-holder bg-mixed-hopes switch-header-cs-class" data-class="bg-mixed-hopes header-text-light">
										</div>
										<div class="swatch-holder bg-strong-bliss switch-header-cs-class" data-class="bg-strong-bliss header-text-light">
										</div>
										<div class="swatch-holder bg-grow-early switch-header-cs-class" data-class="bg-grow-early header-text-light">
										</div>
										<div class="swatch-holder bg-love-kiss switch-header-cs-class" data-class="bg-love-kiss header-text-light">
										</div>
										<div class="swatch-holder bg-premium-dark switch-header-cs-class" data-class="bg-premium-dark header-text-light">
										</div>
										<div class="swatch-holder bg-happy-green switch-header-cs-class" data-class="bg-happy-green header-text-light">
										</div>
									</div>
								</li>
							</ul>
						</div>
						<h3 class="themeoptions-heading">
							<div>Sidebar Options</div>
							<button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-sidebar-cs-class" data-class="">
								Restore Default
							</button>
						</h3>
						<div class="p-3">
							<ul class="list-group">
								<li class="list-group-item">
									<h5 class="pb-2">Choose Color Scheme
									</h5>
									<div class="theme-settings-swatches">
										<div class="swatch-holder bg-primary switch-sidebar-cs-class" data-class="bg-primary sidebar-text-light">
										</div>
										<div class="swatch-holder bg-secondary switch-sidebar-cs-class" data-class="bg-secondary sidebar-text-light">
										</div>
										<div class="swatch-holder bg-success switch-sidebar-cs-class" data-class="bg-success sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-info switch-sidebar-cs-class" data-class="bg-info sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-warning switch-sidebar-cs-class" data-class="bg-warning sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-danger switch-sidebar-cs-class" data-class="bg-danger sidebar-text-light">
										</div>
										<div class="swatch-holder bg-light switch-sidebar-cs-class" data-class="bg-light sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-dark switch-sidebar-cs-class" data-class="bg-dark sidebar-text-light">
										</div>
										<div class="swatch-holder bg-focus switch-sidebar-cs-class" data-class="bg-focus sidebar-text-light">
										</div>
										<div class="swatch-holder bg-alternate switch-sidebar-cs-class" data-class="bg-alternate sidebar-text-light">
										</div>
										<div class="divider">
										</div>
										<div class="swatch-holder bg-vicious-stance switch-sidebar-cs-class" data-class="bg-vicious-stance sidebar-text-light">
										</div>
										<div class="swatch-holder bg-midnight-bloom switch-sidebar-cs-class" data-class="bg-midnight-bloom sidebar-text-light">
										</div>
										<div class="swatch-holder bg-night-sky switch-sidebar-cs-class" data-class="bg-night-sky sidebar-text-light">
										</div>
										<div class="swatch-holder bg-slick-carbon switch-sidebar-cs-class" data-class="bg-slick-carbon sidebar-text-light">
										</div>
										<div class="swatch-holder bg-asteroid switch-sidebar-cs-class" data-class="bg-asteroid sidebar-text-light">
										</div>
										<div class="swatch-holder bg-royal switch-sidebar-cs-class" data-class="bg-royal sidebar-text-light">
										</div>
										<div class="swatch-holder bg-warm-flame switch-sidebar-cs-class" data-class="bg-warm-flame sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-night-fade switch-sidebar-cs-class" data-class="bg-night-fade sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-sunny-morning switch-sidebar-cs-class" data-class="bg-sunny-morning sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-tempting-azure switch-sidebar-cs-class" data-class="bg-tempting-azure sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-amy-crisp switch-sidebar-cs-class" data-class="bg-amy-crisp sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-heavy-rain switch-sidebar-cs-class" data-class="bg-heavy-rain sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-mean-fruit switch-sidebar-cs-class" data-class="bg-mean-fruit sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-malibu-beach switch-sidebar-cs-class" data-class="bg-malibu-beach sidebar-text-light">
										</div>
										<div class="swatch-holder bg-deep-blue switch-sidebar-cs-class" data-class="bg-deep-blue sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-ripe-malin switch-sidebar-cs-class" data-class="bg-ripe-malin sidebar-text-light">
										</div>
										<div class="swatch-holder bg-arielle-smile switch-sidebar-cs-class" data-class="bg-arielle-smile sidebar-text-light">
										</div>
										<div class="swatch-holder bg-plum-plate switch-sidebar-cs-class" data-class="bg-plum-plate sidebar-text-light">
										</div>
										<div class="swatch-holder bg-happy-fisher switch-sidebar-cs-class" data-class="bg-happy-fisher sidebar-text-dark">
										</div>
										<div class="swatch-holder bg-happy-itmeo switch-sidebar-cs-class" data-class="bg-happy-itmeo sidebar-text-light">
										</div>
										<div class="swatch-holder bg-mixed-hopes switch-sidebar-cs-class" data-class="bg-mixed-hopes sidebar-text-light">
										</div>
										<div class="swatch-holder bg-strong-bliss switch-sidebar-cs-class" data-class="bg-strong-bliss sidebar-text-light">
										</div>
										<div class="swatch-holder bg-grow-early switch-sidebar-cs-class" data-class="bg-grow-early sidebar-text-light">
										</div>
										<div class="swatch-holder bg-love-kiss switch-sidebar-cs-class" data-class="bg-love-kiss sidebar-text-light">
										</div>
										<div class="swatch-holder bg-premium-dark switch-sidebar-cs-class" data-class="bg-premium-dark sidebar-text-light">
										</div>
										<div class="swatch-holder bg-happy-green switch-sidebar-cs-class" data-class="bg-happy-green sidebar-text-light">
										</div>
									</div>
								</li>
							</ul>
						</div>
						<h3 class="themeoptions-heading">
							<div>Main Content Options</div>
							<button type="button" class="btn-pill btn-shadow btn-wide ml-auto active btn btn-focus btn-sm">Restore Default
							</button>
						</h3>
						<div class="p-3">
							<ul class="list-group">
								<li class="list-group-item">
									<h5 class="pb-2">Page Section Tabs
									</h5>
									<div class="theme-settings-swatches">
										<div role="group" class="mt-2 btn-group">
											<button type="button" class="btn-wide btn-shadow btn-primary btn btn-secondary switch-theme-class" data-class="body-tabs-line">
												Line
											</button>
											<button type="button" class="btn-wide btn-shadow btn-primary active btn btn-secondary switch-theme-class" data-class="body-tabs-shadow">
												Shadow
											</button>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
						<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
					</div>
					<div class="ps__rail-y" style="top: 0px; height: 667px; right: 0px;">
						<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 413px;"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="app-main">
			<div class="app-sidebar sidebar-shadow">
				<div class="app-header__logo">
					<div class="logo-src"></div>
					<div class="header__pane ml-auto">
						<div>
							<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
								<span class="hamburger-box">
									<span class="hamburger-inner"></span>
								</span>
							</button>
						</div>
					</div>
				</div>
				<div class="app-header__mobile-menu">
					<div>
						<button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
							<span class="hamburger-box">
								<span class="hamburger-inner"></span>
							</span>
						</button>
					</div>
				</div>
				<div class="app-header__menu">
					<span>
						<button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
							<span class="btn-icon-wrapper">
								<i class="fa fa-ellipsis-v fa-w-6"></i>
							</span>
						</button>
					</span>
				</div>
				<div class="scrollbar-sidebar ps">
					<div class="app-sidebar__inner">
						<ul class="vertical-nav-menu metismenu" id="menu_container">
							<script>
								Menu.get($('#menu_container'));
								$('a[href="javascript:void(0)"]').click(function() {
									var text = $(this).html();
									if (text.includes('User Guide')) {
										$.fileDownload(base_url + 'assets/file/USER_GUIDE_EOFFICE_BGR.pdf', {
											failMessageHtml: "There was a problem generating your file, please try again."
										});
									} else if (text.includes('Template Surat')) {
										$.fileDownload(base_url + 'assets/file/TEMPLATE_BUAT_SURAT.docx', {
											failMessageHtml: "There was a problem generating your file, please try again."
										});
									}
								});
							</script>
						</ul>
					</div>
				</div>
			</div>
			<div class="app-main__outer" style="overflow-x:hidden; padding-bottom: 1px; margin-bottom: -1px;">
				<div class="app-main__inner">
					<!-- Content -->
					<button id="modal-btn-lg" type="button" class="btn mr-2 mb-2 btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" hidden>Large modal</button>
					<button id="modal-btn-bsc" type="button" class="btn mr-2 mb-2 btn-primary" data-toggle="modal" data-target="#modal-basic" hidden>Basic Modal</button>

					<div id="rylinx_content">
					</div>
				</div>
				<!-- End Main Outer -->
				<div class="app-wrapper-footer">
					<div class="app-footer">
						<div class="app-footer__inner">
							<div class="app-footer-left">
								<ul class="nav">
									<li class="nav-item">
										<a href="javascript:void(0);" class="changelog">
											Version <?php echo $app_version; ?>
										</a>
									</li>
								</ul>
							</div>
							<div class="app-footer-right">
								<ul class="nav">
									<li class="nav-item">
										<a href="javascript:void(0);" class="nav-link">
											Copyright© <?php echo date('Y') . ' '; ?> BLI Access All Rights Reserved
											<!-- <img style="margin-left:5px;" width="100px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/company_logo.png" alt=""> -->
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <script src="http://maps.google.com/maps/api/js?sensor=true"></script> -->
		</div>
	</div>

	<?php include('scripts-footer.php'); ?>
</body>

</html>

<?php include('footer.php'); ?>