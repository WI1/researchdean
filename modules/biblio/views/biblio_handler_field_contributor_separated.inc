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

  function option_definition() {
    $options = parent::option_definition();
    $options['author_filter'] = array('default' => _get_filter_author_by_affiliation());
    return $options;
  }
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    
    $form['style_name'] = array(
      '#type' => 'checkbox',
      '#title' => t('Filter'),
      '#default_value' => $this->options['author_filter'],
      '#options' => _biblio_admin_get_styles(),
      '#description' => t('Filter within selected authors for affiliation')
    );
  }
  
  function _get_filter author_by_affiliation() {
	  $result = db_query('SELECT DISTINCT affiliation FROM {biblio_contributor_data}');
	  $item = db_fetch_array($result)
	  return $item;  
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
						 ' AND bcd.affiliation IN(' .  implode(", ",$this->options['author_affiliation']). ')' .
                         ' ORDER BY bc.rank');
      while ($item = db_fetch_array($result)) {
        // Clean up the $item from vid.
        $vid = $item['vid']; unset($item['vid']);
        $this->items[$vid][] = $item['name'];
	  }
    }
  }
 
 }