(function($) {
  $('#setRange').click(function() {
    var timeago = "?timeago=" + document.getElementById('select2-TimeAgo-container').innerHTML;
    window.location.href = window.location.href.replace( /[\?#].*|$/, timeago);
  });
})(jQuery);
