<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPdf\Facades\Pdf;
use Carbon\Carbon;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RecipientController extends Controller
{
    /**
     * Tại sao vào đây bị vòng lặp gọi route liên tục?
     *
     * Nguyên nhân phổ biến nhất là trong quá trình xử lý lỗi (catch), bạn dùng `return back()->withErrors(...)`.
     * Nếu route này (previewPdf) là GET và không có referer hợp lệ, hoặc chính nó là trang trước đó,
     * thì `back()` sẽ redirect lại chính route này, gây vòng lặp vô hạn.
     *
     * Để tránh vòng lặp, KHÔNG dùng `back()` trong các route chỉ dùng để xuất file/pdf hoặc API.
     * Thay vào đó, trả về response lỗi rõ ràng (ví dụ: abort(500) hoặc trả về view lỗi).
     */
    public function previewPdf()
    {
        // 1. Chuẩn bị dữ liệu mẫu
        $data = [
            'recipient_name' => 'NGUYỄN VĂN A',
            'award_title' => 'Sinh viên Xuất sắc',
            'academic_year' => '2024 – 2025',
            'award_title_english' => 'Excellent Student of The Class, Academic Year 2024 – 2025',
            'program' => 'TÀI CHÍNH - NGÂN HÀNG KHÓA 17/2024',
            'program_english' => 'Finance - Banking K17/2024',
            'issue_day' => 15,
            'issue_month' => 6,
            'issue_year' => 2024,
            'location' => 'Hà Nội',
            'decision_prefix' => '',
            'rector_name' => 'PGS. TS. TRẦN VĂN B',
            'vietnamese_date' => '15/06/2024',
            'decision_number' => '123/2024',
            'english_date' => 'June 15, 2024',
            'position_adjustments' => [
                'recipient_name_top' => '0',
                'program_top' => '0',
                'award_title_top' => '0',
                'award_title_english_top' => '0',
                'program_english_top' => '0',
                'vietnamese_date_top' => '0',
                'decision_number_top' => '0',
                'rector_name_top' => '0',
                'english_date_top' => '0',
            ],
        ];

        // 2. Xử lý dữ liệu
        $data = $this->processData($data);

        // 3. Đảm bảo position_adjustments
        $data['position_adjustments'] = $data['position_adjustments'] ?? [
            'recipient_name_top' => '0',
            'program_top' => '0',
            'award_title_top' => '0',
            'award_title_english_top' => '0',
            'program_english_top' => '0',
            'vietnamese_date_top' => '0',
            'decision_number_top' => '0',
            'rector_name_top' => '0',
            'english_date_top' => '0',
        ];

        try {
            // Lưu HTML để debug
            $html = view('certificate.pdf-template-pre', ['data' => $data])->render();
            file_put_contents(storage_path('app/debug_pdf.html'), $html);
            Log::info('HTML saved for debugging', ['path' => storage_path('app/debug_pdf.html')]);

            $pdf = Pdf::view('certificate.pdf-template-pre', ['data' => $data])
                ->format('A4')
                ->landscape(true)
                ->margins(10, 10, 10, 10)
                ->showBackground()
                ->setOption('viewport', ['width' => 842, 'height' => 595])
                ->browsershot(function ($browsershot) {
                    $browsershot->setChromePath('C:/Program Files/Google/Chrome/Application/chrome.exe');
                    $browsershot->waitUntilNetworkIdle();
                    $browsershot->setOption('protocolTimeout', 120000);
                    $browsershot->setOption('timeout', 120000);
                })
                ->name('certificate-preview.pdf');

            Log::info('PDF generated successfully', ['filename' => 'certificate-preview.pdf']);
            return $pdf->toResponse();
        } catch (ProcessFailedException $pe) {
            Log::error('ProcessFailedException during PDF generation', [
                'message' => $pe->getMessage(),
                'command' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getCommandLine() : null,
                'output' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getOutput() : null,
                'errorOutput' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getErrorOutput() : null,
            ]);
            // Đừng dùng back() ở đây, vì sẽ gây vòng lặp nếu không có referer hợp lệ!
            return response()->view('errors.pdf-failed', [
                'error' => 'Lỗi tạo PDF: ' . $pe->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error during PDF generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Đừng dùng back() ở đây, vì sẽ gây vòng lặp nếu không có referer hợp lệ!
            return response()->view('errors.pdf-failed', [
                'error' => 'Lỗi tạo PDF: ' . $e->getMessage()
            ], 500);
        }
    }

     public function generatePdf(Request $request)
    {
        try {
            $data = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'award_title' => 'required|string|max:255',
                'academic_year' => 'required|string|max:50',
                'award_title_english' => 'required|string|max:500',
                'program' => 'required|string|max:255',
                'program_english' => 'nullable|string|max:255',
                'issue_day' => 'required|integer|min:1|max:31',
                'issue_month' => 'required|integer|min:1|max:12',
                'issue_year' => 'required|integer|min:2020|max:2030',
                'location' => 'required|string|max:100',
                'decision_prefix' => 'nullable|string|max:50',
                'rector_name' => 'required|string|max:100',
                'position_adjustments' => 'nullable|array',
            ]);

            $processedData = $this->processData($data);

            // Ensure position_adjustments exists
            $processedData['position_adjustments'] = $processedData['position_adjustments'] ?? [
                'recipient_name_top' => '0',
                'program_top' => '0',
                'award_title_top' => '0',
                'award_title_english_top' => '0',
                'program_english_top' => '0',
                'vietnamese_date_top' => '0',
                'decision_number_top' => '0',
                'rector_name_top' => '0',
                'english_date_top' => '0',
            ];

            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $processedData['recipient_name']);
            $filename = 'Certificate_' . $cleanName . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            Log::info('PDF Generation Debug', [
                'template' => 'certificate.pdf-template-pre',
                'data' => $processedData,
                'filename' => $filename,
            ]);

            try {
                $pdf = Pdf::view('certificate.pdf-template-pre', ['data' => $processedData])
                    ->format('A4') // Use standard A4 format
                    ->landscape(true) // Enable landscape orientation
                    ->margins(10, 10, 10, 10) // Set margins in mm (top, right, bottom, left)
                    // ->showBackground() // Removed: Method does not exist in Spatie\LaravelPdf\PdfBuilder
                    ->name($filename);

                Log::info('PDF object created successfully.', ['filename' => $filename]);
                return $pdf->toResponse($request);
            } catch (ProcessFailedException $pe) {
                Log::error('ProcessFailedException during PDF generation', [
                    'message' => $pe->getMessage(),
                    'command' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getCommandLine() : null,
                    'output' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getOutput() : null,
                    'errorOutput' => method_exists($pe, 'getProcess') ? $pe->getProcess()->getErrorOutput() : null,
                ]);
                return back()->withErrors(['error' => 'Lỗi tạo PDF (ProcessFailedException): ' . $pe->getMessage()]);
            } catch (\Exception $e) {
                Log::error('Unexpected error during PDF generation', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withErrors(['error' => 'Lỗi tạo PDF: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            Log::error('PDF generation failed at validation or pre-process', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Lỗi tạo PDF: ' . $e->getMessage()]);
        }
    }
    public function create()
    {
        return view('certificate.create');
    }

    /**
     * Show preview before generating PDF
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'award_title' => 'required|string|max:255',
            'academic_year' => 'required|string|max:50',
            'award_title_english' => 'required|string|max:500',
            'program' => 'required|string|max:255',
            'program_english' => 'nullable|string|max:255',
            'issue_day' => 'required|integer|min:1|max:31',
            'issue_month' => 'required|integer|min:1|max:12',
            'issue_year' => 'required|integer|min:2020|max:2030',
            'location' => 'required|string|max:100',
            'decision_prefix' => 'nullable|string|max:50',
            'rector_name' => 'required|string|max:100',
        ]);

        $processedData = $this->processData($data);

        return view('certificate.preview', [
            'data' => $processedData
        ]);
    }

    /**
     * Preview PDF layout with adjustable positioning
     */
    
    /**
     * Generate PDF using DomPDF
     */
    public function generatePdf2(Request $request)
    {
        try {
            $data = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'award_title' => 'required|string|max:255',
                'academic_year' => 'required|string|max:50',
                'award_title_english' => 'required|string|max:500',
                'program' => 'required|string|max:255',
                'program_english' => 'nullable|string|max:255',
                'issue_day' => 'required|integer|min:1|max:31',
                'issue_month' => 'required|integer|min:1|max:12',
                'issue_year' => 'required|integer|min:2020|max:2030',
                'location' => 'required|string|max:100',
                'decision_prefix' => 'nullable|string|max:50',
                'rector_name' => 'required|string|max:100',
                'position_adjustments' => 'nullable|array',
            ]);

            $processedData = $this->processData($data);

            // Load template PDF
            $pdf = new Fpdi('L', 'mm', 'A4');  // L: Landscape
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $templatePath = public_path('assets/template/GIẤY KHEN 1.pdf');
            $pageCount = $pdf->setSourceFile($templatePath);
            $tpl = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tpl, 0, 0, 297, 210);  // A4 landscape: 297x210 mm

            // Font
            $pdf->SetFont('dejavusans', '', 18);

            // ======================
            // Chèn dữ liệu động
            // ======================

            // Tên người nhận
            $pdf->SetFont('dejavusans', 'B', 48);
            $pdf->SetTextColor(212, 175, 55);  // gold
            $recipientNameTop = isset($processedData['position_adjustments']['recipient_name_top']) ? $processedData['position_adjustments']['recipient_name_top'] : 90;
            $pdf->SetXY(0, $recipientNameTop);  // X=0 để căn giữa
            $pdf->Cell(297, 20, strtoupper(trim($processedData['recipient_name'])), 0, 1, 'C');

            // Lớp
            $pdf->SetFont('dejavusans', 'B', 18);
            $programTop = isset($processedData['position_adjustments']['program_top']) ? $processedData['position_adjustments']['program_top'] : 110;
            $pdf->SetXY(0, $programTop);
            $pdf->Cell(297, 10, 'LỚP: ' . strtoupper(trim($processedData['program'])), 0, 1, 'C');

            // Lớp (tiếng Anh) nếu có
            if (!empty($processedData['program_english'])) {
                $pdf->SetFont('dejavusans', '', 16);
                $programEnglishTop = isset($processedData['position_adjustments']['program_english_top']) ? $processedData['position_adjustments']['program_english_top'] : ($programTop + 12);
                $pdf->SetTextColor(160, 45, 45);  // #a02d2d
                $pdf->SetXY(0, $programEnglishTop);
                $pdf->Cell(297, 10, '(Class: ' . trim($processedData['program_english']) . ')', 0, 1, 'C');
            }

            // Award Title
            $pdf->SetFont('dejavusans', '', 14);
            $pdf->SetTextColor(0, 0, 0);
            $awardTitleTop = isset($processedData['position_adjustments']['award_title_top']) ? $processedData['position_adjustments']['award_title_top'] : 125;
            $pdf->SetXY(40, $awardTitleTop);
            $pdf->Cell(0, 10, $processedData['award_title'] . ' ' . $processedData['academic_year'], 0, 1, 'C');

            // English Award Title
            $pdf->SetFont('dejavusans', 'I', 12);
            $awardTitleEnglishTop = isset($processedData['position_adjustments']['award_title_english_top']) ? $processedData['position_adjustments']['award_title_english_top'] : ($awardTitleTop + 10);
            $pdf->SetTextColor(108, 117, 125);  // #6c757d
            $pdf->SetXY(40, $awardTitleEnglishTop);
            $pdf->Cell(0, 10, '(' . $processedData['award_title_english'] . ')', 0, 1, 'C');

            // ======================

            // Xuất file
            $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $processedData['recipient_name']);
            $filename = 'Certificate_' . $cleanName . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->Output($filename, 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi tạo PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Process and format form data
     */
    private function processData($data)
    {
        $data['recipient_name'] = strtoupper(trim($data['recipient_name']));
        $data['program'] = strtoupper(trim($data['program']));

        $issueDate = Carbon::createFromDate(
            $data['issue_year'],
            $data['issue_month'],
            $data['issue_day']
        );

        if (empty($data['decision_prefix'])) {
            $data['decision_number'] = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT) . '/QĐ-ĐHVTT';
        } else {
            $data['decision_number'] = trim($data['decision_prefix']) . '/QĐ-ĐHVTT';
        }

        $data['vietnamese_date'] = $data['location'] . ', ngày ' . $data['issue_day']
            . ' tháng ' . $data['issue_month']
            . ' năm ' . $data['issue_year'];

        $data['english_date'] = '(' . $data['location'] . ', '
            . $issueDate->format('F j, Y') . ')';

        $data['rector_name'] = trim($data['rector_name']);

        if (!empty($data['program_english'])) {
            $data['program_english'] = trim($data['program_english']);
        }

        return $data;
    }

    /**
     * AJAX endpoint for real-time preview updates
     */
    public function previewAjax(Request $request)
    {
        try {
            $data = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'award_title' => 'required|string|max:255',
                'academic_year' => 'required|string|max:50',
                'award_title_english' => 'required|string|max:500',
                'program' => 'required|string|max:255',
                'program_english' => 'nullable|string|max:255',
                'issue_day' => 'required|integer|min:1|max:31',
                'issue_month' => 'required|integer|min:1|max:12',
                'issue_year' => 'required|integer|min:2020|max:2030',
                'location' => 'required|string|max:100',
                'decision_prefix' => 'nullable|string|max:50',
                'rector_name' => 'required|string|max:100',
            ]);

            $processedData = $this->processData($data);

            return response()->json([
                'success' => true,
                'data' => $processedData,
                'html' => view('certificate.preview-content', [
                    'data' => $processedData
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : ['general' => [$e->getMessage()]]
            ], 422);
        }
    }
}
