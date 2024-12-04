<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
    <!-- Optionally include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Library</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-center">Library Management System</h1>

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

        <!-- Books Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
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
                                <a href="edit.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?= $book['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No results found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
