<?php
$host = 'mis-final-aeafajdmc3dyhqfz.centralus-01.azurewebsites.net'; // Update with your DB host
$user = 'lylykimwin';      // Update with your DB username
$pass = 'UN113498602!';          // Update with your DB password
$dbname = 'book_library'; // Update with your DB name

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
