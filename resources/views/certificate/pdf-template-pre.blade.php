<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Certificate Preview | PiSystem</title>
    <meta name="viewport" content="width=842, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: Arial, Helvetica, sans-serif;
        }
        .certificate-preview-bg {
            width: 842px;
            height: 595px;
            background: url('data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('assets/template/certificate_template.jpg'))) }}') center center / cover no-repeat;
            margin: 0 auto;
            position: relative;
        }
        #certificatePreview {
            width: 842px;
            height: 595px;
            margin: 0 auto;
        }
        #certificateContent {
            width: 100%;
            height: 100%;
            display: block;
            text-align: center;
            padding: 100px 20px;
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
        .recipient-name {
            font-size: 36pt;
            font-weight: 300;
            text-transform: uppercase;
            color: #d4af37;
            line-height: 1.1;
            margin: {{ $data['position_adjustments']['recipient_name_top'] ?? 0 }}px 0 18px;
            font-family: 'UTM HelvetIns', Times, 'Times New Roman', serif;
        }
        .program {
            font-size: 24pt;
            color: #d4af37;
            margin: {{ $data['position_adjustments']['program_top'] ?? 0 }}px 0 4px;
            text-transform: uppercase;
            font-family: 'UVN Nguyen Du', Arial, Helvetica, sans-serif;
            font-weight: 300;
        }
        .award-title {
            font-size: 16pt;
            font-weight: 600;
            margin: {{ $data['position_adjustments']['award_title_top'] ?? 0 }}px 0 6px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .award-title-english {
            font-size: 14pt;
            font-style: italic;
            color: #6c757d;
            margin: {{ $data['position_adjustments']['award_title_english_top'] ?? 0 }}px 0 12px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .program-english {
            font-size: 12pt;
            color: #7a7a7a;
            margin: {{ $data['position_adjustments']['program_english_top'] ?? 0 }}px 0 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .vietnamese-date {
            font-size: 12pt;
            margin: {{ $data['position_adjustments']['vietnamese_date_top'] ?? 0 }}px 0 4px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .decision-number {
            font-size: 12pt;
            margin: {{ $data['position_adjustments']['decision_number_top'] ?? 0 }}px 0 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        .decision-number-label {
            font-size: 11pt;
            color: #888;
            margin: {{ $data['position_adjustments']['decision_number_top'] ?? 0 }}px 0 12px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .rector-label {
            font-size: 12pt;
            font-weight: 600;
            margin: {{ $data['position_adjustments']['rector_name_top'] ?? 0 }}px 0 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        .rector-name {
            font-size: 14pt;
            font-weight: 500;
            margin: {{ $data['position_adjustments']['rector_name_top'] ?? 0 }}px 0 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        .english-date {
            font-size: 12pt;
            color: #888;
            margin: {{ $data['position_adjustments']['english_date_top'] ?? 0 }}px 0 0;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <div class="certificate-preview-bg">
        <div id="certificatePreview">
            <div id="certificateContent">
                <div class="recipient-name">{{ strtoupper(trim($data['recipient_name'])) }}</div>
                <div class="program">LỚP: {{ strtoupper(trim($data['program'])) }}</div>
                <div class="award-title">{{ $data['award_title'] }} {{ $data['academic_year'] }}</div>
                <div class="award-title-english">({{ $data['award_title_english'] }})</div>
                @if (!empty($data['program_english']))
                    <div class="program-english">(Class: {{ trim($data['program_english']) }})</div>
                @endif
                <div class="vietnamese-date">{{ $data['vietnamese_date'] }}</div>
                <div class="decision-number">Quyết định số: {{ $data['decision_number'] }}</div>
                <div class="decision-number-label">(Decision No.)</div>
                <div class="rector-label">Hiệu trưởng/Rector</div>
                <div class="rector-name">{{ trim($data['rector_name']) }}</div>
                <div class="english-date">{{ $data['english_date'] }}</div>
            </div>
        </div>
    </div>
</body>
</html>