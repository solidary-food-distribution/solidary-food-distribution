<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/admin/products_suppliers'=>'Produkte',
  '' => $supplier->name.' importieren',
);
$PROPERTIES['body_class']='header_h5';

$info_options = array(
  '' => '',
  'd' => 'deleted',
  'n' => '<i class="fa-regular fa-circle smaller"></i>',
  'o' => '<i class="fa-regular fa-circle-check smaller"></i>',
);

$type_options = array(
  '' => '',
  'p' => 'Stück',
  'k' => 'kg',
  'w' => 'St./kg',
);


?>

<div class="row">
  <div class="inner_row">
    <form name="products_import_friedls" method="post"  enctype="multipart/form-data">
      <input type="file" name="file" />
      <button>Hochladen</button>
    </form>
  </div>
  <div class="inner_row">
    <table>
      <tr>
        <td>Datei</td>
        <td>offen</td>
        <td>ok</td>
      </tr>
      <?php foreach($uploads as $upload): ?>
        <tr>
          <td><?php echo $upload['filename'] ?></td>
          <td><a href="?upload_id=<?php echo $upload['id'] ?>"><?php echo $upload['open'] ?></a></td>
          <td><a href="?upload_id=<?php echo $upload['id'] ?>&status=1"><?php echo $upload['ok'] ?></a></td>
        </tr>
      <?php endforeach ?>
    </table>
  </div>
</div>

<br>
<br>

<div class="row">
<table>
<?php foreach($rows as $row): ?>
  <?php
    unset($product);
    unset($purchase);
    if(isset($row['product_id'])){
      $product = $products[$row['product_id']];
      $purchase = $prices[$product->id]->purchase;
      $price = $prices[$product->id]->price;
      $tax = $prices[$product->id]->tax;
    }
  ?>
  <tr style="vertical-align:top">
    <td style="width:10%"><?php echo htmlentities($row['info']) ?></td>
    <td style="width:40%"><?php echo htmlentities($row['name']) ?></td>
    <td style="width:15%"><?php echo htmlentities($row['type']) ?></td>
    <td style="width:10%" class="right"><?php echo format_money($row['purchase']) ?></td>
    <td style="width:25%"><?php echo htmlentities($row['brand']) ?></td>
  </tr>
  <?php if(isset($product) && ($product->status!='o' || $purchase!=$row['purchase']) ): ?>
    <tr style="vertical-align:top;color:grey;">
      <td><?php echo $info_options[$product->info] ?></td>
      <td><?php echo htmlentities($product->name) ?></td>
      <td><?php echo htmlentities($type_options[$product->type]) ?></td>
      <td class="right"><?php echo format_money($prices[$product->id]->purchase) ?></td>
      <td><?php echo htmlentities($brands[$product->brand_id]) ?></td>
    </tr>
  <?php endif ?>
  <tr data-id="<?php echo $row['id'] ?>">
    <td style="vertical-align:top;border-bottom:2px solid black; padding-bottom:1em;">
      <?php #if(isset($product)){print_r($product);} ?>
      <div style="display:inline-block;<?php echo (isset($product) && $product->status!='o')?'background-color:#aadd00;':'' ?>">
        <input type="radio" name="info<?php echo $product->id ?>" id="info_o<?php echo $product->id ?>" value="o" checked /><label for="info_o<?php echo $product->id ?>"><i class="fa-regular fa-circle-check smaller"></i></label>
      </div>
      <div style="display:inline-block">
        <input type="radio" name="info<?php echo $product->id ?>" id="info_n<?php echo $product->id ?>" value="n" /><label for="info_n<?php echo $product->id ?>"><i class="fa-regular fa-circle smaller"></i></label>
      </div>
    </td>
    <td style="vertical-align:top;border-bottom:2px solid black;">
      <select style="width:100%" name="product_id" onchange="admin_products_import_friedls_update(this)">
        <option value="0"></option>
        <?php foreach($products as $p): ?>
          <option value="<?php echo $p->id; ?>" <?php echo (isset($product) && $product->id == $p->id)?'selected':'' ?>><?php echo htmlentities($p->name); ?></option>
        <?php endforeach ?>
      </select><br/>
      <?php if(!isset($product)): ?>
        <input style="width:80%;" class="new_product_name" value="" />
        <div class="button" name="new" style="padding-top:0;padding-bottom:0;" onclick="admin_products_import_friedls_update(this)">neu</div>
      <?php endif ?>
    </td>
    <td style="vertical-align:top;border-bottom:2px solid black;">
      <?php if(isset($product)): ?>
        <select name="type" onchange="admin_products_import_friedls_update(this)">
          <?php foreach($type_options as $type => $type_name): ?>
            <option value="<?php echo $type ?>" <?php echo (isset($product) && $product->type == $type)?'selected':'' ?>><?php echo htmlentities($type_name) ?></option>
          <?php endforeach ?>
        </select><br/>
      <?php endif ?>
      <?php if(isset($product) && $product->type=='w'): ?>
        <input style="width:40%;text-align:right;" name="kg_per_piece" value="<?php echo str_replace('.', ',', $product->kg_per_piece) ?>" onchange="admin_products_import_friedls_update(this)" />kg/St
      <?php elseif(isset($product) && $product->type=='k'): ?>
         <input style="width:40%;text-align:right;" name="amount_steps" value="<?php echo str_replace('.', ',', $product->amount_steps) ?>" onchange="admin_products_import_friedls_update(this)" />kg+-
      <?php endif ?>
    </td>
    <td style="vertical-align:top;border-bottom:2px solid black;">
      <?php if(isset($product)): ?>
        <input style="width:100%;text-align:right;<?php echo ($purchase!=$row['purchase'])?'background-color:#aadd00;':'' ?>" name="purchase" onchange="admin_products_import_friedls_update(this)" value="<?php echo format_money($row['purchase']) ?>" ><br/>
        <select name="tax" onchange="admin_products_import_friedls_update(this)" style="width:100%;text-align:right;">
          <?php foreach(array('','0','7','19') as $tax_rate): ?>
            <option value="<?php echo $tax_rate ?>" <?php echo ((string)$tax===$tax_rate)?'selected':'' ?>><?php echo $tax_rate===''?'':$tax_rate.'%' ?></option>
          <?php endforeach ?>
        </select><br/>
        <input style="width:100%;text-align:right; ?>" name="price" onchange="admin_products_import_friedls_update(this)" value="<?php echo format_money($price) ?>" >
      <?php endif ?>
    </td>
    <td style="vertical-align:top;border-bottom:2px solid black;">
      <?php if(isset($product)): ?>
        <select name="brand_id" onchange="admin_products_import_friedls_update(this)" style="width:100%">
          <option></option>
          <option value="0" <?php echo ($product->brand_id == 0)?'selected':'' ?>>Friedls Biohof</option>
          <?php foreach($brands as $brand_id => $brand_name): ?>
            <option value="<?php echo $brand_id; ?>" <?php echo (isset($product) && $product->brand_id == $brand_id)?'selected':'' ?>><?php echo htmlentities($brand_name); ?></option>
          <?php endforeach ?>
        </select><br/>
        <div style="float:right">
          <div class="button" name="ok" onclick="admin_products_import_friedls_update(this)">OK</div>
        </div>
        <br/>
        <span style="color:grey;font-size:80%;"><?php echo format_money($row['purchase']*1.2); ?></span>
      <?php endif ?>
    </td>
  </tr>
<?php endforeach ?>
</table>
</div>