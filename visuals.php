<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch data for the charts
$query = "SELECT genres.name AS genre_name, COUNT(books.id) AS book_count FROM genres 
          LEFT JOIN books ON genres.id = books.genre_id 
          GROUP BY genres.id, genres.name 
          ORDER BY genres.name";
$stmt = $conn->prepare($query);
$stmt->execute();
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$genres = [];
$bookCounts = [];
foreach ($chartData as $data) {
    $genres[] = $data['genre_name'];
    $bookCounts[] = $data['book_count'];
}
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="home.php">Lyly's Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="visuals.php">Visuals</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        <h1 class="text-center mb-4">Library Visualizations</h1>
        <div class="row">
            <!-- Bar Chart -->
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Books Per Genre</h5>
                        <canvas id="genreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        const ctx = document.getElementById('genreChart').getContext('2d');
        const genreChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($genres) ?>, // Genres as labels
                datasets: [{
                    label: 'Number of Books',
                    data: <?= json_encode($bookCounts) ?>, // Book counts as data
                    backgroundColor: 'rgba(0, 123, 255, 0.5)', // Blue bars
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.raw} books`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Books'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Genres'
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
