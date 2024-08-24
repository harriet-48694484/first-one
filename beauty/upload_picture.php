<?php
session_start();
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $file = $_FILES["image"]["tmp_name"];
        $fileName = $_FILES['image']['name'];
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . basename($fileName);
        
        // 将文件移动到上传目录中
        move_uploaded_file($file, $filePath);
        
        $url = "http://82.157.40.72:8001/ocr/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        
        // 使用完整路径发送文件
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "file" => new CURLFile($filePath),
            "url" => $filePath
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        
        $textResults = [];
        if (isset($responseData["ocr_result"]["words_result"]) && is_array($responseData["ocr_result"]["words_result"])) {
            foreach ($responseData["ocr_result"]["words_result"] as $result) {
                if (isset($result["words"])) {
                    $textResults[] = $result["words"];
                }
            }
        }
        
        // Store file information in the database
        $servername = "localhost";
        $username = "root";
        $password = "root2024";
        $dbname = "beauty";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("数据库连接失败：" . $conn->connect_error);
        }

        // 插入文件记录
        $stmt = $conn->prepare("INSERT INTO picfile (user_id, image_name, image_path) VALUES (?, ?, ?)");
        if ($stmt === FALSE) {
            $response['message'] = "准备语句失败：" . $conn->error;
        }
        $stmt->bind_param("iss", $user_id, $fileName, $filePath);
        //$response = array();
        if ($stmt->execute() === FALSE) {
            $response['message'] = "执行语句失败：" . $stmt->error;
        }
        $picfile_id = $stmt->insert_id;
        $stmt->close();
        
    var_dump($textResults);
    $tresults=json_encode($textResults);
    $url = 'http://82.157.40.72:8001/process-pictext/';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $tresults);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
));

$response = curl_exec($ch);

curl_close($ch);

$result = json_decode($response, true);
$embeddings = $result['source_embeddings'];
$segments = $result['segments'];
foreach ($segments as $index => $segment) {
        $segment = trim($segment);
        if (!empty($segment)) {
            //$stmt = $conn->prepare("INSERT INTO pictext (picfile_id, text_fragment, embedding) VALUES (?, ?, ?)");
             $stmt = $conn->prepare("INSERT INTO filefragments (file_id, fragment_text, vector) VALUES (?, ?, ?)");
            if ($stmt === FALSE) {
                $response['message'] = "准备片段语句失败：" . $conn->error;
            }
            $embedding = json_encode($embeddings[$index]);
            $stmt->bind_param("iss", $picfile_id, $segment, $embedding);
            if ($stmt->execute() === FALSE) {
                $response['message'] = "插入片段数据失败：" . $stmt->error;
            }
            $stmt->close();
        }
    }

    } else {
        echo json_encode(["error" => "文件上传失败"]);
    }
} else {
    echo json_encode(["error" => "无效请求"]);
}

?>
