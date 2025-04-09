<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/admin/products_suppliers'=>'Produkte',
  '' => $supplier->name,
);
$PROPERTIES['body_class']='header_h5';

$status_options = array(
  'o' => 'aktiv',
  's' => 'suchbar',
  'n' => 'inaktiv',
);
if($supplier->id != 35){
  unset($status_options['s']);
}
?>

<?php ob_start(); ?>
  <div class="filter">
    <?php echo html_input(array(
      'type' => 'options',
      'options' => $status_options,
      'field' => 'status',
      'value' => $status,
      'onclick' => 'admin_products_filter',
    )); ?>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($products as $product_id => $product): ?>
  <?php
    $purchase = $prices[$product_id]->purchase;
    $price = $prices[$product_id]->price;
  ?>
  <div class="row product" data-id="<?php echo $product_id ?>">
    <div class="col2">
      <div class="image" style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;">
        <?php
          $infos = array();
          if(!empty($product->infos)){
            $infos = json_decode($product->infos, true);
            if($infos['date'] < date('Y-m-d')){
              $infos_lazy_load[] = $product_id;
            }
          }elseif($product->supplier_id == 35){
            $infos_lazy_load[] = $product_id;
          }
          if(isset($infos['link'])){
            echo '<a href="'.$infos['link'].'" target="_blank">';
          }
          if(isset($infos['image'])){
            echo '<img src="'.$infos['image'].'" />';
          }
          if(isset($infos['link'])){
            echo '</a>';
          }
        ?>
      </div>
    </div>
    <div class="col8">
      <div>
        <?php echo htmlentities($product->name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col8">
      <div>
        <?php echo html_input(array(
          'type' => 'options',
          'options' => $status_options,
          'field' => 'status',
          'value' => $product->status,
          'url' => '/admin/products_update_ajax?product_id='.$product_id,
        )); ?>
        <div class="inner_row">
          <?php echo html_input(array(
            'type' => 'money',
            'field' => 'purchase',
            'value' => format_money($purchase),
            'url' => '/admin/products_update_ajax?product_id='.$product_id,
          )); ?> <span>EUR Einkauf exkl. Steuer</span>
        </div>
        <div class="inner_row">
          <?php echo html_input(array(
            'type' => 'money',
            'field' => 'price',
            'value' => format_money($price),
            'url' => '/admin/products_update_ajax?product_id='.$product_id,
          )); ?><span>EUR Verkauf inkl. Steuer</span>
        </div>
        <div class="inner_row">
          <select onchange="admin_products_update_ajax(this)" data-field="category" data-url="/admin/products_update_ajax?product_id=<?php echo $product_id ?>">
            <option value="">Kategorie wählen...</option>
            <?php foreach($categories as $category => $count): ?>
              <option value="<?php echo htmlentities($category) ?>"<?php echo ($category!='' && $product->category == $category)?' selected':'' ?>><?php echo htmlentities($category).' ('.$count.')' ?></option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
    </div>
  </div>
<?php endforeach ?>
