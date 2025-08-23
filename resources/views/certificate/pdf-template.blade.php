<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        @page { 
            margin: 0;
            size: A4 landscape;
            /* Đặt nền cho toàn bộ trang PDF bằng hình ảnh */
            background: url('{{ public_path("assets/template/certificate_template.jpg") }}') no-repeat center center;
            background-size: cover;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            /* Không cần background-color vì đã có nền ảnh */
            background: transparent !important;
        }
        .certificate-preview-bg {
            width: 100%;
            height: 100%;
            background: transparent !important;
        }
        #certificatePreview {
            position: relative;
            width: 842px;
            height: 595px;
            /* Không cần background ở đây nữa */
            margin: 0 auto;
        }
        #certificateContent {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 0;
        }
        .recipient-name {
            font-size: 48pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #d4af37;
            line-height: 1.5;
            margin-top: {{ $data['position_adjustments']['recipient_name_top'] }}px;
        }
        .program {
            font-size: 18pt;
            font-weight: bold;
            color: #d4af37;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
            margin-top: {{ $data['position_adjustments']['program_top'] }}px;
        }
        .award-title {
            font-size: 16pt;
            font-weight: 600;
            margin-bottom: 6px;
            margin-top: {{ $data['position_adjustments']['award_title_top'] }}px;
        }
        .award-title-english {
            font-size: 14pt;
            font-style: italic;
            color: #6c757d;
            margin-bottom: 12px;
            margin-top: {{ $data['position_adjustments']['award_title_english_top'] }}px;
        }
        .program-english {
            font-size: 12pt;
            color: #7a7a7a;
            margin-bottom: 10px;
            margin-top: {{ $data['position_adjustments']['program_english_top'] }}px;
        }
        .vietnamese-date {
            font-size: 12pt;
            margin-bottom: 4px;
            margin-top: {{ $data['position_adjustments']['vietnamese_date_top'] }}px;
        }
        .decision-number {
            font-size: 12pt;
            margin-top: {{ $data['position_adjustments']['decision_number_top'] }}px;
        }
        .decision-number-label {
            font-size: 11pt;
            color: #888;
            margin-bottom: 12px;
            margin-top: {{ $data['position_adjustments']['decision_number_top'] }}px;
        }
        .rector-label {
            font-size: 12pt;
            font-weight: 600;
            margin-top: {{ $data['position_adjustments']['rector_name_top'] }}px;
        }
        .rector-name {
            font-size: 14pt;
            font-weight: 500;
            margin-top: {{ $data['position_adjustments']['rector_name_top'] }}px;
        }
        .english-date {
            font-size: 12pt;
            color: #888;
            margin-top: {{ $data['position_adjustments']['english_date_top'] }}px;
        }
    </style>
</head>
<body>
<div class="certificate-preview-bg">
    <div id="certificatePreview">
        <div id="certificateContent">
            <div class="recipient-name">
                {{ strtoupper(trim($data['recipient_name'])) }}
            </div>
            <div class="program">
                LỚP: {{ strtoupper(trim($data['program'])) }}
            </div>
            <div class="award-title">
                {{ $data['award_title'] }} {{ $data['academic_year'] }}
            </div>
            <div class="award-title-english">
                ({{ $data['award_title_english'] }})
            </div>
            @if(!empty($data['program_english']))
            <div class="program-english">
                (Class: {{ trim($data['program_english']) }})
            </div>
            @endif
            <div class="vietnamese-date">
                {{ $data['vietnamese_date'] }}
            </div>
            <div class="decision-number">
                Quyết định số: {{ $data['decision_number'] }}
            </div>
            <div class="decision-number-label">
                (Decision No.)
            </div>
            <div class="rector-label">
                Hiệu trưởng/Rector
            </div>
            <div class="rector-name">
                {{ trim($data['rector_name']) }}
            </div>
            <div class="english-date">
                {{ $data['english_date'] }}
            </div>
        </div>
    </div>
</div>
</body>
</html>