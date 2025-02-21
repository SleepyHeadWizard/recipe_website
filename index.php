<?php
session_start();
include 'db_connect.php';

$pageTitle = "Home - The Wandering Wok";

// Fetch *all* recipes (for now, we'll show a few)
$sql = "SELECT * FROM recipes ORDER BY date_added DESC";
$result = $conn->query($sql);

// Fetch *featured* recipes (Example:  Last 3 added)
$sql_featured = "SELECT * FROM recipes ORDER BY date_added DESC LIMIT 3";
$result_featured = $conn->query($sql_featured);


$extraStyles = '<style>
    .card-img-top { width: 100%; height: 200px; object-fit: cover; object-position: center; }
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    .card-footer { padding: 1rem; background-color: transparent; border-top: none; }
    .card-title { font-size: 1.25rem; margin-bottom: 0.75rem; }
    .card-text { margin-bottom: 10px; }
    .jumbotron { background-color: #f8f9fa; padding: 2rem 1rem; } /* Light gray background */
    .featured-section { background-color: #e9ecef; padding: 2rem 0; } /* Slightly darker gray */
</style>';

include 'header.php';
?>

<?php if (isset($_SESSION["user_id"])): ?>
    <!-- Logged-in User View -->
    <div class="jumbotron">
        <h1 class="display-4">Welcome back, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <p class="lead">Ready to discover some delicious new recipes, or add your own culinary creations?</p>
        <a href="add_recipe.php" class="btn btn-primary btn-lg">Add a New Recipe</a>
    </div>

    <div class="featured-section">
        <h2>Featured Recipes</h2>
          <div class="row">
            <?php
            if ($result_featured->num_rows > 0) {
                while ($row = $result_featured->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card h-100'>";
                    echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
                    echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
                    echo "<a href='edit_recipe.php?id=" . $row["id"] . "' class='btn btn-info ml-2'>Edit</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No featured recipes yet.</p>";
            }
            ?>
        </div>
    </div>

    <h2>All Recipes</h2>
    <div class="row">
       <?php
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4 mb-4'>";
              echo "<div class='card h-100'>";
              echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
              echo "<div class='card-body'>";
              echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
              echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>";
              echo "</div>";
              echo "<div class='card-footer'>";
              echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
              echo "<a href='edit_recipe.php?id=" . $row["id"] . "' class='btn btn-info ml-2'>Edit</a>";
              echo "</div>";
              echo "</div>";
             echo "</div>";
          }
        } else {
            echo "<div class='col-12'><p>No recipes found yet.</p></div>";
        }
        ?>
    </div>

<?php else: ?>
    <!-- Guest (Pre-Login) View -->
    <div class="jumbotron">
        <h1 class="display-4">Welcome to The Wandering Wok!</h1>
        <p class="lead">Explore a world of delicious recipes. Sign up or log in to share your own culinary adventures.</p>
        <a href="register.php" class="btn btn-primary btn-lg">Register</a>
        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
    </div>

    <div class="featured-section">
        <h2>Featured Recipes</h2>
          <div class="row">
            <?php
            if ($result_featured->num_rows > 0) {
                while ($row = $result_featured->fetch_assoc()) {
                     echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card h-100'>";
                    echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
                    echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No featured recipes yet.</p>";
            }
            ?>
        </div>
    </div>

     <h2>All Recipes</h2>
    <div class="row">
       <?php
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4 mb-4'>";
              echo "<div class='card h-100'>";
              echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
              echo "<div class='card-body'>";
              echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
              echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>";
              echo "</div>";
              echo "<div class='card-footer'>";
              echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
              echo "</div>";
              echo "</div>";
             echo "</div>";
          }
        } else {
            echo "<div class='col-12'><p>No recipes found yet.</p></div>";
        }

        $conn->close();
        ?>
    </div>

<?php endif; ?>

<?php include 'footer.php'; ?>