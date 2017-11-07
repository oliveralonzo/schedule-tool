<?php

function connectToDatabase() {
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

$titles = ["Principles Of Marketing","Abnormal Psychology"];
$rClasses = "";

foreach ($titles as $title) {
  $query = "SELECT crn, subject_code, course_number, instructor_fname, instructor_lname, course_title, Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, start_time, end_time, credit_hrs FROM vw_class_info WHERE course_title = \"{$title}\"";
  // Would output:
  // SELECT `title` FROM `table` WHERE `id` = '5'

  if (!($result=mysqli_query($conn,$query))) {
    printf("Error: %s\n", mysqli_error($conn));
  }

  while ($row = $result->fetch_row()) {
      //old old code printf ("%s (%s)\n", $row[5], $row[6]);
      $classRows = "$row[0], $row[1]$row[2], $row[3] $row[4], $row[5], $row[6]$row[7]$row[8]$row[9]$row[10]$row[11]$row[12], $row[13]-$row[14], $row[15]";
      echo nl2br($classRows."\n");
      $rClasses.=$classRows."\n";
  }
}
// Notice the subtraction from $current_id
$conn->close();

}
function retrieveData(){

}

connectToDatabase();
retrieveData();
 ?>
