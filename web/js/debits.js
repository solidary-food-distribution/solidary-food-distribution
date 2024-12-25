function debits_export(){
  if(!confirm('Wirklich exportieren und als exportiert setzen?')){
    return;
  }
  $('#loading').show();
  location.href='/debits/export_csv';
}