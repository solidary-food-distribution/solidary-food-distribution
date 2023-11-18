<?php

if(isset($PROPERTIES['header'])){
  echo '<header>'.$PROPERTIES['header'].'</header>';
}
echo $CONTENT;
if(isset($PROPERTIES['footer'])){
  echo '<footer>'.$PROPERTIES['footer'].'</footer>';
}
