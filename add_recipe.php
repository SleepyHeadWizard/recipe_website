<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

$pageTitle = "Add Recipe - The Wandering Wok";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $title = $_POST["title"];
    $description = $_POST["description"];
    $ingredients = $_POST["ingredients"];
    $instructions = $_POST["instructions"];
    $prep_time_total = $_POST["prep_time_total"];
    $prep_time_prep = $_POST["prep_time_prep"];
    $prep_time_cook = $_POST["prep_time_cook"];

    // Image Upload Handling
    $image_path = "";
    if ($_FILES["image"]["error"] == 0) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_path = $target_file;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $message = "Error uploading image.";
        }
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO recipes (title, description, ingredients, instructions, image_path, prep_time_total, prep_time_prep, prep_time_cook)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssiii", $title, $description, $ingredients, $instructions, $image_path,
                        $prep_time_total, $prep_time_prep, $prep_time_cook);

    if ($stmt->execute()) {
        $message = "Recipe added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

include 'header.php';
?>

    <h1>Add a New Recipe</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="ingredients">Ingredients:</label>
            <textarea class="form-control" id="ingredients" name="ingredients" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="instructions">Instructions:</label>
            <textarea class="form-control" id="instructions" name="instructions" rows="8" required></textarea>
        </div>
        <div class="form-group">
            <label for="prep_time_prep">Preparation Time (minutes):</label>
            <input type="number" class="form-control" id="prep_time_prep" name="prep_time_prep" oninput="calculateTotal()">
        </div>
        <div class="form-group">
            <label for="prep_time_cook">Cooking Time (minutes):</label>
            <input type="number" class="form-control" id="prep_time_cook" name="prep_time_cook" oninput="calculateTotal()">
        </div>
        <div class="form-group">
            <label for="prep_time_total">Total Prep Time (minutes):</label>
            <input type="number" class="form-control" id="prep_time_total" name="prep_time_total" readonly>
            <small class="form-text text-muted">This is automatically calculated.</small>
        </div>
        <div class="form-group">
            <label for="image">Recipe Image:</label>
            <input type="file" class="form-control-file" name="image" id="image">
        </div>
        <button type="submit" class="btn btn-primary">Add Recipe</button>
        <a href="index.php" class="btn btn-secondary">Back to Recipes</a>
    </form>

    <?php $extraScripts = '<script>
        function calculateTotal() {
            var prepTime = document.getElementById("prep_time_prep").value;
            var cookTime = document.getElementById("prep_time_cook").value;
            prepTime = isNaN(parseInt(prepTime)) ? 0 : parseInt(prepTime);
            cookTime = isNaN(parseInt(cookTime)) ? 0 : parseInt(cookTime);
            var totalTime = prepTime + cookTime;
            document.getElementById("prep_time_total").value = totalTime;
        }
    </script>';
    ?>

<?php
include 'footer.php';
$conn->close();
?>