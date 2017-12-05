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
        });

        $("#subTimeLimit").click(function() {
            addRestriction();
            clearRestrictionsForm();
            // $(".dateTimeLimit").toggle();
        });

        $(".back-schedules").click(function() {
          $(".content").toggleClass('hide');
          $(".schedules-content").slick('unslick');
          $(".schedules .schedules-content").empty();
          $(".schedules-box").toggleClass('hide');
        })

        $(".back-no-schedules").click(function() {
          $('.no-schedules').addClass('hide');
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
    // loading box being hidden again from main.js 340. Make that a promise later on
    $(".loading-box").toggleClass('hide');
    $('.restrictions').modal('hide');
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
        if (data) {
          processSchedules(data);
        } else {
          $('.loading-box').addClass('hide');
          $('.no-schedules').removeClass('hide');
        }
    });
}

function initSemantic() {
  $("#titles").dropdown();
  $("#codes").dropdown();
  $("#courseNumbers").dropdown();
}

function processSchedules(data){
  var schedules = data.split("\n\n");
  var c = 0;
  schedules.forEach(function(schedule){
    $.get("template/index.html",function(data){
      $(".schedules .schedules-content").append(data);
      var courses = schedule.split("\n");
      var eventCount = 1;
      courses.forEach(function(course) {
        if (course){
          var info = course.split(", ");
          var crn = info[0];
          var courseCode = info[1];
          var times = info[4].split(" && ");
          times.forEach(function(time) {
            var $current = $(".schedules .schedules-content .cd-schedule:last-child");
            if (time.trim() !== "-") {
              var days = time.split(" ")[0];
              var hours = time.split(" ")[1].split("-");
              for (var i=0; i<days.length; i++) {
                $current.find("#" + days[i]).append(
                  createEvent(formatTime(hours[0]), formatTime(hours[1]), courseCode + " - " + crn, eventCount));
              }
            } else {
              $current.find(".not-scheduled").removeClass('hide');
              $current.find(".not-scheduled ul").append(
                '<li>' + courseCode + " - " + crn + '</li>'
              );
            }
          });
        }
        eventCount++;
      });
      //console.log($('.cd-schedule'));
      c++;
      if (c===schedules.length) {
        displaySchedules();
      }
    });
  });
}

function displaySchedules() {
  $(".schedules-box").toggleClass('hide');
  $(".content").toggleClass('hide');
  $(".schedules-content").slick({
      dots: true,
      adaptiveHeight: true
  });
  createTemplate();
}

function createEvent(startTime, endTime, title, eventCount) {
  return `<li class="single-event" data-start="${startTime}" data-end="${endTime}" data-content="event-abs-circuit" data-event="event-${eventCount}">
    <a href="#0">
      <em class="event-name">${title}</em>
    </a>
  </li>`
}

function formatTime(time){
  if (time) {
    return time.substring(0, 2)+":"+time.substring(2);
  } else {
    return false;
  }
}

jQuery.cachedScript = function( url, options ) {
  // Allow user to set any option except for dataType, cache, and url
  options = $.extend( options || {}, {
    dataType: "script",
    cache: true,
    async: false,
    url: url
  });

  // Use $.ajax() since it is more flexible than $.getScript
  // Return the jqXHR object so we can chain callbacks
  return jQuery.ajax( options );
};


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
