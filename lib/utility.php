<?php

if(!function_exists('pd_trim_r')) {
	function pd_trim_r($item) {
		if(is_array($item)) {
			return array_map('pd_trim_r', $item);
		} else if(is_string($item)) {
			return trim($item);
		}
		
		return $item;
	}
}

if(!function_exists('pd_error_output_simple')) {
	function pd_error_output_simple($errors, $code, $output = 'class="pd-error-text"') {
		if (is_wp_error($errors) && $errors->get_error_message($code)) {
			echo $output;
		}
	}
}

if(!function_exists('pd_error_output')) {
	function pd_error_output($errors, $code, $before = '<br /><small class="pd-error-text">', $after = '</small>') {
		if (is_wp_error($errors) && $errors->get_error_message($code)) {
			echo $before . esc_html($errors->get_error_message($code)) . $after;
		}
	}
}

if(!function_exists('pd_yes_no')) {
	function pd_yes_no($item) {
		return $item == 'yes' ? 'yes' : 'no';
	}
}

if(!function_exists('pd_array_merge_recursive_distinct')) {
	function pd_array_merge_recursive_distinct() {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		if (!is_array($base))
			$base = empty($base) ? array() : array($base);
		foreach ($arrays as $append) {
			if (!is_array($append))
				$append = array($append);
			foreach ($append as $key => $value) {
				if (!array_key_exists($key, $base) and !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if (is_array($value) or is_array($base[$key])) {
					$base[$key] = splash_array_merge_recursive_distinct($base[$key], $append[$key]);
				} else if (is_numeric($key)) {
					if (!in_array($value, $base))
						$base[] = $value;
				} else {
					$base[$key] = $value;
				}
			}
		}
		return $base;
	}
}