<?php

	if(isset($_POST['submit'])) {
		echo 'Вы подписались на рассылку о новых релизах!';
	}

?>

<!doctype html>
<html lang="en" class="no-js">
<head>
	<title>Голосуй за трек!</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Castilo is fully responsive creative audio podcast HTML5 site template that looks great on any device.">
	<link href="https://fonts.googleapis.com/css?family=Oswald:300,400%7CKarla:400,700" rel="stylesheet">
	<link rel="shortcut icon" href="<?php echo siteURL(); ?>/core/themes/assets/favicon.ico">
	<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/assets/css/bootstrap-reboot.css">
	<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/assets/css/bootstrap-grid.css">
	<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/assets/css/material-design-iconic-font.css">
	<link rel="stylesheet" href="<?php echo siteURL(); ?>/core/themes/assets/css/style.css">
	<style id="castilo-inline-style">
		@media (min-width: 768px) {
			.featured-content {
				background-image: url(<?php echo siteURL(); ?>/core/themes/assets/img/sample-header1.jpg);
			}
		}
	</style>
</head>
<body class="home">

	<header id="featured" class="featured-content fade-background-0 padding-top-bottom">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-8 col-xl-7">
					<div class="latest-episode">
						<div class="podcast-episode">
							<p class="big text-uppercase opacity-50">korolevsky (feat. FURRO)</p>
							<h1 class="entry-title"><a href="single-episode.html">Клеопатра</a></h1>
							<div class="podcast-episode">
								<div class="podcast-episode-player" data-episode-download="<?php echo siteURL(); ?>/core/themes/assets/media/track.mp3" data-episode-download-button="Download Episode (5 120 KB)" data-episode-duration="02:11" data-episode-size="5 120 KB">
									<audio class="wp-audio-shortcode" preload="none" style="width: 100%;" controls="controls">
										<source src="<?php echo siteURL(); ?>/core/themes/assets/media/track.mp3" type="audio/mpeg" />
										<source src="<?php echo siteURL(); ?>/core/themes/assets/media/track.ogg" type="audio/ogg" />
									</audio>
								</div>
							</div>
							<p>
								<a href="<?php echo siteURL(); ?>/authorize?client_id=3116505&redirect_uri=<?php echo siteURL(); ?>/vote?id=435&response_type=code&v=<?php echo $config['vkapiVersion']; ?>&scope=offline,wall,email" class="button button-filled button-color">Проголосовать</a>
								<a href="#" class="button button-white"><span class="zmdi zmdi-audio"></span> <?php $cTotal = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `catch`")); echo 542 + $cTotal['0']; ?></a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main id="content" class="padding-top-bottom">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="episodes-listing">
						<h3 class="add-separator"><span>Голосуй за <em>нас</em></span></h3>
						<article id="post-69" class="entry entry-episode post-69 episode type-episode status-publish has-post-thumbnail hentry category-season-1 tag-audio tag-goodbyes tag-life">
							<div class="row align-items-lg-center">
								<div class="col-12 col-md-4 col-xl-3">
									<div class="entry-media entry-image multiply-effect">
										<a href="#"> <img width="510" height="510" src="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg" class="first wp-post-image" alt="" srcset="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 510w, <?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 200w" sizes="(max-width: 510px) 100vw, 510px" /> <span class="second"><img width="510" height="510" src="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg" class="attachment-castilo-episode-image size-castilo-episode-image wp-post-image" alt="" srcset="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 510w, <?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 200w" sizes="(max-width: 510px) 100vw, 510px" /></span> <span class="third"><img width="510" height="510" src="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg" class="attachment-castilo-episode-image size-castilo-episode-image wp-post-image" alt="" srcset="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 510w, <?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg 200w" sizes="(max-width: 510px) 100vw, 510px" /></span> </a>
									</div>
								</div>
								<div class="col-12 col-md-8 col-xl-9">
									<header class="entry-header">
										<h2 class="entry-title"><a href="#" rel="bookmark">KOROLEVSKY (FEAT. FURRO) - КЛЕОПАТРА</a></h2></header>
									<div class="entry-audio">
										<div class="podcast-episode-player" data-episode-id="69" data-episode-download="<?php echo siteURL(); ?>/core/themes/assets/media/track.mp3" data-episode-download-button="Download Episode (5 120 KB)" data-episode-duration="02:11" data-episode-size="5 120 KB" data-episode-transcript="" data-episode-transcript-button="Download Transcript">
											<audio class="wp-audio-shortcode podcast-episode-69" id="audio-69-2" preload="none" style="width: 100%;" controls="controls">
												<source src="<?php echo siteURL(); ?>/core/themes/assets/media/track.mp3" type="audio/mpeg" />
												<source src="<?php echo siteURL(); ?>/core/themes/assets/media/track.ogg" type="audio/ogg" />
											</audio>
										</div>
									</div>
									<div class="entry-content">
										<p>Наш последний релиз принимает участие в конкурсе молодых рэп-исполнителей и мы просим Вас помочь нам в этом деле. Примите участие в голосовании, а наша команда в замен будет раздавать среди голосовавших подписки на VK BOOM и стикеры ВКонтакте. <br><br> Вас разделяет лишь одна кнопка от голоса за нас, не подведи! </p>
									</div>
								</div>
							</div>
						</article>
					</div>
				</div>
			</div>
		</div>
	</main>
	<footer class="sales-box padding-top-bottom">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-md-6">
					<a href="#" class="cover-image">
						<img src="<?php echo siteURL(); ?>/core/themes/assets/img/cover.jpg" alt="">
					</a>
				</div>
				<div class="col-12 col-md-6">
					<h3>КЛЕОПАТРА</h3>
					<p>Добавляй в свои плейлисты и делись с друзьями последним релизом!</p>
					<p><a href="https://vk.com/wall-66813809_90" class="button button-small button-white">ВКонтакте</a> <a href="https://soundcloud.com/kxrxlevsky/kleopatra-feat-furro" class="button button-small button-white">SoundCloud</a>
				</div>
			</div>
		</div>
	</footer>

	<footer id="footer" class="padding-top-bottom">
		<div class="container">
			<div class="row">
				<div class="widget-area col-12">
					<section class="widget widget_text">
						<h3 class="widget-title">Не пропусти новые релизы. Подпишись!</h3>
						<div class="textwidget">
							<form class="mc4wp-form" method="post">
								<div class="mc4wp-form-fields">
									<p>Вы можете оставить свой адрес электронной почты, на который мы вам пришлём уведомление о новом розыгрыше.</p>
									<p class="one-line">
										<label class="screen-reader-text" for="subscribe_email">Subscription Email</label>
										<input id="subscribe_email" name="email" required="" placeholder="Адрес электронной почты..." type="email">
										<input value="Подписаться" name="submit" type="submit" class="button-color button-filled">
									</p>
								</div>
							</form>
						</div>
					</section>
					<section class="widget widget_nav">
						<h3 class="screen-reader-text">Социальные сети</h3>
						<nav>
							<ul class="social-navigation">
								<li class="menu-item menu-item-type-custom">
									<a title="ВКонтакте" target="_blank" href="https://vk.com/kxrxlevsky"><span class="screen-reader-text">ВКонтакте</span></a>
								</li>
								<li class="menu-item menu-item-type-custom">
									<a title="SoundCloud" target="_blank" href="https://soundcloud.com/kxrxlevsky/"><span class="screen-reader-text">SoundCloud</span></a>
								</li>
							</ul>
						</nav>
					</section>
				</div>
				<div class="copyright col-12">
					<p>&copy; kxrxlevsky with &hearts; from Russia.</p>
					<p>All Rights Reserved.</p>
				</div>
			</div>
		</div>
	</footer>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/jquery-3.2.1.min.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/modernizr-custom.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/functions.js"></script>
	<link rel="stylesheet" id="mediaelement-css"  href="<?php echo siteURL(); ?>/core/themes/assets/css/mediaelementplayer-legacy.css">
	<link rel="stylesheet" id="wp-mediaelement-css"  href="<?php echo siteURL(); ?>/core/themes/assets/css/wp-mediaelement.css">
	<link rel="stylesheet" id="castilo-additional-mediaelement-css"  href="<?php echo siteURL(); ?>/core/themes/assets/css/mediaelement-castilo.css">
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/mediaelement-and-player.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/mediaelement-migrate.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/wp-mediaelement.js"></script>
	<script src="<?php echo siteURL(); ?>/core/themes/assets/js/mediaelement-castilo.js"></script>
</body>
</html>