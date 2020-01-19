<?php

function captcha($len){
//宣告一個空字串變數
 $code="";

 //利用迴圈來累加字串
 for($i=0;$i<$len;$i++){

  //宣告一個亂數變數來決定每次迴圈要產生的是數字還是大小寫英文字
    $type=rand(1,3);

    //利用switch case來切換字元的類型
    switch($type){
       case "1":
          //產生亂數的數字
          $code=$code . rand(0,9);
       break;
       case "2":
          //產生亂數的小寫英文
          $code=$code . chr(rand(97,122));
       break;
       case "3":
          //產生亂數的大寫英文
          $code=$code . chr(rand(65,90));
       break;
    }
 }


 //設定字型資訊
  $fontsize=20;
  $font=0;

  //建立字型清單陣列,需先確認相關目錄下有ttf字型檔
  $fontlist=['times.ttf','timesbd.ttf','timesbi.ttf','timesi.ttf'];

  //設定字形檔的路徑(需要絕對路徑)
  $fontpath=realpath("./font/$fontlist[$font]");
  
  //先計算出字形字串佔用的區域坐標
  $text_box=imagettfbbox($fontsize,0,$fontpath,$code);
  $img_w=$text_box[2]+strlen($code)*10; //利用坐標來算出圖片的寬度並加上間距
  $img_h=$text_box[7]*-1+35;  //利用坐標來算出圖片的高度並加上亂數Y坐標的範圍15及上下邊距20
  //建立一個用來存放驗證碼的圖片資源,全彩
  $img=imagecreatetruecolor($img_w,$img_h);
  
  //設定一個背景色
  $bg=imagecolorallocate($img,255,200,255);
   
  //填上背景色
  imagefill($img,0,0,$bg);
  //先在底圖上畫線條
  $lines=rand(2,4);   //亂數決定線條數
  for($i=0;$i<$lines;$i++){
   //根據函式需要來產生需要的坐標資訊及色彩
     $start_x=rand(5,intval($img_w*0.25));  //在圖片0~1/4的範圍內產生一個x坐標點
     $start_y=rand(10,$img_h-10);           //在驗證碼的高度範圍內產生一個y坐標點
     $end_x=rand(intval($img_w*0.75+5),$img_w-5);    //在圖片3/4~1的範圍內產生一個x坐標點
     $end_y=rand(10,$img_h-10);                      //在驗證碼的高度範圍內產生一個y坐標點
     //用亂數產色一個色彩
     $line_color=imagecolorallocate($img,rand(50,200),rand(50,200),rand(50,200));
     //執行畫線函式
     imageline($img,$start_x,$start_y,$end_x,$end_y,$line_color);
  }
  //在底圖上畫文字
  $str_x=5; //預設從底圖左側5點的地方開始畫
  $str_y=0; 

  //使用迴圈依照驗證碼的字串長度來畫出文字
  for($i=0;$i<strlen($code);$i++){

    $color=imagecolorallocate($img,rand(50,200),rand(50,200),rand(50,200));

     //內建字形的畫字函式->imagestring($img,5,$str_x,$str_y,substr($code,$i,1),$color);
     // imagettfbbox() -> image true type font bounding box -用來取得字形的四點坐標資訊(左下,右下,右上,左上);
     //取得毎個字元的四角坐標值
     $textbox=imagettfbbox($fontsize,0,$fontpath,substr($code,$i,1));
     
     //計算字元在Y軸的位置(上邊距+亂數範圍+字形高度)
     $str_y=10+rand(0,15)+$textbox[7]*-1;
     
     //用亂數產生一個-30~30的傾斜角度
     $angle=rand(-30,30);
     //將單一字元畫在底圖上
     imagettftext($img,$fontsize,$angle,$str_x,$str_y,$color,$fontpath,substr($code,$i,1));
     
     //計算下一個字元在X軸的位置(將上一個字元的x坐標加上字元的寬度),再加上10px的字元間距
     $str_x=$str_x+$textbox[2]+10;
  }

    ob_start(); // 開啟記憶體緩衝區.
    imagepng($img); //將圖形資源在記憶體中輸出成一個圖檔而不是輸出在硬碟中.
    $codestr = ob_get_contents(); //將記憶體緩衝區中的所有內容都取出成為字串
    ob_end_clean(); //關閉記憶體緩衝區.
    
    //將轉出的圖形字串轉為base64,並加上網頁顯示需要的宣告字串
    $codeurl = "data:image/png;base64," . base64_encode($codestr);

    //清除圖形資源，釋放記憶體空間
    imagedestroy($img);
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
  * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: "微軟正黑體", Arial;
  }

  form {
    display: block;
    width: 200px;
    text-align: center;
    border: 1px solid #ccc;
    padding: 20px 15px;
    border-radius: 20px;
    box-shadow: 1px 1px 3px #ccc;
    margin: auto;
  }

  .header {
    text-align: center;
    margin: 50px auto 10px auto;

  }
  </style>
</head>

<body>
  <h1 class="header">圖形處理練習-圖形驗證碼</h1>

  <form action="?" method="post">
    <input type="submit" value="產生驗證碼">
    <!----產生圖形驗證碼----->
    <div>
      <img src="<?=captcha(4);?>" alt="圖形驗證碼">
    </div>
  </form>
</body>

</html>