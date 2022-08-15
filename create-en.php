<?PHP
  $dsn = 'mysql:dbname=pm_app_db;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';

  if (isset($_POST['submit'])) {
    try {
      $pdo = new PDO($dsn, $user, $password);
      $sql_insert = '
        INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code)
        VALUES (:product_code, :product_name, :price, :stock_quantity, :vendor_code)
      ';
      $stmt_insert = $pdo->prepare($sql_insert);
      $stmt_insert->bindValue(':product_code', $_POST['product_code'], PDO::PARAM_INT);
      $stmt_insert->bindValue(':product_name', $_POST['product_name'], PDO::PARAM_STR);
      $stmt_insert->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
      $stmt_insert->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
      $stmt_insert->bindValue(':vendor_code', $_POST['vendor_code'], PDO::PARAM_INT);

      $stmt_insert->execute();

      $count = $stmt_insert->rowCount();
      $message = "We registered {$count} product.";

      header("Location: read-en.php?message={$message}");
    } catch (PDOException $e) {
      exit($e->getMessage());
    }
  }

  try {
    $pdo = new PDO($dsn, $user, $password);
    $sql_select = 'SELECT vendor_code FROM vendors';
    $stmt_select = $pdo->query($sql_select);
    $vendor_codes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product registration</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body class>
  <header>
    <nav>
      <a href="index-en.php" class="nav-title">Product Mgt App</a>
    </nav>
  </header>
  <main>
    <article class="registration">
      <h1>Product registration</h1>
      <div class="back">
        <a href="read-en.php" class="btn">&lt; Return</a>
      </div>
      <form action="create-en.php" method="post" class="registration-form">
        <div>
          <label for="product_code">Product code</label>
          <input type="number" name="product_code" min="0" max="100000000" required>
          <label for="product_name">Product name</label>
          <input type="text" name="product_name" maxlength="50" required>
          <label for="price">Unit price</label>
          <input type="number" name="price" min="0" max="100000000" required>
          <label for="stock_quantity">Stocks</label>
          <input type="number" name="stock_quantity" min="0" max="100000000" required>
          <label for="vendor_code">Supplier code</label>
          <select name="vendor_code" required>
            <option disabled selected value>Select</option>
            <?php
              # 配列の中身を順番に取り出し、セレクトボックスの選択肢として出力する
              foreach ($vendor_codes as $vendor_code) {
                echo "<option value='{$vendor_code}'>{$vendor_code}</option>";
              }
            ?>
          </select>
        </div>
        <button type="submit" class="submit-btn" name="submit" value="create">Register</button>
      </form>
    </article>
  </main>
  <footer>
    <p class="copyright">&copy; Product Management App All rights reserved.</p>
  </footer>
</body>

</html>