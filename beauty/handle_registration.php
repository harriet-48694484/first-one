<?php  
session_start(); // 开始会话  
  
// 数据库连接参数（请根据你的数据库配置进行修改）  
$servername = "localhost";  
$username = "root";  
$password = "root2024";  
$dbname = "beauty";  
  
// 创建连接  
$conn = new mysqli($servername, $username, $password, $dbname);  
  
// 检查连接  
if ($conn->connect_error) {  
    die("连接失败: " . $conn->connect_error);  
}  
  
// 从表单中获取数据  
$register_username = $_POST['username'] ?? ''; 
$register_password = $_POST['password'] ?? ''; 
$register_phone = $_POST['phone'] ?? ''; 
$register_email = $_POST['email'] ?? ''; 
$register_birthday = $_POST['birthday'] ?? ''; 
$register_skin_type = isset($_POST['skin_type']) ? implode(",", $_POST['skin_type']) : ''; 
$register_skin_conditions = isset($_POST['skin_conditions']) ? implode(",", $_POST['skin_conditions']) : ''; 
$register_eye_conditions = isset($_POST['eye_conditions']) ? implode(",", $_POST['eye_conditions']) : ''; 
$register_diet = isset($_POST['diet']) ? implode(",", $_POST['diet']) : ''; 
$register_medication = isset($_POST['medication']) ? implode(",", $_POST['medication']) : ''; 
$register_allergies = isset($_POST['allergies']) ? implode(",", $_POST['allergies']) : ''; 
$register_health = isset($_POST['health']) ? implode(",", $_POST['health']) : ''; 

$check_username = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($check_username);
$stmt->bind_param("s", $register_username);
$stmt->execute();
$result = $stmt->get_result();
if ($register_username === "" || $register_password === "") {
    echo  '<script>alert("用户名或密码不能为空，请重新填写。");window.location.href = "signup.php";</script>';
}
             

if ($result->num_rows > 0) {
    // 用户名已存在，提示用户更换用户名
    echo  '<script>alert("该用户名已存在，请更换用户名。");window.location.href = "signup.php";</script>';
    exit;
} else { 
// 准备SQL语句（这里假设你有一个名为users的表，其中包含所有这些字段）  
$sql = "INSERT INTO users (username, password, phone, email, birthday, skin_type, skin_conditions, eye_conditions, diet, medication, allergies, health)   
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";  
  
// 使用预处理语句防止SQL注入  
$stmt = $conn->prepare($sql);  

var_dump($stmt);
  
// 绑定参数（确保类型和顺序与SQL语句中的占位符匹配）  
$stmt->bind_param("ssssssssssss",   
    $register_username,   
    $register_password,   
    $register_phone, 
    $register_email,   
    $register_birthday,   
    $register_skin_type,   
    $register_skin_conditions,     
    $register_eye_conditions,   
    $register_diet,   
    $register_medication,   
    $register_allergies,   
    $register_health);  
  

// 执行预处理语句  
if ($stmt->execute()) {  
    echo '<script>alert("注册成功"); window.location.href = "login.php";</script>';
    exit;
       
} else {  
    
    
    echo '<script>alert("注册失败"); window.location.href = "signup.php";</script>';
    exit;
}  
}
?>