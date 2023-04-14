<?php

/**
* @author     Phumlani Nyati
* @datetime   13 April 2023
* @purpose    Form Validation
*/



// Connect to the database
$mongo = new MongoDB\Client('mongodb://localhost:27017');
$db = $mongo->test;
$collection = $db->users;

// Get the form data
$name = $_POST['name'];
$surname = $_POST['surname'];
$id_number = $_POST['id_number'];
$date_of_birth = $_POST['date_of_birth'];

// Validate the ID Number
if (!is_numeric($id_number) || strlen($id_number) != 13) {
  die('Invalid ID Number');
}

// Validate the Date of Birth
$date_parts = explode('/', $date_of_birth);
if (count($date_parts) != 3 || !checkdate($date_parts[1], $date_parts[0], $date_parts[2])) {
  die('Invalid Date of Birth');
}

// Check for duplicates
if ($collection->countDocuments(['id_number' => $id_number]) > 0) {
  die('ID Number already exists');
}

// Insert the record
$result = $collection->insertOne([
  'name' => $name,
  'surname' => $surname,
  'id_number' => $id_number,
  'date_of_birth' => $date_of_birth
]);

if ($result->getInsertedCount() == 1) {
  echo 'Record inserted successfully';
} else {
  echo 'Error inserting record';
}
