<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';
$extraScripts = '<script>
    function calculateTotal() {
        var prepTime = document.getElementById("prep_time_prep").value;
        var cookTime = document.getElementById("prep_time_cook").value;

        //Check if the values are valid numbers, if not set to 0
        prepTime = isNaN(parseInt(prepTime)) ? 0 : parseInt(prepTime);
        cookTime = isNaN(parseInt(cookTime)) ? 0 : parseInt(cookTime);

        var totalTime = prepTime + cookTime;

        document.getElementById("prep_time_total").value = totalTime;
    }
</script>';
$message = "";
$recipe = null;

// --- Fetch Existing Recipe Data ---
if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    if (!is_numeric($recipe_id)) {
        echo "Invalid recipe ID (not numeric).";
        exit;
    }

    $sql = "SELECT * FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed (SELECT): " . $conn->error);
    }
    $stmt->bind_param("i", $recipe_id);
    if ($stmt->execute() === false) {
        die("Execute failed (SELECT): " . $stmt->error);
    }
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $recipe = $result->fetch_assoc();
        $pageTitle = "Edit Recipe: " . htmlspecialchars($recipe["title"]) . " - The Wandering Wok"; // Dynamic title
    } else {
        echo "Recipe not found.";
        exit;
    }
    $stmt->close();
} else {
    echo "Invalid recipe ID (not set).";
    exit;
}

// --- Handle Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data, INCLUDING the recipe_id from the HIDDEN field
    $title = $_POST["title"];
    $description = $_POST["description"];
    $ingredients = $_POST["ingredients"];
    $instructions = $_POST["instructions"];
    $prep_time_total = $_POST["prep_time_total"];
    $prep_time_prep = $_POST["prep_time_prep"];
    $prep_time_cook = $_POST["prep_time_cook"];
    $recipe_id = $_POST["recipe_id"]; // Get the ID from the hidden field

    // Validate that recipe_id is still numeric (extra safety)
    if (!is_numeric($recipe_id)) {
        echo "Invalid recipe ID (not numeric) on POST.";
        exit;
    }

    // Image handling
    $image_path = $recipe["image_path"]; // Default: keep existing
    if ($_FILES["image"]["error"] == 0) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_path = $target_file;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $message = "Error uploading image.";
        }
    }

    // --- Update Database with Prepared Statement ---
    $sql = "UPDATE recipes SET
                title = ?, description = ?, ingredients = ?, instructions = ?,
                image_path = ?, prep_time_total = ?, prep_time_prep = ?,
                prep_time_cook = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) { die("Prepare failed (UPDATE): " . $conn->error); }

    // Bind parameters.  Note the "i" at the end for the recipe_id
    $stmt->bind_param("sssssiiii", $title, $description, $ingredients, $instructions,
                     $image_path, $prep_time_total, $prep_time_prep, $prep_time_cook, $recipe_id);

    if ($stmt->execute() === false) { die("Execute failed (UPDATE): " . $stmt->error); }

    if ($stmt->execute()) {
        $message = "Recipe updated successfully!";
        // Refresh the recipe data after update (from DB)
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $stmt_select = $conn->prepare($sql);
        if ($stmt_select === false) { die("Prepare failed (SELECT after UPDATE): " . $conn->error);}
        $stmt_select->bind_param("i", $recipe_id);
        if ($stmt_select->execute() === false) { die("Execute failed (SELECT after UPDATE): " . $stmt_select->error); }
        $result = $stmt_select->get_result();
        $recipe = $result->fetch_assoc(); // Refresh
        $stmt_select->close();
    } else {
        $message = "Error updating recipe: " . $stmt->error;
    }
     $stmt->close();
}

include 'header.php';
?>

    <h1>Edit Recipe</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($recipe): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <!-- Hidden Input Field for Recipe ID -->
            <input type="hidden" name="recipe_id" value="<?php echo htmlspecialchars($recipe["id"]); ?>">

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($recipe["title"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($recipe["description"]); ?></textarea>
            </div>
            <div class="form-group">
                <label for="ingredients">Ingredients:</label>
                <textarea class="form-control" id="ingredients" name="ingredients" rows="4" required><?php echo htmlspecialchars($recipe["ingredients"]); ?></textarea>
            </div>
            <div class="form-group">
                <label for="instructions">Instructions:</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="8" required><?php echo htmlspecialchars($recipe["instructions"]); ?></textarea>
            </div>
            <div class="form-group">
                <label for="prep_time_prep">Preparation Time (minutes):</label>
                <input type="number" class="form-control" id="prep_time_prep" name="prep_time_prep" value="<?php echo htmlspecialchars($recipe["prep_time_prep"] ?? ''); ?>" oninput="calculateTotal()">
            </div>
            <div class="form-group">
                <label for="prep_time_cook">Cooking Time (minutes):</label>
                <input type="number" class="form-control" id="prep_time_cook" name="prep_time_cook" value="<?php echo htmlspecialchars($recipe["prep_time_cook"] ?? ''); ?>" oninput="calculateTotal()">
            </div>
            <div class="form-group">
                <label for="prep_time_total">Total Prep Time (minutes):</label>
                <input type="number" class="form-control" id="prep_time_total" name="prep_time_total" value="<?php echo htmlspecialchars($recipe["prep_time_total"] ?? ''); ?>" readonly>
                <small class="form-text text-muted">This is automatically calculated.</small>
            </div>
           <div class="form-group">
            <label for="image">Recipe Image:</label><br>
            <?php if (!empty($recipe["image_path"])): ?>
                <img src="<?php echo htmlspecialchars($recipe["image_path"]); ?>" alt="<?php echo htmlspecialchars($recipe["title"]); ?>" width="100"><br>
                <small>Keep blank to use existing image.</small><br>
            <?php endif; ?>
            <input type="file" class="form-control-file" name="image" id="image"><br><br>
           </div>
            <button type="submit" class="btn btn-primary">Update Recipe</button>
        </form>
    <?php endif; ?>

    <a href="recipe.php?id=<?php echo $recipe["id"]; ?>" class="btn btn-secondary mt-3">Back to Recipe</a>
    <a href="index.php" class="btn btn-secondary mt-3">Back to Recipes</a>

<?php
include 'footer.php';
$conn->close();
?>