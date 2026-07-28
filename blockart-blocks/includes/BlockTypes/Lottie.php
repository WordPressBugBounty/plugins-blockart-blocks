<?php
/**
 * Lottie block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Lottie block.
 */
class Lottie extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'lottie';

	/**
	 * Render callback.
	 *
	 * @param string $content Block content.
	 *
	 * @return string
	 */
	public function build_html( $content ) {
		// A falsy value is treated as 'auto' so it can never serialize without a working play trigger.
		$play_on = $this->get_attribute( 'playOn', 'auto' );
		$play_on = $play_on ? $play_on : 'auto';

		if ( 'auto' === $play_on ) {
			// Self-heals markup saved before the autoplay attribute went missing.
			if ( false === strpos( $content, ' autoplay' ) && false === strpos( $content, 'data-autoplay-delay' ) ) {
				$content = str_replace( '<lottie-player', '<lottie-player autoplay', $content );
			}
		} else {
			// Marker read by the frontend script, not lottie-player's native `hover` attribute.
			$content = str_replace( '<lottie-player', "<lottie-player data-play-on=\"{$play_on}\"", $content );
		}

		return $content;
	}
}
