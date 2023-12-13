<?php
$PROPERTIES['pathbar']=array(
  '/settings' => 'Einstellungen',
  '/preferences' => 'Präferenzen'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php foreach($products as $product): ?>
  <div class="row product" id="product<?php echo $product->id ?>">
    <div class="col2">
      <div class="image">
        <!--<img src="" />-->
      </div>
    </div>
    <div class="col4">
      <div class="info">
        <div class="name">
          <b><?php echo $product->name ?></b>
        </div>
        <div class="producer">
          <?php echo $product->producer->name ?>
        </div>
      </div>
    </div>
    <div class="col6 preferences">
      <?php
        $select = array(
          3 => 'fa-regular fa-face-grin-stars',
          2 => 'fa-regular fa-face-smile',
          1 => 'fa-regular fa-face-meh',
          0 => 'fa-solid fa-ban'
        );
        if(!isset($preferences[$product->id])){
          $preferences[$product->id] = 2;
        }
      ?>
      <?php foreach($select as $value => $class): ?>
        <div onclick="preferences_select(<?php echo $product->id.','.$value ?>)" class="value<?php echo $value.($preferences[$product->id]==$value?' checked':'') ?>">
          <i class="<?php echo $class ?>"></i>
        </div>
      <?php endforeach ?>
    </div>
  </div>
<?php endforeach ?>
