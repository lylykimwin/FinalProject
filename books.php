<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session to show notifications
include 'db.php'; // Include database connection

// Handle search input and genre filter
$search = $_GET['search'] ?? '';
$genreId = $_GET['genre'] ?? null;

// Validate genre ID (ensure it's numeric)
if ($genreId && !is_numeric($genreId)) {
    die("Invalid genre ID.");
}

// Base query to fetch books
$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year, book_stock.price, book_stock.stock AS quantity
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    JOIN book_stock ON books.id = book_stock.book_id
    WHERE (books.title LIKE :search OR authors.name LIKE :search OR genres.name LIKE :search)
";

// Parameters for the query
$params = [':search' => "%$search%"];

// Add genre filter if genre ID is provided
if ($genreId) {
    $query .= " AND genres.id = :genreId";
    $params[':genreId'] = $genreId;
}

// Execute the query and fetch books
try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching books: " . $e->getMessage());
}
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
    <!-- Notifications -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">
            <?= $genreId ? "Books in " . htmlspecialchars($books[0]['genre'] ?? 'Selected Genre') : "Search the Catalog" ?>
        </h1>

        <!-- Search Bar -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, or genre..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <!-- Books Table -->
        <?php if (!empty($books)): ?>
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
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['id']) ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['genre']) ?></td>
                            <td><?= htmlspecialchars($book['published_year']) ?></td>
                            <td>$<?= number_format($book['price'], 2) ?></td>
                            <td><?= htmlspecialchars($book['quantity']) ?></td>
                            <td>
                                <form action="add_to_cart.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <?= $genreId ? "No books found for this genre." : "No books found. Try a different search." ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
