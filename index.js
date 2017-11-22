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
  $(".codeDropdown").hide();

  $("#submitByTitle").click(function(){
    if (!$(".title").length) {
      var $classes_added = $(".classes-added");
      $classes_added.prepend("<h1> Classes Selected </h1>");
    }
    var $title = $("#titles option:selected");
    $(".classes-added-list").append('<li class="title">'+$title.val()+'</li>');
    $title.remove();
  });

  $("#submitByCode").click(function(){
    if (!$(".title").length) {
      var $classes_added = $(".classes-added");
      $classes_added.prepend("<h1> Classes Selected </h1>");
    }
    var $courseNumber = $("#courseNumbers option:selected");
    $(".classes-added-list").append('<li class="title">'+$courseNumber.val()+'</li>');
    $courseNumber.remove();
  });

  getNumbers($("#codes option:selected").text());

  $("#codes").change(function(){
    getNumbers($("#codes option:selected").text());
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
