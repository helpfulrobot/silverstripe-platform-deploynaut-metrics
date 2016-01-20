(function($) {
  $('#rangeheader a').click(function() {
    var timeago = "?timeago=" + document.getElementById('TimeAgo').value;
    window.location.href = window.location.href.replace( /[\?#].*|$/, timeago);
  });
})(jQuery);
