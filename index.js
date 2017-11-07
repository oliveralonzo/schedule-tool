$(document).ready(function(){
  $.post("dropdown.php", function(data){
    $(".dropdown-con").html(data);

    $("#submitB").click(function(){
      if (!$(".title").length) {
        var $classes_added = $(".classes-added");
        $classes_added.prepend("<h1> Classes Selected </h1>");
      }
      var $title = $("#titles option:selected");
      $(".classes-added-list").append('<li class="title">'+$title.text()+'</li>');
      $title.remove();
    });

    $("#generate").click(function() {
        var titles = [];
        console.log('hey');
        $('.title').each(function() {
            titles.push($(this).text());
        });
        console.log(titles.join(','));
    });

  }).fail(function(err,status){
    alert("err");
  });

});
