<?php
session_start(); // Start the session, even on the registration page

include 'db_connect.php';

$pageTitle = "Register - The Wandering Wok";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];
    $email = $_POST["email"];

    // --- Input Validation ---
    if (empty($username) || empty($password) || empty($password_confirm) || empty($email)) {
        $message = "All fields are required.";
    } elseif ($password != $password_confirm) {
        $message = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // --- Check if username/email already exists ---
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die("Prepare failed: " . $conn->error);
		}
        $stmt->bind_param("ss", $username, $email);
		if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or email already exists.";
        } else {
            // --- Hash the password ---
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // --- Insert user into database ---
            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
			if ($stmt === false) {
				die("Prepare failed: " . $conn->error);
			}
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                // Registration successful.  Redirect to login page.
                header("Location: login.php");
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

include 'header.php'; // Include header AFTER session_start()
?>

    <h1>Register</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password:</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>  <!-- Corrected: Closing form tag was missing -->
    <p>Already have an account? <a href="login.php">Login here</a>.</p> <!-- Corrected: Moved outside the form -->

<?php include 'footer.php';
$conn->close();?>