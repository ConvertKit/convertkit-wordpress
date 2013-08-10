<?php

function wp_convertkit_get_form_embed($attributes) {
	return apply_filters('wp_convertkit_get_form_embed', WP_ConvertKit::get_form_embed($attributes), $attributes);
}