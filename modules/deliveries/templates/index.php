<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Anlieferungen');
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($deliveries as $delivery): ?>
  <div class="row">
    <div class="col4">
      <div>
        <b><?php echo format_date($delivery->created,'j.n.Y') ?></b>
        <br>
        <?php echo format_date($delivery->created,'H:i', false) ?>
      </div>
    </div>
    <div class="col3">
      <div><b><?php echo $suppliers[$delivery->supplier_id]->name ?></b></div>
    </div>
    <div class="col10">
      <div>
        <?php
          $items = '';
          foreach($delivery->items as $item){
            $items .= $products[$item->product_id]->name.', ';
          }
          echo rtrim($items, ', ');
        ?>
      </div>
    </div>
    <div class="col1 right last">
      <span class="button large" onclick="location.href='/delivery?delivery_id=<?php echo $delivery->id ?>';">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

<div class="main_button button" onclick="location.href='/delivery/new'">Neue Anlieferung anlegen</div>

<?php require('scroll_down.part.php') ?>