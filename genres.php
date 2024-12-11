<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch genres and their associated books
$query = "
    SELECT 
        genres.id AS genre_id, 
        genres.name AS genre_name, 
        books.title AS book_title
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    ORDER BY genres.name, books.title
";
$stmt = $conn->prepare($query);
$stmt->execute();
$genreData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group books by genre
$genres = [];
foreach ($genreData as $row) {
    $genreId = $row['genre_id'];
    $genreName = $row['genre_name'];
    $bookTitle = $row['book_title'];

    if (!isset($genres[$genreId])) {
        $genres[$genreId] = [
            'name' => $genreName,
            'books' => []
        ];
    }

    if ($bookTitle) {
        $genres[$genreId]['books'][] = $bookTitle;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genres - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .genre-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .genre-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .genre-icon {
            font-size: 50px;
            color: #007bff;
        }

        .genre-card:hover .genre-icon {
            color: #0056b3;
        }

        .book-list {
            max-height: 150px;
            overflow-y: auto;
            margin-top: 10px;
        }
    </style>
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


    <!-- Genre Cards -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Explore Genres</h1>
        <?php if (!empty($genres)): ?>
            <div class="row">
                <?php foreach ($genres as $genre): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card genre-card">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="genre-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <h5 class="card-title mt-3"><?= htmlspecialchars($genre['name']) ?></h5>
                                </div>
                                <p class="card-text">Discover books in <?= htmlspecialchars($genre['name']) ?>.</p>
                                <div class="book-list">
                                    <?php if (!empty($genre['books'])): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($genre['books'] as $bookTitle): ?>
                                                <li class="list-group-item"><?= htmlspecialchars($bookTitle) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">No books available in this genre.</p>
                                    <?php endif; ?>
                                </div>
                                <a href="books.php?genre=<?= $genreId ?>" class="btn btn-outline-primary btn-sm mt-3">View All Books</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No genres available at the moment. Please check back later.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
