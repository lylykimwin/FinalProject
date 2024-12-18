<?php
include 'db.php'; // Include database connection

// Fetch all authors
$query = "SELECT * FROM authors";
$stmt = $conn->prepare($query);
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="home.php">Lyly's Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="authors.php">Authors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="genres.php">Genres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="books.php">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="visuals.php">Visuals</a> <!-- New Visuals Tab -->
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Authors List -->
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Biography</th>
            <th>Actions</th> <!-- New Column -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($authors as $author): ?>
            <tr>
                <td><?= htmlspecialchars($author['id']) ?></td>
                <td><?= htmlspecialchars($author['name']) ?></td>
                <td><?= htmlspecialchars($author['biography']) ?></td>
                <td>
                    <!-- Edit and Delete Links -->
                    <a href="edit_author.php?id=<?= $author['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_author.php?id=<?= $author['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this author?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
