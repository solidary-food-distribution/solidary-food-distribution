function products_new(){
  if(!confirm("Wirklich neues Produkt anlegen?")){
    return;
  }
  location.href='/products/edit?product_id=new&ts='+get_ts();
}