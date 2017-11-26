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
            addClassToCart($("#titles").find("option:selected"));
        });

        $("#submitByCode").click(function(){
            addClassToCart($("#courseNumbers").find("option:selected"));
        });

        getNumbers($("#codes option:selected").text());

        $("#codes").change(function(){
            getNumbers($("#codes option:selected").text());
        });

        $(".classes-added-list").on("click", ".removeCourse", function() {
            $(this).parent().remove();
            if (!$(".title").length) {
              $(".classes-added h1").remove();
            }
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
        initSemantic();
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
  })

}

function toggle($toShow, $toHide) {
    $toShow.show();
    $toHide.hide();
}

function addClassToCart($course) {
    if (!$(".title").length) {
        $(".classes-added").prepend("<h1> Classes Selected </h1>");
    }

    var title = $course.attr("title");
    if ($(".classes-added-list").text().indexOf(title)<0) {
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
    } else {
      // Display message for not being able to add
    }

    initSemantic();
}

function generateSchedules() {
    $(".schedules .content").empty();
    $(".schedules").prepend('');
    var titles = [];
    $('.courseTitle').each(function() {
        titles.push($(this).attr("value"));
    });

    var blocks = [];
    $('.restriction').each(function() {
      blocks.push($(this).text());
    });

    var credits = $("#credits").val();
    var posting = $.post("courseDB.php", { titles: titles.join(" && "), credits: credits, blocks: blocks.join(" && ") });
    posting.done(function(data){
        $(".schedules .content").html(data);
        //processSchedules(data);
        $(".schedules").modal('refresh');
    });
    $(".schedules").modal('show');
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

function initSemantic() {
  $("#titles").dropdown();
  $("#codes").dropdown();
  $("#courseNumbers").dropdown();
}

function refreshSemantic() {
  $("#titles").dropdown('refresh');
}

function createEvent(startTime, endTime, title, eventCount) {
  return `<li class="single-event" data-start="${startTime}" data-end="${endTime}" data-content="event-abs-circuit" data-event="event-${eventCount}">
    <a href="#0">
      <em class="event-name">${title}</em>
    </a>
  </li>`
}

function formatTime(time){
  return time.substring(0, 2)+":"+time.substring(2);
}

function processSchedules(data){
  var schedules = data.split("\n\n");
  schedules.forEach(function(schedule){
    $.get("template/index.html",function(data){
      $(".schedules .content").append(data);
      var courses = schedule.split("\n");
      courses.forEach(function(course){
        var info = course.split(", ");
        var title = info[3];
        var times = info[4].split(" && ");
        times.forEach(function(time) {
          var days = time.split(" ")[0];
          var hours = time.split(" ")[1].split("-");
          for (var i=0; i<days.length; i++) {
            $(".schedules .content .cd-schedule:last-child #" + days[i]).append(
              createEvent(formatTime(hours[0]), formatTime(hours[1]), title, i));
          }
        });
      })
    });
  });
}
