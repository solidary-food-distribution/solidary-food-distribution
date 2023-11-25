<div class="row" id="delivery_head">
  <div class="col3">
    <div>
      <?php echo format_date($delivery->created,'j.n.Y H:i') ?><br>
      <span class="smaller"><?php echo $delivery->creator->name ?></small>
    </div>
  </div>
  <div class="col2">
    <div>
      <select <?php echo count($delivery->items)?'disabled="disabled"':'' ?> >
        <option value=""></option>
        <?php foreach($suppliers as $supplier): ?>
          <option value="<?php echo $supplier->id ?>" <?php echo $delivery->supplier->id==$supplier->id?'selected="selected"':'' ?> ><?php echo $supplier->name ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>
  <div class="col2">
    <div></div>
  </div>
  <div class="col2 right">
    <div><input type="text" style="text-align:right;" size="7" value="<?php echo number_format($delivery->price_total,2,',','') ?>" /> EUR</div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="ajax_id_replace('delivery_head', '/delivery/head_ajax?id=<?php echo $delivery->id ?>')">
      <i class="fa-solid fa-check"></i>
    </span>
  </div>
</div>