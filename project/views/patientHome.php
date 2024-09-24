<?php
session_start();
// Ensure the user is logged in
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../views/logout.php");
    exit();
}

// Retrieve user information based on the stored email
require "../models/User.php";

$userInfo = null; // Initialize userInfo to avoid undefined variable warning

// Debug: Check if the email is set in the session
if (!isset($_SESSION['email'])) {
    echo "Email is not set in the session.";
    exit();
} else {
    // Fetch user info
    $userInfo = getUserInfoByEmail($_SESSION['email']); // Fetch user info

    if (!$userInfo) {
        echo "No user information found for the email: " . htmlspecialchars($_SESSION['email']);
        exit();
    }
}

// Fetch appointments for the logged-in patient
$appointments = getAppointmentsByEmail($_SESSION['email']); // Get appointments

// Handle actions based on the button clicked
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Home</title>

    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2 class="sidebarH1">Patient Dashboard</h2>
            <ul>
                <li><a href="?action=home">Home</a></li>
                <li><a href="?action=change_password">Change Password</a></li>
                <li><a href="?action=update_info">Update Information</a></li>
                <li><a href="?action=appointment_form">Take Appointment</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            // Include the appropriate content based on the action
            switch ($action) {
                case 'change_password':
                    include 'change_password.php';
                    break;
                case 'update_info':
                    include 'update_info.php';
                    break;
                case 'appointment_form':
                    include 'appointment_form.php';
                    break;
                default:
                    // Display patient's information
                    echo "<h1>Welcome to Patient's Dashboard</h1>";
                    echo "<h2>Patient Information</h2>";
                    if ($userInfo) {
                        echo "<p><strong>Full Name:</strong> {$userInfo['full_name']}</p>";
                        echo "<p><strong>Email:</strong> {$userInfo['email']}</p>";
                        echo "<p><strong>Contact Number:</strong> {$userInfo['contact_number']}</p>";
                        echo "<p><strong>Gender:</strong> {$userInfo['gender']}</p>";
                    } else {
                        echo "<p>Unable to retrieve your information. Please contact support.</p>";
                    }

                    // Display appointments in a table
                    echo "<h2>Your Appointments</h2>";
                    if (!empty($appointments)) {
                        echo "<table>";
                        echo "<tr><th>Doctor ID</th><th>Phone Number</th><th>Problem</th><th>Appointment Date</th><th>Status</th></tr>";
                        foreach ($appointments as $appointment) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($appointment['doctor_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($appointment['phone_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($appointment['problem']) . "</td>";
                            echo "<td>" . htmlspecialchars($appointment['appointment_date']) . "</td>"; // Ensure this field exists
                            echo "<td>" . htmlspecialchars($appointment['status']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>You have no appointments scheduled.</p>";
                    }
            }
            ?>
        </div>
    </div>
</body>

</html>