<?php
session_start();//确保能够访问和储存session就必须要有这个session_star()
header('Content-Type: application/json'); 
//接受从登录后端login1.php中提取储存的session，然后赋给这一大堆的变量。用于之后的prompt
$username = $_SESSION['username']; // 从会话中获取用户 ID
$skin_type = $_SESSION['skin_type'];//php的变量命名需要为$开头
$skin_conditions = $_SESSION['skin_conditions'];
$eye_conditions = $_SESSION['eye_conditions'];
$diet = $_SESSION['diet'];
$medication = $_SESSION['medication'];
$allergies = $_SESSION['allergies'];
$health = $_SESSION['health'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {//if ($_SERVER['REQUEST_METHOD'] === 'POST') {} 的意思是：如果当前的HTTP请求方法是POST，则执行花括号 {} 内的代码块。
    $question = $_POST['text'];//index上通过获取用户提出的问题文本，然后用POST的形式发给了这个php，于是我使用$question = $_POST['text'];来获取。

    // 调用 FastAPI 获取输入问题的向量表示
    $url = 'http://82.157.40.72:8001/get-sentence-embedding/';
    $data = json_encode(['text' => $question]);/*注意不要搞混，这个含义是将 $question 的值作为一个名为 text 的属性放入一个关联数组中，
    并将该数组转换为JSON格式的字符串，以便于在网络上传输或者与其他系统进行交互。
    记忆方法：text是键，$question是值， $question 的值是 "What is your name?"，那么生成的JSON字符串将是 {"text":"What is your name?"}*/
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => $data,
        ],
    ];
    $context = stream_context_create($options);/*它是用来在文件操作中传递参数的一种方法。
    在这里，它使用了之前定义的 $options 变量，包括请求头、方法还有内容*/
    $result = file_get_contents($url, false, $context);/*file_get_contents() 函数用于从文件或者URL中
    获取内容，也就是说进入这个fastapi的ur，带着你的一系列要求和方法，然后得到result，false是说把返回的结果直接以字符串的方式返回
    而如果是true的话，就会成为一个数组。因为之后要解析你里面的JSON数据，所以就是直接用false就可以了。*/
   //$result的内容就是fastAPI最后return {"embedding": embedding}，因为传递过程中是一个JSON，我要是想提取出来embedding到底是什么，我需要使用json_decode()，将他变成不同的php数组
    if ($result === false) {
        //echo json_encode(['error' => 'Error occurred while getting question embedding.']);
        exit();
    }
    $response = json_decode($result, true);//将你返回的JSON数据转化为php数组（即 PHP 中的关联数组，键值对形式）
    $question_embedding = $response['embedding'];//这个就是从reponse这个关联数组中找到embedding为键的那个内容，就是句子的向量。

    // 连接到 MySQL 数据库
    $servername = "localhost";
    $username = "root";
    $password = "root2024";
    $dbname = "beauty";
//连接指定的数据库
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error); // 增加日志记录
        //echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
        exit();
    }


    // 查询 text_segments 表，计算输入问题与每个已存问题的余弦相似度
    $sql = "SELECT id, fragment_text, vector FROM filefragments";
    $result = $conn->query($sql);

    if (!$result) {
        //echo json_encode(['error' => 'Error occurred while querying database: ' . $conn->error]);
        exit();
    }
/*$sql：是一个包含 SQL 查询语句的字符串。
在这里，查询的是名为 filefragments 的表中的 id、fragment_text 和 vector 列的数据。
$conn->query($sql)：执行 SQL 查询，将查询结果赋给 $result。
如果查询失败，$result 的值将是 false。*/
    $max_similarity = -1;
    $best_match =[];
    $stored_vector = json_decode($row['vector'], true);
 if ($result->num_rows > 0) {//查的内容有数据行
        while ($row = $result->fetch_assoc()) {#用fatch_assoc()遍历三个字段每一行的内容，并赋给$row
            $stored_vector = json_decode($row['vector'], true);//于是就可以用$row来表示这一列的全部内容
            if (!is_array($stored_vector)) {
                error_log("Stored vector is not an array: " . $row['vector']);
                continue; // 如果$stored_vector不是数组的话，那就可以蹦过那一行然后继续。
            }
            // 计算余弦相似度
            $similarity = cosineSimilarity($question_embedding, $stored_vector);
           $best_matches[]=['text'=>$row['fragment_text'],
           'similarity'=>$similarity ];
        usort($best_matches,function($a,$b){
            return $b['similarity']<=>$a['similarity'];
        });
        }
        $best_matches=array_slice($best_matches,0,3); 
       foreach($best_matches as $match){
        $best_matches_sentences[]=$match['text'];
       }
       $context_string = implode("\n", $best_matches_sentences);
    }
    $conn->close();
    // 返回最匹配的文本
    //echo json_encode(['best_match' => $best_match ? $best_match : 'No suitable match found.']);
   
}else {
    // 没有找到匹配项的情况下
    $conn->close();
}
// 辅助函数，计算余弦相似度
function cosineSimilarity($vectorA, $vectorB) {
    if (!is_array($vectorA) || !is_array($vectorB)) {
        error_log("One of the vectors is not an array.");
        return 0; // Return 0 similarity in case of error
    }

    $dotProduct = array_sum(array_map(function($a, $b) { return $a * $b; }, $vectorA, $vectorB));
    $normA = sqrt(array_sum(array_map(function($a) { return $a * $a; }, $vectorA)));
    $normB = sqrt(array_sum(array_map(function($b) { return $b * $b; }, $vectorB)));

    if ($normA == 0 || $normB == 0) {
        error_log("One of the vectors is zero vector.");
        return 0; // Avoid division by zero
    }

    return $dotProduct / ($normA * $normB);
}// 构造发送给 FastAPI 的数据
if ($best_matches_sentences) {
    $data = json_encode([
        'text' => $question,
        'context' => "### 您提供文本中的参考内容如下\n\n" . 
        "------\n\n" . 
        $context_string . "\n\n" . 
        "### 您提供的个人信息如下\n\n" . 
        "1. **皮肤类型:** " . $skin_type . "\n" .
        "2. **皮肤状况:** " . $skin_conditions . "\n" .
        "3. **眼睛状况:** " . $eye_conditions . "\n" .
        "4. **饮食偏好:** " . $diet . "\n" .
        "5. **是否有特殊用药:** " . $medication . "\n" .
        "6. **是否有过敏原:** " . ($allergies ?: '(无)') . "\n" .
        "7. **是否有其他疾病:** " . ($health ?: '(无)')]);
    }
        else {
    $data = json_encode([
        'text' => $question,
        'context' => '',
    ]);
}

$url = 'http://82.157.40.72:8001/answerquestion/';
$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => $data,
    ],
];
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === false) {
    $error = error_get_last();
    echo json_encode(['error' => 'HTTP request failed', 'details' => $error['message']]);
    exit();
}
$response = json_decode($result, true);
$answer = $response['answer'];
$reference= $response['context'];
echo json_encode(['answer' => $answer, 'reference' => $reference]);


?>