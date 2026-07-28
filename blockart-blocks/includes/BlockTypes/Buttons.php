<?php
/**
 * Buttons block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Buttons block.
 */
class Buttons extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'buttons';

	/**
	 * Hides pagination buttons container if there are not enough post pages to paginate.
	 *
	 * @param string $content Saved block content.
	 * @return string
	 */
	protected function build_html( $content ) {
		$is_pagination = $this->get_attribute( 'isPagination', false );

		if ( ! $is_pagination || blockart_is_rest_request() ) {
			return $content;
		}

		$query_id = $this->block->context['blockart/queryId'] ?? null;

		if ( ! $query_id ) {
			return $content;
		}

		$query_args = PostTemplate::get_query_args( $this->block->context );
		$query      = new \WP_Query( $query_args );
		$max_pages  = max( 1, (int) $query->max_num_pages );

		if ( $max_pages <= 1 ) {
			return '';
		}

		return $content;
	}
}
