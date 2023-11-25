<div class="row" id="delivery_head">
  <div class="col3">
    <div>
      <?php echo format_date($delivery->created,'j.n.Y H:i') ?><br>
      <span class="smaller"><?php echo $delivery->creator->name ?></span>
    </div>
  </div>
  <div class="col2">
    <div><?php echo $delivery->supplier->name ?></div>
  </div>
  <div class="col2">
    <div></div>
  </div>
  <div class="col2 right">
    <div><?php echo number_format($delivery->price_total,2,',','') ?> EUR</div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="alert('NOCH NICHT IMPLEMENTIERT');return false;ajax_id_replace('delivery_head', '/delivery/head_ajax?id=<?php echo $delivery->id ?>&edit=1')">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>