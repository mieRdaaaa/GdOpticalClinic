<?php

require('../fpdf/fpdf.php');
include('db.php');


// Get the certificate_id and patient_id from the query string
$certificate_id = intval($_GET['id'] ?? 0);
$patients_id = intval($_GET['patient_id'] ?? 0);

// Debug output
if ($certificate_id <= 0) {
    die("Invalid certificate ID: " . htmlspecialchars($_GET['id'] ?? 'not set'));
}
if ($patients_id <= 0) {
    die("Invalid patient ID: " . htmlspecialchars($_GET['patient_id'] ?? 'not set'));
}

// Fetch the certificate details from the database
$certificate_sql = "SELECT *, eye_result_id FROM certificate WHERE certificate_id = ?";
$stmt = $conn->prepare($certificate_sql);
$stmt->bind_param("i", $certificate_id);
$stmt->execute();
$certificate_result = $stmt->get_result();

// Check if the certificate exists
if ($certificate_result->num_rows > 0) {
    $certificate_data = $certificate_result->fetch_assoc();

    // Fetch patient details
    $patient_sql = "SELECT first_name, middle_name, last_name FROM patients WHERE patients_id = ?";
    $patient_stmt = $conn->prepare($patient_sql);
    $patient_stmt->bind_param("i", $patients_id);
    $patient_stmt->execute();
    $patient_result = $patient_stmt->get_result();
    
    if ($patient_result->num_rows > 0) {
        $patients = $patient_result->fetch_assoc();
    } else {
        die("Patient not found.");
    }

     // Fetch eye result details based on eye_result_id from the certificate
     $eye_result_id = intval($certificate_data['eye_result_id']);
     $eye_result_sql = "SELECT *, date_added AS date2 FROM eye_result WHERE eye_result_id = ?";
     $eye_result_stmt = $conn->prepare($eye_result_sql);
     $eye_result_stmt->bind_param("i", $eye_result_id);
     $eye_result_stmt->execute();
     $eye_result_result = $eye_result_stmt->get_result();
 
     if ($eye_result_result->num_rows > 0) {
         $eye_result = $eye_result_result->fetch_assoc();
     } else {
         die("Eye result not found.");
     }
     
      // Fetch the doctor's name based on created_by
    $doctor_id = intval($certificate_data['created_by']);
    $doctor_sql = "SELECT fullname, license_no, ptr_no FROM accounts WHERE accounts_id = ?";
    $doctor_stmt = $conn->prepare($doctor_sql);
    $doctor_stmt->bind_param("i", $doctor_id);
    $doctor_stmt->execute();
    $doctor_result = $doctor_stmt->get_result();

    if ($doctor_result->num_rows > 0) {
        $doctor_data = $doctor_result->fetch_assoc();
        $doctor_name = htmlspecialchars($doctor_data['fullname']);
        $license_no = htmlspecialchars($doctor_data['license_no']);
        $ptr_no = htmlspecialchars($doctor_data['ptr_no']);
    } else {
        die("Doctor not found.");
    }
 
 } else {
     die("Certificate not found.");
 }


 
 


 



 header('Content-Type: image/jpeg');
 $font = "arial.ttf";
 $fontbd='arialbd.ttf';
 $image = imagecreatefromjpeg("Jules12.jpg");
 $color = imagecolorallocate($image, 19, 20, 21);



$name1 = htmlspecialchars($patients['first_name']. ' ' . $patients['middle_name']. ' ' . $patients['last_name']); // Adjust to the correct field
//$name1 = "dsadas";

$date1= htmlspecialchars($certificate_data['date_added']);
$formatted_date1 = date('F j, Y', strtotime($date1)); 

$date2 = htmlspecialchars($eye_result['date2']); // This is now your additional date
$formatted_date2 = date('F j, Y', strtotime($date2)); 

$symptoms=htmlspecialchars($certificate_data['symptoms']);
$examination=htmlspecialchars($certificate_data['examination']);
$diagnosis=htmlspecialchars($eye_result['diagnosis']);
$recommendation=htmlspecialchars($certificate_data['recommendation']);
//RX
//OD right
$oduva=htmlspecialchars($certificate_data['oduva']);
$odsphere=htmlspecialchars($eye_result['r_sphere']);
$odcylinder=htmlspecialchars($eye_result['r_cylinder']);
$odaxis=htmlspecialchars($eye_result['r_axis']);
$odadd=htmlspecialchars($certificate_data['odadd']);
$odpd=htmlspecialchars($eye_result['pd']);
$odbcva=htmlspecialchars($certificate_data['odbcva']);
//OS left
$osuva=htmlspecialchars($certificate_data['osuva']);
$ossphere=htmlspecialchars($eye_result['l_sphere']);
$oscylinder=htmlspecialchars($eye_result['l_cylinder']);
$osaxis=htmlspecialchars($eye_result['l_axis']);
$osadd=htmlspecialchars($certificate_data['osadd']);
$ospd=htmlspecialchars($eye_result['pd']);
$osbcva=htmlspecialchars($certificate_data['osbcva']);
$doctor='glen';
imagettftext($image, 28 , 0 ,299, 435, $color, $font, $formatted_date1);
                        ///size   angle   x   y

imagettftext($image, 28 , 0 ,616, 588, $color, $font, $name1);

imagettftext($image, 27 , 0 ,409, 628, $color, $font, $formatted_date2);

imagettftext($image, 27 , 0 ,1128, 628, $color, $font, $symptoms);





