<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Handle search input and genre filter
$search = $_GET['search'] ?? '';
$genreId = $_GET['genre'] ?? null;

$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year, book_stock.price, book_stock.stock AS quantity
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    JOIN book_stock ON books.id = book_stock.book_id
    WHERE (books.title LIKE :search OR authors.name LIKE :search OR genres.name LIKE :search)
";

$params = [':search' => "%$search%"];

if ($genreId) {
    $query .= " AND genres.id = :genreId";
    $params[':genreId'] = $genreId;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
