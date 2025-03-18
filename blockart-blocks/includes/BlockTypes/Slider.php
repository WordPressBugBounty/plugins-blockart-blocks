<?php

/**
 * Slider block.
 *
 * @package BlockArt
 */

namespace BlockArt\BlockTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Buttons block.
 */
class Slider extends AbstractBlock {



	/**
	 * Block name.
	 *
	 * @var string Block name.
	 */
	protected $block_name = 'slider';

	/**
	 * Build html.
	 *
	 * @param string    $content Block content.
	 * @return string
	 */
	public function build_html( $content ) {
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			$html_attrs = blockart_build_html_attrs(
				array(
					'class'       => 'splide',
					'data-splide' => wp_json_encode(
						array(
							'perPage'      => $this->get_attribute( 'perPage', 1 ),
							'perMove'      => $this->get_attribute( 'perMove', 1 ),
							'autoplay'     => $this->get_attribute( 'autoplay', false ),
							'pauseOnHover' => $this->get_attribute( 'pauseOnHover', false ),
							'arrows'       => $this->get_attribute( 'arrows', true ),
							'pagination'   => $this->get_attribute( 'pagination', false ),
							'speed'        => $this->get_attribute( 'speed', 800 ),
							'rewindSpeed'  => $this->get_attribute( 'speed', 800 ),
							'interval'     => $this->get_attribute( 'interval', 5000 ),
							'type'         => $this->get_attribute( 'loop', false ) ? 'loop' : 'slide',
							'gap'          => $this->get_attribute( 'spaceBetween', 10 )['value'] ?? 20,
							'breakpoints'  => array(
								'640'  => array(
									'perPage' => max( 1, $this->get_attribute( 'perPage', 1 ) - 2 ),
								),
								'768'  => array(
									'perPage' => max( 1, $this->get_attribute( 'perPage', 1 ) - 1 ),
									'perMove' => max( 1, $this->get_attribute( 'perMove', 1 ) - 1 ),
								),
								'1024' => array(
									'perPage' => $this->get_attribute( 'perPage', 1 ),
								),
							),
						)
					),
				)
			);
			$content    = str_replace( 'class="splide"', $html_attrs, $content );
		}
		return $content;
	}
}
