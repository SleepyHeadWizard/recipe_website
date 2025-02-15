<?php
include 'db_connect.php';

// Fetch recipes from the database
$sql = "SELECT * FROM recipes ORDER BY date_added DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Recipe Website</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Your custom styles (override Bootstrap if needed) -->
    <style>
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-footer {
            padding: 1rem;
            background-color: transparent;
            border-top: none;
        }

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

       .card-text {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">  <!-- Container for the navbar -->
        <a class="navbar-brand" href="index.php">Recipe Website</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_recipe.php">Add Recipe</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="my-4">Welcome to Our Recipe Collection</h1>  <!-- Added margin top/bottom -->

    <a href="add_recipe.php" class="btn btn-primary mb-3">Add a New Recipe</a> <!-- Added margin bottom -->

    <div class="row">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<div class='col-md-4 mb-4'>"; // mb-4 for more vertical spacing, col-md-4 for 3 cards per row
            echo "<div class='card h-100'>"; // h-100 makes cards same height
            echo "<img src='" . htmlspecialchars($row["image_path"]) . "' class='card-img-top img-fluid' alt='" . htmlspecialchars($row["title"]) . "'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($row["title"]) . "</h5>";
            echo "<p class='card-text'>" . htmlspecialchars(substr($row["description"], 0, 50)) . "...</p>"; // Limited description
            echo "</div>";
            echo "<div class='card-footer'>";
            echo "<a href='recipe.php?id=" . $row["id"] . "' class='btn btn-primary'>View Recipe</a>";
            echo "</div>";
            echo "</div>";
           echo "</div>";
        }
    } else {
        echo "<div class='col-12'><p>No recipes found yet.</p></div>"; // Take full width if no recipes
    }

    $conn->close();
    ?>
    </div> <!-- .row -->
</div> <!-- .container -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>