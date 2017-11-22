$(document).ready(function(){
  var getting = $.get("dropdown.php");
  getting.done(function(data) {
    $(".dropdown-con").html(data);

    $("#submitB").click(function(){
      if (!$(".title").length) {
        $(".classes-added").prepend("<h1> Classes Selected </h1>");
      }
      var $title = $("#titles option:selected");
      $(".classes-added-list").append(
          '<li class="title"> <span class="courseTitle">'+
          $title.text()+
          '</span> <span class="removeCourse">Remove</span> </li>');
      $title.attr('disabled','disabled');
      $('#titles').children('option:enabled').eq(0).prop('selected',true);
    });

    $(".classes-added-list").on("click", ".removeCourse", function() {
        var courseTitle = $(this).parent().find(".courseTitle").text();
        addToSelect($("#titles"), courseTitle);
        $(this).parent().remove();
        $("#titles option[value='"+courseTitle+"']").attr("disabled", false);
        $('#titles').children('option:enabled').eq(0).prop('selected',true);
        if (!$(".title").length) {
          $(".classes-added h1").remove();
        }
    });

    $("#generate").click(function() {
        $(".schedules").empty();
        $(".schedules").append("<h1>Possible Schedules</h1>");
        var titles = [];
        $('.courseTitle').each(function() {
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

function addToSelect($select, option) {
    var added = false;
    $select.find("option").each(function() {
        if ($(this).text().localeCompare(option) > 0) {
            var optionTag = '<'
        }
    });
}
