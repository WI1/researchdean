<?php
// $Id: biblio_handler_field_contributor.inc,v 1.1.2.2.2.2 2010/01/25 02:57:39 rjerome Exp $
class biblio_handler_field_contributor_separated extends biblio_handler_field_prerender_list {
  
  function construct() {
    parent::construct();
    $this->additional_fields['vid'] = array('table' => 'biblio', 'field' => 'vid');
    $this->auth_category = isset($this->definition['auth_category']) ? $this->definition['auth_category'] : 1;
  }

  function query() {
    $this->add_additional_fields();
    $this->field_alias = $this->aliases['vid'];
    // add the biblio_type field as tid
    $this->query->add_field($this->table_alias, 'biblio_type', 'biblio_tid');
  }

  function pre_render($values) {
    module_load_include('inc', 'biblio', 'styles/biblio_style_'.$this->options['style_name']);
    $author_options_func = 'biblio_style_'.$this->options['style_name'].'_author_options';
    $this->author_options = $author_options_func();

    $vids = array();
    $this->items = array();

    foreach ($values as $result) {
      // Don't add empty results to the array.
      if (!empty($result->{$this->aliases['vid']})) {
        $vids[] = $result->{$this->aliases['vid']};
      }
    }

    if (count($vids)) {
      // Optimize query if $vids has only 1 item in the array, to use '=' instead of 'IN'.
      if (count($vids) > 1){
        $vidstr = 'vid IN ('. implode(',', $vids) .')';
      }
      else {
        $vidstr = 'vid = '. $vids[0];
      }
      $result = db_query('SELECT * FROM {biblio_contributor} bc
                          INNER JOIN {biblio_contributor_data} bcd ON bc.cid = bcd.cid
                          WHERE '. $vidstr .
                         ' AND auth_category='. $this->auth_category.
                         ' ORDER BY bc.rank');
      while ($item = db_fetch_array($result)) {
        // Clean up the $item from vid.
        $vid = $item['vid']; unset($item['vid']);
        $this->items[$vid][] = $item;
      }
    }
  }

  function render($values) {
    parent::set_label($values);
    $vid = $values->{$this->field_alias};
    if (!isset($this->items[$vid])) return NULL;
    return theme('biblio_format_authors', $this->items[$vid], $this->author_options);
  }
}