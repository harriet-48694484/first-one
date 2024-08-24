<?php

session_start();
header('Content-Type: application/json'); // 设置响应头

$response = [];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$response['message'] = '文件上传成功'; // 直接赋给$response数组了一对键值对.

$user_id = $_SESSION['user_id']; // 从会话中获取用户 ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['document']) || $_FILES['document']['error'] != UPLOAD_ERR_OK) {
        $response['message'] = '文件上传出错。';
    }

    $file = $_FILES['document']['tmp_name'];
    $fileName = $_FILES['document']['name'];

    // 检查文件类型是否为 TXT
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileType !== 'txt') {
        $response['message'] = '无效的文件类型。只允许 TXT 文件。';
    }
    //echo json_encode($response);
    // 使用 cURL 发送文件到 FastAPI
    $url = 'http://127.0.0.1:8001/process-txt/';
    $curl = curl_init();

    $cfile = new CURLFile($file, 'text/plain', $fileName);

    $postFields = ['file' => $cfile];

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'Content-Type: multipart/form-data',
        ],
    ]);

    $result = curl_exec($curl);

    if (curl_errno($curl)) {
        $response['message'] = '处理文本时发生错误：' . curl_error($curl);
    }

    curl_close($curl);

    // 解析 FastAPI 返回的结果
    $responses = json_decode($result, true);
    if (!isset($responses['source_embeddings']) || !isset($responses['segments'])) {
        $response['message'] = "FastAPI 返回无效响应：" . json_encode($responses);
    }

    $embeddings = $responses['source_embeddings'];
    $segments = $responses['segments'];

    // 连接到 MySQL 数据库
    $servername = "localhost";
    $username = "root";
    $password = "root2024";
    $dbname = "beauty";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("数据库连接失败：" . $conn->connect_error);
    }

    // 插入文件记录
    $stmt = $conn->prepare("INSERT INTO files (user_id, file_name, file_path) VALUES (?, ?, ?)");
    if ($stmt === FALSE) {
        $response['message'] = "准备语句失败：" . $conn->error;
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . basename($fileName, ".txt");
    if (!move_uploaded_file($file, $filePath)) {
        $response['message'] = "文件保存失败。";
    }

    $stmt->bind_param("iss", $user_id, $fileName, $filePath);
    if ($stmt->execute() === FALSE) {
        $response['message'] = "执行语句失败：" . $stmt->error;
    }

    $file_id = $stmt->insert_id;
    $stmt->close();

    foreach ($segments as $index => $segment) {
        $segment = trim($segment);
        if (!empty($segment)) {
            $stmt = $conn->prepare("INSERT INTO filefragments (file_id, fragment_text, vector) VALUES (?, ?, ?)");
            if ($stmt === FALSE) {
                $response['message'] = "准备片段语句失败：" . $conn->error;
            }
            $embedding = json_encode($embeddings[$index]);
            $stmt->bind_param("iss", $file_id, $segment, $embedding);
            if ($stmt->execute() === FALSE) {
                $response['message'] = "插入片段数据失败：" . $stmt->error;
            }
            $stmt->close();
        }
    }

    // 返回成功消息或者其他操作
    $response['message'] = 'TXT file processed and stored successfully.';
    echo json_encode(['message' => $response['message']]);
}

?>
