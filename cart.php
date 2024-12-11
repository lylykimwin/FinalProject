<?php
session_start(); // Start session to access cart data
include 'db.php'; // Include database connection

// Initialize cart
$cart = $_SESSION['cart'] ?? [];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $book_id => $quantity) {
            if (isset($cart[$book_id])) {
                if ($quantity > 0) {
                    // Update quantity
                    $cart[$book_id]['quantity'] = $quantity;
                } else {
                    // Remove item if quantity is 0
                    unset($cart[$book_id]);
                }
            }
        }
        $_SESSION['cart'] = $cart; // Save updated cart to session
    }

    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']); // Clear the cart
    }

    // Refresh the page to reflect updates
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="visuals.php">Visuals</a> <!-- New Visuals Tab -->
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container mt-5">
        <h1 class="text-center">Your Cart</h1>

        <?php if (!empty($cart)): ?>
            <form method="POST">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($cart as $book_id => $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <input type="number" name="quantities[<?= $book_id ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width: 80px;">
                                </td>
                                <td>$<?= number_format($subtotal, 2) ?></td>
                                <td>
                                    <button type="submit" name="quantities[<?= $book_id ?>]" value="0" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?');">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-between">
                    <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                    <button type="submit" name="clear_cart" class="btn btn-danger">Clear Cart</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">Your cart is empty.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
