$(document).ready(function(){
    $("#titleButton").click(function(){
        toggle($(".titleDropdown"), $(".codeDropdown"));
    });
    $("#codeButton").click(function(){
        toggle($(".codeDropdown"), $(".titleDropdown"));
    });

    var getting = $.get("dropdown.php");
    getting.done(function(data) {

        $(".dropdowns").html(data);
        $(".search-wrap").toggleClass('hide');
        $(".search-loading").toggleClass('hide');
        $(".codeDropdown").hide();

        // $("#timeRes").click(function() {
        //   $(".dateTimeLimit").toggle();
        // });

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
            $(this).parent().parent().remove();
            if (!$(".title").length) {
              $(".classes-added").toggleClass('hide');
            }
        });

        $(".activeRestrictions").on("click", ".removeRestriction", function() {
            $(this).parent().remove();
        });

        $("#generate").click(function() {
            generateSchedules();
            $('.restrictions').modal('hide');
        });

        $("#subTimeLimit").click(function() {
            addRestriction();
            clearRestrictionsForm();
            // $(".dateTimeLimit").toggle();
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
    var title = $course.attr("title");
    if (title && $(".classes-added-list").text().indexOf(title)<0) {
      if ($(".title").length == 0) {
        $('.classes-added').toggleClass('hide');
      }

      var course = $course.attr("course");
      var credits = $course.attr("credits");
      var variCredits = '';

      if (credits == "") {
        variCredits = '<input type="text" maxlength="1" size="2" value="" name="credits">';
      }

      $(".classes-added-list").append(
          '<li class="title"> <div value="'+title+'" credits="'+credits+'"class="course-title course-list-element"><span class="title-span">'+
          title+'</span><span class="course-span">'+course+'</span><span class="credits-span">'+credits+variCredits+
          '</span><span class="removeCourse remove remove-span">Remove</span></div>  </li>');

      $('input[name="credits"]').change(function () {
        var credits = $(this).val();
        $(this).parent().attr("credits", credits);
      });
    } else {
      // Display message for not being able to add
    }

}

function generateSchedules() {
    $(".schedules .schedules-content").empty();
    $(".schedules").prepend('');
    var titles = [];
    $('.course-title').each(function() {
        titles.push($(this).attr("value"));
    });

    var blocks = [];
    $('.restriction').each(function() {
      blocks.push($(this).attr("value"));
    });

    var credits = $("#credits").val();
    var posting = $.post("courseDB.php", { titles: titles.join(" && "), credits: credits, blocks: blocks.join(" && ") });
    posting.done(function(data){
        // Output for testing
        // $(".schedules .schedules-content").html(data);
        // $(".schedules").modal('refresh');
        // $(".schedules").modal('show');

        //Output for production
        processSchedules(data);
    });
}

function addRestriction() {
  // format is MWTR 0000-000
  var startTime = $("#startTime").val();
  var endTime = $("#endTime").val();
  var daysCodes = "";
  var days = "";
   $('.dayCheckboxes input:checked').each(function(){
     daysCodes+= $(this).val();
     days += $("label[for='"+$(this).attr("id")+"']").text()+", ";
   });
   var element = '<li class="restriction" value="'+daysCodes+" "+startTime+"-"+endTime+'"><span>'+days.substring(0, days.length - 2)+' '+startTime+"-"+endTime+' </span><span class="removeRestriction remove">Remove</span></li>';

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
      $(".schedules .schedules-content").append(data);
      var courses = schedule.split("\n");
      var eventCount = 1;
      courses.forEach(function(course) {
        if (course){
          var info = course.split(", ");
          var title = info[3];
          var times = info[4].split(" && ");
          times.forEach(function(time) {
            var days = time.split(" ")[0];
            var hours = time.split(" ")[1].split("-");
            for (var i=0; i<days.length; i++) {
              $(".schedules .schedules-content .cd-schedule:last-child #" + days[i]).append(
                createEvent(formatTime(hours[0]), formatTime(hours[1]), title, eventCount));
            }
          });
        }

        eventCount++;
      })
    });
  });
  $.cachedScript("template/js/main.js").done(function () {
    $(".content").toggleClass('hide');
    $(".schedules-content").slick();
    $(".schedules-box").toggleClass('hide');
  });
  // $(".schedules").modal('refresh');
}

jQuery.cachedScript = function( url, options ) {
  // Allow user to set any option except for dataType, cache, and url
  options = $.extend( options || {}, {
    dataType: "script",
    cache: true,
    url: url
  });

  // Use $.ajax() since it is more flexible than $.getScript
  // Return the jqXHR object so we can chain callbacks
  return jQuery.ajax( options );
};
