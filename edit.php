<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $genre_id = $_POST['genre_id'];
    $published_year = $_POST['published_year'];

    $stmt = $conn->prepare("
        UPDATE books
        SET title = :title, author_id = :author_id, genre_id = :genre_id, published_year = :published_year
        WHERE id = :id
    ");
    $stmt->execute([
        ':title' => $title,
        ':author_id' => $author_id,
        ':genre_id' => $genre_id,
        ':published_year' => $published_year,
        ':id' => $id
    ]);

    header('Location: index.php');
}
?>

<form method="POST">
    <input type="text" name="title" value="<?= $book['title'] ?>" required>
    <input type="number" name="author_id" value="<?= $book['author_id'] ?>" required>
    <input type="number" name="genre_id" value="<?= $book['genre_id'] ?>" required>
    <input type="number" name="published_year" value="<?= $book['published_year'] ?>" required>
    <button type="submit">Update</button>
</form>
