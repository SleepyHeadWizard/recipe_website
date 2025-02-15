<?php
include 'db_connect.php';

if (isset($_GET['q'])) {
    $search_term = $_GET['q'];

    // Prepared statement to prevent SQL injection
    $sql = "SELECT * FROM recipes WHERE title LIKE ? OR ingredients LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $search_term_like = "%" . $search_term . "%"; // Add wildcards for LIKE

    $stmt->bind_param("sss", $search_term_like, $search_term_like, $search_term_like);

    $stmt->execute();
    $result = $stmt->get_result();  // Use get_result() to get the result set

    if ($result->num_rows > 0) {
        echo "<h2>Search Results for: " . htmlspecialchars($search_term) . "</h2>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='recipe.php?id=" . $row["id"] . "'>" . htmlspecialchars($row["title"]) . "</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No recipes found matching your search term.</p>";
    }
    $stmt->close();  // Close the statement
} else {
    echo "<p>Please enter a search term.</p>";
}

$conn->close();
?>
<a href="index.php">Back to Recipes</a>