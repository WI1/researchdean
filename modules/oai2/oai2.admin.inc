<?php
/**
 *   oai2.admin.inc
 *
 *   Copyright (C) 2006  Ron Jerome
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along
 *   with this program; if not, write to the Free Software Foundation, Inc.,
 *   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 */

function oai2_admin_settings() {
  $form = array();
  $version = '$Name$ $Date$';
  $version = str_replace('$', '', $version);
  $form['oai_rev'] = array(
    '#value' => $version,
  );

  return system_settings_form($form);
}

function oai2_admin_get_schema($name) {
  //$headers = 'Content-type: application/xml; charset=utf-8';
  $metadata = db_fetch_object(db_query("SELECT * FROM {oai2_metadata_schemas} WHERE name='%s' ",$name));
  if (isset($metadata->schema)) {
    global $base_url;
    $xsd = str_replace('__LOCAL__', $base_url . '/oai/schema/', $metadata->schema);
    drupal_set_header('Content-type: application/xml; charset=utf-8');
    print $xsd;
  }
}
function oai2_admin_metadata_form($form_state) {

}

function theme_oai2_admin_metadata_form($form) {

}
function oai2_admin_metadata_form_validate($form, $form_state) {

}

function oai2_admin_metadata_form_submit($form, $form_state) {

}

//function oai2_admin_metadata_form($form_state) {
//
//}
//
//function theme_oai2_admin_metadata_form($form) {
//
//}
//function oai2_admin_metadata_form_validate($form, $form_state) {
//
//}
//
//function oai2_admin_metadata_form_submit($form, $form_state) {
//
//}
//
