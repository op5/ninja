<?php

	$in_menu = false;

	if (isset($links)) {

		$uri = str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
		$uri = str_replace('?', '', $uri);
		$uri = preg_replace('~/+~', '/', $uri);

		foreach ($links as $section => $entry) {
			
			$linkstring = '';
			if (strtolower($section) == 'about') {
				$linkstring .= '<li style="background: none">'.Kohana::config('config.product_name') . ":" . config::get_version_info().'</li>';
			} elseif (empty($entry)) {
				continue;
			}

			$i = 0;

			$in_menu = false;

			if($entry) {
				foreach ($entry as $name => $data) {

					$id = strtolower($section)."-".$data[1];

					if ($data[2] == 0) {

						// Do not add white-space, line-feeds or carriage returns in here, it will screw up JavaScript .children's and .nextSibling's

						$siteuri = url::site($data[0], null);
						$siteuri = preg_replace('~/+~', '/', $siteuri);

						if (strpos($siteuri, '?')) {
							$siteuri = substr($siteuri, 0, strpos($siteuri, '?'));
						}

						if ($uri == $siteuri) {
							$linkstring .= "<li class='active'><a href='".rtrim(url::base(true), "/").$data[0]."' id='$id' title='".ucwords($name)."' class='ninja_menu_links'>";
							if (strpos($data[1], '.') !== false)
								$linkstring .= "<span class='icon-menu-dark' style='background-image: url(".ninja::add_path('icons/menu-dark/'.$data[1]).")'></span>";
							else
								$linkstring .= "<span class='icon-menu-dark menu-dark-".$data[1]."'></span>";
							$linkstring .= "<span class='nav-seg-span'>".ucwords($name)."</span></a></li>";
							$in_menu = true;
						} else {
							$linkstring .= "<li class='nav-seg'><a href='".rtrim(url::base(true), "/").$data[0]."' id='$id' title='".ucwords($name)."' class='ninja_menu_links'>";
							if (strpos($data[1], '.') !== false)
								$linkstring .= "<span class='icon-menu' style='background-image: url(".ninja::add_path('icons/menu/'.$data[1]).")'></span>";
							else
								$linkstring .= "<span class='icon-menu menu-".$data[1]."'></span>";
							$linkstring .= "<span class='nav-seg-span'>".ucwords($name)."</span></a></li>";
						}

						$i++;

					} // common external links
						elseif($data[2] == 1 ||
							($data[2] == 2 && Kohana::config('config.site_domain') == '/monitor/') ||
							($data[2] == 3 && Kohana::config('config.site_domain') != '/monitor/')) {

						$linkstring .= "<li class='nav-seg'><a href='".$data[0]."' id='$id' title='".ucwords($name)."' target='_blank' class='ninja_menu_links'>";
							if (strpos($data[1], '.') !== false)
								$linkstring .= "<img src='".ninja::add_path('icons/menu/'.$data[1])."' />";
							else
								$linkstring .= "<span class='icon-menu menu-".$data[1]."'></span>";
							$linkstring .= "<span class='nav-seg-span'>".ucwords($name)."</span></a></li>";

					}


				}
			}

			if ($in_menu == true) {
				echo "<ul id='".strtolower($section)."-menu' class='current-sup-menu' style='display: block'>";
			} else {
				echo "<ul id='".strtolower($section)."-menu'>";
			}

			echo $linkstring;

			echo "</ul>";

		}

	}