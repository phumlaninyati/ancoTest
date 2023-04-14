<?php

/**
* @author     Phumlani Nyati
* @datetime   14 April 2023
* @purpose    Data and File Handling
*/


// Generate random data for the CSV file
function generateCsvData($numRecords) {
    $names = array(
        "Emma", "Olivia", "Ava", "Isabella", "Sophia",
        "Mia", "Charlotte", "Amelia", "Harper", "Evelyn",
        "Liam", "Noah", "Ethan", "Oliver", "Aiden",
        "Jackson", "Lucas", "Logan", "Caleb", "Mason"
    );
    
    $surnames = array(
        "Smith", "Johnson", "Brown", "Taylor", "Miller",
        "Jones", "Clark", "Hall", "Davis", "Wilson",
        "Moore", "Allen", "Young", "King", "Wright",
        "Scott", "Green", "Baker", "Adams", "Nelson"
    );
    
    $data = array(array("Id", "Name", "Surname", "Initials", "Age", "DateOfBirth"));
    $uniqueRows = array();
    
    while(count($uniqueRows) < $numRecords) {
        $name = $names[array_rand($names)];
        $surname = $surnames[array_rand($surnames)];
        $age = rand(18, 60);
        $dob = date('d/m/Y', strtotime('-'.$age.' years'));
        $row = array($name, $surname, substr($name, 0, 1), $age, $dob);
        
        // Check for duplicates
        if(!in_array($row, $uniqueRows)) {
            $uniqueRows[] = $row;
        }
    }
    
    // Add unique rows to data array
    foreach($uniqueRows as $key => $row) {
        $data[] = array($key+1, $row[0], $row[1], $row[2], $row[3], $row[4]);
    }
    
    return $data;
}

// Generate the CSV file
function generateCsvFile($numRecords) {
    $data = generateCsvData($numRecords);
    $file = fopen('output/output.csv', 'w');
    
    foreach($data as $row) {
        fputcsv($file, $row);
    }
    
    fclose($file);
    return count($data)-1;
}

// Generate a CSV file with 1,000,000 records if it is not coming from an imported file
$numRecords = $_POST['variations'];
if(empty($numRecords)){
    $numRecords = 1000000;
}
$numRecordsGenerated = generateCsvFile($numRecords);

// Import the CSV file to a SQLite database
if(isset($_FILES["csv_file"])) {
    // Create the "csv_import" table
    $db = new SQLite3('csv_data.db');
    $db->exec("CREATE TABLE csv_import (
                id INTEGER PRIMARY KEY,
                name TEXT,
                surname TEXT,
                initials TEXT,
                age INTEGER,
                dob TEXT)");
    
    // Open the CSV file and import its contents into the database
    $filename = $_FILES["csv_file"]["tmp_name"];
    $file = fopen($filename, "r");
    
    while(($row = fgetcsv($file, 0, ",")) !== FALSE) {
        $db->exec("INSERT INTO csv_import (name, surname, initials, age, dob)
                   VALUES ('".$row[1]."', '".$row[2]."', '".$row[3]."', '".$row[4]."', '".$row[5]."')");
    }
    
    fclose($file);
    echo "CSV file imported successfully.";
}

?>