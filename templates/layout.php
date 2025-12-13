<?php
global $user;
$version='202512130110';
$body_class='';
$header='';
$footer='';
$authed=(isset($_SESSION['user']) && !empty($_SESSION['user']));
if($authed){
  $pathbar=isset($PROPERTIES['pathbar'])?$PROPERTIES['pathbar']:array();
  $pathbar=array('/'=>'Start')+$pathbar;
  $body_class=isset($PROPERTIES['body_class'])?$PROPERTIES['body_class']:'';
  $header=isset($PROPERTIES['header'])?$PROPERTIES['header']:'';
  $footer=isset($PROPERTIES['footer'])?$PROPERTIES['footer']:'';
  $footer=str_replace('%VERSION%',$version,$footer);
}
$scale = isset($_SESSION['scale']) && intval($_SESSION['scale']);
if($scale){
  $body_class = trim($body_class.' scale');
}
$html_class = '';
if(isset($_SESSION['browser'])){
  $html_class .= 'browser_set';
}
?>
<!doctype html>
<html class="<?php echo $html_class ?>">
<head>
<title>Mit Sinn Leben eG</title>
<script data-cfasync="false" type="text/javascript" src="/js/jquery-3.6.0.min.js"></script>
<script data-cfasync="false" type="text/javascript" src="/js/main.js?v=<?php echo $version ?>"></script>
<?php if(file_exists('../web/js/'.$MODULE.'.js')): ?>
  <script data-cfasync="false" type="text/javascript" src="/js/<?php echo $MODULE ?>.js?v=<?php echo $version ?>"></script>
<?php endif ?>
<link rel="stylesheet" type="text/css" media="screen" href="/css/fontawesome/css/fontawesome.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/fontawesome/css/regular.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/fontawesome/css/solid.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/main.css?v=<?php echo $version ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/design.css?v=<?php echo $version ?>" />
<?php if(file_exists('../web/css/'.$MODULE.'.css')): ?>
  <link rel="stylesheet" type="text/css" media="screen" href="/css/<?php echo $MODULE ?>.css?v=<?php echo $version ?>" />
<?php endif ?>
<script>
  $(document).ready(document_ready);
</script>
</head>
<body class="<?php echo $body_class ?>">
  <header>
    <div class="top">
      <?php if(!empty($pathbar)): ?>
          <div class="pathbar">
            <?php 
              foreach($pathbar as $pb_href=>$pb_label){
                $onclick='';
                if(isset($pb_href)){
                  $onclick='onclick="location.href=\''.$pb_href.'\';"';
                }
                echo '<div class="path" '.$onclick.'>'.$pb_label.'</div>';
              }
            ?>
          </div>
          <div class="logout" onclick="location.href='/auth/logout';"><?php echo $scale?($user['name'].' - '):'' ?>Logout</div>
      <?php else: ?>
          <div class="center headline">
            <div class="image">
              <img src="/img/Mit-Sinn-Leben-Logo-1030x579.png" />
            </div>
            <div class="text">Mit Sinn Leben eG</div>
          </div>
      <?php endif ?>
    </div>
    <div id="header">
      <?php echo $header ?>
    </div>
  </header>
  <main>
    <div id="ppcm"></div>
    <div id="background"></div>
    <div id="main">
      <?php echo $CONTENT ?>
    </div>
    <div id="scrollup" onclick="main_scroll(-200)">
      <div class="center">
        <i class="fas fa-angle-up"></i>
      </div>
      <div class="right" onclick="main_scroll(-2000)">
        <i class="fas fa-angle-double-up"></i>
      </div>
    </div>
    <div id="scrolldown" onclick="main_scroll(200)">
      <div class="center">
        <i class="fas fa-angle-down"></i>
      </div>
      <div class="right" onclick="main_scroll(2000)">
        <i class="right fas fa-angle-double-down"></i>
      </div>
    </div>
  </main>
  <footer id="footer">
    <?php echo $footer ?>
  </footer>
  <div id="loading"></div>
  <div id="notify">
    <div id="notify_text"></div>
    <div style="width:100%;text-align:right">
      <div id="notify_cancel" class="button" onclick="$('#notify').hide();$('#loading').hide();">Abbrechen</div>
      <div id="notify_ok" class="button" onclick="$('#notify').hide();$('#loading').hide();">Ok</div>
    </div>
  </div>
</body>
</html>
