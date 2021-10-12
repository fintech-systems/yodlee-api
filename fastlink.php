<?php 

$jwtToken = "xxx"; // REDACTED

$fastLinkURL = 'https://ukyinode.stage.yodleedeveloper.uk/authenticate/xxx/?channelAppName=xxx'; // REDACTED

?>

<html>
<head>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">
</head>
<script type='text/javascript' src='https://cdn.yodlee.com/fastlink/v3/initialize.js'></script>
<body>
  <div id="container-fastlink">
    <div style="text-align: center;">
      <input type="submit" id="btn-fastlink" value="Link an Account">
    </div>
  </div>
  <script>
    (function (window) {
      //Open FastLink
      var fastlinkBtn = document.getElementById('btn-fastlink');

      fastlinkBtn.addEventListener('click', function() {
        window.fastlink.open({
          fastLinkURL: '<?php echo $fastLinkURL ;?>',
          jwtToken: 'Bearer <?php echo $jwtToken ;?> ', 
          params: {
            userExperienceFlow : 'Aggregation'
          },
          onSuccess: function (data) {
            console.log(data);
          },
          onError: function (data) {
            console.log(data);
          },
          onExit: function (data) {
            console.log(data);
          },
          onEvent: function (data) {
            console.log(data);
          }
        },
        'container-fastlink');
      },
      false);
    }(window));
  </script>
</body>
