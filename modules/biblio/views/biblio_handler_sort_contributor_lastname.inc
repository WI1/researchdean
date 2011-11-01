<?php
// $Id: biblio_handler_sort_contributor_lastname.inc,v 1.1.6.2 2010/12/01 03:22:51 rjerome Exp $

class biblio_handler_sort_contributor_lastname extends views_handler_sort {

  function query() {
    parent::query();
    $this->query->add_where(0, "biblio_contributor.rank = 0");
  }

}

