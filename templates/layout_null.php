<?php if(!isset($PROPERTIES['no_document_ready'])): ?>
<script>
  $(document).ready(document_ready);
</script>
<?php endif ?><?php

if(isset($PROPERTIES['body_class'])){
  echo '<BODY_CLASS>'.$PROPERTIES['body_class'].'</BODY_CLASS>';
}
if(isset($PROPERTIES['header'])){
  echo '<HEADER>'.$PROPERTIES['header'].'</HEADER>';
}
echo $CONTENT;
if(isset($PROPERTIES['footer'])){
  echo '<FOOTER>'.$PROPERTIES['footer'].'</FOOTER>';
}


