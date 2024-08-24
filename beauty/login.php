
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beauty - 登录</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Custom styles */
    body {
      font-family: 'Times New Roman', Times, serif;
    }
    .navbar-brand {
      font-size: 24px;
    }
    .welcome-text {
      font-family: '仿宋', 'STFangsong', serif; /* 这里填入仿宋字体的具体名称 */
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
          <li class="nav-item">
            <a class="nav-link" href="signup.php">注册</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="#">欢迎登录 <span class="sr-only">(current)</span></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  
  <div class="container mt-5">
    <h2>用户登录</h2>
    <form action="login1.php" method="post">
      <div class="form-group">
        <label for="username">用户名</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名">
      </div>
      <div class="form-group">
        <label for="password">密码</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码">
      </div>
      <button type="submit" class="btn btn-primary">登录</button>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
