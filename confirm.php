<?php
// confirm.php

// Include các tập tin và khởi tạo các thư viện cần thiết
include 'classes/user.php';
 // Giả sử bạn có một lớp Session để quản lý phiên
 include_once($filepath . '/../lib/session.php');
// Khởi tạo biến lỗi
$error = '';

// Kiểm tra nếu yêu cầu là POST và có các tham số cần thiết
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userId']) && isset($_POST['captcha'])) {
    // Lấy userId và captcha từ POST data
    $userId = $_POST['userId'];
    $captcha = $_POST['captcha'];

    // Xử lý xác minh tài khoản
    $user = new User(); // Khởi tạo một đối tượng User
    $result = $user->confirm($userId, $captcha); // Gọi phương thức confirm từ đối tượng User

    // Kiểm tra kết quả của việc xác minh
    if ($result === true) {
        // Nếu xác minh thành công, chuyển hướng đến trang login.php hoặc trang khác
        header("Location: login.php");
        exit();
    } else {
        // Nếu xác minh không thành công, hiển thị thông báo lỗi
        $error = "Xác minh không thành công. Vui lòng thử lại.";
    }
} elseif (isset($_GET['userId'])) {
    // Nếu có userId được truyền qua GET, hiển thị form xác minh
    $userId = $_GET['userId'];
} else {
    // Nếu không có userId, hiển thị thông báo lỗi
    $error = "Thiếu thông tin cần thiết để xác minh tài khoản.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh Email</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <label class="logo">MELODYMART</label>
        <ul>
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="productList.php">Sản phẩm</a></li>
            <li><a href="register.php" id="signup" class="active">Đăng ký</a></li>
            <li><a href="login.php" id="signin">Đăng nhập</a></li>
            <li><a href="order.php" id="order">Đơn hàng</a></li>
            <li>
                <a href="checkout.php">
                    <i class="fa fa-shopping-bag"></i>
                    <span class="sumItem">0</span>
                </a>
            </li>
        </ul>
    </nav>

    <section class="banner"></section>

    <div class="featuredProducts">
        <h1>Xác minh Email</h1>
    </div>

    <div class="container-single">
        <div class="login">
            <?php if (!empty($error)) : ?>
                <b class="error"><?= $error ?></b>
            <?php else : ?>
                <p>Vui lòng nhập mã xác minh đã được gửi đến email của bạn.</p>
            <?php endif; ?>
            <form action="confirm.php" method="post" class="form-login">
                <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
                <label for="captcha">Mã xác minh:</label>
                <input type="text" id="captcha" name="captcha" placeholder="Mã xác minh..." required>
                <input type="submit" value="Xác minh" name="submit">
            </form>
        </div>
    </div>

    <footer>
        <div class="social">
            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
        </div>
        <ul class="list">
            <li><a href="index.php">Trang Chủ</a></li>
            <li><a href="productList.php">Sản Phẩm</a></li>
        </ul>
        <p class="copyright">MELODYMART @ 2021</p>
    </footer>
</body>
</html>
