<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch data for the bar chart
$query = "
    SELECT 
        genres.name AS genre_name, 
        COUNT(books.id) AS book_count
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY genres.name;
";
$stmt = $conn->prepare($query);
$stmt->execute();
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$genres = array_column($chartData, 'genre_name');
$bookCounts = array_column($chartData, 'book_count');

// Fetch data for the doughnut chart (book availability)
$queryAvailability = "
    SELECT 
        SUM(CASE WHEN book_stock.stock > 0 THEN 1 ELSE 0 END) AS available,
        SUM(CASE WHEN book_stock.stock = 0 THEN 1 ELSE 0 END) AS out_of_stock
    FROM books
    JOIN book_stock ON books.id = book_stock.book_id;
";
$stmtAvailability = $conn->prepare($queryAvailability);
$stmtAvailability->execute();
$availability = $stmtAvailability->fetch(PDO::FETCH_ASSOC);

// Fetch data for the bubble chart (pricing and popularity)
// Mocking popularity data for demonstration
$queryPopularity = "
    SELECT 
        books.title AS book_title, 
        book_stock.price AS price, 
        FLOOR(RAND() * 100) AS popularity
    FROM books
    JOIN book_stock ON books.id = book_stock.book_id
    ORDER BY RAND()
    LIMIT 10;
";
$stmtPopularity = $conn->prepare($queryPopularity);
$stmtPopularity->execute();
$popularityData = $stmtPopularity->fetchAll(PDO::FETCH_ASSOC);

$titles = array_column($popularityData, 'book_title');
$prices = array_column($popularityData, 'price');
$popularities = array_column($popularityData, 'popularity');

// Fetch total number of books
$queryBooks = "SELECT COUNT(*) AS total_books FROM books";
$stmtBooks = $conn->prepare($queryBooks);
$stmtBooks->execute();
$totalBooks = $stmtBooks->fetch(PDO::FETCH_ASSOC)['total_books'];

// Fetch total number of genres
$queryGenres = "SELECT COUNT(*) AS total_genres FROM genres";
$stmtGenres = $conn->prepare($queryGenres);
$stmtGenres->execute();
$totalGenres = $stmtGenres->fetch(PDO::FETCH_ASSOC)['total_genres'];

// Fetch most popular genre (genre with the most books)
$queryPopularGenre = "
    SELECT genres.name AS genre_name, COUNT(books.id) AS book_count
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY book_count DESC
    LIMIT 1;
";
$stmtPopularGenre = $conn->prepare($queryPopularGenre);
$stmtPopularGenre->execute();
$popularGenre = $stmtPopularGenre->fetch(PDO::FETCH_ASSOC);

// Fetch most prolific author (author with the most books)
$queryProlificAuthor = "
    SELECT 
        authors.name AS author_name, 
        COUNT(books.id) AS book_count
    FROM authors
    LEFT JOIN books ON authors.id = books.author_id
    GROUP BY authors.id, authors.name
    ORDER BY book_count DESC
    LIMIT 1;
";
$stmtProlificAuthor = $conn->prepare($queryProlificAuthor);
$stmtProlificAuthor->execute();
$prolificAuthor = $stmtProlificAuthor->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visuals - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Metrics Overview Section -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Library Overview</h1>
        <div class="row">
            <!-- Total Books Card -->
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Books</h5>
                        <p class="display-4"><?= htmlspecialchars($totalBooks) ?></p>
                    </div>
                </div>
            </div>
            <!-- Total Genres Card -->
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Genres</h5>
                        <p class="display-4"><?= htmlspecialchars($totalGenres) ?></p>
                    </div>
                </div>
            </div>
            <!-- Most Popular Genre Card -->
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Most Popular Genre</h5>
                        <p class="display-6"><?= htmlspecialchars($popularGenre['genre_name']) ?></p>
                        <p><?= htmlspecialchars($popularGenre['book_count']) ?> Books</p>
                    </div>
                </div>
            </div>
            <!-- Most Prolific Author Card -->
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-body">
                        <h5 class="card-title">Most Prolific Author</h5>
                        <p class="display-6"><?= htmlspecialchars($prolificAuthor['author_name']) ?></p>
                        <p><?= htmlspecialchars($prolificAuthor['book_count']) ?> Books</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visualizations -->
    <div class="container mt-4">
        <!-- Bar Chart: Books Per Genre -->
        <h2 class="text-center mb-4">Books Per Genre</h2>
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <canvas id="genreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart: Book Availability -->
        <h2 class="text-center mb-4">Book Availability</h2>
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <canvas id="availabilityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bubble Chart: Pricing vs Popularity -->
        <h2 class="text-center mb-4">Pricing vs. Popularity</h2>
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <canvas id="popularityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Bar Chart: Books Per Genre
        const genreCtx = document.getElementById('genreChart').getContext('2d');
        new Chart(genreCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($genres) ?>,
                datasets: [{
                    label: 'Number of Books',
                    data: <?= json_encode($bookCounts) ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Books' } },
                    x: { title: { display: true, text: 'Genres' } }
                }
            }
        });

        // Doughnut Chart: Book Availability
        const availabilityCtx = document.getElementById('availabilityChart').getContext('2d');
        new Chart(availabilityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Out of Stock'],
                datasets: [{
                    data: [<?= $availability['available'] ?>, <?= $availability['out_of_stock'] ?>],
                    backgroundColor: ['#4CAF50', '#FF6347'],
                }]
            }
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
