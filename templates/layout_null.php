<script>
  $(document).ready(document_ready);
</script>
<?php

if(isset($PROPERTIES['header'])){
  echo '<HEADER>'.$PROPERTIES['header'].'</HEADER>';
}
echo $CONTENT;
if(isset($PROPERTIES['footer'])){
  echo '<FOOTER>'.$PROPERTIES['footer'].'</FOOTER>';
}


