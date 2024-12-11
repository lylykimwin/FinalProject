<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch data for the charts
// Books by Genre
$queryGenreDistribution = "
    SELECT 
        genres.name AS genre_name, 
        COUNT(books.id) AS book_count
    FROM genres
    LEFT JOIN books ON genres.id = books.genre_id
    GROUP BY genres.id, genres.name
    ORDER BY genres.name;
";
$stmtGenreDistribution = $conn->prepare($queryGenreDistribution);
$stmtGenreDistribution->execute();
$genreData = $stmtGenreDistribution->fetchAll(PDO::FETCH_ASSOC);

// Library Growth Over Time
$queryLibraryGrowth = "
    SELECT 
        YEAR(published_year) AS year, 
        COUNT(id) AS book_count
    FROM books
    GROUP BY YEAR(published_year)
    ORDER BY YEAR(published_year);
";
$stmtLibraryGrowth = $conn->prepare($queryLibraryGrowth);
$stmtLibraryGrowth->execute();
$growthData = $stmtLibraryGrowth->fetchAll(PDO::FETCH_ASSOC);

// Most Prolific Authors
$queryProlificAuthors = "
    SELECT 
        authors.name AS author_name, 
        COUNT(books.id) AS book_count
    FROM authors
    LEFT JOIN books ON authors.id = books.author_id
    GROUP BY authors.id, authors.name
    ORDER BY book_count DESC
    LIMIT 5;
";
$stmtProlificAuthors = $conn->prepare($queryProlificAuthors);
$stmtProlificAuthors->execute();
$authorData = $stmtProlificAuthors->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$genres = array_column($genreData, 'genre_name');
$genreCounts = array_column($genreData, 'book_count');

$years = array_column($growthData, 'year');
$yearCounts = array_column($growthData, 'book_count');

$authors = array_column($authorData, 'author_name');
$authorCounts = array_column($authorData, 'book_count');
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

    <!-- Visualizations -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Library Visualizations</h1>

        <!-- Pie Chart: Books by Genre -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Books by Genre</h5>
                        <canvas id="genrePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart: Library Growth Over Time -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Library Growth Over Time</h5>
                        <canvas id="growthLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horizontal Bar Chart: Most Prolific Authors -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Most Prolific Authors</h5>
                        <canvas id="authorsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        // Pie Chart: Books by Genre
        const genreCtx = document.getElementById('genrePieChart').getContext('2d');
        new Chart(genreCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($genres) ?>,
                datasets: [{
                    data: <?= json_encode($genreCounts) ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#9966FF'],
                }]
            }
        });

        // Line Chart: Library Growth Over Time
        const growthCtx = document.getElementById('growthLineChart').getContext('2d');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($years) ?>,
                datasets: [{
                    label: 'Books Added',
                    data: <?= json_encode($yearCounts) ?>,
                    borderColor: '#007bff',
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Year' } },
                    y: { title: { display: true, text: 'Books Added' }, beginAtZero: true }
                }
            }
        });

        // Horizontal Bar Chart: Most Prolific Authors
        const authorCtx = document.getElementById('authorsBarChart').getContext('2d');
        new Chart(authorCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($authors) ?>,
                datasets: [{
                    label: 'Number of Books',
                    data: <?= json_encode($authorCounts) ?>,
                    backgroundColor: '#4CAF50'
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { title: { display: true, text: 'Books' } },
                    y: { title: { display: true, text: 'Authors' } }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
