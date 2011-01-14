<?php
// $Id: biblio_handler_field_contributor.inc,v 1.1.2.2.2.2 2010/01/25 02:57:39 rjerome Exp $
class biblio_handler_field_contributor_separated extends views_handler_field_prerender_list {
  
  function init(&$view, $options) {
    parent::init($view, $options);
    if ($view->base_table == 'node_revisions') {
      $this->additional_fields['vid'] = array('table' => 'node_revisions', 'field' => 'vid');
    }
    else {
      $this->additional_fields['vid'] = array('table' => 'node', 'field' => 'vid');
    }
	$this->auth_category = isset($this->definition['auth_category']) ? $this->definition['auth_category'] : 1;
  }

  function query() {
    $this->add_additional_fields();
    $this->field_alias = $this->aliases['vid'];
    // add the biblio_type field as tid
    $this->query->add_field($this->table_alias, 'biblio_type', 'biblio_tid');
  }

  function pre_render($values) {
  
   
    $this->items = array();

	$this->field_alias = $this->aliases['vid'];
    $vids = array();
    foreach ($values as $result) {
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
                         ' AND auth_category='. $this->auth_category .
                         ' ORDER BY bc.rank');
      while ($item = db_fetch_array($result)) {
        // Clean up the $item from vid.
        $vid = $item['vid']; unset($item['vid']);
        $this->items[$vid][] = $item['name'];
	  }
    }
  }
 
 }