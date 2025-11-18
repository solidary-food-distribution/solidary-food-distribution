<?php

require_once('inc.php');

function execute_run(){
  global $user;
  $ignore_next_run = get_request_param('ignore_next_run');
  $user = array('user_id' => 1); //for SQL::update-logger
  require_once('sql.class.php');
  $where = "next_run<='".date('Y-m-d H:i:s')."'";
  if($ignore_next_run){
    $where = "1=1";
  }
  $qry = "SELECT * FROM msl_crons WHERE $where ORDER BY next_run";
  $crons = SQL::select($qry);
  foreach($crons as $cron){
    if(substr($cron['task'],0,5) == 'cron_' && function_exists($cron['task'])){
      call_user_func($cron['task']);
      $qry = "UPDATE msl_crons SET last_run='".date('Y-m-d H:i:s')."', next_run='".date('Y-m-d H:i:s', time() + 60*intval($cron['minutes_interval']))."' WHERE cron_id='".$cron['cron_id']."'";
      SQL::update($qry);
    }
  }
  exit;
}

function cron_close_pickups(){
  require_once('pickups.class.php');
  $pickups = new Pickups(array('created<' => date('Y-m-d'), 'created<=' => date('Y-m-d H:i:s', strtotime('-2 HOURS',time())), 'status' => 'o'));
  foreach($pickups as $pickup){
    $pickup->update(array('status' => 'a'));
  }
}

function cron_update_delivery_dates(){
  require_once('sql.class.php');
  $qry = "SELECT * FROM msl_delivery_dates WHERE `date`>=CURDATE() ORDER BY `date`";
  $delivery_dates = SQL::select($qry);

  $qry = "SELECT id, purchase_time FROM msl_members WHERE producer>0 AND status='a' AND purchase_time!=''";
  $supplier_purchase_times = SQL::selectKey2Val($qry, 'id', 'purchase_time');

  foreach($delivery_dates as $delivery_date){
    $qry = "INSERT INTO msl_purchases (delivery_date_id, supplier_id, `datetime`) VALUES ";
    foreach($supplier_purchase_times as $supplier_id => $purchase_time){
      $qry .= "('".intval($delivery_date['id'])."', '".intval($supplier_id)."', ";
      $purchase_datetime = date('Y-m-d H:i:s', strtotime($purchase_time, strtotime($delivery_date['date'])));
      $qry .= "'".$purchase_datetime."'),";
    }
    $qry = rtrim($qry, ',')." ON DUPLICATE KEY UPDATE `delivery_date_id`=VALUES(`delivery_date_id`)";
    SQL::update($qry);
  }
}

function cron_may_send_purchases(){
  require_once('purchases.class.php');
  $now = date('Y-m-d H:i:s');
  $purchases = new Purchases(array('datetime<=' => $now, 'sent' => '0000-00-00 00:00:00'));
  foreach($purchases as $purchase){
    send_purchase($purchase->id);
    $purchase->update(array('sent' => date('Y-m-d H:i:s')));
  }
}

function cron_may_create_deliveries(){
  require_once('purchases.class.php');
  $now = date('Y-m-d H:i:s');
  $purchases = new Purchases(array('id>=' => 9, 'datetime<=' => $now, 'sent!=' => '0000-00-00 00:00:00'), array('id' => 'DESC'), 0, 8);
  foreach($purchases as $purchase){
    create_delivery($purchase->id);
  }
}

function send_purchase($purchase_id){
  require_once('purchases.class.php');
  $purchase = Purchases::sget($purchase_id);
  require_once('delivery_dates.class.php');
  $delivery_date = DeliveryDates::sget($purchase->delivery_date_id);
  require_once('purchases.inc.php');
  $product_sums = purchases_get_product_sums($delivery_date->date, $purchase->supplier_id);
  if(count($product_sums) == 0){
    return;
  }
  #logger("product_sums ".print_r($product_sums,1));
  $fields = array('amount_pieces', 'amount_bundles', 'amount_weight', 'price_type', 'purchase', 'purchase_sum');
  $fields = array_flip($fields);
  require_once('purchase_items.class.php');
  foreach($product_sums as $product_id => $sums){
    $purchase_items = new PurchaseItems(array('purchase_id' => $purchase_id, 'product_id' => $product_id));
    if(count($purchase_items)){
      $purchase_item = $purchase_items->first();
    }else{
      $purchase_item = PurchaseItem::create($purchase_id, $product_id);
    }
    $updates = array_intersect_key($sums, $fields);
    $purchase_item->update($updates);
  }
  send_purchase_email($purchase_id);
}

