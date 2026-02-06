<?php
/**
 * Counter block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Counter block.
 */
class Counter extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'counter';

	/**
	 * Render callback.
	 */
	public function render( $attributes, $content, $block ) {
		$this->attributes = $attributes;
		$this->block      = $block;
		$this->content    = $content;

		$content = $this->sanitize_counter_attributes( $content );

		$content = apply_filters(
			"blockart_{$this->block_name}_content",
			$content,
			$this
		);

		return $content;
	}

	/**
	 * Sanitize counter data attributes to prevent XSS.
	 *
	 * @param string $content Raw HTML content.
	 * @return string Sanitized HTML.
	 */
	private function sanitize_counter_attributes( $content ) {
		if ( empty( $content ) || strpos( $content, 'blockart-counter' ) === false ) {
			return $content;
		}

		// Sanitize data-separator attribute
		$content = preg_replace_callback(
			'/data-separator=(["\'])([^"\']*?)\1/i',
			function ( $matches ) {
				$quote = $matches[1];
				$value = $matches[2];

				// Decode any HTML entities
				$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

				// Strip all HTML tags and dangerous characters
				$value = sanitize_text_field( $value );

				// Additional security: only allow safe separator characters
				// Whitelist: comma, space, period, hyphen, apostrophe
				$value = preg_replace( '/[^,.\s\-\']/', '', $value );

				// Escape for safe attribute output
				$value = esc_attr( $value );

				return 'data-separator=' . $quote . $value . $quote;
			},
			$content
		);

		$numeric_attrs = array( 'data-start', 'data-end', 'data-decimal', 'data-animation' );

		foreach ( $numeric_attrs as $attr ) {
			$content = preg_replace_callback(
				'/' . preg_quote( $attr, '/' ) . '=(["\'])([^"\']*?)\1/i',
				function ( $matches ) use ( $attr ) {
					$quote = $matches[1];
					$value = absint( $matches[2] );
					return $attr . '=' . $quote . $value . $quote;
				},
				$content
			);
		}

		return $content;
	}
}
