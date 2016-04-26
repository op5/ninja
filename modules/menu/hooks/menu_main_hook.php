<?php

Event::insert_event('ninja.menu.setup', 0, function () {

  $menu = Event::$data;

  $menu->set('Branding', null, 0, null, array('style' => 'padding-top: 2px'))
    ->get('Branding')->set_html_label(brand::get())
    ->set('About', 'menu/about', 0, 'icon-16 x16-op5', array('id' => 'open-about-button'))
    ->set('HTTP API', '/api/help', 3, 'icon-16 x16-op5', array('target' => '_blank'));

  $menu->set('Monitor', null, 1, 'icon-16 x16-monitoring', array('style' => 'padding-top: 8px'))->get('Monitor')
    ->set('Tactical Overview', 'tac', 0, 'icon-16 x16-tac')
    ->set('Network Outages', 'outages', 1, 'icon-16 x16-outages')
    ->set('NagVis', 'nagvis', null, 'icon-16 x16-nagvis');

  $menu->set('Report', null, 2, 'icon-16 x16-reporting', array('style' => 'margin-top: 8px'));

});

Event::add('system.post_controller_constructor', function () {

  $controller = Event::$data;

  $controller->template->js[] = 'modules/menu/media/js/about.js';
  $controller->template->css[] = 'modules/menu/media/css/about.css';

});
