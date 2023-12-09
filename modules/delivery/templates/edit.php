<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen','/delivery?delivery_id='.$delivery->id => format_date($delivery->created,'j.n.Y').' '.$delivery->supplier->name);
?>

<div class="row bottom_no_radius" id="delivery_head">
  <div class="col6">
    <div>
      <?php echo format_date($delivery->created,'j.n.Y H:i') ?><br>
      <span class="smaller"><?php echo $delivery->creator->name ?></small>
    </div>
  </div>
  <div class="col4">
    <div>
      <div><b><?php echo $delivery->supplier->name ?></b></div>
      <!--
      <select id="delivery_supplier_id" <?php echo count($delivery->items)?'disabled="disabled"':'' ?> onchange="ajax_field_update($(this).attr('id'), '/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>')" >
        <option value=""></option>
        <?php foreach($suppliers as $supplier): ?>
          <option value="<?php echo $supplier->id ?>" <?php echo $delivery->supplier->id==$supplier->id?'selected="selected"':'' ?> ><?php echo $supplier->name ?></option>
        <?php endforeach ?>
      </select>
    -->
    </div>
  </div>
  <div class="col4"></div>
  <div class="col3 right">
    <div><div class="input money" onclick="input_onfocus(this)" data-info="Lieferung Betrag gemäß Lieferschein" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>" data-field="price_total" data-type="money" data-value="<?php echo $delivery->price_total ?>"><?php echo format_money($delivery->price_total) ?></div> EUR</div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button ok" onclick="active_input_post_value_goto('/delivery?delivery_id=<?php echo $delivery->id ?>');">
        <i class="fa-solid fa-check"></i>
      </div>
      <br>
      <div class="button trash mt0_5" onclick="active_input_post_value();delivery_delete('<?php echo $delivery->id ?>')">
        <i class="fa-regular fa-trash-can"></i>
      </div>
    </div>
  </div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  $('.input[data-field]').first().click();
</script>