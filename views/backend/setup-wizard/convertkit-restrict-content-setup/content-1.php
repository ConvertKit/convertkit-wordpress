<?php
/**
 * Outputs the content for the Restrict Content Setup Wizard > Setup step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<h1><?php esc_html_e( 'Member Content', 'convertkit' ); ?></h1>
<p>
	<?php
	echo sprintf(
		/* translators: Link to ConvertKit Products */
		esc_html__( 'This will generate content that visitors can access once they purchase a %s.', 'convertkit' ),
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_attr( 'https://app.convertkit.com/products' ),
			esc_html__( 'ConvertKit product', 'convertkit' )
		)
	);
	?>
</p>

<hr />

<h2><?php esc_html_e( 'What type of content are you offering?', 'convertkit' ); ?></h2>

<div class="convertkit-setup-wizard-grid">
	<div>
		<a href="<?php echo esc_url( $this->download_url ); ?>" class="button button-primary button-hero">
			<?php esc_html_e( 'Download', 'convertkit' ); ?>
		</a>
		<span class="description">
			<?php esc_html_e( 'Require visitors to purchase a ConvertKit product, granting access to a single Page\'s content, which includes downloadable assets.', 'convertkit' ); ?>
		</span>
	</div>

	<div>
		<a href="<?php echo esc_url( $this->course_url ); ?>" class="button button-primary button-hero">
			<?php esc_html_e( 'Course', 'convertkit' ); ?>
		</a>
		<span class="description">
			<?php esc_html_e( 'Require visitors to purchase a ConvertKit product, granting access to a sequential series of Pages, such as a course, lessons or tutorials.', 'convertkit' ); ?>
		</span>
	</div>
</div>
