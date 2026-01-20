<?php
// ====== Dữ liệu cá nhân (bạn sửa lại theo thông tin của bạn) ======
$name = "Trần Quốc Hiệu";
$dob  = "18/03/2004";
$hometown = "Hà Nội";
$hobbies  = "Đọc sách, nghe nhạc, đá bóng";

// Ngày giờ truy cập (server time)
$visitedAt = date("H:i:s - d/m/Y");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Info - PHP</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: #f4f6fb;
      color: #1f2a37;
    }
    .container{
      max-width: 860px;
      margin: 40px auto;
      padding: 20px;
    }
    .card{
      background: #ffffff;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    .title{
      font-size: 28px;
      margin: 0 0 10px;
    }
    .subtitle{
      margin: 0 0 20px;
      color: #6b7280;
    }
    .grid{
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-top: 14px;
    }
    .item{
      background: #f9fafb;
      border: 1px solid #eef2f7;
      border-radius: 12px;
      padding: 14px;
    }
    .label{
      font-size: 12px;
      color: #6b7280;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }
    .value{
      font-size: 16px;
      font-weight: 600;
    }
    .footer{
      margin-top: 16px;
      font-size: 13px;
      color: #6b7280;
    }
    @media (max-width: 640px){
      .grid{ grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1 class="title">Trang Thông Tin Cá Nhân</h1>
      <p class="subtitle">Demo PHP Buổi 01: nhúng PHP vào HTML + echo/print + comment</p>

      <div class="grid">
        <div class="item">
          <div class="label">Họ tên</div>
          <div class="value"><?php echo $name; ?></div>
        </div>

        <div class="item">
          <div class="label">Ngày sinh</div>
          <div class="value"><?php echo $dob; ?></div>
        </div>

        <div class="item">
          <div class="label">Quê quán</div>
          <div class="value"><?php echo $hometown; ?></div>
        </div>

        <div class="item">
          <div class="label">Sở thích</div>
          <div class="value"><?php print $hobbies; ?></div>
        </div>
      </div>

      <p class="footer">
        ⏰ Thời gian truy cập: <b><?php echo $visitedAt; ?></b>
      </p>
    </div>
  </div>
</body>
</html>
