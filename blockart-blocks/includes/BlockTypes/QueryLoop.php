<?php

/**
 * Query Loop block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * QueryLoop block.
 */
class QueryLoop extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'query-loop';

	/**
	 * Render callback.
	 *
	 * @param array     $attributes Block attributes.
	 * @param string    $content Block content.
	 * @param \WP_Block $block Block object.
	 *
	 * @return string
	 */
	public function render( $attributes, $content, $block ) {
		// Populate $this->attributes so get_attribute() reads saved block data.
		$this->attributes = $attributes;
		$this->block      = $block;
		$this->content    = $content;

		if ( blockart_is_rest_request() ) {
			return $content;
		}

		$classes = array(
			'blockart-query-loop',
			'blockart-query-loop-' . $this->get_attribute( 'clientId', '', true ),
		);

		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}

		if ( ! empty( $attributes['align'] ) ) {
			$classes[] = 'align' . $attributes['align'];
		}

		$id_attr = ! empty( $attributes['cssID'] ) ? sprintf( ' id="%s"', esc_attr( $attributes['cssID'] ) ) : '';

		return sprintf(
			'<div class="%s"%s>%s</div>',
			esc_attr( implode( ' ', $classes ) ),
			$id_attr,
			$content
		);
	}
}
