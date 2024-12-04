<?php
include 'db.php'; // Include database connection

// Handle search and filter inputs
$search = $_GET['search'] ?? '';
$author = $_GET['author'] ?? '';
$genre = $_GET['genre'] ?? '';

// Build the query dynamically based on filters
$query = "
    SELECT books.id, books.title, authors.name AS author, genres.name AS genre, books.published_year
    FROM books
    JOIN authors ON books.author_id = authors.id
    JOIN genres ON books.genre_id = genres.id
    WHERE books.title LIKE :search
";

$params = [':search' => "%$search%"];

// Add filters for author and genre if provided
if (!empty($author)) {
    $query .= " AND books.author_id = :author";
    $params[':author'] = $author;
}

if (!empty($genre)) {
    $query .= " AND books.genre_id = :genre";
    $params[':genre'] = $genre;
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch authors and genres for the dropdown menus
$authors = $conn->query("SELECT id, name FROM authors")->fetchAll(PDO::FETCH_ASSOC);
$genres = $conn->query("SELECT id, name FROM genres")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Homepage</title>
</head>
<body>
    <h1>Library</h1>

    <!-- Search and Filter Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">

        <!-- Author Dropdown -->
        <select name="author">
            <option value="">Select Author</option>
            <?php foreach ($authors as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id'] == $author ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Genre Dropdown -->
        <select name="genre">
            <option value="">Select Genre</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $g['id'] == $genre ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filter</button>
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
</html>
