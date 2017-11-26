$(document).ready(function(){

    $("#titleButton").click(function(){
        toggle($(".titleDropdown"), $(".codeDropdown"));
    });
    $("#codeButton").click(function(){
        toggle($(".codeDropdown"), $(".titleDropdown"));
    });

    var getting = $.get("dropdown.php");
    getting.done(function(data) {

        $(".dropdown-con").prepend(data);
        $(".codeDropdown").hide();

        $("#submitByTitle").click(function(){
            addClassToCart($("#titles"), $("#courseNumbers"));
        });

        $("#submitByCode").click(function(){
            addClassToCart($("#courseNumbers"), $("#titles"));
        });

        getNumbers($("#codes option:selected").text());

        $("#codes").change(function(){
            getNumbers($("#codes option:selected").text());
        });

        $(".classes-added-list").on("click", ".removeCourse", function() {
            var courseTitle = $(this).parent().find(".courseTitle").text();
            $(this).parent().remove();
            enableDropDowns(courseTitle);
        });

        $("#generate").click(function() {
            generateSchedules();
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
    var $courseNumbers = $("#courseNumbers");
    $courseNumbers.append(data);
    $courseNumbers.find("option").each(function() {
        if ($(".classes-added-list").text().indexOf($(this).val())>-1) {
            $(this).attr('disabled','disabled');
        };
    });
    $courseNumbers.children('option:enabled').eq(0).prop('selected',true);
  })

}

function toggle($toShow, $toHide) {
    $toShow.show();
    $toHide.hide();
}

function addClassToCart($main, $other) {
    if (!$(".title").length) {
        $(".classes-added").prepend("<h1> Classes Selected </h1>");
    }

    var $course = $main.find("option:selected");
    var title = $course.val().split(" - ")[0];

    $(".classes-added-list").append(
        '<li class="title"> <span value="'+title+'"class="courseTitle">'+
        $course.val()+
        '</span> <span class="removeCourse">Remove</span> </li>');

    $course.attr('disabled','disabled');
    $main.children('option:enabled').eq(0).prop('selected',true);

    $courseInOther = $other.find("option[value='"+$course.val()+"']");
    if ($courseInOther.length) {
        $courseInOther.attr('disabled','disabled');
        $other.children('option:enabled').eq(0).prop('selected',true);
    }
}

function enableDropDowns(courseTitle) {
    enableDropDown($("#titles"), courseTitle);
    enableDropDown($("#courseNumbers"), courseTitle);
    if (!$(".title").length) {
      $(".classes-added h1").remove();
    }
}

function enableDropDown($dropdown, courseTitle) {
    $dropdown.find("option[value='"+courseTitle+"']").attr("disabled", false);
    $dropdown.children('option:enabled').eq(0).attr("selected",true);
}

function generateSchedules() {
    $(".schedules").empty();
    $(".schedules").append("<h1>Possible Schedules</h1>");
    var titles = [];
    $('.courseTitle').each(function() {
        titles.push($(this).attr("value"));
    });
    var credits = $("#credits").val();
    var posting = $.post("courseDB.php", { titles: titles.join(","), credits: credits });
    posting.done(function(data){
        $(".schedules").append(data);
    });
}
