<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Certificate Preview | PiSystem</title>
    <meta name="viewport" content="width=842, initial-scale=1">
    <style>
        html, body {
            width: 842px;
            height: 595px;
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        body {
            width: 842px;
            height: 595px;
            overflow: hidden;
        }
         .certificate-preview-bg {
            width: 842px;
            height: 595px;
            background: url('data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('assets/template/certificate_template.jpg'))) }}') center center / cover no-repeat;
            position: relative;
            margin: 0;
            padding: 0;
        }
        #certificateContent {
            width: 842px;
            height: 595px;
            position: absolute;
            top: 167px;
            left: 0;
            box-sizing: border-box;
        }
        @font-face {
            font-family: 'UTM HelvetIns';
            src: url('data:font/truetype;base64,{{ base64_encode(file_get_contents(public_path('fonts/UTM HelvetIns.ttf'))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'UVN Nguyen Du';
            src: url('data:font/truetype;base64,{{ base64_encode(file_get_contents(public_path('fonts/unicode.display.UVNNguyenDu.TTF'))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Times New Roman Bold Custom';
            src: url('data:font/truetype;base64,{{ base64_encode(file_get_contents(public_path('fonts/timesbd_0.ttf'))) }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Times New Roman Custom';
            src: url('data:font/truetype;base64,{{ base64_encode(file_get_contents(public_path('fonts/times_0.ttf'))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'SVN-Agency FB';
            src: url('data:font/truetype;base64,{{ base64_encode(file_get_contents(public_path('fonts/SVN-Agency FB.ttf'))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        .recipient-name {
            font-size: 32pt;
            font-weight: 300;
            text-transform: uppercase;
            color: #d4af37;
            line-height: 1;
            margin: {{ $data['position_adjustments']['recipient_name_top'] ?? 0 }}px 0 8px 0;
            font-family: 'UTM HelvetIns', Times, 'Times New Roman', serif;
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .program {
            font-size: 20pt;
            color: #d4af37;
            margin: {{ $data['position_adjustments']['program_top'] ?? 0 }}px 0 0px 0;
            text-transform: uppercase;
            font-family: 'UVN Nguyen Du', Arial, Helvetica, sans-serif;
            font-weight: 300;
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .program-english {
            font-size: 18pt;
            color: #8a2323;
            margin: {{ $data['position_adjustments']['program_english_top'] ?? 0 }}px 0 0px 0;
            font-family: 'SVN-Agency FB', Arial, Helvetica, sans-serif;
            font-weight: 400;
            letter-spacing: 0.5px;
            position: absolute;
            top: 180px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .award-title {
            font-size: 20pt;
            font-weight: 700;
            margin: {{ $data['position_adjustments']['award_title_top'] ?? 0 }}px 0 1px 0;
            font-family: 'UVN Nguyen Du', Arial, Helvetica, sans-serif;
            color: #d4af37;
            letter-spacing: 0.5px;
            position: absolute;
            top: 220px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .award-title-english {
            font-size: 18pt;
            font-style: normal;
            color: #8a2323;
            margin: {{ $data['position_adjustments']['award_title_english_top'] ?? 0 }}px 0 2px 0;
            font-family: 'SVN-Agency FB', Arial, Helvetica, sans-serif;
            font-weight: 400;
            letter-spacing: 0.5px;
            position: absolute;
            top: 250px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
         .right-block {
            position: absolute;
            top: 50%; /* Căn giữa theo chiều dọc */
            right: 20px; /* Căn phải cách lề phải 20px */
            transform: translateY(-50%); /* Điều chỉnh để căn chính giữa theo chiều dọc */
            width: 220px; /* Chiều rộng cố định */
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            bottom: 280px;
            right: 80px;
            text-align: right; /* Đảm bảo nội dung bên trong căn phải */
            white-space: nowrap; /* Ngăn chữ xuống dòng */
        }
        .vietnamese-date {
            font-size: 8pt;
            margin: {{ $data['position_adjustments']['vietnamese_date_top'] ?? 0 }}px 0 1px 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #8a2323;
            font-weight: 400;
            white-space: nowrap; /* Ngăn chữ xuống dòng */
            text-align: center;
            width: 100%;
        }
        .english-date {
            font-size: 6pt;
            color: #8a2323;
            margin: {{ $data['position_adjustments']['english_date_top'] ?? 0 }}px 0 2px 0;
            font-family: Arial, Helvetica, sans-serif;
            font-style: italic;
            white-space: nowrap; /* Ngăn chữ xuống dòng */
            text-align: center;
            width: 100%;
        }
        .rector-label {
            font-size: 8pt;
            font-weight: 600;
            margin: 0px 0 0 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #8a2323;
            white-space: nowrap; /* Ngăn chữ xuống dòng */
            text-align: center;
            width: 100%;
        }
        .rector-name {
            font-size: 8pt;
            font-weight: 600;
            margin: 8px 0 0 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #8a2323;
            padding-top: 50px;
            white-space: nowrap; /* Ngăn chữ xuống dòng */
            text-align: center;
            width: 100%;
        }
        .decision-number {
            font-size: 8pt;
            margin: {{ $data['position_adjustments']['decision_number_top'] ?? 0 }}px 0 0 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #8a2323;
            position: absolute;
            top: 372px;
            left: 120px;
            text-align: left;
        }
        .decision-number-label {
            font-size: 8pt;
            color: #8a2323;
            margin: 0 0 0 0;
            font-family: Arial, Helvetica, sans-serif;
            font-style: italic;
            position: absolute;
            top: 384px;
            left: 120px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="certificate-preview-bg">
        <div id="certificateContent">
            <div class="recipient-name">{{ strtoupper(trim($data['recipient_name'])) }}</div>
            <div class="program">LỚP: {{ strtoupper(trim($data['program'])) }}</div>
            @if (!empty($data['program_english']))
                <div class="program-english">(Class: {{ trim($data['program_english']) }})</div>
            @endif
            <div class="award-title">{{ $data['award_title'] }} {{ $data['academic_year'] }}</div>
            <div class="award-title-english">({{ $data['award_title_english'] }})</div>
            <div class="right-block">
                <div class="vietnamese-date">{{ $data['vietnamese_date'] }}</div>
                <div class="english-date">{{ $data['english_date'] }}</div>
                <div class="rector-label">HIỆU TRƯỞNG/RECTOR</div>
                <div class="rector-name">{{ trim($data['rector_name']) }}</div>
            </div>
            <div class="decision-number">Quyết định số: {{ $data['decision_number'] }}</div>
            <div class="decision-number-label">(Decision No.)</div>
        </div>
    </div>
</body>
</html>