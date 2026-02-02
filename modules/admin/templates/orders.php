<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Mitglieder Bestellungen'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php
$date_until = '';
if($date_next){
  $date_until = date('Y-m-d',strtotime('-1 DAYS',strtotime($date_next)));
}
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $date_prev?'':'disabled' ?>" onclick="<?php echo $date_prev?'location.href=\'/admin/orders?date='.$date_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    Abholung: <?php echo format_date($date).($date_next?' - '.format_date($date_until):''); ?>
  </div><div class="control_r input <?php echo $date_next?'':'disabled' ?>" onclick="<?php echo $date_next?'location.href=\'/admin/orders?date='.$date_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
  <?php if(count($members) && $date_until >= date('Y-m-d') && $date_prev < date('Y-m-d')): ?>
    <div class="button" onclick="location.href='/admin/pickup_emails?date=<?php echo $date ?>';">Noch Abholende E-Mails</div>
  <?php endif ?>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member_id => $member): ?>
  <div class="row">
    <div class="inner_row" style="display:none" id="admin_orders_edit" data-product_id="">
      <div class="col9">
        <div>
          <select name="product_id" style="width:100%">
            <option></option>
            <?php foreach($products as $product_id => $product): ?>
              <option value="<?php echo $product_id ?>"><?php echo htmlentities($product->name) ?></option>
            <?php endforeach ?>
          </select>
          <br>
          <?php echo html_input(array(
            'type' => 'input_text',
            'class' => 'admin_orders_input',
            'field' => 'order_item_comment',
            'value' => '',
          )); ?>
        </div>
      </div>
      <div class="col2">
        <?php echo html_input(array(
          'type' => 'number',
          'class' => 'admin_orders_input',
          'field' => 'amount_order',
          'value' => '',
        )); ?>
      </div>
      <div class="col1"></div>
      <div class="col3 right">
        <div>
          <?php echo html_input(array(
            'type' => 'number',
            'class' => 'admin_orders_input',
            'field' => 'amount_pickup',
            'value' => '',
          )); ?>
          <br>
          <?php echo html_input(array(
            'type' => 'number',
            'class' => 'admin_orders_input',
            'field' => 'amount_pickup_weight',
            'value' => '',
          )); ?>
        </div>
      </div>
      <div class="col1 right">
        <span class="button small" onclick="admin_orders_update(this)">
          <i class="fa-solid fa-check" style="font-size:75%"></i>
        </span>
      </div>
    </div>
    <?php foreach($member_orders[$member_id] as $order_id => $order): ?>
      <div class="inner_row">
        <div class="col9">
          <b><?php echo htmlentities($members[$member_id]->name) ?></b>
        </div>
        <div class="col2 right">
          Bestellt
        </div>
        <div class="col1 right">
        </div>
        <div class="col3 right">
          Abgeholt
        </div>
      </div>
      <?php ksort($order_items_array[$order->id]) ?>
      <?php foreach($order_items_array[$order->id] as $order_item): ?>
        <?php 
          $product = $products[$order_item->product_id];
          $pickup_item = $pickup_items_array[$member_id][$order_item->id];
          unset($pickup_items_array[$member_id][$order_item->id]);
          $class = '';
          if(isset($replaced_items[$order_item->id])){
            $class = 'linethrough grey';
          }
        ?>
        <div class="inner_row <?php echo $class ?>" data-order_item_id="<?php echo $order_item->id ?>" data-product_id="<?php echo $product->id ?>" data-pickup_item_id="<?php echo $pickup_item->id ?>" data-product_type="<?php echo $product->type ?>">
          <?php
            if($order_item->split_status == 'o'){
              $split_data = json_decode($order_item->split_data, 1);
              $order_item->amount_pieces = $split_data['ordered'];
            }
            $amount_pickup_weight=0;
            if($product->type == 'k'){
              $amount_order = $order_item->amount_weight;
              $amount_pickup = $pickup_item->amount_weight;
              $unit = 'kg';
            }else{
              $amount_order = $order_item->amount_pieces;
              $amount_pickup = $pickup_item->amount_pieces;
              $unit = 'St.';
            }
          ?>
          <div class="col9">
            <div>
              <?php if($product->supplier_id == 35): ?>
                <?php echo htmlentities($product->supplier_product_id) ?>
              <?php endif ?>
              <?php echo htmlentities($product->name) ?><br>
              <span class="order_item_comment" style="font-size:75%;position:relative;top:-0.2em;"><?php echo htmlentities($order_item->comment) ?></span>
            </div>
          </div>
          <div class="col2 right">
            <span class="amount_order"><?php echo format_amount($amount_order) ?></span>&nbsp;<?php echo $unit ?>
          </div>
          <div class="col1 right">
          </div>
          <div class="col3 right <?php echo (!$amount_pickup || ($product->type == 'w' && !$pickup_item->amount_weight))?'warn':'' ?>">
            <div>
              <span class="amount_pickup"><?php echo format_amount($amount_pickup) ?></span>&nbsp;<?php echo $unit ?>
              <?php if($product->type == 'w'): ?>
                <br><span class="amount_pickup_weight"><?php echo format_amount($pickup_item->amount_weight) ?></span>&nbsp;kg
              <?php endif ?>
            </div>
          </div>
          <div class="col1 right">
            <span class="button small edit" onclick="admin_orders_edit(this,'<?php echo $order_item->id ?>','<?php echo $pickup_item->id ?>')">
              <i class="fa-solid fa-pencil" style="font-size:75%"></i>
            </span>
          </div>
        </div>
      <?php endforeach ?>
      <?php foreach($pickup_items_array[$member_id] as $pickup_item): ?>
        <?php 
          $product = $products[$pickup_item->product_id];
          if($product->type == 'k'){
            $amount_pickup = $pickup_item->amount_weight;
            $unit = 'kg';
          }else{
            $amount_pickup = $pickup_item->amount_pieces;
            $unit = 'St.';
          }
        ?>
        <div class="inner_row" data-product_id="<?php echo $product->id ?>" data-pickup_item_id="<?php echo $order_item->id ?>"  data-product_type="<?php echo $product->type ?>">
          <div class="col9">
            <?php if($product->supplier_id == 35): ?>
              <?php echo htmlentities($product->supplier_product_id) ?>
            <?php endif ?>
            <?php echo htmlentities($product->name) ?>
          </div>
          <div class="col2 right">
          </div>
          <div class="col1 right">
          </div>
          <div class="col3 right">
            <div>
              <span class="amount_pickup"><?php echo format_amount($amount_pickup) ?></span>&nbsp;<?php echo $unit ?>
              <?php if($product->type == 'w'): ?>
                <br><span class="amount_pickup_weight"><?php echo format_amount($pickup_item->amount_weight) ?></span>&nbsp;kg
              <?php endif ?>
            </div>
          </div>
          <div class="col1 right">
            <span class="button small edit" onclick="admin_orders_edit(this,'<?php echo $pickup_item->id ?>','<?php echo $pickup_item->id ?>')">
              <i class="fa-solid fa-pencil" style="font-size:75%"></i>
            </span>
          </div>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
  </div>
<?php endforeach ?>
