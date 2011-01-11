/**
 * Show/Hide block content in block admin.
 */

$(document).ready(function(){
  $('.block-at-admin-content').hide();
  $('a.toggle-block').click(function() {
    var id = $(this).attr('id');
    $('.toggle-' + id).toggle('fast');
    return false;
  });
});