function send_purchase_email($purchase_id){
  require_once('purchases.class.php');
  $purchase = Purchases::sget($purchase_id);
  require_once('members.class.php');
  $supplier = Members::sget($purchase->supplier_id);
  require_once('users.class.php');
  $users = new Users(array('member_id' => $purchase->supplier_id));
  require_once('delivery_dates.class.php');
  $delivery_date = DeliveryDates::sget($purchase->delivery_date_id);
  require_once('purchase_items.class.php');
  $purchase_items = new PurchaseItems(array('purchase_id' => $purchase_id));
  $purchase_items_array = array();
  foreach($purchase_items as $purchase_item){
    $purchase_items_array[$purchase_item->product_id] = $purchase_item;
  }
  require_once('products.class.php');
  $products = new Products(array('id' => $purchase_items->get_product_ids()), array('FIELD(type,\'k\',\'w\',\'p\')' => '', 'name' => 'ASC'));

  $html = '<html><head></head><body>';
  $html .= '<table><tr><th>Produkt</th>';
  if($supplier->id == 35){
    $html .= '<th>Art.Nr</th>';
  }
  $html .= '<th>Menge</th><th>Einheit</th></tr>';
  foreach($products as $product){
    $purchase_item = $purchase_items_array[$product->id];
    $html .= '<tr>';
    $html .= '<td>'.htmlentities($product->name);
    if($product->type == 'w'){
      $html .= ' (ca. '.format_amount($product->kg_per_piece).' kg)';
    }
    $html .= '</td>';
    if($supplier->id == 35){
      $html .= '<td>'.htmlentities($product->supplier_product_id).'</td>';
    }
    $html .= '<td align="right">';
    if($product->type == 'k'){
      $html .= format_amount($purchase_item->amount_weight).'</td><td>kg';
    }elseif($purchase_item->amount_bundles){
      $html .= format_amount($purchase_item->amount_bundles).'</td><td>Gb. <small>('.format_amount($purchase_item->amount_pieces).' St.)</small>';
    }else{
      $html .= format_amount($purchase_item->amount_pieces).'</td><td>St.';
    }
    $html .= '</td></tr>';
  }
  $html .= '</table>';
  $html .= '<br>Wir bitten um Bestätigung, dass die E-Mail angekommen ist und die Bestellung möglich ist.<br>';
  $html .= '<br>Diese E-Mail ist automatisch erzeugt worden, und dennoch:<br>';
  $html .= 'Wir bedanken uns und wünschen von Herzen alles Gute!<br><br>';
  $html .= 'Stefan D, Mathias und Stefan O und alle Mitglieder';
  $html .= '</body></html>';

  $headers['MIME-Version'] = '1.0';
  $headers['Content-Type'] = 'text/html; charset=utf-8';
  $to = array();
  foreach($users as $user){
    $to[] = $user->email;
  }
  $to = implode(', ', $to);
  if($to == ''){
    $to = 'info@mit-sinn-leben.de';
  }else{
    $headers['Cc'] = 'info@mit-sinn-leben.de';
  }
  $subject = 'Bestellung zur Abholung am '.format_date($delivery_date->date).' von '.$supplier->name;
  send_email($to, $subject, $html, $headers);
  $purchase->update(array('content' => $html));
}


function create_delivery($purchase_id){
  #logger("create_delivery $purchase_id");
  require_once('purchases.class.php');
  $purchase = Purchases::sget($purchase_id);
  require_once('purchase_items.class.php');
  $purchase_items = new PurchaseItems(array('purchase_id' => $purchase_id));
  require_once('delivery_dates.class.php');
  $delivery_date = DeliveryDates::sget($purchase->delivery_date_id);
  require_once('deliveries.class.php');
  $deliveries = new Deliveries(array('purchase_id' => $purchase_id));
  if($deliveries->count()){
    return;
  }
  $delivery = Delivery::create($purchase->supplier_id, 1);
  $delivery->update(array(
    'purchase_id' => $purchase_id,
    'created' => $delivery_date->date,
  ));
  require_once('delivery_items.class.php');
  foreach($purchase_items as $purchase_item){
    $delivery_item = DeliveryItem::create($delivery->id, $purchase_item->product_id);
    $updates = array(
      'amount_pieces' => $purchase_item->amount_pieces,
      'amount_bundles' => $purchase_item->amount_bundles,
      'amount_weight' => $purchase_item->amount_weight,
      'price_type' => $purchase_item->price_type,
      'purchase' => $purchase_item->purchase,
      'purchase_sum' => $purchase_item->purchase_sum,
    );
    $delivery_item->update($updates);
  }
}

