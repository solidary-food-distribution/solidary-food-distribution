<div class="popup_screen" id="purchase_date_ajax">
  <div class="popup_window">
    <div>
      <b><?php echo $supplier->name ?> für <?php echo format_date($delivery_date->date) ?></b>
    </div>
    <div>
      Bestellzeitpunkt: 
      <div class="input" onclick="admin_purchase_date(<?php echo $purchase->id ?>, 'date', 'prev')">
        <i class="fa-solid fa-caret-left"></i>
      </div>
      <?php echo format_date(substr($purchase->datetime,0,10)) ?>
      <div class="input" onclick="admin_purchase_date(<?php echo $purchase->id ?>, 'date', 'next')">
        <i class="fa-solid fa-caret-right"></i>
      </div>
      <div class="input" onclick="admin_purchase_date(<?php echo $purchase->id ?>, 'time', 'prev')">
        <i class="fa-solid fa-caret-left"></i>
      </div>
      <?php echo substr($purchase->datetime,11,5) ?>
      <div class="input" onclick="admin_purchase_date(<?php echo $purchase->id ?>, 'time', 'next')">
        <i class="fa-solid fa-caret-right"></i>
      </div>
    </div>
    <div class="right">
      <div class="button" onclick="location.reload()">Schließen</div>
    </div>
  </div>
</div>