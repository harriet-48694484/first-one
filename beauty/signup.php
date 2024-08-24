<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beauty - 注册</title>
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
  <script>
      function redirectToLogin() {
          window.location.href = "login.php";
      }
  </script>
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
          <a class="nav-link" href="#">欢迎注册 <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">退出</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h2>填写个人信息</h2>
  <form action="handle_registration.php" method="post">
    <div class="form-group">
      <label for="username">用户名</label>
      <input type="text" class="form-control" name="username" placeholder="请输入用户名">
    </div>
    <div class="form-group">
      <label for="phone">电话</label>
      <input type="tel" class="form-control" name="phone" placeholder="请输入电话号码">
    </div>
    <div class="form-group">
      <label for="email">邮箱</label>
      <input type="email" class="form-control" name="email" placeholder="请输入邮箱">
    </div>
    <div class="form-group">
      <label for="password">密码</label>
      <input type="password" class="form-control" name="password" placeholder="请输入密码">
      </div>
    <div class="form-group">
      <label for="birthday">出生年月</label>
      <input type="date" class="form-control" name="birthday">
    </div>
    <div class="form-group">
      <label for="skin_type">皮肤类型</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="中性" name="skin_type[]">
        <label class="form-check-label" for="skinNeutral">中性</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="干性" name="skin_type[]">
        <label class="form-check-label" for="skinDry">
          干性
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="油性" name="skin_type[]">
        <label class="form-check-label" for="youxing">
          油性
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="混干" name="skin_type[]">
        <label class="form-check-label" for="hungan">
          混干
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="混油" name="skin_type[]">
        <label class="form-check-label" for="hunyou">
          混油
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="敏感性" name="skin_type[]">
        <label class="form-check-label" for="mingan">
          敏感性
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="衰老性" name="skin_type[]">
        <label class="form-check-label" for="shuailao">
          衰老性
        </label>
      </div>
    </div>
    <div class="form-group">
      <label for="skin_conditions">皮肤表征</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="粉刺" name="skin_conditions[]">
        <label class="form-check-label" for="fenci">
          粉刺
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="暗疮" name="skin_conditions[]">
        <label class="form-check-label" for="anchuang">
          暗疮
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="色斑" name="skin_conditions[]">
        <label class="form-check-label" for="seban">
          色斑
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="过敏" name="skin_conditions[]">
        <label class="form-check-label" for="guomin">
          过敏
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="皱纹" name="skin_conditions[]">
        <label class="form-check-label" for="zhouwen">
          皱纹
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="红血丝" name="skin_conditions[]">
        <label class="form-check-label" for="hongxuesi">
          红血丝
        </label>
      </div>
    </div>
    <div class="form-group">
      <label for="eye_conditions">眼部解析</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="黑眼圈" name="eye_conditions[]">
        <label class="form-check-label" for="heiyanquan">
          黑眼圈
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="眼袋" name="eye_conditions[]">
        <label class="form-check-label" for="yandai">
          眼袋
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="鱼尾纹" name="eye_conditions[]">
        <label class="form-check-label" for="yuweiwen">
          鱼尾纹
        </label>
      </div>
    </div>
    <div class="form-group">
      <label for="diet">饮食</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="油炸"id="fried" name="diet[]">
        <label class="form-check-label" for="fried">
          油炸
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="辛辣" id="spicy" name="diet[]">
        <label class="form-check-label" for="spicy">
          辛辣
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="暴食" id=baoshi name="diet[]">
        <label class="form-check-label" for="baoshi">
          暴食
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="偏食"id="pianshi" name="diet[]">
        <label class="form-check-label" for="pianshi">
          偏食
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="烟酒" id="yanjiu"name="diet[]">
        <label class="form-check-label" for="yanjiu">
          烟酒
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="正常" id="normal" name="diet[]">
        <label class="form-check-label" for="zhengchang">
          正常
        </label>
      </div>
    </div>
     <script>
        document.getElementById('normal').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('spicy').disabled = true;
                document.getElementById('fried').disabled = true;
                document.getElementById('pianshi').disabled = true;
                document.getElementById('baoshi').disabled = true;
                document.getElementById('yanjiu').disabled = true;
            } else {
                document.getElementById('spicy').disabled = false;
                document.getElementById('fried').disabled = false;
                document.getElementById('pianshi').disabled = false;
                document.getElementById('baoshi').disabled = false;
                document.getElementById('yanjiu').disabled = false;
            }
        });
    </script>
    <div class="form-group">
      <label for="medication">服用药物</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="null" value="无" name="medication[]">
        <label class="form-check-label" for="null">
          无
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="保健类" id="baojian"name="medication[]">
        <label class="form-check-label" for="baojian">
          保健类
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="激素类"id="jisu" name="medication[]">
        <label class="form-check-label" for="jisu">
          激素类
        </label>
      </div>
    </div>
    <script>
        document.getElementById('null').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('baojian').disabled = true;
                document.getElementById('jisu').disabled = true;
            } else {
                document.getElementById('baojian').disabled = false;
                document.getElementById('jisu').disabled = false;
            }
        });
    </script>
    <div class="form-group">
      <label for="allergies">过敏史</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="花粉"id="pollen" name="allergies[]">
        <label class="form-check-label" for="huafen">
          花粉
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="粉尘"id="dust" name="allergies[]">
        <label class="form-check-label" for="dust">
          粉尘
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="海鲜"id="seafood" name="allergies[]">
        <label class="form-check-label" for="seafood">
          海鲜
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="无"id="null_allergies" name="allergies[]">
        <label class="form-check-label" for="null">
          无
        </label>
      </div>
    </div>
     <script>
        document.getElementById('null_allergies').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('pollen').disabled = true;
                document.getElementById('dust').disabled = true;
                document.getElementById('seafood').disabled = true;
            } else {
                document.getElementById('pollen').disabled = false;
                document.getElementById('dust').disabled = false;
                document.getElementById('seafood').disabled = false;
            }
        });
    </script>
    <div class="form-group">
      <label for="health">生理机能状况</label>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="高血压" id="hypertension"name="health[]">
        <label class="form-check-label" for="pressure">
          高血压
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="心脏病"id="heartattack" name="health[]">
        <label class="form-check-label" for="heart">
          心脏病
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="糖尿病" id="diabete"name="health[]">
        <label class="form-check-label" for="tangniaobing">
          糖尿病
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="无" id="null_disease"name="health[]">
        <label class="form-check-label" for="acne">
          无
        </label>
      </div>
    </div>
    <script>
        document.getElementById('null_disease').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('hypertension').disabled = true;
                document.getElementById('heartattack').disabled = true;
                document.getElementById('diabete').disabled = true;
            } else {
                document.getElementById('hypertension').disabled = false;
                document.getElementById('heartattack').disabled = false;
                document.getElementById('diabete').disabled = false;
            }
        });
    </script>
    <button type="submit" class="btn btn-primary">提交</button>
  </form>
</div>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
