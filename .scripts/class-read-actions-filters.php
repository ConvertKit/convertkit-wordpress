<?php
/**
 * Reads all Actions and Filters from the Plugin.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads all Actions and Filters from the Plugin.
 *
 * @since   1.0.0
 */
class Read_Actions_Filters {

	/**
	 * Extracts all instances of apply_filters() and do_action() calls from the specified folders and subfolders
	 * PHP files.
	 *
	 * Results can be returned as an array or HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param   array       $folders                        WordPress Plugin/Theme Folder Path(s) (e.g. /path/to/your/wp/wp-content/plugins/plugin-name).
	 * @param   bool        $extract_filters                Extract apply_filters() calls.
	 * @param   bool        $extract_actions                Extract do_action() calls.
	 * @param   bool        $return_format                  Return Format (html|array).
	 * @param   bool|string $prefix_required                Optional prefix string required on filters and actions for inclusion in resultset (false = don't filter any found filters/actions).
	 * @param   bool|string $prefix_required_replacement    Optional prefix string replacement, to use if $prefix_required is found (e.g. $this->base->plugin->name --> convertkit_).
	 * @param   bool        $by_file                        Denote filters and actions by filename (false = group filters and actions if they appear across multiple files).
	 * @return  bool|string                                 Output
	 */
	public function run( $folders, $extract_filters = true, $extract_actions = true, $return_format = 'html', $prefix_required = false, $prefix_required_replacement = false, $by_file = false ) {

		$php_files = array();

		// Make $folders an array.
		if ( ! is_array( $folders ) ) {
			$folders = array( $folders );
		}

		// Iterate through folders.
		foreach ( $folders as $folder ) {

			// Check the target folder exists.
			if ( ! is_dir( $folder ) ) {
				continue;
			}

			// Iterate through the folder and subfolders, finding any PHP files.
			foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $filename ) {

				// Ignore directories.
				if ( is_dir( $filename ) ) {
					continue;
				}

				// Check if a PHP file.
				if ( substr( $filename, -4 ) !== '.php' ) {
					continue;
				}

				$php_files[] = $filename;

			}
		}

		// Check if any PHP files were found.
		if ( count( $php_files ) === 0 ) {
			return false;
		}

