<?php
include 'db.php'; // Include database connection

// Fetch all genres
$query = "SELECT * FROM genres";
$stmt = $conn->prepare($query);
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genres - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
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
                        <a class="nav-link active" href="genres.php">Genres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Books</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Genres List -->
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th> <!-- New Column -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($genres as $genre): ?>
            <tr>
                <td><?= htmlspecialchars($genre['id']) ?></td>
                <td><?= htmlspecialchars($genre['name']) ?></td>
                <td>
                    <!-- Edit and Delete Links -->
                    <a href="edit_genre.php?id=<?= $genre['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_genre.php?id=<?= $genre['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this genre?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
