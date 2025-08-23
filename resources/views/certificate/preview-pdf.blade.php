@extends('layouts.main')

@section('title', 'Preview PDF Layout | PiSystem')

@section('content')
<div class="container">
    <h2>Preview and Adjust PDF Layout / Xem trước và điều chỉnh bố cục PDF</h2>
    <p>Adjust the positioning of elements below and preview the result. Submit to generate the final PDF.</p>

    <div class="row">
        <!-- Preview Section -->
        <div class="col-md-8">
            <div class="certificate-preview-bg" style="padding: 20px; background-color: #f8f9fa;">
                <div 
                    id="certificatePreview"
                    class="mx-auto"
                    style="
                        position: relative;
                        width: 100%;
                        max-width: 842px;
                        aspect-ratio: 297/210;
                        background: url('{{ asset('assets/template/certificate_template.jpg') }}') center center / cover no-repeat;
                        overflow: hidden;
                        margin: 0 auto;
                    "
                >
                    <div 
                        id="certificateContent"
                        class="d-flex flex-column justify-content-center align-items-center text-center"
                        style="
                            position: relative;
                            z-index: 2;
                            width: 100%;
                            height: 100%;
                            padding: 60px 0;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            text-align: center;
                        "
                    >
                        <!-- Recipient Name -->
                        <div 
                            style="
                                font-size: 48pt;
                                font-weight: 900;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                                color: #d4af37;
                                font-family: 'DejaVu Sans', sans-serif;
                                text-align: center;
                                line-height: 1.5;
                                margin-top: {{ $data['position_adjustments']['recipient_name_top'] }}px;
                            "
                        >
                            {{ strtoupper(trim($data['recipient_name'])) }}
                        </div>

                        <!-- Class Information -->
                        <div 
                            style="
                                font-size: 18pt;
                                font-weight: 700;
                                color: #d4af37;
                                font-family: 'DejaVu Sans', sans-serif;
                                text-align: center;
                                letter-spacing: 0.5px;
                                margin-bottom: 4px;
                                text-transform: uppercase;
                                margin-top: {{ $data['position_adjustments']['program_top'] }}px;
                            "
                        >
                            LỚP: {{ strtoupper(trim($data['program'])) }}
                        </div>

                        <!-- Award Title Vietnamese -->
                        <div 
                            style="
                                font-size: 16pt;
                                font-weight: 600;
                                margin-bottom: 6px;
                                margin-top: {{ $data['position_adjustments']['award_title_top'] }}px;
                            "
                        >
                            {{ $data['award_title'] }} {{ $data['academic_year'] }}
                        </div>
                        
                        <!-- Award Title English -->
                        <div 
                            style="
                                font-size: 14pt;
                                font-style: italic;
                                color: #6c757d;
                                margin-bottom: 12px;
                                margin-top: {{ $data['position_adjustments']['award_title_english_top'] }}px;
                            "
                        >
                            ({{ $data['award_title_english'] }})
                        </div>
                        
                        @if(!empty($data['program_english']))
                        <div 
                            style="
                                font-size: 12pt;
                                color: #7a7a7a;
                                margin-bottom: 10px;
                                margin-top: {{ $data['position_adjustments']['program_english_top'] }}px;
                            "
                        >
                            (Class: {{ trim($data['program_english']) }})
                        </div>
                        @endif
                        
                        <!-- Date Information -->
                        <div 
                            style="
                                font-size: 12pt;
                                margin-bottom: 4px;
                                margin-top: {{ $data['position_adjustments']['vietnamese_date_top'] }}px;
                            "
                        >
                            {{ $data['vietnamese_date'] }}
                        </div>
                        
                        <!-- Decision Number -->
                        <div 
                            style="
                                font-size: 12pt;
                                margin-top: {{ $data['position_adjustments']['decision_number_top'] }}px;
                            "
                        >
                            Quyết định số: {{ $data['decision_number'] }}
                        </div>
                        <div 
                            style="
                                font-size: 11pt;
                                color: #888;
                                margin-bottom: 12px;
                                margin-top: {{ $data['position_adjustments']['decision_number_top'] }}px;
                            "
                        >
                            (Decision No.)
                        </div>
                        
                        <!-- Rector Information -->
                        <div 
                            style="
                                font-size: 12pt;
                                font-weight: 600;
                                margin-top: {{ $data['position_adjustments']['rector_name_top'] }}px;
                            "
                        >
                            Hiệu trưởng/Rector
                        </div>
                        <div 
                            style="
                                font-size: 14pt;
                                font-weight: 500;
                                margin-top: {{ $data['position_adjustments']['rector_name_top'] }}px;
                            "
                        >
                            {{ trim($data['rector_name']) }}
                        </div>
                        
                        <!-- English Date -->
                        <div 
                            style="
                                font-size: 12pt;
                                color: #888;
                                margin-top: {{ $data['position_adjustments']['english_date_top'] }}px;
                            "
                        >
                            {{ $data['english_date'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adjustment Form -->
        <div class="col-md-4">
            <form method="POST" action="{{ route('certificate.preview.pdf') }}" id="adjustmentForm">
                @csrf
                <input type="hidden" name="recipient_name" value="{{ $data['recipient_name'] }}">
                <input type="hidden" name="award_title" value="{{ $data['award_title'] }}">
                <input type="hidden" name="academic_year" value="{{ $data['academic_year'] }}">
                <input type="hidden" name="award_title_english" value="{{ $data['award_title_english'] }}">
                <input type="hidden" name="program" value="{{ $data['program'] }}">
                <input type="hidden" name="program_english" value="{{ $data['program_english'] }}">
                <input type="hidden" name="issue_day" value="{{ $data['issue_day'] }}">
                <input type="hidden" name="issue_month" value="{{ $data['issue_month'] }}">
                <input type="hidden" name="issue_year" value="{{ $data['issue_year'] }}">
                <input type="hidden" name="location" value="{{ $data['location'] }}">
                <input type="hidden" name="decision_prefix" value="{{ $data['decision_prefix'] }}">
                <input type="hidden" name="rector_name" value="{{ $data['rector_name'] }}">

                <h4>Adjust Positions / Điều chỉnh vị trí</h4>
                <div class="form-group mb-3">
                    <label for="recipient_name_top">Recipient Name Top (px)</label>
                    <input type="number" name="position_adjustments[recipient_name_top]" id="recipient_name_top" class="form-control" value="{{ $data['position_adjustments']['recipient_name_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="program_top">Program Top (px)</label>
                    <input type="number" name="position_adjustments[program_top]" id="program_top" class="form-control" value="{{ $data['position_adjustments']['program_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="award_title_top">Award Title Top (px)</label>
                    <input type="number" name="position_adjustments[award_title_top]" id="award_title_top" class="form-control" value="{{ $data['position_adjustments']['award_title_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="award_title_english_top">Award Title English Top (px)</label>
                    <input type="number" name="position_adjustments[award_title_english_top]" id="award_title_english_top" class="form-control" value="{{ $data['position_adjustments']['award_title_english_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="program_english_top">Program English Top (px)</label>
                    <input type="number" name="position_adjustments[program_english_top]" id="program_english_top" class="form-control" value="{{ $data['position_adjustments']['program_english_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="vietnamese_date_top">Vietnamese Date Top (px)</label>
                    <input type="number" name="position_adjustments[vietnamese_date_top]" id="vietnamese_date_top" class="form-control" value="{{ $data['position_adjustments']['vietnamese_date_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="decision_number_top">Decision Number Top (px)</label>
                    <input type="number" name="position_adjustments[decision_number_top]" id="decision_number_top" class="form-control" value="{{ $data['position_adjustments']['decision_number_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="rector_name_top">Rector Name Top (px)</label>
                    <input type="number" name="position_adjustments[rector_name_top]" id="rector_name_top" class="form-control" value="{{ $data['position_adjustments']['rector_name_top'] }}" step="1">
                </div>
                <div class="form-group mb-3">
                    <label for="english_date_top">English Date Top (px)</label>
                    <input type="number" name="position_adjustments[english_date_top]" id="english_date_top" class="form-control" value="{{ $data['position_adjustments']['english_date_top'] }}" step="1">
                </div>

                <button type="submit" class="btn btn-primary">Update Preview / Cập nhật xem trước</button>
                <button type="button" class="btn btn-success" onclick="generatePDF()">Generate PDF / Tạo PDF</button>
            </form>
        </div>
    </div>
</div>

<script>
    function generatePDF() {
        const form = document.getElementById('adjustmentForm');
        form.action = "{{ route('certificate.generate.pdf') }}";
        form.submit();
    }
</script>
@endsection