function cron_polls_mandatory_check(){
  require_once('polls.class.php');
  require_once('sql.class.php');
  $reminded_max = date('Y-m-d H:i:s', strtotime('-72 HOURS',time()));
  $polls = new Polls(array('mandatory' => 1, 'close_datetime' => '0000-00-00 00:00:00', 'reminded<' => $reminded_max));
  foreach($polls as $poll){
    $qry = "SELECT GROUP_CONCAT(m.id) member_ids FROM msl_members m WHERE m.id NOT IN (SELECT u.member_id FROM msl_users u, msl_poll_answers pa, msl_poll_votes pv WHERE pa.poll_answer_id=pv.poll_answer_id AND pv.value=1 AND pv.user_id=u.id AND pa.poll_id=".$poll->poll_id.") AND m.id NOT IN (1) AND m.consumer=1";
    $member_ids = SQL::selectOne($qry)['member_ids'];
    $member_ids = explode(',', $member_ids);
    require_once('members.class.php');
    $members = new Members(array('id' => $member_ids));
    require_once('users.class.php');
    $users = new Users(array('member_id' => $member_ids));
    foreach($users as $u){
      if(strpos(' '.$u->name, 'GEKÜNDIGT') || strpos(' '.$u->email, 'GEKÜNDIGT') || !strpos($u->email, '@')){
        continue;
      }
      send_poll_reminder_email($poll, $u, $members[$u->member_id]);
    }
    $poll->update(array('reminded' => date('Y-m-d H:i:s')));
  }
}

function send_poll_reminder_email($poll, $user, $member){
  $to = $user->email;
  $subject = "Erinnerung: Mitmachen bei Umfrage '".$poll->title."'";
  $content = "Liebes Mitglied,\n";
  $content .= "Du hast noch nicht bei der Umfrage '".$poll->title."' mitgemacht.\n";
  $content .= "Hier gehts zur Umfrage:\n";
  $content .= "https://".$_SERVER['HTTP_HOST']."/polls/poll?poll_id=".$poll->poll_id."\n";
  $content .= "Lieben Dank.\n";
  send_email($to, $subject, $content);
}

function cron_send_order_notifications(){
  #$now = date('Y-m-d H:i:s');
  $now = date('Y-m-d H:i:s',strtotime('2025-11-19 19:00:00'));
  require_once('sql.class.php');
  $last = SQL::selectOne("SELECT value FROM msl_var WHERE var='NOTIFICATION_ORDER_REMINDER_LAST'")['value'];
  logger("now $now last $last");

  $qry = "SELECT setting, GROUP_CONCAT(user_id) user_ids FROM msl_settings WHERE setting LIKE 'NOTIFICATION_ORDER_REMINDER%' AND value='1' GROUP BY setting";
  $settings = SQL::selectId($qry, 'setting');
  $notifications = array();
  foreach($settings as $setting => $notification){
    $delta = substr($setting, strlen('NOTIFICATION_ORDER_REMINDER'));
    $delta = str_replace('_', ' ',$delta);
    $hours = preg_replace('/[^0-9]/','', $delta);
    $notification['delta'] = $delta;
    $notification['hours'] = $hours;
    $notifications[$hours] = $notification;
  }
  ksort($notifications);
  #logger(print_r($notifications,1));

  require_once('purchases.class.php');
  $purchases = new Purchases(array('datetime>' => $now, 'status' => 'a', 'sent' => '0000-00-00 00:00:00'));
  #logger(print_r($purchases,1));
  foreach($purchases as $purchase){
    foreach($notifications as $hours => $notification){
      $delta = $notification['delta'];
      $datetime = date('Y-m-d H:i:s', strtotime($delta, strtotime($purchase->datetime)));
      #logger("now $now hours $hours purchase.datetime ".$purchase->datetime." purchase.order_notification_last ".$purchase->order_notification_last." $delta $datetime");
      if($datetime <= $now && $datetime > $last){
        $notifications[$hours]['purchases'][] = $purchase;
      }
    }
  }
  logger(print_r($notifications,1));
  $user_notifications = array();
  foreach($notifications as $hour => $notification){
    
  }
}

function cron_may_deactivate_members(){
  require_once('members.class.php');
  $members = new Members(array('deactivate_on<=' => date('Y-m-d'), 'deactivate_on!=' => '0000-00-00', 'status' => 'a'));
  foreach($members as $member){
    logger('Member-ID ' . $member->id . ' deaktiviert');
    $member->update(array('status' => 'i', 'modified' => date('Y-m-d H:i:s')));
  }
}