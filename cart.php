<?php
session_start();
include 'db.php'; // Include database connection

// Initialize cart
$cart = $_SESSION['cart'] ?? [];

// Fetch book details for items in the cart
$booksInCart = [];
$totalPrice = 0;

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $query = "
        SELECT books.id, books.title, book_stock.price 
        FROM books
        JOIN book_stock ON books.id = book_stock.book_id
        WHERE books.id IN ($placeholders)
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute(array_keys($cart));
    $booksInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total price
    foreach ($booksInCart as &$book) {
        $bookId = $book['id'];
        $book['quantity'] = $cart[$bookId]['quantity'];
        $book['subtotal'] = $book['price'] * $book['quantity'];
        $totalPrice += $book['subtotal'];
    }
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $bookId => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$bookId]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$bookId]); // Remove item if quantity is 0
            }
        }
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['remove_item'])) {
        $bookIdToRemove = $_POST['remove_item'];
        unset($_SESSION['cart'][$bookIdToRemove]); // Remove the specific item
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']); // Clear the cart
        header('Location: cart.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Lyly's Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmRemove() {
            return confirm('Are you sure you want to remove this item from the cart?');
        }
    </script>
</head>
<body>
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


    <!-- Cart Content -->
    <div class="container mt-5">
        <h1 class="text-center">Your Cart</h1>
        <?php if (!empty($booksInCart)): ?>
            <form method="POST">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($booksInCart as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td>$<?= number_format($book['price'], 2) ?></td>
                                <td>
                                    <input type="number" name="quantities[<?= $book['id'] ?>]" value="<?= $book['quantity'] ?>" min="0" class="form-control" style="width: 80px;">
                                </td>
                                <td>$<?= number_format($book['subtotal'], 2) ?></td>
                                <td>
                                    <button type="submit" name="remove_item" value="<?= $book['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmRemove();">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-end">
                    <h4>Total Price: $<?= number_format($totalPrice, 2) ?></h4>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                    <button type="submit" name="clear_cart" class="btn btn-danger">Clear Cart</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">Your cart is empty. Browse our <a href="books.php" class="alert-link">books</a> to add items.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
