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
        GROUP_CONCAT(books.title ORDER BY books.title SEPARATOR ', ') AS book_titles
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY genres.name;
";
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
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

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
                                        <i class="fas fa-book"></i> <!-- Replace with your desired icons -->
                                    </div>
                                    <h5 class="card-title mt-3"><?= htmlspecialchars($genre['genre_name']) ?></h5>
                                </div>
                                <p class="card-text">Discover books in <?= htmlspecialchars($genre['genre_name']) ?>.</p>
                                <div class="book-list">
                                    <?php if (!empty($genre['book_titles'])): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach (explode(', ', $genre['book_titles']) as $bookTitle): ?>
                                                <li class="list-group-item"><?= htmlspecialchars($bookTitle) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">No books available in this genre.</p>
                                    <?php endif; ?>
                                </div>
                                <a href="books.php?genre=<?= $genre['genre_id'] ?>" class="btn btn-outline-primary btn-sm mt-3">View All Books</a>
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
