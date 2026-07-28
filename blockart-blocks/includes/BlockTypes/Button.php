<?php
/**
 * Button block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Button block class.
 */
class Button extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'button';

	/**
	 * Rewrites pagination buttons (prev/next/page_list) into real links for the
	 * query loop they belong to. The block otherwise only carries a marker
	 * attribute with no link, since the target page depends on the query
	 * results and current page at render time.
	 *
	 * @param string $content Saved block content.
	 * @return string
	 */
	protected function build_html( $content ) {
		$pagination_action = $this->get_attribute( 'paginationAction', '' );

		if ( ! $pagination_action || blockart_is_rest_request() ) {
			return $content;
		}

		$query_id = $this->block->context['blockart/queryId'] ?? null;

		if ( ! $query_id ) {
			return $content;
		}

		$query_args = PostTemplate::get_query_args( $this->block->context );
		$query      = new \WP_Query( $query_args );
		$max_pages  = max( 1, (int) $query->max_num_pages );
		$current    = (int) blockart_get_query_page( $query_id );
		$param      = "bart_query_{$query_id}_page";

		$build_url = function ( $page ) use ( $param ) {
			return esc_url( add_query_arg( $param, $page ) );
		};

		switch ( $pagination_action ) {
			case 'prev':
			case 'next':
				$target    = 'prev' === $pagination_action ? $current - 1 : $current + 1;
				$is_within = $target >= 1 && $target <= $max_pages;

				if ( ! $is_within ) {
					return preg_replace_callback(
						'/<a\b[^>]*>/',
						function ( $matches ) {
							$tag = preg_match( '/class="/', $matches[0] )
								? preg_replace( '/class="/', 'class="is-disabled ', $matches[0], 1 )
								: preg_replace( '/<a\b/', '<a class="is-disabled"', $matches[0], 1 );

							return preg_replace( '/<a\b/', '<a aria-disabled="true"', $tag, 1 );
						},
						$content,
						1
					);
				}

				$href = $build_url( $target );

				return preg_replace_callback(
					'/<a\b[^>]*>/',
					function ( $matches ) use ( $href ) {
						return preg_match( '/href="/', $matches[0] )
							? preg_replace( '/href="[^"]*"/', 'href="' . esc_attr( $href ) . '"', $matches[0], 1 )
							: preg_replace( '/<a\b/', '<a href="' . esc_attr( $href ) . '"', $matches[0], 1 );
					},
					$content,
					1
				);

			case 'page_list':
				// Reuse core's own truncation (1 2 ... 8 9) rather than a bespoke algorithm.
				$page_links = paginate_links(
					array(
						'base'      => add_query_arg( $param, '%#%' ),
						'format'    => '',
						'current'   => $current,
						'total'     => $max_pages,
						'type'      => 'array',
						'prev_next' => false,
					)
				);

				if ( ! $page_links ) {
					return $content;
				}

				$links = array();

				foreach ( $page_links as $page_link ) {
					if ( false !== strpos( $page_link, 'dots' ) ) {
						$links[] = '<span class="blockart-pagination-ellipsis">&hellip;</span>';
						continue;
					}

					$style = $this->get_attribute( 'style', 'filled' );
					$style = in_array( $style, array( 'filled', 'outline', 'plain', 'link' ), true ) ? $style : 'filled';

					$size = $this->get_attribute( 'size', 'large' );
					$size = in_array( $size, array( 'large', 'medium', 'small', 'custom' ), true ) ? $size : 'large';

					$is_current = false !== strpos( $page_link, 'current' );
					$classes    = sprintf(
						'blockart-button-link is-style-%s is-%s blockart-pagination-page%s',
						$style,
						$size,
						$is_current ? ' is-active' : ''
					);
					$page_link  = preg_replace( '/class="[^"]*"/', 'class="' . $classes . '"', $page_link, 1 );

					// The current page renders as a <span> by default; keep markup consistent as a link.
					if ( $is_current ) {
						$page_link = preg_replace( '/^<span/', '<a href="' . esc_url( $build_url( $current ) ) . '"', $page_link, 1 );
						$page_link = preg_replace( '/<\/span>$/', '</a>', $page_link, 1 );
					}

					$links[] = $page_link;
				}

				return preg_replace( '/<a\b[^>]*>.*?<\/a>/s', implode( '', $links ), $content );
		}

		return $content;
	}
}
