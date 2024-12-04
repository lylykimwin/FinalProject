<?php
include 'db.php';

// Handle search input
$search = $_GET['search'] ?? '';
$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    WHERE books.title LIKE :search
";
$stmt = $conn->prepare($query);
$stmt->execute([':search' => "%$search%"]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Homepage</title>
</head>
<body>
    <h1>
