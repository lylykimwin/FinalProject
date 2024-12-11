<?php
session_start();
include 'db.php';

// Get cart items
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Your Cart</h1>
        <?php if (!empty($cart)): ?>
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $book_id => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($book_id) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                        </tr>
                        <?php $total += $item['quantity']; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-end">
                <h4>Total Items: <?= $total ?></h4>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Your cart is empty.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
