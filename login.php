<?php
session_start(); // Start the session

include 'db_connect.php';

$pageTitle = "Login - The Wandering Wok";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // --- Input Validation (Basic) ---
    if (empty($username) || empty($password)) {
        $message = "Both username and password are required.";
    } else {
        // --- Retrieve User from Database ---
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
		if ($stmt === false) {
			die("Prepare failed: " . $conn->error);
		}
        $stmt->bind_param("s", $username);
		if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // --- Verify Password ---
            if (password_verify($password, $user["password"])) {
                // Password is correct, start session and redirect
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                header("Location: index.php"); // Redirect to the homepage
                exit;
            } else {
                $message = "Invalid username or password.";
            }
        } else {
            $message = "Invalid username or password.";
        }
        $stmt->close();
    }
}

include 'header.php'; // Include header AFTER session_start()
?>

    <h1>Login</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
     <p>Don't have an account? <a href="register.php">Register here</a>.</p>

<?php
include 'footer.php';
$conn->close();
?>