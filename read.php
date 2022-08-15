<?PHP
  $dsn = 'mysql:dbname=pm_app_db;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';

  try {
    $pdo = new PDO($dsn, $user, $password);

    # ===== 並び替え機能 =====
    # orderパラメータの値が存在すれば（並び替えボタンを押したとき）、その値を変数$orderに代入する
    if (isset($_GET['order'])) {
      $order = $_GET['order'];
    } else {
      $order = NULL;
    }
    # ====== 検索機能 =====
    # keywordパラメータの値が存在すれば（商品名を検索したとき）、その値を変数$keywordに代入する    
    if (isset($_GET['keyword'])) {
      $keyword = $_GET['keyword'];
    } else {
      $keyword = NULL;
    }

    # orderパラメータの値によってSQL文を変更する
    if ($order === 'desc') {
      $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
      $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';
    }
    
    // SQL文を用意する
    $stmt_select = $pdo->prepare($sql_select);

    # SQLのLIKE句で使うため、変数$keyword（検索ワード）の前後を%で囲む（部分一致）
    $partial_match = "%{$keyword}%";
    # bindValue()メソッドを使って実際の値をプレースホルダにバインドする（割り当てる）
    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
    # SQL文を実行する
    $stmt_select->execute();

    // SQL文の実行結果を配列で取得し、$productsに代入
    $products = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品一覧</title>
  <link rel="stylesheet" href="css/style.css">

  <!-- Google Fontsの読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
  <header>
    <nav>
      <a href="index.php" class="nav-title">商品管理アプリ</a>
    </nav>
  </header>
  <main>
    <article class="products">
      <h1>商品一覧</h1>
      <?php
        # （商品の登録・編集・削除後）messageパラメータの値を受け取っていれば、それを表示する
        if (isset($_GET['message'])) {
          echo "<p class='success'>{$_GET['message']}</p>";
        }
      ?>
      <div class="products-ui">
        <div>
          <!-- ☆並び替え機能と検索機能を同時に使えるようにする -->
          <!-- ☆①並び替えにキーワードのパラメータも一緒に -->
          <a href="read.php?order=desc&keyword=<?= $keyword ?>">
            <img src="img/desc.png" alt="降順に並び替え" class="sort-img">
          </a>
          <a href="read.php?order=asc&keyword=<?= $keyword ?>">
            <img src="img/asc.png" alt="昇順に並び替え" class="sort-img">
          </a>
          <!-- 作成した検索フォームのようにform要素内にinput要素が1つしかない場合、送信ボタン（type="submit"）を作成しなくてもエンターキーを押せば入力内容を送信できる。またinputタグ（入力欄）のvalue属性（初期値）に変数$keywordの値を埋め込み、検索後に「どの検索ワードで検索したか」をわかりやすくしている -->
          <form action="read.php" method="get" class="search-form">
            <!-- ☆②input type="hidden"を使って、フォームの入力内容以外の隠しデータで並び替えのパラメーターを一緒に渡す -->
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="text" class="search-box" placeholder="商品名で検索" name="keyword" value="<?= $keyword ?>">
          </form>
        </div>
        <a href="create.php" class="btn">商品登録</a>
      </div>
      <table class="products-table">
        <tr>
          <th>商品コード</th>
          <th>商品名</th>
          <th>単価</th>
          <th>在庫数</th>
          <th>仕入先コード</th>
          <th>編集</th>
          <th>削除</th>
        </tr>
        <?php
        // 配列の中身を順番に取り出し、表形式で出力する
        foreach ($products as $product) {
          $table_row = "
            <tr>
              <td>{$product['product_code']}</td>
              <td>{$product['product_name']}</td>
              <td>{$product['price']}</td>
              <td>{$product['stock_quantity']}</td>
              <td>{$product['vendor_code']}</td>
              <td><a href='update.php?id={$product['id']}'><img src='img/edit.png' alt='編集' class='edit-icon'></a></td>
              <td><a href='delete.php?id={$product['id']}'><img src='img/delete.png' alt='削除' class='delete-icon'></a></td> 
            </tr>
          ";
          echo $table_row;
        }
        ?>
      </table>
    </article>
  </main>
  <footer>
      <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
  </footer>
</body>

</html>