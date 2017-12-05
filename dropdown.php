<?php

/**
 * Connects to the database, then formats, builds, and displays the course
 * selection dropdowns. Assigns metadata
 *
 * @author Josh Nichols & Oliver Alonzo
 * @version 1.0
 */

function buildCourseDropdowns() {
  $servername = "RaDLabpeoplecounter.creighton.edu";
  $username = "jen94317";
  $password = "SVOOyr80h7m3sSsT";
  $mainDatabase= "for_capstone";
  // Create connection
  $conn = new mysqli($servername, $username, $password, $mainDatabase);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    echo "Connected fail";
  }
  $sql="SHOW DATABASES";

  $query = "SELECT DISTINCT course_title, subject_code, course_number, credit_hrs FROM vw_class_info";

  if (!($result=mysqli_query($conn,$query))) {
    printf("Error: %s\n", mysqli_error($conn));
  }

  $courseTitleArray = array();
  $subjectCodeArray = array();
  while($row = $result->fetch_assoc()) {
    $courseTitleArray[] = str_replace('"',"",$row['course_title']) . " && " . $row['subject_code'] . $row['course_number'] . " && " . $row['credit_hrs'];
    $subjectCode = $row['subject_code'];
    if (!in_array($subjectCode,$subjectCodeArray)) {
        array_push($subjectCodeArray, $subjectCode);
    }
  }

  $conn->close();
  // Sort dropdowns contents alphabetically
  asort($courseTitleArray);
  asort($subjectCodeArray);
  $resultDrop = "";
  // Dropdown for select by course title
  echo '<div class="titleDropdown">';
  // Builds dropdown
  echo '<select id="titles" name="credits" onchange="" class="ui search selection dropdown">';
  echo '<option value=""> Search by Title... </option>';
  // Populate dropdown
  foreach ($courseTitleArray as $i => $title) {
    // Skip dummy course
    if ($i == 0){
      continue;
    }

    $values = explode(" && ", $title);

    echo '<option title="'.$values[0].'" course="'.$values[1].'" credits="'.$values[2].'">'.$values[0].'</option>';
  }
  echo '</select>';
  echo '<input id="submitByTitle" type="button" name="add" value="Add" class="ui button addButton addButton">';
  echo '</div>';
  // Dropdown for select by course code and number
  echo '<div class="codeDropdown">';
  // Builds dropdown
  echo '<select id="codes" name="credits" onchange="" class="ui search selection dropdown">';
  echo '<option value=""> Search by Code... </option>';
  // Populate dropdown
  foreach ($subjectCodeArray as $i => $code) {
    // Skip dummy course
    if ($i == 0){
      continue;
    }
    echo '<option value="'.$code.'">'.$code.'</option>';
  }
  echo '</select>';
  // Subject numbers dropdown
  echo '<select id="courseNumbers" name="credits" onchange="" class="ui search selection dropdown">';
  echo '</select>';
  
  echo '<input id="submitByCode" type="button" name="add" value="Add" class="ui button addButton">';
  echo '</div>';

}

buildCourseDropdowns();

?>
