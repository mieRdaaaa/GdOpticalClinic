<?php
include('db.php');

// User info
$user_fullname = '';
$user_role = '';

// Check if the user is logged in
session_start(); // Make sure to start the session
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

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $r_sphere = $_POST['right_sphere'];
    $l_sphere = $_POST['left_sphere'];
    $r_cylinder = $_POST['right_cylinder'];
    $l_cylinder = $_POST['left_cylinder'];
    $r_axis = $_POST['right_axis'];
    $l_axis = $_POST['left_axis'];
    $pd = $_POST['pupillary_distance'];
    $diagnosis = $_POST['diagnosis']; // New field for diagnosis

    // Prepare the SQL statement to insert the data into the database
    $sql = "INSERT INTO eye_result
            (r_sphere, l_sphere, r_cylinder, l_cylinder, r_axis, l_axis, pd, diagnosis, patients_id) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddddssi", $r_sphere, $l_sphere, $r_cylinder, $l_cylinder, $r_axis, $l_axis, $pd, $diagnosis, $patient_id);

    // Execute the query and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect to the desired page after submission
        header("Location: doctor_table.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Start: Main -->
    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all">
        <div class="py-2 px-6 bg-white flex items-center shadow-md sticky top-0 z-30">
            <button type="button" class="text-lg text-gray-600 sidebar-toggle">
                <i class="ri-menu-line"></i>
            </button>
            <ul class="flex items-center text-sm ml-4">
                <li class="mr-2">
                    <a href="doctor_table.php" class="text-gray-400 hover:text-gray-600 font-medium">Patients Table</a>
                </li>
                <li class="text-gray-600 mr-2 font-medium">/</li>
                <li class="text-black-600 mr-2 font-medium">Eye Examination</li>
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

        <div class="container mx-auto px-4 py-8">
        <h3 class="text-3xl text-black-600 mr-2 font-medium">Eye Examination for ID: <?php echo $patient_id; ?></h3>
            <form method="POST" action="" class="bg-white p-6 rounded shadow-md">
                <div class="mb-4">
                    <label for="right_sphere" class="block text-gray-700"><i class="fa fa-eye"></i> Right Sphere:</label>
                    <input type="number" step="0.01" id="right_sphere" name="right_sphere" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="left_sphere" class="block text-gray-700"><i class="fa fa-eye"></i> Left Sphere:</label>
                    <input type="number" step="0.01" id="left_sphere" name="left_sphere" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="right_cylinder" class="block text-gray-700"><i class="fa fa-eye"></i> Right Cylinder:</label>
                    <input type="number" step="0.01" id="right_cylinder" name="right_cylinder" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="left_cylinder" class="block text-gray-700"><i class="fa fa-eye"></i> Left Cylinder:</label>
                    <input type="number" step="0.01" id="left_cylinder" name="left_cylinder" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="right_axis" class="block text-gray-700"><i class="fa fa-eye"></i> Right Axis:</label>
                    <input type="number" step="0.01" id="right_axis" name="right_axis" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="left_axis" class="block text-gray-700"><i class="fa fa-eye"></i> Left Axis:</label>
                    <input type="number" step="0.01" id="left_axis" name="left_axis" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="pupillary_distance" class="block text-gray-700"><i class="fa fa-eye"></i> Pupillary Distance:</label>
                    <input type="number" step="0.01" id="pupillary_distance" name="pupillary_distance" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <div class="mb-4">
                    <label for="diagnosis" class="block text-gray-700"><i class="fa fa-eye"></i> Diagnosis:</label>
                    <textarea id="diagnosis" name="diagnosis" required class="mt-1 block w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-300"></textarea>
                </div>

                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600" onclick="alert('New eye record added')"><i class="fas fa-check"></i> Submit</button>
                    <button type="button" class="bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400" onclick="window.location.href='doctor_view.php?id=<?php echo $patient_id; ?>';"><i class="fas fa-times"></i> Cancel</button>

            </form>
        </div>
    </main>
</body>
<?php include('doctor_homepage.php'); ?>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../dist/js/script.js"></script>
</html>
