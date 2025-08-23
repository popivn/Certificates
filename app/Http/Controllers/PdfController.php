<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfController extends Controller
{
    public function showUploadForm()
{
    return view('pdf-upload'); // trỏ đến file resources/views/pdf-upload.blade.php
}

    /**
     * Thêm ảnh nền vào từng trang PDF, phủ kín toàn bộ trang (không để khoảng trắng), giữ đúng tỉ lệ, không méo ảnh.
     */
    /**
     * Thêm ảnh nền vào từng trang PDF, phủ kín toàn bộ trang (không để khoảng trắng), giữ đúng tỉ lệ, không méo ảnh.
     * Lưu ý: FPDI/TCPDF có thể tự động thêm lề (margin) nếu không cấu hình lại. Để loại bỏ hoàn toàn khoảng trắng (đặc biệt là phía dưới),
     * cần set margin về 0 và đảm bảo kích thước trang PDF khớp với template gốc.
     */
    public function addBackground(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
            'background_image' => 'required|image'
        ]);

        // Đường dẫn file tạm thời
        $pdfPath = $request->file('pdf_file')->getRealPath();
        $bgPath = $request->file('background_image')->getRealPath();

        $pdf = new FPDI();

        // Loại bỏ toàn bộ margin mặc định của TCPDF
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $pageCount = $pdf->setSourceFile($pdfPath);

        // Lấy kích thước thực tế của trang PDF gốc (mm)
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        // Đặt kích thước ảnh nền là 1813 x 1300 px (chuyển sang mm)
        $targetWidthPx = 1813;
        $targetHeightPx = 1300;
        $dpi = 96; // TCPDF/Fpdi mặc định 96dpi
        $mmPerInch = 25.4;
        $targetWidthMM = $targetWidthPx * $mmPerInch / $dpi;
        $targetHeightMM = $targetHeightPx * $mmPerInch / $dpi;

        // Lấy kích thước thực tế của ảnh upload (px)
        list($imgOrigWidth, $imgOrigHeight) = getimagesize($bgPath);

        // Tính toán tỉ lệ để ảnh nền phủ kín vùng 1813x1300 mà không bị méo, không dư trắng
        $pageW = $targetWidthMM;
        $pageH = $targetHeightMM;
        $pageRatio = $targetWidthPx / $targetHeightPx;
        $imgRatio = $imgOrigWidth / $imgOrigHeight;

        if ($imgRatio > $pageRatio) {
            // Ảnh nền rộng hơn trang PDF => scale theo chiều cao, cắt bớt chiều ngang
            $imgDrawHeightMM = $pageH;
            $imgDrawWidthMM = $imgDrawHeightMM * $imgRatio;
            $imgX = -($imgDrawWidthMM - $pageW) / 2;
            $imgY = 0;
        } else {
            // Ảnh nền cao hơn trang PDF => scale theo chiều rộng, cắt bớt chiều dọc
            $imgDrawWidthMM = $pageW;
            $imgDrawHeightMM = $imgDrawWidthMM / $imgRatio;
            $imgX = 0;
            $imgY = -($imgDrawHeightMM - $pageH) / 2;
        }

        // Thêm trang mới với kích thước 1813 x 1300 px (mm) và orientation gốc
        $pdf->AddPage($size['orientation'], [$pageW, $pageH]);
        // Vẽ ảnh nền phủ kín trang, không dư trắng
        $pdf->Image($bgPath, $imgX, $imgY, $imgDrawWidthMM, $imgDrawHeightMM);

        // Chèn nội dung PDF gốc lên trên ảnh nền, đúng vị trí, đúng kích thước
        // Đặt lại vị trí và kích thước template cho khớp với vùng 1813x1300
        $pdf->useTemplate($templateId, 0, 40, $pageW, $pageH, true);

        // Xuất PDF mới
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf_with_background.pdf"');
    }
}