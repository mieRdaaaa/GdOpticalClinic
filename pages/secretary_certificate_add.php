<?php
include('db.php');

// User info
$user_fullname = '';
$user_role = '';

session_start(); // Start the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare SQL statement to get user info
    $stmt = $conn->prepare("SELECT fullname, account_type FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_fullname = $row['fullname'];
        $user_role = $row['account_type'];
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}
// Get the patient's ID from the query string
$patient_id = intval($_GET['id'] ?? 0);

// Validate the patient_id
if ($patient_id <= 0) {
    die("Invalid patient ID.");
}

// Check if the patient exists
$check_patient_sql = "SELECT * FROM patients WHERE patients_id = ?";
$stmt = $conn->prepare($check_patient_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient_result = $stmt->get_result();

if ($patient_result->num_rows === 0) {
    die("Patient ID does not exist.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $examination = $_POST['examination'];
    $recommendation = $_POST['recommendation'];
    $osuva = $_POST['osuva'];
    $oduva = $_POST['oduva'];
    $osadd = $_POST['osadd'];
    $odadd = $_POST['odadd'];
    $odbcva = $_POST['odbcva'];
    $osbcva = $_POST['osbcva'];

    // Prepare an SQL statement for inserting data
    $sql = "INSERT INTO certificate (examination, recommendation, osuva, oduva, osadd, odadd, odbcva, osbcva, patients_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    // Bind parameters, including the patient_id
    $stmt->bind_param('ssssssssi', $examination, $recommendation, $osuva, $oduva, $osadd, $odadd, $odbcva, $osbcva, $patient_id);
 
    // Execute the query and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect to the desired page after submission
        header("Location: secretary_certificate.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the MySQLi connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Add Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/ico.png" />
</head>
<body>
    <!-- Start: Main -->
<main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
    <div class="py-2 px-6 bg-white flex items-center shadow-md sticky top-0 z-30">
        <button type="button" class="text-lg text-gray-600 sidebar-toggle">
            <i class="ri-menu-line"></i>
        </button>
        <ul class="flex items-center text-sm ml-4">
            <li class="mr-2">
                <a href="secretary_certificate.php" class="text-gray-400 hover:text-gray-600 font-medium">Certificate</a>
            </li>
            <li class="text-black-600 mr-2 font-medium">/</li>
            <li class="text-black-600 mr-2 font-medium">Add Certificate</li>
        </ul>
        <div class="ml-auto flex items-center">
            <div class="dropdown ml-3">
                <button type="button" class="dropdown-toggle flex items-center">
                    <img src="../images/profile.png" alt="Profile Image" class="w-8 h-8 rounded-full block object-cover">
                </button>
                <ul class="dropdown-menu shadow-md z-30 hidden py-1.5 rounded-md bg-white border border-gray-100">
                    <li>
                        <a href="../index.php" class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-black-50">Logout</a>
                    </li>
                </ul>
            </div>
            <div class="user-details ml-3">
                <span class="name text-sm font-semibold text-gray-900 block"><?php echo htmlspecialchars($user_fullname); ?></span>
                <span class="role text-xs text-gray-500"><?php echo ucfirst(htmlspecialchars($user_role)); ?></span>
            </div>
        </div>
    </div>

    <div class="max-w-full mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Add Certificate</h2>

        <form action="" method="POST">
            <div class="flex space-x-4 mb-4">
                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="symptoms">Symptoms:</label>
                    <input type="text" id="symptoms" name="symptoms" placeholder="Enter symptoms" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="examination">Examination:</label>
                    <input type="text" id="examination" name="examination" required placeholder="Enter examination results" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="input-box w-full">
                    <label class="block text-sm font-semibold mb-1" for="recommendation">Recommendation:</label>
                    <textarea id="recommendation" name="recommendation" rows="6" required placeholder="Enter recommendations" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="osuva">Osuva:</label>
                    <input type="text" id="osuva" name="osuva" placeholder="Enter Osuva" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="oduva">Oduva:</label>
                    <input type="text" id="oduva" name="oduva" placeholder="Enter Oduva" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="osadd">Osadd:</label>
                    <input type="text" id="osadd" name="osadd" placeholder="Enter Osadd" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="odadd">Odadd:</label>
                    <input type="text" id="odadd" name="odadd" placeholder="Enter Odadd" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="odbcva">Odbcva:</label>
                    <input type="text" id="odbcva" name="odbcva" placeholder="Enter Odbcva" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="input-box w-1/2">
                    <label class="block text-sm font-semibold mb-1" for="osbcva">Osbcva:</label>
                    <input type="text" id="osbcva" name="osbcva" placeholder="Enter Osbcva" class="block w-full border border-gray-300 bg-white rounded-md py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mb-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
            </div>
        </form>
    </div>
</main>
<?php include('secretary_homepage.php'); ?>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../dist/js/script.js"></script>

</body>
</html>

