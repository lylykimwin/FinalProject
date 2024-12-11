<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Include database connection

// Fetch data for the charts
$query = "
    SELECT 
        books.genre_id,
        genres.name AS genre_name
    FROM books
    LEFT JOIN genres ON books.genre_id = genres.id
";
$stmt = $conn->prepare($query);
$stmt->execute();
$rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle multi-genre entries
$genresCount = [];
foreach ($rawData as $row) {
    $genreNames = explode(',', $row['genre_name']); // Split comma-separated genres
    foreach ($genreNames as $genreName) {
        $genreName = trim($genreName); // Remove extra spaces
        if (!isset($genresCount[$genreName])) {
            $genresCount[$genreName] = 0;
        }
        $genresCount[$genreName]++;
    }
}

// Prepare data for Chart.js
$genres = array_keys($genresCount);
$bookCounts = array_values($genresCount);
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
