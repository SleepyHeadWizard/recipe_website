<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    $sql = "DELETE FROM recipes WHERE id = " . $recipe_id;

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?message=Recipe deleted successfully!"); // Redirect with message
        exit;
    } else {
        echo "Error deleting recipe: " . $conn->error;
    }
} else {
    echo "Invalid recipe ID.";
}
$conn->close();
?>