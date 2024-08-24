
<?php
session_start(); // 开始会话（应该放在文件的最开始处）


// 数据库连接参数（请根据你的数据库配置进行修改）
$servername = "localhost";
$username = "root";
$password = "root2024";
$dbname = "beauty";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("无法连接服务器: " . $conn->connect_error);
}

// 设置字符集（如果你的数据库不是 UTF-8，或者你的连接字符串中没有指定）
mysqli_query($conn, "SET NAMES 'utf8'");
$error_message = ''; // 用于存储错误信息

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // 预处理查询
    $user_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($user_result) == 1) {
        $row = mysqli_fetch_assoc($user_result);
        $password_db = $row["password"];
///注意看这里是第一次使用session_start()，因为要开启会话，将一些东西保存起来，传给别的php去使用，进行个性化的处理。
        if ($password==$password_db){
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] =  $row["id"];
            $_SESSION["skin_type"] = $row["skin_type"];
            $_SESSION["skin_conditions"] = $row["skin_conditions"];
            $_SESSION["diet"] = $row["diet"];
            $_SESSION["allergies"] = $row["alleries"];
            $_SESSION["medication"] = $row["medication"];
            $_SESSION["health"] = $row["health"];
            $_SESSION["eye_conditions"] = $row["eye_conditions"];
            $_SESSION['loggedin'] = true;
            header('Location: index.php');
            exit;
        } else {
            echo '<script>alert("密码错误"); window.location.href = "login.php";</script>';
            exit;
        }
    } else {
        echo '<script>alert("账户不存在"); window.location.href = "login.php";</script>';
        exit;
    }

    // 关闭预处理语句
   # mysqli_stmt_close($stmt);
}
// 关闭数据库连接
mysqli_close($conn);
if (!empty($error_message)) {
    echo "<script>alert('$error_message');</script>";
}
?>