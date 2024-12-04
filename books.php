
<?php
include 'db.php'; // Include database connection

// Handle search input
$search = $_GET['search'] ?? '';

// Query to fetch books based on search, including price and quantity
$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year, books.price, books.quantity
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    WHERE books.title LIKE :search OR authors.name LIKE :search OR genres.name LIKE :search
";
$params = [':search' => "%$search%"];

$stmt = $conn->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Lyly's Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="authors.php">Authors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="genres.php">Genres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="books.php">Books</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Bar -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Search the Catalog</h1>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, or genre..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <!-- Books Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Published Year</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['id']) ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['genre']) ?></td>
                            <td><?= htmlspecialchars($book['published_year']) ?></td>
                            <td>$<?= htmlspecialchars(number_format($book['price'], 2)) ?></td>
                            <td><?= htmlspecialchars($book['quantity']) ?></td>
                            <td>
                                <form action="add_to_cart.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

