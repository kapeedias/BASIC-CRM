<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Include the database connection file
include("_include/dbc.php");

// Fetch user data from the database
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM crm_users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the PDO connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
</head>

<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Your Account Information:</p>
    <ul>
        <li>User ID: <?php echo $user['user_id']; ?></li>
        <li>Username: <?php echo $user['username']; ?></li>
        <li>Email: <?php echo $user['email']; ?></li>
    </ul>
    <p><a href="logout.php">Logout</a></p>
</body>

</html>
