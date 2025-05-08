<?php
$PROPERTIES['pathbar']=array('/pickups'=>'Abholungen');
$PROPERTIES['body_class']='header_h5';

#logger(print_r($pickups,1));
#logger(print_r($pickup_items,1));
?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php
$last_pickup = '';
$pickups_counter = 0;
?>

<?php foreach($pickups as $pickup): ?>
  <?php
    $pickups_counter++;
    $last_pickup = $pickup->created;
  ?>
  <div class="row">
    <div class="col4">
      <div>
        <b><?php echo format_date($pickup->created,'j.n.Y') ?></b>
        <br>
        <?php echo format_date($pickup->created,'H:i', false) ?>
      </div>
    </div>
    <div class="col12">
      <div>
        <?php
          $items = '';
          foreach($pickup_items[$pickup->id] as $item){
            if($item->amount_pieces || $item->amount_weight || (count($pickups) == $pickups_counter)){
              $items .= $products[$item->product_id]->name.', ';
            }
          }
          echo rtrim($items, ', ');
        ?>
      </div>
    </div>
    <div class="col2 right last">
      <?php if(empty($items)): ?>
        <span class="button large" onclick="pickups_delete('<?php echo $pickup->id ?>')">
          <i class="fa-regular fa-trash-can"></i>
        </span>
      <?php endif ?>
      <span class="button large" onclick="location.href='/pickup?pickup_id=<?php echo $pickup->id ?>';">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

<?php if(substr($last_pickup,0,10) != date('Y-m-d')): ?>
  <div class="main_button button" onclick="location.href='/pickup/new'">Abholung für <?php echo format_date(date('Y-m-d H:i:s')); ?> anlegen</div>
<?php endif ?>

<?php require('scroll_down.part.php') ?>