//examination done
{
    // Define a maximum width for the text block
    $maxWidth = 700; // Adjust this to your image width constraints
    $fontSize = 28;
    $xPosition1 = 710;
    $yPosition1 = 782;
    $lineHeight = 77; // Vertical space between lines
    
    // Split the examination text into lines
    $words = explode(' ', $examination);
    $currentLine = '';
    foreach ($words as $word) {
        $testLine = $currentLine . ' ' . $word;
        $bbox = imagettfbbox($fontSize, 0, $font, $testLine);
        $lineWidth = $bbox[2] - $bbox[0]; // Width of the text
    
        if ($lineWidth > $maxWidth && !empty($currentLine)) {
            // Print the current line
            imagettftext($image, $fontSize, 0, $xPosition1, $yPosition1, $color, $font, trim($currentLine));
    
            // Move to the next line
            $currentLine = $word;
            $yPosition1 += $lineHeight;
        } else {
            $currentLine = $testLine;
        }
    }
    
    // Print the last line
    if (!empty($currentLine)) {
        imagettftext($image, $fontSize, 0, $xPosition1, $yPosition1, $color, $font, trim($currentLine));
    }
    }
    
    
    //diagnosis
    {
    
    $maxWidth = 700; // Adjust this to your image width constraints
    $fontSize = 28;
    $xPosition2 = 710;
    $yPosition2 = 1012;
    $lineHeight = 77; // Vertical space between lines
    
    // Split the diagnosis text into lines
    $words = explode(' ', $diagnosis);
    $currentLine = '';
    foreach ($words as $word) {
        $testLine = $currentLine . ' ' . $word;
        $bbox = imagettfbbox($fontSize, 0, $font, $testLine);
        $lineWidth = $bbox[2] - $bbox[0]; // Width of the text
    
        if ($lineWidth > $maxWidth && !empty($currentLine)) {
            // Print the current line
            imagettftext($image, $fontSize, 0, $xPosition2, $yPosition2, $color, $font, trim($currentLine));
    
            // Move to the next line
            $currentLine = $word;
            $yPosition2 += $lineHeight;
        } else {
            $currentLine = $testLine;
        }
    }
    
    // Print the last line
    if (!empty($currentLine)) {
        imagettftext($image, $fontSize, 0, $xPosition2, $yPosition2, $color, $font, trim($currentLine));
    }
    }

//recommendation
{
    $maxWidth = 400; // Adjust this to your image width constraints
    $fontSize = 28;
    $xPosition = 710;
    $yPosition = 1245;
    $lineHeight = 77; // Vertical space between lines
    
    // Split the recommendation text into lines
    $words = explode(' ', $recommendation);
    $currentLine = '';
    foreach ($words as $word) {
        $testLine = $currentLine . ' ' . $word;
        $bbox = imagettfbbox($fontSize, 0, $font, $testLine);
        $lineWidth = $bbox[2] - $bbox[0]; // Width of the text
    
        if ($lineWidth > $maxWidth && !empty($currentLine)) {
            // Print the current line
            imagettftext($image, $fontSize, 0, $xPosition, $yPosition, $color, $font, trim($currentLine));
    
            // Move to the next line
            $currentLine = $word;
            $yPosition += $lineHeight;
        } else {
            $currentLine = $testLine;
        }
    }
    
    // Print the last line
    if (!empty($currentLine)) {
        imagettftext($image, $fontSize, 0, $xPosition, $yPosition, $color, $font, trim($currentLine));
    }
    }
    
///spectacle rx
//od

imagettftext($image, 28 , 0 ,394, 1559, $color, $font, $oduva);
imagettftext($image, 28 , 0 ,553, 1559, $color, $font, $odsphere);
imagettftext($image, 28 , 0 ,730, 1559, $color, $font, $odcylinder);
imagettftext($image, 28 , 0 ,915, 1559, $color, $font, $odaxis);
imagettftext($image, 28 , 0 ,1071, 1559, $color, $font, $odadd);
imagettftext($image, 28 , 0 ,1225, 1559, $color, $font, $odpd);
imagettftext($image, 28 , 0 ,1379, 1559, $color, $font, $odbcva);

//os

imagettftext($image, 28 , 0 ,394, 1599, $color, $font, $osuva);
imagettftext($image, 28 , 0 ,553, 1599, $color, $font, $ossphere);
imagettftext($image, 28 , 0 ,730, 1599, $color, $font, $oscylinder);
imagettftext($image, 28 , 0 ,915, 1599, $color, $font, $osaxis);
imagettftext($image, 28 , 0 ,1071, 1599, $color, $font, $osadd);
imagettftext($image, 28 , 0 ,1225, 1599, $color, $font, $ospd);
imagettftext($image, 28 , 0 ,1379, 1599, $color, $font, $osbcva);


//DOCTOR
imagettftext($image, 27, 0, 226, 1908, $color, $fontbd, strtoupper($doctor_name));


//license
imagettftext($image, 24, 0, 419, 2064, $color, $fontbd, $license_no);
imagettftext($image, 24, 0, 369, 2102, $color, $fontbd, $ptr_no);


/*

// Save the generated image
imagejpeg($image);
imagedestroy($image);   
*/





// Save the generated image
imagejpeg($image, 'output_image.jpg');
imagedestroy($image);

// Create a new PDF document
$pdf = new FPDF('P', 'mm', 'Letter' );
$pdf->AddPage();
$pdf->Image('output_image.jpg', 0, 0, 216, 279); // Adjust dimensions as needed
$pdf->Output('D', 'output.pdf'); // Download the PDF file 
?>