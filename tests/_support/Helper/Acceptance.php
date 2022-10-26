<?php
namespace Helper;

/**
 * For readibility and portability of helpers between Plugins,
 * Acceptance helpers which work with specific WordPress / API
 * functionality (such as Gutenberg, Plugin config or the API)
 * can be found in the `Acceptance` subfolder.
 * If adding a new Helper to the `Acceptance` subfolder, remember
 * to load it through the `acceptance.suite.yml` file.
 *
 * Helper functions placed here should be very generic.
 */
class Acceptance extends \Codeception\Module
{
	/**
	 * Define custom actions here
	 */
}
