<?php

/**
 * Post template block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * PostTemplate block.
 */
class PostTemplate extends AbstractBlock {


	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'post-template';

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
		$query_args = $this->get_query_args( $block->context );

		if ( $query_args ) {
			$query = new \WP_Query( $query_args );

			$inner_content = '';

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_content = (
						new \WP_Block(
							$block->parsed_block,
							array(
								'postType'         => get_post_type(),
								'postId'           => get_the_ID(),
								'blockart/queryId' => $block->context['blockart/queryId'],
							)
						)
					)->render( array( 'dynamic' => false ) );

					$inner_content .= '<li>' . $post_content . '</li>';
				}
			}
		}

		$format = '<ul class="%s">%s</ul>';

		$classes = array(
			'blockart-post-template',
			'blockart-post-template-' . $attributes['clientId'],
			'columns-' . $attributes['columns'],
		);

		$block_content = sprintf(
			$format,
			implode( ' ', $classes ),
			$inner_content
		);

		return $block_content;
	}


	/**
	 * Returns the query arguments array replacing the default values if not provided.
	 *
	 * @param [object] $block Block Object.
	 * @param [array] $attributes Attributes array.
	 * @return array
	 */
	public static function get_query_args( $context ) {
		$raw_args  = $context['query'] ?? [];
		$args_list = $context['queryArgs'] ?? [];
		$query_id  = $context['blockart/queryId'] ?? null;

		$args = array();

		foreach ( $raw_args as $key => $value ) {
			// Do not pass empty args.
			if ( ! $value || ! in_array( $key, array_merge( $args_list, array( 'post_type', 'per_page', 'inherit' ) ) ) ) {
				continue;
			}

			switch ( $key ) {
				case 'per_page':
					$args['posts_per_page'] = $value;
					break;

				case 'before':
					$args['date_query']['before'] = $value;
					break;

				case 'after':
					$args['date_query']['after'] = $value;
					break;

				default:
					$args[ $key ] = is_array( $value ) ? implode( ',', $value ) : $value;
			}
		}

		$defaults = array(
			'posts_per_page' => 4,
			'post_type'      => 'post',
		);

		$args = wp_parse_args( $args, $defaults );

		$page           = blockart_get_query_page( $query_id );
		$args['offset'] = ( $page - 1 ) * $args['posts_per_page'];

		return $args;
	}
}
