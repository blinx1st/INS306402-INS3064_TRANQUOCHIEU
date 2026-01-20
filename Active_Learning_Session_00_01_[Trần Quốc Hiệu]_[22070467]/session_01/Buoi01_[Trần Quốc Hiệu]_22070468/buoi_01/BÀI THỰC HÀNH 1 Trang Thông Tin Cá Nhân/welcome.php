<?php
// =====================
// Thiết lập múi giờ Việt Nam
// =====================
date_default_timezone_set("Asia/Ho_Chi_Minh");

// =====================
// 1. Lời chào theo thời gian
// =====================
$hour = date("H");

if ($hour < 12) {
    $greeting = "Chào buổi sáng 🌅";
} elseif ($hour < 18) {
    $greeting = "Chào buổi chiều ☀️";
} else {
    $greeting = "Chào buổi tối 🌙";
}

// =====================
// 2. Ngày trong tuần (Tiếng Việt)
// =====================
$daysOfWeek = [
    "Sunday"    => "Chủ nhật",
    "Monday"    => "Thứ Hai",
    "Tuesday"   => "Thứ Ba",
    "Wednesday" => "Thứ Tư",
    "Thursday"  => "Thứ Năm",
    "Friday"    => "Thứ Sáu",
    "Saturday"  => "Thứ Bảy"
];

$todayEnglish = date("l");
$todayVietnamese = $daysOfWeek[$todayEnglish];

// =====================
// 3. Đếm số ngày còn lại trong tháng
// =====================
$currentDay   = date("d");
$totalDays    = date("t");
$daysLeft     = $totalDays - $currentDay;

// =====================
// Ngày giờ hiện tại
// =====================
$currentDateTime = date("H:i:s - d/m/Y");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Welcome Page - PHP</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #e0f2fe, #fef9c3);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #ffffff;
            padding: 30px 36px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            max-width: 520px;
            width: 100%;
            text-align: center;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 32px;
            color: #1f2937;
        }

        .time {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-top: 12px;
            text-align: left;
        }

        .info p {
            margin: 8px 0;
            font-size: 16px;
        }

        .highlight {
            font-weight: bold;
            color: #2563eb;
        }
    </style>
</head>
<body>

<div class="card">
    <h1><?php echo $greeting; ?></h1>
    <div class="time">⏰ <?php echo $currentDateTime; ?></div>

    <div class="info">
        <p>📅 Hôm nay là: <span class="highlight"><?php echo $todayVietnamese; ?></span></p>
        <p>📆 Số ngày còn lại trong tháng: 
            <span class="highlight"><?php echo $daysLeft; ?></span> ngày
        </p>
    </div>
</div>

</body>
</html>
