function debits_export(month){
  if(!confirm('Wirklich exportieren?')){
    return;
  }
  $('#loading').show();
  location.href = '/debits/export_csv?month=' + month;
  $('#loading').hide();
}