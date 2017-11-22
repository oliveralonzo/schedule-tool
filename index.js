$(document).ready(function(){

  $("#titleButton").click(function(){
    $(".titleDropdown").show();
    $(".codeDropdown").hide();
  });
  $("#codeButton").click(function(){
    $(".codeDropdown").show();
    $(".titleDropdown").hide();
  });

  var getting = $.get("dropdown.php");
  getting.done(function(data) {

  $(".dropdown-con").prepend(data);
  console.log(data);
  $(".codeDropdown").hide();

  $("#submitByTitle").click(function(){
      if (!$(".title").length) {
        $(".classes-added").prepend("<h1> Classes Selected </h1>");
      }
      var $title = $("#titles option:selected");
      $(".classes-added-list").append(
          '<li class="title"> <span class="courseTitle">'+
          $title.val()+
          '</span> <span class="removeCourse">Remove</span> </li>');
      $title.attr('disabled','disabled');
      $('#titles').children('option:enabled').eq(0).prop('selected',true);
    });

    $("#submitByCode").click(function(){
        if (!$(".title").length) {
          var $classes_added = $(".classes-added");
          $classes_added.prepend("<h1> Classes Selected </h1>");
        }
        var $courseNumber = $("#courseNumbers option:selected");
        $(".classes-added-list").append(
            '<li class="title"> <span class="courseTitle">'+
            $courseNumber.val()+
            '</span> <span class="removeCourse">Remove</span> </li>');
        $courseNumber.attr('disabled','disabled');
        $('#titles').children('option:enabled').eq(0).prop('selected',true);
    });

    getNumbers($("#codes option:selected").text());

    $("#codes").change(function(){
        getNumbers($("#codes option:selected").text());
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

function getNumbers(subject_code) {
  $("#courseNumbers").empty();
  var getNums = $.post("courseNumbers.php",{
    subject_code: subject_code
  })
  getNums.done(function(data){
    console.log(subject_code);
    $("#courseNumbers").append(data);
  })

}
