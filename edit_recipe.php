<?php
include 'db_connect.php';

$message = "";
$recipe = null; // Initialize

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Retrieve recipe data
    $sql = "SELECT * FROM recipes WHERE id = " . $recipe_id;
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $recipe = $result->fetch_assoc();
    } else {
        echo "Recipe not found.";
        exit;
    }
} else {
    echo "Invalid recipe ID.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $ingredients = $_POST["ingredients"];
    $instructions = $_POST["instructions"];
    $recipe_id = $_POST["recipe_id"]; // Hidden field

    // Image handling - similar to add_recipe.php, but more complex
    // (Handle both new image upload AND keeping existing image)
    $image_path = $recipe["image_path"]; // Default: keep existing

    if ($_FILES["image"]["error"] == 0) { // New image uploaded
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_path = $target_file;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Image uploaded successfully
        } else {
            $message = "Error uploading image.";
        }
    }
    $sql = "UPDATE recipes SET
            title = '" . $conn->real_escape_string($title) . "',
            description = '" . $conn->real_escape_string($description) . "',
            ingredients = '" . $conn->real_escape_string($ingredients) . "',
            instructions = '" . $conn->real_escape_string($instructions) . "',
            image_path = '" . $conn->real_escape_string($image_path) . "'
            WHERE id = " . $recipe_id;

    if ($conn->query($sql) === TRUE) {
        $message = "Recipe updated successfully!";
        // Refresh the recipe data after update
        $sql = "SELECT * FROM recipes WHERE id = " . $recipe_id;
        $result = $conn->query($sql);
        $recipe = $result->fetch_assoc(); // Refresh the recipe data
    } else {
        $message = "Error updating recipe: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Edit Recipe</h1>

<?php if (!empty($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<?php if ($recipe): ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe["id"]; ?>">

        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($recipe["title"]); ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50"><?php echo htmlspecialchars($recipe["description"]); ?></textarea><br><br>

        <label for="ingredients">Ingredients:</label><br>
        <textarea id="ingredients" name="ingredients" rows="4" cols="50" required><?php echo htmlspecialchars($recipe["ingredients"]); ?></textarea><br><br>

        <label for="instructions">Instructions:</label><br>
        <textarea id="instructions" name="instructions" rows="8" cols="50" required><?php echo htmlspecialchars($recipe["instructions"]); ?></textarea><br><br>

        <label for="image">Recipe Image:</label><br>
        <?php if (!empty($recipe["image_path"])): ?>
            <img src="<?php echo htmlspecialchars($recipe["image_path"]); ?>" alt="<?php echo htmlspecialchars($recipe["title"]); ?>" width="100"><br>
            <small>Keep blank to use existing image.</small><br>
        <?php endif; ?>
        <input type="file" name="image" id="image"><br><br>

        <input type="submit" value="Update Recipe">
    </form>
<?php endif; ?>

<a href="recipe.php?id=<?php echo $recipe["id"]; ?>">Back to Recipe</a> |
<a href="index.php">Back to Recipes</a>

</body>
</html>

<?php $conn->close(); ?>