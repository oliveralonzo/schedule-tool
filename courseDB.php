<?php

/**
 * Connects and pulls course data from the database for an array of titles
 * collected from the user. Creates and returns a customly formatted array.
 *
 * @author Josh Nichols & Oliver Alonzo
 * @version 1.0
 */

function fetchCourses() {
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
  // Separate titles
  $titles = explode(" && ", $_POST["titles"]);
  $sections = [];
  foreach ($titles as $title) {
    $query = "SELECT crn, subject_code, course_number, instructor_fname, instructor_lname, course_title, Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, start_time, end_time, credit_hrs FROM vw_class_info WHERE course_title = \"{$title}\"";

    if (!($result=mysqli_query($conn,$query))) {
      echo "Error:". mysqli_error($conn). "\n";
    }
    /**
    * Build course array in this format:
    * CRN, Department+Code, Professor Name, Course Title,  Scheduled days+times, Credits, Course Type
    * (e.g. 11362, JRM219, Richard Johnson, Media Writing, MW 1100-1215, 3, Journalism)
    */
    while ($row = $result->fetch_row()) {
      $classRows = "$row[0], $row[1]$row[2], $row[3] $row[4], ".str_replace(","," ",$row[5]).", $row[6]$row[7]$row[8]$row[9]$row[10]$row[11]$row[12] $row[13]-$row[14], $row[15]";
      array_push($sections, $classRows);
    }
  }
  $conn->close();
  // This is calling the main PHP file, not elegant
  include 'index.php';
}

fetchCourses();

?>
