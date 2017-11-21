$(document).ready(function(){
  var getting = $.get("dropdown.php");
  getting.done(function(data) {
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
        $(".schedules").empty();
        $(".schedules").append("<h1>Possible Schedules</h1>");
        var titles = [];
        $('.title').each(function() {
            titles.push($(this).text());
        });
        var credits = $("#credits").val();
        console.log(credits);
        var posting = $.post("index-access.php", { titles: titles.join(","), credits: credits });
        posting.done(function(data){
            $(".schedules").append(data);
        });
    });

  }).fail(function(err,status){
    alert("err");
  });

});
