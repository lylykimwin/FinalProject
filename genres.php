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
    <style>
        /* Genre Cards */
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

        .search-bar {
            margin-bottom: 20px;
        }
    </style>
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
                        <a class="nav-link" href="books.php">Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Explore Genres</h1>

        <!-- Search Bar -->
        <form method="GET" class="search-bar">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search genres..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- Genre Cards -->
        <div class="row">
            <?php foreach ($genres as $genre): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card genre-card text-center">
                        <div class="card-body">
                            <div class="genre-icon">
                                <i class="fas fa-book"></i> <!-- Replace with icons or images -->
                            </div>
                            <h5 class="card-title mt-3"><?= htmlspecialchars($genre['name']) ?></h5>
                            <p class="card-text">Explore books under the <?= htmlspecialchars($genre['name']) ?> genre.</p>
                            <a href="books.php?genre=<?= $genre['id'] ?>" class="btn btn-outline-primary btn-sm">View Books</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
