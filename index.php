<?php
include 'db.php';

// Fetch books from the database
$stmt = $conn->prepare("
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Homepage</title>
</head>
<body>
    <h1>Library</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Published Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td><?= $book['title'] ?></td>
                    <td><?= $book['author'] ?></td>
                    <td><?= $book['genre'] ?></td>
                    <td><?= $book['published_year'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $book['id'] ?>">Edit</a> |
                        <a href="delete.php?id=<?= $book['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
