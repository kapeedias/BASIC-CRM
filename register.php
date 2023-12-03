<?php
// Start the session
session_start();

// Include the database connection file
include("_include/dbc.php");

// Function to generate a random salt
function generateSalt() {
    return bin2hex(random_bytes(32));
}

// Function to sanitize user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $email = sanitizeInput($_POST['email']);

    try {
        // Check if a user with the same email already exists
        $stmt_check = $conn->prepare("SELECT id FROM crm_users WHERE email = :email");
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            echo "User with the same email already exists!";
        } else {
            // Generate a random salt
            $salt = generateSalt();

            // Combine the password and salt, and then hash them
            $hashedPassword = hash('sha256', $password . $salt);

            // Prepare and execute the SQL query to insert user data into the database
            $stmt = $conn->prepare("INSERT INTO crm_users (username, password_hash, salt, email) VALUES (:username, :password_hash, :salt, :email)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $hashedPassword);
            $stmt->bindParam(':salt', $salt);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo "Registration successful!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the PDO connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <h2>Register</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <input type="submit" value="Register">
    </form>
</body>

</html>
