<?php

	/**
	 * Ninja Toolbar Management
	 *
	 *
	 */

	class Toolbar_Controller {

		public $title = false;
		public $subtitle = false;
		public function __construct ( $title = false, $subtitle = false ) {

			$this->title = ( gettype( $title ) == "string" ) ? $title: false;
			$this->subtitle = ( gettype( $subtitle ) == "string" ) ? $subtitle: false;

		}

		private $should_render_buttons = false;
		public function should_render_buttons($should_render_buttons = true) {
			$this->should_render_buttons = $should_render_buttons;
		}

		private $buttons = array();
		public function button ( $title, $attr = false ) {
			$this->should_render_buttons(true);
			if ( !$attr ) $attr = array();

			$this->buttons[ ] = array(
				"name" => $title,
				"attr" => $attr
			);

		}

		private $tabs = array();
		public function tab ( $title, $attr = false ) {

			if ( !$attr ) $attr = array();

			$this->tabs[ ] = array(
				"name" => $title,
				"attr" => $attr
			);

		}

		private $info = array();
		public function info ( $html ) {

			if ( gettype( $html ) == "string" ) {
				$this->info[ ] = $html;
				return true;
			}

			return false;

		}

		private function get_button_html () {

			$h = "";

			foreach ( $this->buttons as $b ) {
				$a = array();
				foreach ( $b[ "attr" ] as $k => $v )
					$a[] = "$k=\"$v\"";
				$h .= "<a " . implode( " ", $a ) . ">" . $b[ "name" ] . "</a>";
			}

			return $h;

		}

		public function render () {

			print '<div class="main-toolbar">';

			if ( gettype( $this->title ) == "string" ) {
				print '<div class="main-toolbar-title">' . $this->title . '</div>';
			}

			if ( gettype( $this->subtitle ) == "string" ) {
				print '<div class="main-toolbar-subtitle">' . $this->subtitle . '</div>';
			}

			if ( count( $this->info ) > 0 ) {
				print '<div class="main-toolbar-info">';
				foreach ( $this->info as $html ) print $html;
				print '</div>';
			}

			if ($this->should_render_buttons) {
				print '<div class="main-toolbar-buttons">';
				print $this->get_button_html();
				print '</div>';
			}

			print '<div class="clear"></div>';
			print '</div>';

		}

	}