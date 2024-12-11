<?php
session_start();
include 'db.php';

if (!isset($_POST['book_id'])) {
    header('Location: books.php');
    exit;
}

$book_id = $_POST['book_id'];

// Check stock availability
$query = "SELECT stock FROM book_stock WHERE book_id = :book_id";
$stmt = $conn->prepare($query);
$stmt->execute([':book_id' => $book_id]);
$book_stock = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book_stock || $book_stock['stock'] <= 0) {
    $_SESSION['error'] = "This book is out of stock.";
    header('Location: books.php');
    exit;
}

// Add to cart logic
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$book_id])) {
    $_SESSION['cart'][$book_id]['quantity']++;
} else {
    $_SESSION['cart'][$book_id] = ['quantity' => 1];
}

// Success message
$_SESSION['success'] = "The book has been added to your cart!";
header('Location: books.php');
exit;
?>
