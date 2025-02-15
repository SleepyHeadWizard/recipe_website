<?php
session_start();
include 'db_connect.php';

$pageTitle = "Recipe List - The Wandering Wok";

$sql = "SELECT * FROM recipes ORDER BY date_added DESC";
$result = $conn->query($sql);

$extraStyles = '<style>
    .card-img-top { width: 100%; height: 200px; object-fit: cover; object-position: center; }
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    .card-footer { padding: 1rem; background-color: transparent; border-top: none; }
    .card-title { font-size: 1.25rem; margin-bottom: 0.75rem; }
    .card-text { margin-bottom: 10px; }
</style>';

include 'header.php';
?>

    <h1 class="my-4">Welcome to The Wandering Wok</h1>

    <?php if (isset($_SESSION["user_id"])): ?>
    <a href="add_recipe.php" class="btn btn-primary mb-3">Add a New Recipe</a>
    <?php endif; ?>

    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card h-100'>";
                echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
                echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>";
                echo "</div>";
                echo "<div class='card-footer'>";
                echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
                if (isset($_SESSION["user_id"])):
                    echo "<a href='edit_recipe.php?id=" . $row["id"] . "' class='btn btn-info ml-2'>Edit</a>";
                endif;
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<div class='col-12'><p>No recipes found yet.</p></div>";
        }
        ?>
    </div>

<?php
include 'footer.php';
$conn->close();
?>