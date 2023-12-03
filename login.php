<?php
// Start the session
session_start();

// Include the database connection file
include("_include/dbc.php");

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

    try {
        // Prepare and execute the SQL query to retrieve user data from the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Verify the password using the stored salt
            $hashedPassword = hash('sha256', $password . $row['salt']);

            // Compare the hashed password with the stored hashed password
            if (hash_equals($hashedPassword, $row['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];

                // Redirect to the myaccount.php page
                header("Location: myaccount.php");
                exit();
            } else {
                echo "Incorrect username or password!";
            }
        } else {
            echo "User not found!";
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
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>

</html>
