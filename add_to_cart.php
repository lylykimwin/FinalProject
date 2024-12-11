<?php
session_start(); // Start session to store cart data
include 'db.php'; // Include database connection

// Ensure book ID is provided
if (!isset($_POST['book_id'])) {
    header('Location: books.php');
    exit;
}

// Retrieve the book ID
$book_id = $_POST['book_id'];

// Fetch book details (price, stock, title)
$query = "
    SELECT books.title, book_stock.price, book_stock.stock 
    FROM books
    JOIN book_stock ON books.id = book_stock.book_id
    WHERE books.id = :book_id
";
$stmt = $conn->prepare($query);
$stmt->execute([':book_id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book || $book['stock'] <= 0) {
    $_SESSION['error'] = "This book is out of stock.";
    header('Location: books.php');
    exit;
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update the book in the cart
if (isset($_SESSION['cart'][$book_id])) {
    if ($_SESSION['cart'][$book_id]['quantity'] < $book['stock']) {
        $_SESSION['cart'][$book_id]['quantity']++;
    } else {
        $_SESSION['error'] = "You cannot add more of this book than available in stock.";
    }
} else {
    $_SESSION['cart'][$book_id] = [
        'title' => $book['title'],
        'price' => $book['price'],
        'quantity' => 1
    ];
}

// Success notification
$_SESSION['success'] = "{$book['title']} has been added to your cart.";

// Redirect back to books page
header('Location: books.php');
exit;
?>
