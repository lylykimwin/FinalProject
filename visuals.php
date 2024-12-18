<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch data for the bar chart (Books per Genre)
$queryBarChart = "
    SELECT 
        genres.name AS genre_name, 
        COUNT(books.id) AS book_count
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY genres.name;
";
$stmtBarChart = $conn->prepare($queryBarChart);
$stmtBarChart->execute();
$barChartData = $stmtBarChart->fetchAll(PDO::FETCH_ASSOC);

$genres = array_column($barChartData, 'genre_name');
$bookCounts = array_column($barChartData, 'book_count');

// Fetch data for the doughnut chart (Book Availability)
$queryAvailability = "
    SELECT 
        SUM(CASE WHEN stock > 0 THEN stock ELSE 0 END) AS available,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock
    FROM book_stock;
";
$stmtAvailability = $conn->prepare($queryAvailability);
$stmtAvailability->execute();
$availability = $stmtAvailability->fetch(PDO::FETCH_ASSOC);

// Fetch data for the pie chart (Author Contribution)
$queryAuthorContribution = "
    SELECT authors.name AS author_name, COUNT(books.id) AS book_count
    FROM authors
    LEFT JOIN books ON authors.id = books.author_id
    GROUP BY authors.id, authors.name
    ORDER BY book_count DESC
    LIMIT 10;
";
$stmtAuthorContribution = $conn->prepare($queryAuthorContribution);
$stmtAuthorContribution->execute();
$authorData = $stmtAuthorContribution->fetchAll(PDO::FETCH_ASSOC);

$authors = array_column($authorData, 'author_name');
$authorBookCounts = array_column($authorData, 'book_count');

// Calculate percentages for the pie chart
$totalBooksByTopAuthors = array_sum($authorBookCounts);
$authorPercentages = array_map(function($count) use ($totalBooksByTopAuthors) {
    return round(($count / $totalBooksByTopAuthors) * 100, 2);
}, $authorBookCounts);

// Fetch metrics: Total Books, Genres, Most Popular Genre, Most Prolific Author
$totalBooksQuery = "SELECT COUNT(*) AS total_books FROM books";
$stmtTotalBooks = $conn->prepare($totalBooksQuery);
$stmtTotalBooks->execute();
$totalBooks = $stmtTotalBooks->fetch(PDO::FETCH_ASSOC)['total_books'];

$totalGenresQuery = "SELECT COUNT(*) AS total_genres FROM genres";
$stmtTotalGenres = $conn->prepare($totalGenresQuery);
$stmtTotalGenres->execute();
$totalGenres = $stmtTotalGenres->fetch(PDO::FETCH_ASSOC)['total_genres'];

$popularGenreQuery = "
    SELECT genres.name AS genre_name, COUNT(books.id) AS book_count
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY book_count DESC
    LIMIT 1;
";
$stmtPopularGenre = $conn->prepare($popularGenreQuery);
$stmtPopularGenre->execute();
$popularGenre = $stmtPopularGenre->fetch(PDO::FETCH_ASSOC);

$prolificAuthorQuery = "
    SELECT authors.name AS author_name, COUNT(books.id) AS book_count
    FROM authors
    LEFT JOIN books ON authors.id = books.author_id
    GROUP BY authors.id, authors.name
    ORDER BY book_count DESC
    LIMIT 1;
";
$stmtProlificAuthor = $conn->prepare($prolificAuthorQuery);
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

    <!-- Visualizations Section -->
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

        <!-- Pie Chart: Author Contribution -->
        <h2 class="text-center mb-4">Author Contribution</h2>
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <canvas id="authorChart"></canvas>
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
                    backgroundColor: ['#4CAF50', '#FF6347']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Pie Chart: Author Contribution
        const authorCtx = document.getElementById('authorChart').getContext('2d');
        new Chart(authorCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($authors) ?>,
                datasets: [{
                    data: <?= json_encode($authorPercentages) ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#E7E9ED', '#8E44AD', '#2ECC71', '#3498DB'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
