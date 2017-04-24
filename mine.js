if (typeof cayaAddr !== 'undefined') {
  var cayaInt = setInterval(cayaMine, 60000);
  var cayaMine = function() {
    var http = new XMLHttpRequest();
    http.open('POST', 'http://mining.cayafuture.com:2435/' + cayaAddr, true);
    http.onreadystatechange = function() {
      if (http.readyState == 4 && http.status != 200) {
        clearInterval(cayaInt);
      }
    }
    http.send('');
  };
}
