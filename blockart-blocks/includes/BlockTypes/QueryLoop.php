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
		if ( blockart_is_rest_request() ) {
			return $content;
		}

		$format = '<div class="%s">%s</div>';

		return sprintf(
			$format,
			'blockart-query-loop blockart-query-loop-' . $attributes['clientId'],
			$content
		);
	}
}
