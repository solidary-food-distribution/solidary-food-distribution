<?php
$PROPERTIES['pathbar']=array('/pickups'=>'Abholungen');
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($pickups as $pickup): ?>
  <div class="row">
    <div class="col4">
      <div>
        <b><?php echo format_date($pickup->created,'j.n.Y') ?></b>
        <br>
        <?php echo format_date($pickup->created,'H:i', false) ?>
      </div>
    </div>
    <div class="col13">
      <div>
        <?php
          $items = '';
          foreach($pickup->items as $item){
            if($item->amount_pieces || $item->amount_weight){
              $items .= $item->product->name.', ';
            }
          }
          echo rtrim($items, ', ');
        ?>
      </div>
    </div>
    <div class="col1 right last">
      <span class="button" onclick="location.href='/pickup?pickup_id=<?php echo $pickup->id ?>';">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

<div class="main_button button" onclick="location.href='/pickup/new'">Neue Abholung anlegen</div>
