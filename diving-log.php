<?php
/*
Plugin Name: Diving Log
Version: 0.1-alpha
Description: Add taxonomies "Life", "Point" to post.
Author: Takayuki Miyauchi
Author URI: https://firegoby.jp/
Plugin URI: https://github.com/miya0001/diving-log
Text Domain: diving-log
Domain Path: /languages
*/

class Diving_Log
{
	public function __construct()
	{
		add_action( "plugins_loaded", array( $this, "plugins_loaded" ) );
	}

	public function plugins_loaded()
	{
		load_plugin_textdomain(
			"diving-log",
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'the_content', array( $this, 'the_content' ), 11 );
	}

	public function init()
	{
		$args = array(
			'hierarchical'          => false,
			'label'                => __( 'Living Things', 'diving-log' ),
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'life' ),
		);

		register_taxonomy( 'life', array( 'post', 'attachment' ), $args );

		$args = array(
			'hierarchical'          => false,
			'label'                => __( 'Point Name', 'diving-log' ),
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'point' ),
		);

		register_taxonomy( 'point', array( 'post', 'attachment' ), $args );
	}

	function the_content( $content )
	{
		$html = '';

		$lives = wp_get_object_terms( get_the_ID(), 'life', array(
			'orderby' => 'count',
			'order' => 'DESC',
		) );

		if ( $lives ) {
			$html .= '<h2>' . __( 'Living Things', 'diving-log' ) . '</h2>';
			$html .= $this->get_term_labels( $lives );
		}

		$points = wp_get_object_terms( get_the_ID(), 'point', array(
			'orderby' => 'count',
			'order' => 'DESC',
		) );

		if ( $points ) {
			$html .= '<h2>' . __( 'Point Name', 'diving-log' ) . '</h2>';
			$html .= $this->get_term_labels( $points );
		}

		return $content . $html;
	}

	private function get_term_labels( $terms )
	{
		$labels = array();
		foreach ( $terms as $term ) {
			$labels[] = sprintf(
				'<a href="%s" class="tax-%s">%s</a>',
				esc_url( get_term_link( $term ) ),
				esc_attr( $term->taxonomy ),
				esc_attr( $term->name )
			);
		}

		return join( ', ', $labels );
	}
}

$diving_log = new Diving_Log();
