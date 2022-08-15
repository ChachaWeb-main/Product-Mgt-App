<?PHP
  $dsn = 'mysql:dbname=pm_app_db;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';

  try {
    $pdo = new PDO($dsn, $user, $password);

    if (isset($_GET['order'])) {
      $order = $_GET['order'];
    } else {
      $order = NULL;
    }
    if (isset($_GET['keyword'])) {
      $keyword = $_GET['keyword'];
    } else {
      $keyword = NULL;
    }

    if ($order === 'desc') {
      $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
      $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';
    }
    
    $stmt_select = $pdo->prepare($sql_select);

    $partial_match = "%{$keyword}%";
    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
    $stmt_select->execute();

    $products = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product list</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Google Fontsの読み込み -->
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
    <article class="products">
      <h1>Product List</h1>
      <?php
        if (isset($_GET['message'])) {
          echo "<p class='success'>{$_GET['message']}</p>";
        }
      ?>
      <div class="products-ui">
        <div>
          <a href="read-en.php?order=desc">
            <img src="img/desc.png" alt="降順に並び替え" class="sort-img">
          </a>
          <a href="read-en.php?order=asc">
            <img src="img/asc.png" alt="昇順に並び替え" class="sort-img">
          </a>
          <form action="read-en.php" method="get" class="search-form">
            <input type="text" class="search-box" placeholder="Search by Product name" name="keyword" value="<?= $keyword ?>">
          </form>
        </div>
        <a href="create-en.php" class="btn">Product register</a>
      </div>
      <table class="products-table">
        <tr>
          <th>Product code</th>
          <th>Produce name</th>
          <th>Unit price</th>
          <th>Stocks</th>
          <th>Supplier code</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
        <?php
          foreach ($products as $product) {
            $table_row = "
              <tr>
                <td>{$product['product_code']}</td>
                <td>{$product['product_name']}</td>
                <td>{$product['price']}</td>
                <td>{$product['stock_quantity']}</td>
                <td>{$product['vendor_code']}</td>
                <td><a href='update-en.php?id={$product['id']}'><img src='img/edit.png' alt='Edit' class='edit-icon'></a></td>
                <td><a href='delete-en.php?id={$product['id']}'><img src='img/delete.png' alt='Delete' class='delete-icon'></a></td> 
              </tr>
            ";
            echo $table_row;
          }
        ?>
      </table>
    </article>
  </main>
  <footer>
    <p class="copyright">&copy; Product Management App All rights reserved.</p>
  </footer>
</body>

</html>