<?php
include 'db.php'; // Include database connection

// Handle search input
$search = $_GET['search'] ?? ''; // Check for search query from the GET parameter
$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    WHERE books.title LIKE :search
";
$stmt = $conn->prepare($query);
$stmt->execute([':search' => "%$search%"]); // Bind search term to the query
$books = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch filtered results
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Homepage</title>
</head>
<body>
    <h1>Library</h1>

    <!-- Search Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Books Table -->
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
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['genre']) ?></td>
                        <td><?= $book['published_year'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $book['id'] ?>">Edit</a> |
                            <a href="delete.php?id=<?= $book['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No results found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</ht
