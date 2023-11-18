<head>
  <script data-cfasync="false" type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
<script>
function tester(){
  var date = new Date();
  var ts=date.getTime();
  $.ajax({
    type: 'GET',
    url: 'http://127.0.0.1/cgi-bin/readscale.cgi?do=read&ts='+ts,
    dataType: "json",
    success: function(data) {
      console.log(data);
    }
  });
}
</script>
</head>
<body>
  <button onclick="tester()">Tester</button>
</body>