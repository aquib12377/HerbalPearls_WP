<?php
/**
 * Contact page template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- Contact Header -->
<header class="hp-blog-header">
	<div class="hp-container">
		<h1><?php esc_html_e( 'Get In Touch', 'herbalpearls' ); ?></h1>
		<p class="hp-subtitle">
			<?php esc_html_e( 'Have a question about our products, your order, or skincare in general? We would love to hear from you.', 'herbalpearls' ); ?>
		</p>
	</div>
</header>

<!-- Contact Info Cards -->
<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<div class="hp-grid-3">
			<div class="hp-contact-card">
				<div class="hp-contact-card__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--hp-gold-600)" stroke-width="1.5" aria-hidden="true">
						<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Call Us', 'herbalpearls' ); ?></h3>
				<p><a href="tel:+919819887128">+91 9819887128</a></p>
				<span class="hp-caption"><?php esc_html_e( 'Mon–Sat, 10am–7pm IST', 'herbalpearls' ); ?></span>
			</div>

			<div class="hp-contact-card">
				<div class="hp-contact-card__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--hp-gold-600)" stroke-width="1.5" aria-hidden="true">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
						<polyline points="22,6 12,13 2,6"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Email', 'herbalpearls' ); ?></h3>
				<p><a href="mailto:info@herbalpearls.co.in">info@herbalpearls.co.in</a></p>
				<span class="hp-caption"><?php esc_html_e( 'We reply within 24 hours', 'herbalpearls' ); ?></span>
			</div>

			<div class="hp-contact-card">
				<div class="hp-contact-card__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--hp-gold-600)" stroke-width="1.5" aria-hidden="true">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
						<circle cx="12" cy="10" r="3"/>
					</svg>
				</div>
				<h3><?php esc_html_e( 'Visit Us', 'herbalpearls' ); ?></h3>
				<p><?php esc_html_e( 'Mumbai – 400007', 'herbalpearls' ); ?></p>
				<span class="hp-caption"><?php esc_html_e( 'Maharashtra, India', 'herbalpearls' ); ?></span>
			</div>
		</div>
	</div>
</section>

<!-- Contact Form -->
<section class="hp-section hp-surface-1">
	<div class="hp-container hp-container--prose">
		<div class="hp-text-center hp-mb-2">
			<h2><?php esc_html_e( 'Send Us a Message', 'herbalpearls' ); ?></h2>
			<p class="hp-subtitle hp-mt-1">
				<?php esc_html_e( 'Fill out the form and we will get back to you within 24 hours.', 'herbalpearls' ); ?>
			</p>
		</div>

		<div class="hp-contact-form">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<?php
get_footer();
