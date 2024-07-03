<?php
/**
 * Manages Smart Send plugin updating on the Plugins screen.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class SS_Plugins_Screen_Updates
 */
class SS_Plugins_Screen_Updates {

	/**
	 * The upgrade notice shown inline.
	 *
	 * @var string
	 */
	protected $upgrade_notice = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'in_plugin_update_message-smart-send-logistics/smart-send-logistics.php', array( $this, 'in_plugin_update_message' ), 10, 2 );
	}

	/**
	 * Show plugin changes on the plugins screen.
	 *
	 * @param array    $args Unused parameter.
	 * @param stdClass $response Plugin update response.
	 */
	public function in_plugin_update_message( $args, $response ) {
		$this->new_version            = $response->new_version;
		$this->upgrade_notice         = $this->get_upgrade_notice( $response->new_version );
		
		echo apply_filters( 'ss_in_plugin_update_message', $this->upgrade_notice ? '</p>' . wp_kses_post( $this->upgrade_notice ) . '<p class="dummy">' : '' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get the upgrade notice from WordPress.org.
	 *
	 * @param  string $version Smart Send new version.
	 * @return string
	 */
	protected function get_upgrade_notice( $version ) {
		$transient_name = 'ss_upgrade_notice_' . $version;
		$upgrade_notice = get_transient( $transient_name );//Set to false to skip cashing for debugging

		if ( false === $upgrade_notice ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/smart-send-logistics/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = $this->parse_update_notice( $response['body'], $version );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}
		return $upgrade_notice;
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param  string $content Smart Send readme file content.
	 * @param  string $new_version Smart Send new version.
	 * @return string
	 */
	private function parse_update_notice( $content, $new_version ) {
		$version_parts     = explode( '.', $new_version );
		$check_for_notices = array(
			$version_parts[0] . '.0', // Major.
			$version_parts[0] . '.0.0', // Major.
			$version_parts[0] . '.' . $version_parts[1], // Minor.
			$version_parts[0] . '.' . $version_parts[1] . '.' . $version_parts[2], // Patch.
		);

		// Remove any duplicates to not display the message twice
		$check_for_notices = array_unique( $check_for_notices );

		$notice_regexp     = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $new_version ) . '\s*=|$)~Uis';
		$upgrade_notice    = '';

		foreach ( $check_for_notices as $check_version ) {
			if ( version_compare( SS_SHIPPING_VERSION, $check_version, '>' ) ) {
				continue;
			}

			$matches = null;
			if ( preg_match( $notice_regexp, $content, $matches ) ) {
				$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

				if ( version_compare( trim( $matches[1] ), $check_version, '=' ) ) {
					$upgrade_notice .= '<p class="wc_plugin_upgrade_notice">';
					$upgrade_notice .= preg_replace('/\[([^\[]+)\]\(([^\)]+)\)/', "<a href='$2' target='_blank'>$1</a>", $notices[0]);//Replace markdown links
					$upgrade_notice .= '</p>';
				}
			}
		}

		return wp_kses_post( $upgrade_notice );
	}
}
