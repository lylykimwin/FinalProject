<?php
session_start(); // Start the session to store cart data
include 'db.php'; // Include database connection

// Ensure book ID is provided
if (!isset($_POST['book_id'])) {
    header('Location: books.php');
    exit;
}

// Retrieve the book ID
$book_id = $_POST['book_id'];

// Check stock availability
$query = "SELECT stock, price FROM book_stock WHERE book_id = :book_id";
$stmt = $conn->prepare($query);
$stmt->execute([':book_id' => $book_id]);
$book_stock = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book_stock || $book_stock['stock'] <= 0) {
    // If stock is unavailable, redirect with an error message
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
    // If the book is already in the cart, check stock before incrementing
    if ($_SESSION['cart'][$book_id]['quantity'] < $book_stock['stock']) {
        $_SESSION['cart'][$book_id]['quantity']++;
    } else {
        $_SESSION['error'] = "You cannot add more of this book than available in stock.";
    }
} else {
    // Add the book to the cart
    $_SESSION['cart'][$book_id] = [
        'price' => $book_stock['price'],
        'quantity' => 1
    ];
}

// Redirect back to the books page
header('Location: books.php');
exit;
?>
