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

$authors = $conn->query("SELECT id, name FROM authors")->fetchAll(PDO::FETCH_ASSOC);
$genres = $conn->query("SELECT id, name FROM genres")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Library Management</h1>

        <!-- Search and Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="author" class="form-select">
                    <option value="">Select Author</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $author ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="genre" class="form-select">
                    <option value="">Select Genre</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= $g['id'] == $genre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <!-- Dropdown for Each Book -->
        <div class="accordion" id="booksAccordion">
            <?php foreach ($books as $index => $book): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $index ?>">
                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                            <?= htmlspecialchars($book['title']) ?> (ID: <?= $book['id'] ?>)
                        </button>
                    </h2>
                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#booksAccordion">
                        <div class="accordion-body">
                            <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                            <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                            <p><strong>Published Year:</strong> <?= $book['published_year'] ?></p>
                            <div>
                                <a href="edit.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?= $book['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message -->
        <?php if (count($books) === 0): ?>
            <p class="text-center mt-4">No results found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
