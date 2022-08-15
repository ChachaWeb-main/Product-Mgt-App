<?PHP
  $dsn = 'mysql:dbname=pm_app_db;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';
  
  try {
    $pdo = new PDO($dsn, $user, $password);

    $sql_delete = 'DELETE FROM products WHERE id = :id';
    $stmt_delete = $pdo->prepare($sql_delete);

    $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    $stmt_delete->execute();

    $count = $stmt_delete->rowCount();

    $message = "We deleted {$count} product.";

    header("Location: read-en.php?message={$message}");
  } catch (PDOException $e) {
    exit($e->getMessage());
  }