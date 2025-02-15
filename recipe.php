<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    if (!is_numeric($recipe_id)) {
        echo "Invalid recipe ID.";
        exit;
    }

    $sql = "SELECT * FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $recipe = $result->fetch_assoc();
        $pageTitle = htmlspecialchars($recipe["title"]) . " - The Wandering Wok"; // Dynamic title
    } else {
        echo "Recipe not found.";
        exit;
    }
    $stmt->close();

} else {
    echo "Invalid recipe ID.";
    exit;
}

$extraStyles = '<style>
        img.recipe-image { max-width: 100%; height: auto; margin-bottom: 1rem; }
        .ingredient-list { list-style-type: disc; padding-left: 20px; }
        .instruction-step { margin-bottom: 1rem; }
          :root {
          --primary-color: hsl(14, 45%, 36%);
          --secondary-color: hsl(332, 51%, 32%);
          --body-color: hsl(30, 54%, 90%);

          --title-color: hsl(24, 5%, 18%);
          --text-color: hsl(30, 10%, 34%);
          --border-color: hsl(30, 18%, 87%);
          --white: hsl(0, 0%, 100%);

          --prepation-bg: hsl(330, 100%, 98%);

          font-size: 16px;
        }

        /* Add custom styles here (if needed) */
        body {
          width: 100%;
          min-height: 100vh;
          background-color: var(--body-color);
        }

        main {
          width: 100%;
          max-width: 750px;
          padding: 2.5rem;
          background-color: var(--white);
          border-radius: 24px;
          margin: 20px auto;
        }

        img {
          max-width: 100%;
          border-radius: 10px;
        }

        h1,
        h2 {
          font-family: \'Young Serif\', serif; /* Use Google Font name */
        }

        h1 {
          margin: 1.5rem 0;
          color: var(--title-color);
          font-size: 2rem;
        }

        h2 {
          color: var(--primary-color);
        }

        p,
        li {
          color: var(--text-color);
          font-family: \'Outfit\', sans-serif; /* Use Google Font name */
        }

        ul {
          margin-left: 0.5rem;
        }

        li {
          margin-left: 1rem;
          padding-left: 1rem;
          line-height: 1.7rem;
          list-style-position: outside;
        }

        li::marker {
          color: var(--primary-color);
          font-weight: 700;
        }

        th {
          font-family: \'Outfit\', sans-serif; /* Use Google Font name */
          font-weight: normal;
        }

        tr {
          padding: 0.8rem 2rem;
          display: flex;
          justify-content: space-between;
        }

        tr:not(:last-child) {
          border-bottom: 1px solid var(--border-color);
        }

        th,
        td {
          flex: 1;
        }

        td {
          color: var(--primary-color);
          font-weight: 700;
        }

        span {
          color: var(--text-color);
          font-weight: 700;
        }

        .preparation-time {
          padding: 1rem;
          margin: 1rem 0;
          background-color: var(--prepation-bg);
          border-radius: 0.5rem;
        }

        .preparation-time > h2 {
          margin-bottom: 0.5rem;
          color: var(--secondary-color);
          font-family: \'Outfit\', sans-serif; /* Use Google Font name */
          font-weight: 700;
          font-size: 1.2rem;
        }

        section:not(:first-child) > h2 {
          margin-bottom: 1rem;
        }

        hr {
          margin: 2rem auto;
          border-bottom: 1px solid var(--border-color);
        }

        table {
          width: 100%;
          border-collapse: collapse;
          margin: 1rem 0;
          text-align: left;
          color: var(--text-color);
          font-family: \'Outfit\', sans-serif; /* Use Google Font name */
        }

        @media (max-width: 768px) {
          body {
            padding: 0;
          }

          main {
            padding: 0;
            border-radius: 0;
          }

          img {
            border-radius: 0;
          }

          section {
            padding: 2rem;
          }

          hr {
            width: calc(100% - 4rem);
          }
        }
    </style>';
include 'header.php';
?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($recipe["title"]); ?></li>
        </ol>
    </nav>

    <main>
        <div>
            <img src="<?php echo htmlspecialchars($recipe["image_path"]); ?>" alt="<?php echo htmlspecialchars($recipe["title"]); ?>">
        </div>
        <section>
            <div>
                <h1><?php echo htmlspecialchars($recipe["title"]); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($recipe["description"])); ?></p>
            </div>
            <div class="preparation-time">
                <h2>Preparation time</h2>
                <ul>
                    <?php if ($recipe["prep_time_total"] !== null): ?>
                        <li><span>Total: </span>Approximately <?php echo htmlspecialchars($recipe["prep_time_total"]); ?> minutes</li>
                    <?php endif; ?>
                    <?php if ($recipe["prep_time_prep"] !== null): ?>
                        <li><span>Preparation: </span><?php echo htmlspecialchars($recipe["prep_time_prep"]); ?> minutes</li>
                    <?php endif; ?>
                    <?php if ($recipe["prep_time_cook"] !== null): ?>
                        <li><span>Cooking: </span><?php echo htmlspecialchars($recipe["prep_time_cook"]); ?> minutes</li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
        <section>
            <h2>Ingredients</h2>
            <ul>
                <?php
                $ingredients = explode("\n", htmlspecialchars($recipe["ingredients"]));
                foreach ($ingredients as $ingredient) {
                    echo "<li>" . trim($ingredient) . "</li>";
                }
                ?>
            </ul>
        </section>

        <hr>

        <section>
            <h2>Instructions</h2>
            <ol>
                <?php
                $instructions = explode("\n", htmlspecialchars($recipe["instructions"]));
                $stepNumber = 1;
                foreach ($instructions as $instruction) {
                    echo "<li><span>Step " . $stepNumber . ": </span>" . trim($instruction) . "</li>";
                    $stepNumber++;
                }
                ?>
            </ol>
        </section>

        <hr>

        <section>
            <h2>Nutrition</h2>
            <p>The table below shows nutritional values per serving without the additional fillings.</p>
            <table>
                <tr>
                    <th>Calories</th>
                    <td><?php echo ($recipe["calories"] !== null) ? htmlspecialchars($recipe["calories"]) . 'kcal' : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Carbs</th>
                    <td><?php echo ($recipe["carbs"] !== null) ? htmlspecialchars($recipe["carbs"]) . 'g' : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Protein</th>
                    <td><?php echo ($recipe["protein"] !== null) ? htmlspecialchars($recipe["protein"]) . 'g' : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Fat</th>
                    <td><?php echo ($recipe["fat"] !== null) ? htmlspecialchars($recipe["fat"]) . 'g' : 'N/A'; ?></td>
                </tr>
            </table>
        </section>
    </main>
     <?php if (isset($_SESSION["user_id"])): ?>
        <a href="edit_recipe.php?id=<?php echo $recipe["id"]; ?>" class="btn btn-primary">Edit Recipe</a>
        <a href="delete_recipe.php?id=<?php echo $recipe["id"]; ?>" class="btn btn-danger">Delete Recipe</a>
     <?php endif; ?>
    <a href="index.php" class="btn btn-secondary">Back to Recipes</a>

<?php include 'footer.php'; ?>