$('#getStarted').click(function(){
  $('.welcome-box').remove();
  $('.search-box').toggleClass('hide');
});

$('#showCredits').click(function(){
  $('.restrictions').modal('show');
});
