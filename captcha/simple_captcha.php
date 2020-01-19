<?php

function captcha($len)
{

//宣告一個空字串變數
  $code = "";

  //利用迴圈來累加字串
  for ($i = 0; $i < $len; $i++) {

    //宣告一個亂數變數來決定每次迴圈要產生的是數字還是大小寫英文字
    $type = rand(1, 3);

    //利用switch case來切換字元的類型
    switch ($type) {
      case "1":
        //產生亂數的數字
        $code = $code . rand(0, 9);
        break;
      case "2":
        //產生亂數的小寫英文
        $code = $code . chr(rand(97, 122));
        break;
      case "3":
        //產生亂數的大寫英文
        $code = $code . chr(rand(65, 90));
        break;
    }
  }

  $img_w = 80;
  $img_h = 30;

  //建立一個用來存放驗證碼的圖片資源,全彩
  $img = imagecreatetruecolor($img_w, $img_h);

  //設定一個背景色
  $bg    = imagecolorallocate($img, 255, 200, 255);
  $black = imagecolorallocate($img, 0, 0, 0);
  //填上背景色
  imagefill($img, 0, 0, $bg);

  //在底圖上畫文字
  $str_x = 5; //預設從底圖左側5點的地方開始畫
  $str_y = 5; //預設從底圖上側5點的地方開始畫

  //將字串$code 寫入圖形資源中
  imagestring($img, 5, $str_x, $str_y, $code, $black);

  ob_start(); // 開啟記憶體緩衝區.
  imagepng($img); //將圖形資源在記憶體中輸出成一個圖檔而不是輸出在硬碟中.
  $codestr = ob_get_contents(); //將記憶體緩衝區中的所有內容都取出成為字串
  ob_end_clean(); //關閉記憶體緩衝區.
  
  //將轉出的圖形字串轉為base64,並加上網頁顯示需要的宣告字串
  $codeurl = "data:image/png;base64," . base64_encode($codestr);

  return $codeurl;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>PHP圖形處理-圖形驗證碼</title>
  <link rel="stylesheet" href="style.css">
  <style>
  *{
    padding:0;
    margin:0;
    box-sizing:border-box;
    font-family:"微軟正黑體",Arial;
  }
    form {
      display: block;
      width:200px;
      text-align:center;
      border: 1px solid #ccc;
      padding: 20px 15px;
      border-radius: 20px;
      box-shadow: 1px 1px 3px #ccc;
      margin:auto;
    }
    .header{
      text-align:center;
      margin:50px auto 10px auto;

    }
  </style>
</head>

<body>
  <h1 class="header">圖形處理練習-簡易文字驗證碼</h1>

  <form action="?" method="post">
    <input type="submit" value="產生驗證碼">


  <!----產生圖形驗證碼----->
  <div>
    <img src="<?=captcha(4);?>" alt="圖形驗證碼">
  </div>
  </form>
</body>

</html>