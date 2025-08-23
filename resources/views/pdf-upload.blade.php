<!DOCTYPE html>
<html>
<head>
    <title>Upload PDF & Background</title>
</head>
<body>
    <form action="{{ route('pdf.add-background') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>PDF gốc:</label>
        <input type="file" name="pdf_file" accept="application/pdf" required><br><br>
        <label>Hình nền (JPG/PNG):</label>
        <input type="file" name="background_image" accept="image/*" required><br><br>
        <button type="submit">Tạo PDF với nền</button>
    </form>
</body>
</html>
