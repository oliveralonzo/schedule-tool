$(document).ready(function(){
  $.post("dropdown.php", function(data){
    $(".dropdown-con").html(data);

    $("#submitB").click(function(){
      var $title = $("#titles option:selected");
      $(".classes-added").append('<p class="title">'+$title.text()+'</p>');
      $title.remove();
    });
  }).fail(function(err,status){
    alert("err");
  });


});
