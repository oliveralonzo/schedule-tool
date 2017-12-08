var timeout;

// All jquery functions are attached during $(document).ready();
$(document).ready(function(){
    $("#titleButton").click(function(){
        toggle($(".titleDropdown"), $(".codeDropdown"));
    });
    $("#codeButton").click(function(){
        toggle($(".codeDropdown"), $(".titleDropdown"));
    });

    var getting = $.get("php/dropdown.php");
    getting.done(function(data) {
        $(".dropdowns").prepend(data);
        $(".search-wrap").toggleClass('hide');
        $(".search-loading").toggleClass('hide');
        $(".codeDropdown").hide();

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
        });

        $('#getStarted').click(function(){
          $('.welcome-box').remove();
          $('.search-box').toggleClass('hide');
        });

        $('#showCredits').click(function(){
          $('.restrictions').modal('show');
        });

        $(".back-no-schedules").click(function() {
          $('.no-schedules').addClass('hide');
        });

        $('.message .close').on('click', function() {
          $(this).closest('.message').transition('fade');
          clearTimeout(timeout);
        });

        initSemantic();
  }).fail(function(err,status){
    alert("err");
  });

});

// Function that gets course numbers for selected course code
function getNumbers(subject_code) {
  $("#courseNumbers").empty();
  var getNums = $.post("php/courseNumbers.php",{
    subject_code: subject_code
  })
  getNums.done(function(data){
    var $courseNumbers = $("#courseNumbers");
    $courseNumbers.append(data);
  })
}

// Function that adds functionality from Semantic UI to dropdowns
function initSemantic() {
  $("#titles").dropdown();
  $("#codes").dropdown();
  $("#courseNumbers").dropdown();
}

// Function that shows and element and hides another
function toggle($toShow, $toHide) {
    $toShow.show();
    $toHide.hide();
}

// Function that adds a class to the classes selected section
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
    } else if (title) {
      $('.dropdowns .message').transition('show');
      timeout = window.setTimeout(function() {
        $('.dropdowns .message').transition('hide');
      }, 3000);
    }

}

// Function that starts the process for generating schedules, calling the php File
// once that's done, it calls processSchedules with the returned data
function generateSchedules() {
  var credits = $("#credits").val();
  if (credits) {
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

    var posting = $.post("php/courseDB.php", { titles: titles.join(" && "), credits: credits, blocks: blocks.join(" && ") });
    posting.done(function(data){
        // Output for testing
        // console.log(data);

        //Output for production
        if (data) {
          processSchedules(data);
        } else {
          $('.loading-box').addClass('hide');
          $('.no-schedules').removeClass('hide');
        }
    });
  } else {
    $('.credits-box .message').transition('show');
    timeout = window.setTimeout(function() {
      $('.credits-box .message').transition('hide');
    }, 3000);
  }
}

// Function that creates the template for all the schedules generated
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
          var professor = info[2].trim();
          var times = info[4].split(" && ");
          times.forEach(function(time) {
            var $current = $(".schedules .schedules-content .cd-schedule:last-child");
            if (time.trim() !== "-") {
              var days = time.split(" ")[0];
              var hours = time.split(" ")[1].split("-");
              for (var i=0; i<days.length; i++) {
                $current.find("#" + days[i]).append(
                  createEvent(formatTime(hours[0]), formatTime(hours[1]), courseCode + " - " + crn, professor, eventCount));
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

// Function that displays the templated schedules
function displaySchedules() {
  $(".schedules-box").toggleClass('hide');
  $(".content").toggleClass('hide');
  $(".schedules-content").slick({
      dots: true,
      adaptiveHeight: true
  });
  createTemplate();
}

// Function that returns a li element for event
function createEvent(startTime, endTime, title, courseCode, eventCount) {
  return `<li class="single-event" data-start="${startTime}" data-end="${endTime}" data-content="${courseCode}" data-event="event-${eventCount}">
    <a href="#0">
      <em class="event-name">${title}</em>
    </a>
  </li>`
}

// Function that returns a li element for event
function formatTime(time){
  if (time) {
    return time.substring(0, 2)+":"+time.substring(2);
  } else {
    return false;
  }
}

// Function that adds a restriction to the restrictions modal
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

// Function that clears the restrictions form in the modal
function clearRestrictionsForm(){
  $(".dayCheckboxes input").prop("checked", false);
  $("#startTime").val("");
  $("#endTime").val("");
}
