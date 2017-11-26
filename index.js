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

        $("#timeRes").click(function() {
          $(".dateTimeLimit").toggle();
        });

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
            var courseTitle = $(this).parent().find(".courseTitle").attr("value");
            $(this).parent().remove();
            enableDropDowns(courseTitle);
        });

        $(".activeRestrictions").on("click", ".removeRestriction", function() {
            $(this).parent().remove();
        });

        $("#generate").click(function() {
            generateSchedules();
        });

        $("#subTimeLimit").click(function() {
            addRestriction();
            clearRestrictionsForm();
            $(".dateTimeLimit").toggle();
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
        if ($(".classes-added-list").text().indexOf($(this).prop("title"))>-1) {
            $(this).attr('disabled','disabled');1
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
    var title = $course.attr("title");
    var course = $course.attr("course");
    var credits = $course.attr("credits");
    var variCredits = '';

    if (credits == "") {
      variCredits = '<input type="text" maxlength="1" size="2" value="" name="credits">';
    }

    $(".classes-added-list").append(
        '<li class="title"> <span value="'+title+'" credits="'+credits+'"class="courseTitle">'+
        title+" - "+course+" - "+credits+variCredits+
        '</span> <span class="removeCourse remove">Remove</span> </li>');

    $('input[name="credits"]').change(function () {
      var credits = $(this).val();
      $(this).parent().attr("credits", credits);
    });

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
    $dropdown.find("option[title='"+courseTitle+"']").attr("disabled", false);
    $dropdown.children('option:enabled').eq(0).prop("selected",true);
}

function generateSchedules() {
    $(".schedules").empty();
    $(".schedules").append("<h1>Possible Schedules</h1>");
    var titles = [];
    $('.courseTitle').each(function() {
        titles.push($(this).attr("value"));
    });

    var blocks = [];
    $('.restriction').each(function() {
      blocks.push($(this).text());
    });

    var credits = $("#credits").val();
    var posting = $.post("courseDB.php", { titles: titles.join(","), credits: credits, blocks: blocks.join(",") });
    posting.done(function(data){
        $(".schedules").append(data);
    });
}

function addRestriction() {
  // format is MWTR 0000-000
  var startTime = $("#startTime").val();
  var endTime = $("#endTime").val();
  var days = "";
   $('.dayCheckboxes input:checked').each(function(){
     days+= $(this).val();
   });
   var element = '<li value="test"><span class="restriction">'+days+" "+
   startTime+"-"+endTime+
   ' </span><span class="removeRestriction remove">Remove</span></li>';

   if ($('.dayCheckboxes input:checked').length > 0 && $(".activeRestrictions").html().indexOf(element)<0){
     $(".activeRestrictions").append(element);
   }
}

function clearRestrictionsForm(){
  $(".dayCheckboxes input").prop("checked", false);
  $("#startTime").val("");
  $("#endTime").val("");
}