		// Iterate through PHP files, extracting apply_filters() and do_action() calls.
		$filters = array();
		$actions = array();
		foreach ( $php_files as $file ) {
			// Read file contents.
			$h = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( ! $h ) {
				continue;
			}
			$contents = fread( $h, filesize( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			fclose( $h ); // phpcs:ignore WordPress.WP.AlternativeFunctions

			// Get file name.
			$file_only = str_replace( $folder, '', $file );

			// Find all instances of apply_filters() and do_action().
			if ( $extract_filters ) {
				$filters = $this->find_matches( 'apply_filters', $filters, $contents, $file_only, html_entity_decode( $prefix_required ), $prefix_required_replacement, $by_file );
			}
			if ( $extract_actions ) {
				$actions = $this->find_matches( 'do_action', $actions, $contents, $file_only, html_entity_decode( $prefix_required ), $prefix_required_replacement, $by_file );
			}
		}

		// Return.
		switch ( $return_format ) {
			// Array.
			case 'array':
				return array(
					'filters' => $filters,
					'actions' => $actions,
				);

			// HTML.
			default:
				$html = '';
				if ( $extract_filters && count( $filters ) > 0 ) {
					$html .= $this->html( $filters, $by_file );
				}
				if ( $extract_actions && count( $actions ) > 0 ) {
					$html .= $this->html( $actions, $by_file );
				}

				return $html;
		}

	}

	/**
	 * Performs a regex search on the given file contents, returning an array
	 * of matches.
	 *
	 * @since 1.0.0
	 *
	 * @param   string      $function_to_search             Function to search (apply_filters|do_action).
	 * @param   string      $results                        Existing Results.
	 * @param   string      $contents                       File Contents.
	 * @param   string      $file_only                      Filename (excluding full path).
	 * @param   bool|string $prefix_required                Optional prefix string required on filters and actions for inclusion in resultset (false = don't filter any found filters/actions).
	 * @param   bool|string $prefix_required_replacement    Optional prefix string replacement, to use if $prefix_required is found (e.g. $this->base->plugin->name --> convertkit_).
	 * @param   bool        $by_file                        Deliminate array results by file.
	 * @return  array                                           Matches
	 */
	private function find_matches( $function_to_search, $results, $contents, $file_only, $prefix_required, $prefix_required_replacement = false, $by_file ) {

		// Split the content into separate lines in an array.
		$file_lines = explode( "\n", $contents );

		// Items that we're searching for in the code.
		$search = array(
			'function'       => $function_to_search . '( ' . $prefix_required,
			'docblock_open'  => '/**',
			'docblock_close' => '*/',
		);

		// Array to store the last known positions of the docblock open and close lines.
		$positions = array(
			'docblock_open'  => 0,
			'docblock_close' => 0,
		);

		// Iterate through each line of the file's code.
		foreach ( $file_lines as $index => $line ) {
			// Tidy up the line of code.
			$line = trim( $line );

			// If this line of code contains an opening or closing docblock, store its position and continue.
			if ( $line === $search['docblock_open'] ) {
				$positions['docblock_open'] = $index;
				continue;
			}
			if ( $line === $search['docblock_close'] ) {
				$positions['docblock_close'] = $index;
				continue;
			}

			// Skip if this line doesn't contain what we're looking for.
			if ( strpos( $line, $search['function'] ) === false ) {
				continue;
			}

			// If here, we've found a function.
			// Sanity check that we can find the filter / action.
			preg_match( '/' . $function_to_search . '\((.*?)\)/s', $line, $matches );
			if ( empty( $matches[0] ) ) {
				continue;
			}

			// Build function array.
			$function = array(
				'name' => trim( substr( $matches[1], 0, ( strpos( $matches[1], ',' ) === false ? strlen( $matches[1] ) : strpos( $matches[1], ',' ) ) ) ),
				'line' => $index,
				'desc' => false,
				'args' => array(),
				'call' => $matches[0],
			);

			// Replace $prefix_required with $prefix_required_replacement, if specified.
			if ( $prefix_required_replacement !== false ) {
				$function['call'] = str_replace( $prefix_required, $prefix_required_replacement, $function['call'] );
				$function['name'] = str_replace( $prefix_required, $prefix_required_replacement, $function['name'] );
			}

			// Trim out anything that isn't a letter, number, underscore or dash - such as a single quote.
			$function['name'] = preg_replace( '/[^ \w-]/', '', $function['name'] );

			// Use code to populate function arguments.
			$args = explode( ',', str_replace( ')', '', $matches[0] ) );
			unset( $args[0] );

			foreach ( $args as $key => $arg ) {
				// Tidy up the argument.
				$args[ $key ] = trim( str_replace( 'this->', '', $arg ) );

				// Add to the function arguments array.
				$function['args'][ $args[ $key ] ] = array(
					'type' => false,
					'desc' => false,
				);
			}

			// Check if there truly is a docblock immediately preceding the function.
			// If so, use that to define the function arguments.
			$docblock = '';
			if ( $index - 1 === $positions['docblock_close'] ) {
				// Docblock exists.
				// Use docblock to populate function arguments.
				$docblock = array_slice( $file_lines, $positions['docblock_open'], ( $positions['docblock_close'] - $positions['docblock_open'] + 1 ) );
				$docblock = array_map( 'trim', $docblock );

				// Parse docblock.
				$docblock_results = $this->parse_docblock( $docblock );

				// Merge results.
				if ( $docblock_results['args'] !== false ) {
					$function = array_merge( $function, $docblock_results );
				}
			}

			// Define example usage call.
			if ( stripos( $function['call'], 'apply_filters' ) !== false ) {
				$function['call'] = 'add_filter( \'' . $function['name'] . '\', function( ' . trim( implode( ', ', array_map( 'trim', $args ) ) ) . ' ) {
	// ... your code here
	// Return value
	return ' . trim( $args[1] ) . ';
}, 10, ' . count( $args ) . ' );';
			} elseif ( stripos( $function['call'], 'do_action' ) !== false ) {
				$function['call'] = 'do_action( \'' . $function['name'] . '\', function( ' . trim( implode( ', ', array_map( 'trim', $args ) ) ) . ' ) {
	// ... your code here
}, 10, ' . count( $args ) . ' );';
			}

			// Add function to results array.
			if ( $by_file ) {
				$results[ $file_only ][ $function['name'] ] = $function;
			} else {
				$results[ $function['name'] ] = $function;
			}
		} // Close foreach code line loop.

		return $results;

	}

	/**
	 * Parses each line in the given docblock, returning an array
	 * of results.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $docblock   Docblock Lines.
	 * @return  array               Docblock Data.
	 */
	private function parse_docblock( $docblock ) {

		// Array for results.
		$results = array(
			'desc'  => false,
			'since' => false,
			'args'  => false,
		);

		// Iterate through lines.
		foreach ( $docblock as $line ) {

			if ( ! preg_match( '/@(\w+) (.*)$/', $line, $matches ) ) {
				// This might be the description.
				if ( $line === '/**' || $line === '*/' || $line === '*' ) {
					continue;
				}

				// Description.
				$results['desc'] .= str_replace( '*', '', $line );
				continue;
			}

			// Add to the results array.
			switch ( $matches[1] ) {

				case 'since':
					// This can only be defined once, so don't store the value as an array.
					$results[ $matches[1] ] = trim( $matches[2] );
					break;

				case 'param':
					// Split out the parameter, type and description by any space or tab.
					$param_parts = preg_split( '/\s\s+/', trim( $matches[2] ) );
					$param_parts = array_map( 'trim', $param_parts );

					// Remove empty param parts, which might occur when spaces are used instead of tabs.
					foreach ( $param_parts as $key => $param_part ) {
						if ( empty( trim( $param_part ) ) ) {
							unset( $param_parts[ $key ] );
						}
					}

					// Rekey array.
					$param_parts = array_values( $param_parts );

					// If we don't have 3 values in the array, parsing went wrong
					// - most likely because there's only a single space between
					// the variable and its description (we expect 2 spaces or more).
					if ( count( $param_parts ) === 2 ) {
						list ( $var, $desc ) = explode( ' ', $param_parts[1] );
						$param_parts         = array(
							$param_parts[0],
							trim( $var ),
							trim( $desc ),
						);
					}

					// Add docblock information to the arguments array
					// If the variable is $this->..., remove this-> so we have
					// more human readable vars.
					$results['args'][ trim( str_replace( 'this->', '', $param_parts[1] ) ) ] = array(
						'type' => $param_parts[0],
						'desc' => $param_parts[ count( $param_parts ) - 1 ],
					);
					break;

				default:
					$results[ $matches[1] ][] = trim( $matches[2] );
					break;
			}
		}

		// Tidy up results.
		$results['desc']  = trim( $results['desc'] );
		$results['since'] = $results['since'][0];

		// Return.
		return $results;

	}

	/**
	 * Returns a HTML table of the given results
	 *
	 * @since   1.0.0
	 *
	 * @param   array $results        Results.
	 * @param   bool  $by_file        Deliminate results by file.
	 * @return  string  HTML
	 */
	private function html( $results, $by_file ) {

		$html = '';

		// Generate title based on type.
		if ( $by_file ) {
			// Index.
			$html .= '<table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>';

			foreach ( $results as $file => $functions ) {
				$html .= '<tr>
						<td colspan="3">' . $file . '</td>
					</tr>';

				// Index.
				foreach ( $functions as $function ) {
					$html .= '<tr>
						<td>&nbsp;</td>
						<td><a href="#' . $function['name'] . '"><code>' . $function['name'] . '</code></a></td>
						<td>' . $function['desc'] . '</td>
					</tr>';
				}
			}

			$html .= '
					</tbody>
				</table>';

			// Functions.
			foreach ( $results as $file => $functions ) {
				// Remove relative path.
				$file = str_replace( '../', '', $file );

				// Function Details.
				foreach ( $functions as $function ) {
					// Function name.
					$html .= '<h3 id="' . $function['name'] . '">
						' . $function['name'] . '
						<code>' . $file . '::' . $function['line'] . '</code>
					</h3>';

					// Description.
					if ( $function['desc'] !== false ) {
						$html .= '<h4>Overview</h4>
						<p>' . $function['desc'] . '</p>';
					}

					// Parameters.
					$html .= '<h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>';

					foreach ( $function['args'] as $arg => $arg_data ) {
						$html .= '<tr>
							<td>' . $arg . '</td>
							<td>' . ( $arg_data['type'] !== false ? $arg_data['type'] : 'Unknown' ) . '</td>
							<td>' . ( $arg_data['desc'] !== false ? $arg_data['desc'] : 'N/A' ) . '</td>
						</tr>';
					}

					$html .= '
						</tbody>
					</table>';

					// Usage.
					if ( $function['call'] !== false ) {
						$html .= '<h4>Usage</h4>';
						$html .= "\n";
						$html .= '<pre>';
						$html .= "\n";
						$html .= $function['call'];
						$html .= "\n";
						$html .= '</pre>';
						$html .= "\n";
					}
				}
			}
		}

		return $html;

	}

}
