<?php

session_start(); // 开始会话
include('login1.php'); 
include("upload_picture.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // 如果未登录，重定向到 login.php
    exit(); // 确保代码执行到这里时停止
}
$logged_user = $_SESSION["username"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Q&A</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
        }
        .user-message {
            text-align: right;
            background-color: #d1ecf1;
            margin-top:10px;
        }
        .api-message {
            text-align: left;
            background-color: #b8e9c4;
        }
        #progress-bar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            color: white;
        }
        #progress-container {
            width: 100%;
            background-color: #ddd;
            margin-bottom: 20px;
        }
            #upload-container {
            display: flex;
            flex-direction: column;
            align-items: left;
            margin-bottom: 20px;
        }
        #image-preview {
            max-width: 300px;
            max-height: 300px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">Beauty</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">欢迎<?php echo $logged_user?>来到问答页面<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">退出</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <div class="container">
        <h1 class="mt-5">Beauty Q&A</h1>
         <div class="form-group">
            <label for="document">Please upload one picture</label>
            <a href="index.php">Upload txt file</a>
                <div id="upload-container">
        <input type="file" id="image-upload" accept="image/*">
        <img id="image-preview" src="#" alt="预览图" style="display:none;">
    </div>
    <div id=textresults></div>
        <button id="upload-button" class="btn btn-secondary">Upload picture</button>
        <div id="progress-container">
            <div id="progress-bar">0%</div>
        </div>
        <div id="upload-message"></div>
         <form id="Q&A-form" class="mt-4">
            <div class="form-group">
                <label for="text">Your Questions (OK for both English and Chinese):</label>
                <input type="text" id="text" class="form-control" required>
            </div>
            <button type="button" id="submit-button" class="btn btn-primary">Submit</button>
        </form>
        <div id="messages"></div>
         <div id="answers"></div>
    <div id="loading" style="display: none;">
    <div class="spinner"></div>
    <p>Loading...</p>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
   <script>
    document.getElementById('image-upload').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById('image-preview').src = event.target.result;
                document.getElementById('image-preview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('image-preview').style.display = 'none';
        }
    });

    $(document).ready(function(){
        var accessToken; // 将 accessToken 声明在此处
        $("#upload-button").click(function(){
            var fileInput = $("#image-upload")[0];
            if (fileInput.files.length === 0) {
                alert("请上传你的图片。");
                //window.location.href="picture.php";
            } else {
                var formData = new FormData();
                var file = $("#image-upload")[0].files[0];
                formData.append("image", file);
                $("#progress-container").show();
                $("#progress-bar").width("0%").text("0%");
                
                $.ajax({
                    url: "upload_picture.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() { 
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('#progress-bar').width(percentComplete + '%');
                                $('#progress-bar').text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        $("#upload-message").text("上传成功！");
                        accessToken = response.access_token;
                        //console.log("Access Token:", accessToken); // 现在可以访问 accessToken 了
                       // $('#textresults').html('<div class="alert alert-success">'+response+'</div>');
                        console.log(response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#upload-message').text('上传失败，请重试。');
                        console.error('Error:', textStatus, errorThrown);
                    }
                });
            }
        });
    });
    
    $('#submit-button').click(function() {
                var question = $('#text').val().trim(); // 获取并去除文本前后的空白字符

                if (question === '') {
                    alert('问题不能为空。');
                    return; // 如果为空，停止执行后续代码
                }

                // .val() 方法获取该元素（通常是输入框或文本域）的当前值
                //选中message的元素，然后在里面添加上question这个内容，他的类别是 message和user-message在前面已经有定义的CSSstyle
                $('#messages').append('<div class="message user-message">' + question + '</div>');
                $('#loading').show(); // 显示加载动画

                $.ajax({
                    url: 'find_similar_sentences.php', //发送到的地址
                    type: 'POST', //选项为POST或者GET
                    data: { text: question }, //发送的数据
                    dataType: 'json',  //这里的dataType指的是预期中期待返回的数据类型，应该是JSON
                    success: function(response) {   //成功的回调函数
                        console.log(response);  // 现在这应该直接记录 JSON 对象
                        $('#loading').hide(); // 隐藏加载动画

                        console.log(marked);  // 这应该打印出 marked 函数
                       answers.innerHTML  = marked.parse(response.answer)+"<br>"+marked.parse(response.reference);
    
                        //$('#messages').append('<div class="message api-message">' + response.answer + '</div>');
                        //$('#messages').append('<div class="message api-message">'+ response.reference + '</div>');
                    },
                    error: function() { //错误的回调函数
                        $('#loading').hide(); // 隐藏加载动画
                        $('#messages').append('<div class="message api-message">Error occurred while answering.</div>');
                    }
                });
                
                // Clear the input field
                $('#text').val('');
            });
</script>

</body>
</html>
