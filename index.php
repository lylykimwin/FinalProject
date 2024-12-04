<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .welcome-section {
            margin-top: 40px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .welcome-section h2 {
            color: #007bff;
        }

        .welcome-section p {
            color: #555;
        }

        .btn-explore {
            margin-top: 20px;
        }
    </style>
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="authors.php">Authors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="genres.php">Genres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php">Books</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="welcome-section text-center">
            <h2>Welcome to Lyly's Library</h2>
            <p class="lead">
                Discover a collection of books across various genres and authors. Lyly's Library is your one-stop destination for exploring the world of literature. Whether you're looking for timeless classics, contemporary novels, or insightful non-fiction, we have something for everyone.
            </p>
            <a href="books.php" class="btn btn-primary btn-lg btn-explore">Explore Books</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
