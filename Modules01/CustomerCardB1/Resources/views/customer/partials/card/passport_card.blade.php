<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Namecard Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --unIQue_cusTOmer_PsPt_Card-card-width: 120.6mm;
            --unIQue_cusTOmer_PsPt_Card-card-height: 85.5mm;
        }

        .unIQue_cusTOmer_PsPt_Card-editor-container * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
            outline: none;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            list-style: none;
        }

        .unIQue_cusTOmer_PsPt_Card-editor-container {
            font-family: Arial, 'Battambang', sans-serif;
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            gap: 5px;
            background-color: #f5f5f5;
        }

        .unIQue_cusTOmer_PsPt_Card_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }

        .unIQue_cusTOmer_PsPt_Card_filters-container h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }

        .unIQue_cusTOmer_PsPt_Card_filters-container hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }

        .unIQue_cusTOmer_PsPt_Card_category-toggle {
            padding: 10px;
            background-color: #d4e6ff;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
        }

        .unIQue_cusTOmer_PsPt_Card_category-toggle:hover {
            background-color: #c8d8ff;
        }

        .unIQue_cusTOmer_PsPt_Card_category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .unIQue_cusTOmer_PsPt_Card_category-content.unIQue_cusTOmer_PsPt_Card_active {
            max-height: 500px;
            padding: 10px;
        }

        .unIQue_cusTOmer_PsPt_Card_filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .unIQue_cusTOmer_PsPt_Card_background-dropdown,
        .unIQue_cusTOmer_PsPt_Card_font-size-dropdown,
        .unIQue_cusTOmer_PsPt_Card_font-style-dropdown {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
        }

        .unIQue_cusTOmer_PsPt_Card_custom-button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
            background-color: #17a2b8;
            color: white;
        }

        .unIQue_cusTOmer_PsPt_Card_custom-button:hover {
            background-color: #138496;
        }

        .unIQue_cusTOmer_PsPt_Card_custom-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .unIQue_cusTOmer_PsPt_Card_reset-button {
            background-color: #dc3545;
            color: white;
        }

        .unIQue_cusTOmer_PsPt_Card_reset-button:hover {
            background-color: #c82333;
        }

        .unIQue_cusTOmer_PsPt_Card_secondary-button {
            background-color: #6c757d;
            color: white;
        }

        .unIQue_cusTOmer_PsPt_Card_secondary-button:hover {
            background-color: #5a6268;
        }

        .unIQue_cusTOmer_PsPt_Card_text-input,
        .unIQue_cusTOmer_PsPt_Card_color-picker,
        .unIQue_cusTOmer_PsPt_Card_number-input {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .unIQue_cusTOmer_PsPt_Card_text-input:focus,
        .unIQue_cusTOmer_PsPt_Card_number-input:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }

        .unIQue_cusTOmer_PsPt_Card_hidden {
            display: none;
        }

        .unIQue_cusTOmer_PsPt_Card_arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }

        .unIQue_cusTOmer_PsPt_Card_category-toggle.unIQue_cusTOmer_PsPt_Card_active .unIQue_cusTOmer_PsPt_Card_arrow {
            transform: rotate(180deg);
        }

        .unIQue_cusTOmer_PsPt_Card_download-btn {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
            background-color: #17a2b8;
            color: white;
        }

        .unIQue_cusTOmer_PsPt_Card_download-btn:hover {
            background-color: #138496;
        }

        .unIQue_cusTOmer_PsPt_Card-display-area {
            width: 70%;
            padding: 0;
            border: 1px solid gray;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .unIQue_cusTOmer_PsPt_Card-side {
            position: relative;
            width: var(--unIQue_cusTOmer_PsPt_Card-card-width);
            height: var(--unIQue_cusTOmer_PsPt_Card-card-height);
            overflow: hidden;
            background-color: transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid black;
            border-radius: 8px;
        }

        .unIQue_cusTOmer_PsPt_Card-front {
            background: url('/docs/images/employee_namecard_landscape_front.png') no-repeat center center;
            background-size: cover;
        }

        .unIQue_cusTOmer_PsPt_Card-profile-pic {
            width: 43.5mm;
            height: 43.5mm;
            border-radius: 50%;
            border: 2px solid #17a2b8;
            position: absolute;
            pointer-events: auto;
            cursor: move;
            object-fit: cover;
        }

        .unIQue_cusTOmer_PsPt_Card-text {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-size: 4mm;
            color: #000;
            font-family: 'Times New Roman', 'Battambang', serif;
        }

        .unIQue_cusTOmer_PsPt_Card-front .unIQue_cusTOmer_PsPt_Card-text {
            color: #fff;
        }

        .unIQue_cusTOmer_PsPt_Card_qr-container {
            position: absolute;
            width: 70px;
            height: 70px;
            pointer-events: auto;
            cursor: move;
        }

        .unIQue_cusTOmer_PsPt_Card_qr-container div {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 8px;
        }

        .unIQue_cusTOmer_PsPt_Card_draggable {
            position: absolute;
            cursor: move;
            user-select: none;
            pointer-events: auto;
        }

        .unIQue_cusTOmer_PsPt_Card_selected {
            border: 1px solid cyan;
        }

        .unIQue_cusTOmer_PsPt_Card_qr-container.unIQue_cusTOmer_PsPt_Card_selected {
            border: 0.5px solid cyan;
        }

        .unIQue_cusTOmer_PsPt_Card_image-container {
            position: absolute;
            display: inline-block;
            pointer-events: auto;
        }

        .unIQue_cusTOmer_PsPt_Card_overlay-image {
            width: auto;
            height: auto;
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            display: block;
        }

        .unIQue_cusTOmer_PsPt_Card_overlay-image.unIQue_cusTOmer_PsPt_Card_square {
            border-radius: 0;
        }

        .unIQue_cusTOmer_PsPt_Card_overlay-image:not(.unIQue_cusTOmer_PsPt_Card_square) {
            border-radius: 50%;
        }

        .unIQue_cusTOmer_PsPt_Card_resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
            display: none;
        }

        .unIQue_cusTOmer_PsPt_Card_image-container.unIQue_cusTOmer_PsPt_Card_selected .unIQue_cusTOmer_PsPt_Card_resize-handle {
            display: block;
        }

        .unIQue_cusTOmer_PsPt_Card_resize-handle.unIQue_cusTOmer_PsPt_Card_top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }

        .unIQue_cusTOmer_PsPt_Card_resize-handle.unIQue_cusTOmer_PsPt_Card_top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }

        .unIQue_cusTOmer_PsPt_Card_resize-handle.unIQue_cusTOmer_PsPt_Card_bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }

        .unIQue_cusTOmer_PsPt_Card_resize-handle.unIQue_cusTOmer_PsPt_Card_bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }
    </style>
</head>
<body>
    <div class="unIQue_cusTOmer_PsPt_Card-editor-container">
        <div class="unIQue_cusTOmer_PsPt_Card_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="unIQue_cusTOmer_PsPt_Card_category-toggle" data-toggle="unIQue_cusTOmer_PsPt_Card_background-controls">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="unIQue_cusTOmer_PsPt_Card_arrow">▼</span>
            </div>
            <div class="unIQue_cusTOmer_PsPt_Card_category-content" id="unIQue_cusTOmer_PsPt_Card_background-controls">
                <div class="unIQue_cusTOmer_PsPt_Card_filter-group">
                    <select class="unIQue_cusTOmer_PsPt_Card_background-dropdown" id="unIQue_cusTOmer_PsPt_Card_backgroundDropdown">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/background1.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/background2.png">ផ្ទៃខាងក្រោយ ២</option>
                    </select>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button" id="unIQue_cusTOmer_PsPt_Card_uploadBackgroundBtn">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="unIQue_cusTOmer_PsPt_Card_backgroundUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_reset-button" id="unIQue_cusTOmer_PsPt_Card_resetBackgroundBtn">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="unIQue_cusTOmer_PsPt_Card_category-toggle" data-toggle="unIQue_cusTOmer_PsPt_Card_profile-controls">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="unIQue_cusTOmer_PsPt_Card_arrow">▼</span>
            </div>
            <div class="unIQue_cusTOmer_PsPt_Card_category-content" id="unIQue_cusTOmer_PsPt_Card_profile-controls">
                <div class="unIQue_cusTOmer_PsPt_Card_filter-group">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button" id="unIQue_cusTOmer_PsPt_Card_changeProfileBtn">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="unIQue_cusTOmer_PsPt_Card_profileUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_squareShapeBtn">រាងការ៉េ</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_circleShapeBtn">រាងមូល</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_reset-button" id="unIQue_cusTOmer_PsPt_Card_resetProfileBtn">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Controls -->
            <div class="unIQue_cusTOmer_PsPt_Card_category-toggle" data-toggle="unIQue_cusTOmer_PsPt_Card_text-controls">
                <i class="fas fa-font"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="unIQue_cusTOmer_PsPt_Card_arrow">▼</span>
            </div>
            <div class="unIQue_cusTOmer_PsPt_Card_category-content" id="unIQue_cusTOmer_PsPt_Card_text-controls">
                <div class="unIQue_cusTOmer_PsPt_Card_filter-group">
                    <input type="text" id="unIQue_cusTOmer_PsPt_Card_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="unIQue_cusTOmer_PsPt_Card_text-input">
                    <input type="color" id="unIQue_cusTOmer_PsPt_Card_textColorPicker" value="#000000" class="unIQue_cusTOmer_PsPt_Card_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ">
                    <select class="unIQue_cusTOmer_PsPt_Card_font-size-dropdown" id="unIQue_cusTOmer_PsPt_Card_fontSizeDropdown">
                        <option value="12">១២ ភីកសែល</option>
                        <option value="14">៱៤ ភីកសែល</option>
                        <option value="16">៱៦ ភីកសែល</option>
                        <option value="18">៱៨ ភីកសែល</option>
                        <option value="20">២០ ភីកសែល</option>
                        <option value="24">៲៤ ភីកសែល</option>
                        <option value="30">៣០ ភីកសែល</option>
                        <option value="36">៳៦ ភីកសែល</option>
                        <option value="custom">ផ្ទាល់ខ្លួន</option>
                    </select>
                    <input type="number" id="unIQue_cusTOmer_PsPt_Card_customFontSize" class="unIQue_cusTOmer_PsPt_Card_number-input unIQue_cusTOmer_PsPt_Card_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)" min="8">
                    <select class="unIQue_cusTOmer_PsPt_Card_font-style-dropdown" id="unIQue_cusTOmer_PsPt_Card_fontStyleDropdown">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button" id="unIQue_cusTOmer_PsPt_Card_textButton">បន្ថែមអត្ថបទ</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_reset-button" id="unIQue_cusTOmer_PsPt_Card_cancelTextBtn">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="unIQue_cusTOmer_PsPt_Card_category-toggle" data-toggle="unIQue_cusTOmer_PsPt_Card_image-overlay-controls">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="unIQue_cusTOmer_PsPt_Card_arrow">▼</span>
            </div>
            <div class="unIQue_cusTOmer_PsPt_Card_category-content" id="unIQue_cusTOmer_PsPt_Card_image-overlay-controls">
                <div class="unIQue_cusTOmer_PsPt_Card_filter-group">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button" id="unIQue_cusTOmer_PsPt_Card_addImageBtn">បន្ថែមរូបភាព</button>
                    <input type="file" id="unIQue_cusTOmer_PsPt_Card_imageUploadOverlay" accept="image/*" style="display: none;">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button" id="unIQue_cusTOmer_PsPt_Card_changeImageBtn">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="unIQue_cusTOmer_PsPt_Card_changeImageUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_imageSquareShapeBtn">រាងការ៉េ</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_imageCircleShapeBtn">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="unIQue_cusTOmer_PsPt_Card_category-toggle" data-toggle="unIQue_cusTOmer_PsPt_Card_element-controls">
                <i class="fas fa-cog"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="unIQue_cusTOmer_PsPt_Card_arrow">▼</span>
            </div>
            <div class="unIQue_cusTOmer_PsPt_Card_category-content" id="unIQue_cusTOmer_PsPt_Card_element-controls">
                <div class="unIQue_cusTOmer_PsPt_Card_filter-group">
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_reset-button" id="unIQue_cusTOmer_PsPt_Card_deleteSelectedElementsBtn" disabled>លុបធាតុជ្រើសរើស</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_bringForwardBtn" disabled>នាំមកមុខ</button>
                    <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_secondary-button" id="unIQue_cusTOmer_PsPt_Card_sendBackwardBtn" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="unIQue_cusTOmer_PsPt_Card_custom-button unIQue_cusTOmer_PsPt_Card_reset-button" id="unIQue_cusTOmer_PsPt_Card_resetAllBtn">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="unIQue_cusTOmer_PsPt_Card_download-btn" id="unIQue_cusTOmer_PsPt_Card_downloadBtn">បោះពុម្ពនាមប័ណ្ត<i class="fas fa-print" style="margin-left: 8px;"></i></button>
        </div>
        <div class="unIQue_cusTOmer_PsPt_Card-display-area">
            <div class="unIQue_cusTOmer_PsPt_Card-side unIQue_cusTOmer_PsPt_Card-front">
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable" style="left: 5mm; top: 1mm; font-size: 4mm;">
                    លេខកូដអតិថិជន: {{ $contact->contact_id ?? '-' }}
                </p>
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable" style="right: 5mm; top: 1mm; font-size: 4mm;">
                    លេខរៀង: {{ $contact->contact_id ?? '-' }}
                </p>
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable" style="left: 55mm; top: 6mm; font-size: 4mm;">
                    @lang('business.business_name'): {{ $contact->supplier_business_name ?? '-' }}
                </p>
                
                <img class="unIQue_cusTOmer_PsPt_Card-profile-pic unIQue_cusTOmer_PsPt_Card_draggable"
                    src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile"
                    style="left: 5mm; top: 9mm;">
            
                <h3 class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 14mm; font-size: 4mm;">
                    នាមត្រកូល: {{ $contact->last_name ?? '-' }}
                </h3>
            
                <h3 class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 20mm; font-size: 4mm;">
                    នាមខ្លួន: {{ $contact->first_name ?? '-' }}
                </h3>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 26mm; font-size: 4mm;">
                    @lang('customercardb1::contact.dob'): {{ $contact->dob ? @format_date($contact->dob) : '-' }}
                </p>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 32mm; font-size: 4mm;">
                    @lang('customercardb1::contact.gender'): {{ $contact->gender ? ucfirst($contact->gender) : '-' }}
                </p>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 38mm; font-size: 4mm;">
                    @lang('customercardb1::contact.register_date'): {{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}
                </p>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 44mm; font-size: 4mm;">
                    @lang('customercardb1::contact.expired_at'): {{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') : '-' }}
                </p>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 55mm; top: 50mm; font-size: 4mm;">
                    @lang('customercardb1::contact.mobile'): {{ $contact->mobile ?? '-' }}
                </p>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 45mm; top: 56mm; font-size: 4mm;">
                    <strong>@lang('business.address'):</strong>
                    {{ implode(', ', array_filter([
                        $contact->address_line_1,
                        $contact->address_line_2,
                        $contact->city,
                        $contact->state,
                        $contact->country,
                        $contact->zip_code
                    ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
                </p>
            
                <div class="unIQue_cusTOmer_PsPt_Card_qr-container unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 17mm; top: 55mm;">
                    <div id="unIQue_cusTOmer_PsPt_Card_qrCodeFront"></div>
                </div>
            
                <p class="unIQue_cusTOmer_PsPt_Card-text unIQue_cusTOmer_PsPt_Card_draggable"
                    style="left: 5mm; top: 75mm; font-size: 4mm;">
                    តំបន់គ្រប់គ្រង: {{ $contact->supplier_business_name ?? '-' }}
                </p>
            </div>            
        </div>
    </div>

    <script>
        const contactBusinessId = '<?php echo $contact->business_id; ?>';
        const contactId = '<?php echo $contact->id; ?>';
    </script>

    <script>
        (function() {
            let unIQue_cusTOmer_PsPt_Card_selectedElements = [];
            let unIQue_cusTOmer_PsPt_Card_textCounter = 0;
            let unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
            const STORAGE_KEY = 'unIQue_cusTOmer_PsPt_Card_state';
            const MOVE_STEP = 1;
            let unIQue_cusTOmer_PsPt_Card_zIndexCounter = 10;

            function unIQue_cusTOmer_PsPt_Card_toggleCategory(categoryId) {
                const content = document.getElementById(categoryId);
                if (!content) return;

                const toggle = content.previousElementSibling;
                const isActive = content.classList.contains('unIQue_cusTOmer_PsPt_Card_active');

                document.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_category-content').forEach(c => {
                    if (c !== content) {
                        c.classList.remove('unIQue_cusTOmer_PsPt_Card_active');
                        c.previousElementSibling.classList.remove('unIQue_cusTOmer_PsPt_Card_active');
                    }
                });

                content.classList.toggle('unIQue_cusTOmer_PsPt_Card_active', !isActive);
                toggle.classList.toggle('unIQue_cusTOmer_PsPt_Card_active', !isActive);
            }

            function unIQue_cusTOmer_PsPt_Card_updateTextButton() {
                const textInput = document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput');
                const textButton = document.getElementById('unIQue_cusTOmer_PsPt_Card_textButton');
                if (textInput.value.trim() === '' && unIQue_cusTOmer_PsPt_Card_editingTextElement) {
                    textButton.textContent = 'បន្ថែមអត្ថបទ';
                    unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
                } else if (unIQue_cusTOmer_PsPt_Card_editingTextElement) {
                    textButton.textContent = 'កែសម្រួលអត្ថបទ';
                } else {
                    textButton.textContent = 'បន្ថែមអត្ថបទ';
                }
            }

            function unIQue_cusTOmer_PsPt_Card_handleTextAction() {
                const textInput = document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput');
                const text = textInput.value.trim();
                const color = document.getElementById('unIQue_cusTOmer_PsPt_Card_textColorPicker').value;
                const fontSize = document.getElementById('unIQue_cusTOmer_PsPt_Card_fontSizeDropdown').value;
                const fontStyle = document.getElementById('unIQue_cusTOmer_PsPt_Card_fontStyleDropdown').value;
                const customFontSize = document.getElementById('unIQue_cusTOmer_PsPt_Card_customFontSize').value;

                if (!text) {
                    alert('សូមបញ្ចូលអត្ថបទ!');
                    return;
                }

                const size = fontSize === 'custom' ? (customFontSize || 12) + 'px' : fontSize + 'px';
                const side = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');

                if (unIQue_cusTOmer_PsPt_Card_editingTextElement) {
                    unIQue_cusTOmer_PsPt_Card_editingTextElement.textContent = text;
                    unIQue_cusTOmer_PsPt_Card_editingTextElement.style.color = color;
                    unIQue_cusTOmer_PsPt_Card_editingTextElement.style.fontSize = size;
                    unIQue_cusTOmer_PsPt_Card_editingTextElement.style.fontFamily = fontStyle;
                    textInput.value = '';
                    unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
                } else {
                    const textElement = document.createElement('p');
                    textElement.id = `unIQue_cusTOmer_PsPt_Card_text-element-${unIQue_cusTOmer_PsPt_Card_textCounter++}`;
                    textElement.classList.add('unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable', 'unIQue_cusTOmer_PsPt_Card_custom-element');
                    textElement.style.left = '50px';
                    textElement.style.top = '50px';
                    textElement.style.color = color;
                    textElement.style.fontSize = size;
                    textElement.style.fontFamily = fontStyle;
                    textElement.style.zIndex = unIQue_cusTOmer_PsPt_Card_zIndexCounter++;
                    textElement.textContent = text;
                    side.appendChild(textElement);
                    unIQue_cusTOmer_PsPt_Card_makeDraggable(textElement);
                    textInput.value = '';
                }
                unIQue_cusTOmer_PsPt_Card_updateTextButton();
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_generateQRCode(containerId, size) {
                const contactLink = `https://arimako.com/business/${contactBusinessId}/customer/${contactId}`;
                console.log('Generating QR for:', contactLink, 'Container:', containerId);
                const qrOptions = {
                    text: contactLink,
                    width: size,
                    height: size,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                };
                const qrContainer = document.getElementById(containerId);
                if (qrContainer) {
                    qrContainer.innerHTML = '';
                    try {
                        new QRCode(qrContainer, qrOptions);
                    } catch (e) {
                        console.error('Error generating QR code:', e);
                    }
                } else {
                    console.error('QR Container not found:', containerId);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_changeBackgroundFromDropdown() {
                const selectedBg = document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundDropdown').value;
                if (selectedBg) {
                    document.querySelector('.unIQue_cusTOmer_PsPt_Card-front').style.backgroundImage = `url(${selectedBg})`;
                    unIQue_cusTOmer_PsPt_Card_saveState();
                }
            }

            function unIQue_cusTOmer_PsPt_Card_changeBackground() {
                const file = document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundUpload').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        document.querySelector('.unIQue_cusTOmer_PsPt_Card-front').style.backgroundImage = `url(${e.target.result})`;
                        unIQue_cusTOmer_PsPt_Card_saveState();
                    };
                    reader.readAsDataURL(file);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_resetBackground() {
                const side = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');
                side.style.backgroundImage = `url('/docs/images/employee_namecard_landscape_front.png')`;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundDropdown').value = '';
                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundUpload').value = '';
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_changeProfilePic() {
                const file = document.getElementById('unIQue_cusTOmer_PsPt_Card_profileUpload').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const profilePic = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front .unIQue_cusTOmer_PsPt_Card-profile-pic');
                        if (profilePic) {
                            profilePic.src = e.target.result;
                            unIQue_cusTOmer_PsPt_Card_selectElement(profilePic);
                            unIQue_cusTOmer_PsPt_Card_saveState();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_resetProfile() {
                const profilePic = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front .unIQue_cusTOmer_PsPt_Card-profile-pic');
                if (profilePic) {
                    profilePic.src = '{{ $img_src ?? "https://via.placeholder.com/120" }}';
                    profilePic.style.width = '43.5mm';
                    profilePic.style.height = '43.5mm';
                    profilePic.style.borderRadius = '50%';
                    profilePic.style.border = '2px solid #17a2b8';
                    profilePic.style.left = '5mm';
                    profilePic.style.top = '5mm';
                    profilePic.dataset.deltaX = '0';
                    profilePic.dataset.deltaY = '0';
                    profilePic.style.transform = 'none';
                    document.getElementById('unIQue_cusTOmer_PsPt_Card_profileUpload').value = '';
                    unIQue_cusTOmer_PsPt_Card_selectElement(profilePic);
                    unIQue_cusTOmer_PsPt_Card_saveState();
                }
            }

            function unIQue_cusTOmer_PsPt_Card_toggleProfileShape(isSquare) {
                const profilePic = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front .unIQue_cusTOmer_PsPt_Card-profile-pic');
                if (profilePic) {
                    profilePic.style.borderRadius = isSquare ? '0' : '50%';
                    unIQue_cusTOmer_PsPt_Card_saveState();
                }
            }

            function unIQue_cusTOmer_PsPt_Card_addOverlayImage() {
                const file = document.getElementById('unIQue_cusTOmer_PsPt_Card_imageUploadOverlay').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const side = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');
                        const imageContainer = document.createElement('div');
                        imageContainer.className = 'unIQue_cusTOmer_PsPt_Card_image-container unIQue_cusTOmer_PsPt_Card_custom-element unIQue_cusTOmer_PsPt_Card_draggable';
                        imageContainer.id = `unIQue_cusTOmer_PsPt_Card_image-container-${unIQue_cusTOmer_PsPt_Card_textCounter++}`;
                        imageContainer.style.left = '100px';
                        imageContainer.style.top = '100px';
                        imageContainer.style.zIndex = unIQue_cusTOmer_PsPt_Card_zIndexCounter++;

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'unIQue_cusTOmer_PsPt_Card_overlay-image';
                        img.style.width = 'auto';
                        img.style.height = 'auto';
                        img.style.maxWidth = '200px';
                        img.style.maxHeight = '200px';
                        imageContainer.appendChild(img);

                        const createResizeHandle = className => {
                            const handle = document.createElement('div');
                            handle.className = `unIQue_cusTOmer_PsPt_Card_resize-handle unIQue_cusTOmer_PsPt_Card_${className}`;
                            handle.addEventListener('mousedown', unIQue_cusTOmer_PsPt_Card_initResize);
                            return handle;
                        };

                        imageContainer.appendChild(createResizeHandle('bottom-right'));
                        imageContainer.appendChild(createResizeHandle('bottom-left'));
                        imageContainer.appendChild(createResizeHandle('top-right'));
                        imageContainer.appendChild(createResizeHandle('top-left'));

                        side.appendChild(imageContainer);
                        unIQue_cusTOmer_PsPt_Card_makeDraggable(imageContainer);
                        unIQue_cusTOmer_PsPt_Card_selectElement(imageContainer);
                        document.getElementById('unIQue_cusTOmer_PsPt_Card_imageUploadOverlay').value = '';
                        unIQue_cusTOmer_PsPt_Card_saveState();
                    };
                    reader.readAsDataURL(file);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_changeOverlayImage() {
                const file = document.getElementById('unIQue_cusTOmer_PsPt_Card_changeImageUpload').files[0];
                if (file && unIQue_cusTOmer_PsPt_Card_selectedElements.length === 1 && unIQue_cusTOmer_PsPt_Card_selectedElements[0].classList.contains('unIQue_cusTOmer_PsPt_Card_image-container')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = unIQue_cusTOmer_PsPt_Card_selectedElements[0].querySelector('img');
                        if (img) {
                            img.src = e.target.result;
                            unIQue_cusTOmer_PsPt_Card_saveState();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_toggleImageShape(isSquare) {
                if (unIQue_cusTOmer_PsPt_Card_selectedElements.length === 1 && unIQue_cusTOmer_PsPt_Card_selectedElements[0].classList.contains('unIQue_cusTOmer_PsPt_Card_image-container')) {
                    const img = unIQue_cusTOmer_PsPt_Card_selectedElements[0].querySelector('img');
                    if (img) {
                        img.classList.toggle('unIQue_cusTOmer_PsPt_Card_square', isSquare);
                        unIQue_cusTOmer_PsPt_Card_saveState();
                    }
                }
            }

            function unIQue_cusTOmer_PsPt_Card_changeFontSize() {
                const fontSize = document.getElementById('unIQue_cusTOmer_PsPt_Card_fontSizeDropdown').value;
                const customFontSize = document.getElementById('unIQue_cusTOmer_PsPt_Card_customFontSize');
                customFontSize.classList.toggle('unIQue_cusTOmer_PsPt_Card_hidden', fontSize !== 'custom');
                if (unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_cusTOmer_PsPt_Card-text')) {
                        const size = fontSize === 'custom' ? (customFontSize.value || 6) + 'px' : fontSize + 'px';
                        element.style.fontSize = size;
                    }
                });
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_changeFontStyle() {
                const style = document.getElementById('unIQue_cusTOmer_PsPt_Card_fontStyleDropdown').value;
                if (unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_cusTOmer_PsPt_Card-text')) {
                        element.style.fontFamily = style;
                    }
                });
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_changeTextColor() {
                const color = document.getElementById('unIQue_cusTOmer_PsPt_Card_textColorPicker').value;
                if (unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_cusTOmer_PsPt_Card-text')) {
                        element.style.color = color;
                    }
                });
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_cancelText() {
                const textInput = document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput');
                textInput.value = '';
                unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
                unIQue_cusTOmer_PsPt_Card_updateTextButton();
                unIQue_cusTOmer_PsPt_Card_deselectAll();
            }

            function unIQue_cusTOmer_PsPt_Card_bringForward() {
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                    const currentZ = parseInt(element.style.zIndex) || 5;
                    element.style.zIndex = currentZ + 1;
                });
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_sendBackward() {
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                    const currentZ = parseInt(element.style.zIndex) || 5;
                    if (currentZ > 1) {
                        element.style.zIndex = currentZ - 1;
                    }
                });
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_deleteSelectedElements() {
                if (unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0) return alert('Please select elements to delete!');
                if (confirm('Are you sure you want to delete the selected elements?')) {
                    unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(element => {
                        if (element === unIQue_cusTOmer_PsPt_Card_editingTextElement) {
                            document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput').value = '';
                            unIQue_cusTOmer_PsPt_Card_updateTextButton();
                            unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
                        }
                        element.remove();
                    });
                    unIQue_cusTOmer_PsPt_Card_deselectAll();
                    unIQue_cusTOmer_PsPt_Card_saveState();
                }
            }

            function unIQue_cusTOmer_PsPt_Card_deselectAll() {
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(el => el.classList.remove('unIQue_cusTOmer_PsPt_Card_selected'));
                unIQue_cusTOmer_PsPt_Card_selectedElements = [];
                document.getElementById('unIQue_cusTOmer_PsPt_Card_bringForwardBtn').disabled = true;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_sendBackwardBtn').disabled = true;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_deleteSelectedElementsBtn').disabled = true;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput').value = '';
                unIQue_cusTOmer_PsPt_Card_updateTextButton();
                unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
            }

            function unIQue_cusTOmer_PsPt_Card_makeDraggable(item) {
                let isDragging = false;
                let startX, startY, deltaX = 0, deltaY = 0;

                const updatePosition = () => {
                    item.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                };

                const startDrag = (x, y, e) => {
                    if (e.target.classList.contains('unIQue_cusTOmer_PsPt_Card_resize-handle')) return;
                    isDragging = true;
                    startX = x;
                    startY = y;
                    deltaX = parseFloat(item.dataset.deltaX) || 0;
                    deltaY = parseFloat(item.dataset.deltaY) || 0;
                    unIQue_cusTOmer_PsPt_Card_selectElement(item, e.ctrlKey || e.metaKey);
                    item.style.transition = 'none';
                    item.focus();
                    e.preventDefault();
                };

                const drag = (x, y) => {
                    if (isDragging) {
                        deltaX = x - startX + (parseFloat(item.dataset.deltaX) || 0);
                        deltaY = y - startY + (parseFloat(item.dataset.deltaY) || 0);
                        requestAnimationFrame(updatePosition);
                    }
                };

                const stopDrag = () => {
                    if (isDragging) {
                        isDragging = false;
                        item.dataset.deltaX = deltaX;
                        item.dataset.deltaY = deltaY;
                        item.style.transition = 'transform 0.1s ease-out';
                        unIQue_cusTOmer_PsPt_Card_saveState();
                    }
                };

                item.addEventListener('mousedown', e => {
                    startDrag(e.clientX, e.clientY, e);
                    const moveHandler = e => drag(e.clientX, e.clientY);
                    const upHandler = () => {
                        stopDrag();
                        document.removeEventListener('mousemove', moveHandler);
                        document.removeEventListener('mouseup', upHandler);
                    };
                    document.addEventListener('mousemove', moveHandler);
                    document.addEventListener('mouseup', upHandler);
                });

                item.addEventListener('touchstart', e => {
                    const touch = e.touches[0];
                    startDrag(touch.clientX, touch.clientY, e);
                }, { passive: false });

                item.addEventListener('touchmove', e => {
                    if (isDragging) {
                        const touch = e.touches[0];
                        drag(touch.clientX, touch.clientY);
                        e.preventDefault();
                    }
                }, { passive: false });

                item.addEventListener('touchend', stopDrag);

                item.setAttribute('tabindex', '0');

                item.addEventListener('keydown', e => {
                    if (!unIQue_cusTOmer_PsPt_Card_selectedElements.includes(item)) return;
                    deltaX = parseFloat(item.dataset.deltaX) || 0;
                    deltaY = parseFloat(item.dataset.deltaY) || 0;

                    switch (e.key) {
                        case 'ArrowUp':
                            deltaY -= MOVE_STEP;
                            e.preventDefault();
                            break;
                        case 'ArrowDown':
                            deltaY += MOVE_STEP;
                            e.preventDefault();
                            break;
                        case 'ArrowLeft':
                            deltaX -= MOVE_STEP;
                            e.preventDefault();
                            break;
                        case 'ArrowRight':
                            deltaX += MOVE_STEP;
                            e.preventDefault();
                            break;
                        default:
                            return;
                    }

                    unIQue_cusTOmer_PsPt_Card_selectElement(item, e.ctrlKey || e.metaKey);
                    updatePosition();
                    item.dataset.deltaX = deltaX;
                    item.dataset.deltaY = deltaY;
                    unIQue_cusTOmer_PsPt_Card_saveState();
                });

                item.addEventListener('click', e => {
                    unIQue_cusTOmer_PsPt_Card_selectElement(item, e.ctrlKey || e.metaKey);
                    item.focus();
                    e.stopPropagation();
                });
            }

            function unIQue_cusTOmer_PsPt_Card_selectElement(element, addToSelection = false) {
                if (!element) {
                    unIQue_cusTOmer_PsPt_Card_deselectAll();
                    return;
                }
                if (!addToSelection) {
                    unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(el => el.classList.remove('unIQue_cusTOmer_PsPt_Card_selected'));
                    unIQue_cusTOmer_PsPt_Card_selectedElements = [element];
                } else {
                    if (unIQue_cusTOmer_PsPt_Card_selectedElements.includes(element)) {
                        unIQue_cusTOmer_PsPt_Card_selectedElements = unIQue_cusTOmer_PsPt_Card_selectedElements.filter(el => el !== element);
                        element.classList.remove('unIQue_cusTOmer_PsPt_Card_selected');
                    } else {
                        unIQue_cusTOmer_PsPt_Card_selectedElements.push(element);
                    }
                }
                unIQue_cusTOmer_PsPt_Card_selectedElements.forEach(el => el.classList.add('unIQue_cusTOmer_PsPt_Card_selected'));

                document.getElementById('unIQue_cusTOmer_PsPt_Card_bringForwardBtn').disabled = unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_sendBackwardBtn').disabled = unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0;
                document.getElementById('unIQue_cusTOmer_PsPt_Card_deleteSelectedElementsBtn').disabled = unIQue_cusTOmer_PsPt_Card_selectedElements.length === 0;

                const textInput = document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput');
                if (element.classList.contains('unIQue_cusTOmer_PsPt_Card-text') && !addToSelection) {
                    unIQue_cusTOmer_PsPt_Card_editingTextElement = element;
                    textInput.value = element.textContent.trim();
                    unIQue_cusTOmer_PsPt_Card_updateTextButton();
                } else if (!unIQue_cusTOmer_PsPt_Card_selectedElements.some(el => el.classList.contains('unIQue_cusTOmer_PsPt_Card-text'))) {
                    textInput.value = '';
                    unIQue_cusTOmer_PsPt_Card_updateTextButton();
                    unIQue_cusTOmer_PsPt_Card_editingTextElement = null;
                }
            }

            function unIQue_cusTOmer_PsPt_Card_initResize(e) {
                e.stopPropagation();
                const container = e.target.parentElement;
                const img = container.querySelector('img');
                const startX = e.clientX;
                const startY = e.clientY;
                const startWidth = parseInt(img.style.width) || img.offsetWidth;
                const startHeight = parseInt(img.style.height) || img.offsetHeight;
                const startLeft = parseInt(container.style.left) || 0;
                const startTop = parseInt(container.style.top) || 0;
                const handleClass = e.target.className.split(' ')[1].replace('unIQue_cusTOmer_PsPt_Card_', '');

                function doResize(e) {
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    let newWidth = startWidth;
                    let newHeight = startHeight;
                    let newLeft = startLeft;
                    let newTop = startTop;

                    if (handleClass === 'bottom-right') {
                        newWidth = startWidth + dx;
                        newHeight = startHeight + dy;
                    } else if (handleClass === 'bottom-left') {
                        newWidth = startWidth - dx;
                        newHeight = startHeight + dy;
                        newLeft = startLeft + dx;
                    } else if (handleClass === 'top-right') {
                        newWidth = startWidth + dx;
                        newHeight = startHeight - dy;
                        newTop = startTop + dy;
                    } else if (handleClass === 'top-left') {
                        newWidth = startWidth - dx;
                        newHeight = startHeight - dy;
                        newLeft = startLeft + dx;
                        newTop = startTop + dy;
                    }

                    if (newWidth > 20 && newWidth < 300) {
                        img.style.width = newWidth + 'px';
                        container.style.left = newLeft + 'px';
                    }
                    if (newHeight > 20 && newHeight < 300) {
                        img.style.height = newHeight + 'px';
                        container.style.top = newTop + 'px';
                    }
                }

                function stopResize() {
                    document.removeEventListener('mousemove', doResize);
                    document.removeEventListener('mouseup', stopResize);
                    unIQue_cusTOmer_PsPt_Card_saveState();
                }

                document.addEventListener('mousemove', doResize);
                document.addEventListener('mouseup', stopResize);
            }

            function unIQue_cusTOmer_PsPt_Card_resetLayout() {
                const frontCard = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');
                unIQue_cusTOmer_PsPt_Card_zIndexCounter = 10;

                frontCard.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_draggable').forEach(el => el.remove());

                const defaultFrontElements = [
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '5mm', top: '1mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: 'លេខកូដអតិថិជន: {{ $contact->contact_id ?? "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { right: '5mm', top: '1mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: 'លេខរៀង: {{ $contact->contact_id ?? "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '6mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("business.business_name"): {{ $contact->supplier_business_name ?? "-" }}'
                    },
                    {
                        tag: 'IMG',
                        classes: ['unIQue_cusTOmer_PsPt_Card-profile-pic', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '5mm', top: '9mm', width: '43.5mm', height: '43.5mm', borderRadius: '50%', border: '2px solid #17a2b8', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        src: '{{ $img_src ?? "https://via.placeholder.com/120" }}',
                        isProfile: true
                    },
                    {
                        tag: 'H3',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '14mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: 'នាមត្រកូល: {{ $contact->last_name ?? "-" }}'
                    },
                    {
                        tag: 'H3',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '20mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: 'នាមខ្លួន: {{ $contact->first_name ?? "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '26mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("lang_v1.dob"): {{ $contact->dob ? @format_date($contact->dob) : "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '32mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("lang_v1.gender"): {{ $contact->gender ? ucfirst($contact->gender) : "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '38mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("contact.register_date"): {{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format("d-m-Y") : "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '44mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("contact.expired_at"): {{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format("d-m-Y") : "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '55mm', top: '50mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '@lang("contact.mobile"): {{ $contact->mobile ?? "-" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '45mm', top: '56mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: '<strong>@lang("business.address"):</strong> {{ implode(", ", array_filter([$contact->address_line_1, $contact->address_line_2, $contact->city, $contact->state, $contact->country, $contact->zip_code], fn($value) => !is_null($value) && $value !== "")) ?: "-" }}'
                    },
                    {
                        tag: 'DIV',
                        classes: ['unIQue_cusTOmer_PsPt_Card_qr-container', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '17mm', top: '55mm', width: '70px', height: '70px', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        isQR: true,
                        id: 'unIQue_cusTOmer_PsPt_Card_qrCodeFront'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_cusTOmer_PsPt_Card-text', 'unIQue_cusTOmer_PsPt_Card_draggable'],
                        style: { left: '5mm', top: '75mm', fontSize: '4mm', zIndex: unIQue_cusTOmer_PsPt_Card_zIndexCounter++ },
                        text: 'តំបន់គ្រប់គ្រង: {{ $contact->supplier_business_name ?? "-" }}'
                    }
                ];

                const createElement = (data, container) => {
                    let element;
                    if (data.isProfile) {
                        element = document.createElement('img');
                        element.src = data.src;
                        element.alt = 'Profile';
                    } else if (data.isQR) {
                        element = document.createElement('div');
                        const qrDiv = document.createElement('div');
                        qrDiv.id = data.id;
                        element.appendChild(qrDiv);
                        setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode(data.id, 70), 500);
                    } else {
                        element = document.createElement(data.tag);
                        element.innerHTML = data.text;
                    }
                    element.className = data.classes.join(' ');
                    Object.assign(element.style, data.style);
                    container.appendChild(element);
                    unIQue_cusTOmer_PsPt_Card_makeDraggable(element);
                };

                defaultFrontElements.forEach(data => createElement(data, frontCard));

                frontCard.style.backgroundImage = `url('/docs/images/employee_namecard_landscape_front.png')`;
                unIQue_cusTOmer_PsPt_Card_deselectAll();
                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundDropdown').value = '';
                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundUpload').value = '';
                document.getElementById('unIQue_cusTOmer_PsPt_Card_profileUpload').value = '';
                document.getElementById('unIQue_cusTOmer_PsPt_Card_imageUploadOverlay').value = '';
                document.getElementById('unIQue_cusTOmer_PsPt_Card_changeImageUpload').value = '';
                unIQue_cusTOmer_PsPt_Card_textCounter = 0;
                unIQue_cusTOmer_PsPt_Card_saveState();
            }

            function unIQue_cusTOmer_PsPt_Card_saveState() {
                const frontSide = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');
                const state = {
                    elements: []
                };

                frontSide.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_draggable').forEach(el => {
                    const isProfile = el.classList.contains('unIQue_cusTOmer_PsPt_Card-profile-pic');
                    const isText = el.classList.contains('unIQue_cusTOmer_PsPt_Card-text');
                    const isQR = el.classList.contains('unIQue_cusTOmer_PsPt_Card_qr-container');
                    const isImageContainer = el.classList.contains('unIQue_cusTOmer_PsPt_Card_image-container');

                    const elData = {
                        id: el.id || `element_${Math.random().toString(36).substr(2, 9)}`, // Ensure unique ID
                        tagName: el.tagName,
                        classList: Array.from(el.classList),
                        style: {
                            left: el.style.left || '0px',
                            top: el.style.top || '0px',
                            zIndex: el.style.zIndex || '10',
                            transform: el.style.transform || '',
                            width: el.style.width || getComputedStyle(el).width,
                            height: el.style.height || getComputedStyle(el).height,
                            borderRadius: el.style.borderRadius || '',
                            border: el.style.border || ''
                        }
                    };

                    if (isText) {
                        elData.style.fontSize = el.style.fontSize || '';
                        elData.style.color = el.style.color || '';
                        elData.style.fontFamily = el.style.fontFamily || '';
                        elData.style.fontWeight = el.style.fontWeight || '';
                    } else if (isImageContainer) {
                        const img = el.querySelector('img');
                        elData.imageStyle = {
                            width: img.style.width || '',
                            height: img.style.height || '',
                            maxWidth: img.style.maxWidth || '',
                            maxHeight: img.style.maxHeight || ''
                        };
                        elData.isSquare = img.classList.contains('unIQue_cusTOmer_PsPt_Card_square');
                    } else if (isQR) {
                        elData.isQR = true;
                    }

                    state.elements.push(elData);
                });

                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    console.error('Error saving to localStorage:', e);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_loadState() {
                try {
                    const savedState = localStorage.getItem(STORAGE_KEY);
                    if (!savedState) {
                        setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode('unIQue_cusTOmer_PsPt_Card_qrCodeFront', 70), 500);
                        return;
                    }

                    const parsedState = JSON.parse(savedState);
                    const frontSide = document.querySelector('.unIQue_cusTOmer_PsPt_Card-front');
                    const existingElements = Array.from(frontSide.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_draggable'));

                    // Map existing elements by their type and content to preserve order
                    const elementMap = new Map();
                    existingElements.forEach(el => {
                        const key = el.classList.contains('unIQue_cusTOmer_PsPt_Card-profile-pic') ? 'profile-pic' :
                                    el.classList.contains('unIQue_cusTOmer_PsPt_Card_qr-container') ? 'qr-code' :
                                    el.classList.contains('unIQue_cusTOmer_PsPt_Card_image-container') ? `image-${el.id}` :
                                    `text-${el.textContent.trim()}`;
                        elementMap.set(key, el);
                    });

                    parsedState.elements.forEach(elData => {
                        let element;
                        const isProfile = elData.classList.includes('unIQue_cusTOmer_PsPt_Card-profile-pic');
                        const isQR = elData.isQR;
                        const isImageContainer = elData.classList.includes('unIQue_cusTOmer_PsPt_Card_image-container');
                        const isText = elData.classList.includes('unIQue_cusTOmer_PsPt_Card-text');

                        // Find matching existing element
                        const key = isProfile ? 'profile-pic' :
                        isQR ? 'qr-code' :
                        isImageContainer ? `image-${elData.id}` :
                        isText ? `text-${elData.id || elData.textContent?.trim()}` : elData.id;
                        element = elementMap.get(key);

                        if (element) {
                            // Update existing element's styles
                            Object.assign(element.style, elData.style);
                            element.dataset.deltaX = parseFloat(element.style.transform?.match(/translate\((-?\d+\.?\d*)/)?.[1]) || 0;
                            element.dataset.deltaY = parseFloat(element.style.transform?.match(/translate\([^,]+,\s*(-?\d+\.?\d*)/)?.[1]) || 0;
                            if (isImageContainer) {
                                const img = element.querySelector('img');
                                if (img && elData.imageStyle) {
                            Object.assign(img.style, elData.imageStyle);
                            img.classList.toggle('unIQue_cusTOmer_PsPt_Card_square', elData.isSquare);
                                }
                            }
                            unIQue_cusTOmer_PsPt_Card_makeDraggable(element);
                        } else if (isImageContainer) {
                            // Create new image container if not found
                            element = document.createElement('div');
                            element.id = elData.id;
                            element.className = elData.classList.join(' ');
                            const img = document.createElement('img');
                            img.className = 'unIQue_cusTOmer_PsPt_Card_overlay-image';
                            if (elData.isSquare) img.classList.add('unIQue_cusTOmer_PsPt_Card_square');
                            Object.assign(img.style, elData.imageStyle || {});
                            element.appendChild(img);

                            const createResizeHandle = className => {
                                const handle = document.createElement('div');
                                handle.className = `unIQue_cusTOmer_PsPt_Card_resize-handle unIQue_cusTOmer_PsPt_Card_${className}`;
                                handle.addEventListener('mousedown', unIQue_cusTOmer_PsPt_Card_initResize);
                                return handle;
                            };

                            element.appendChild(createResizeHandle('bottom-right'));
                            element.appendChild(createResizeHandle('bottom-left'));
                            element.appendChild(createResizeHandle('top-right'));
                            element.appendChild(createResizeHandle('top-left'));

                            frontSide.appendChild(element);
                            Object.assign(element.style, elData.style);
                            element.dataset.deltaX = parseFloat(element.style.transform?.match(/translate\((-?\d+\.?\d*)/)?.[1]) || 0;
                            element.dataset.deltaY = parseFloat(element.style.transform?.match(/translate\([^,]+,\s*(-?\d+\.?\d*)/)?.[1]) || 0;
                            unIQue_cusTOmer_PsPt_Card_makeDraggable(element);
                        }
                    });

                    // Generate QR code
                    if (elementMap.has('qr-code')) {
                        const qrContainer = elementMap.get('qr-code').querySelector('div');
                        if (qrContainer) {
                            setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode(qrContainer.id, 70), 500);
                        }
                    } else {
                        setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode('unIQue_cusTOmer_PsPt_Card_qrCodeFront', 70), 500);
                    }
                } catch (e) {
                    console.error('Error loading from localStorage:', e);
                    setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode('unIQue_cusTOmer_PsPt_Card_qrCodeFront', 70), 500);
                }
            }

            function unIQue_cusTOmer_PsPt_Card_downloadCard() {
                const DPI = 300;
                const MM_TO_PX = DPI / 25.4; // Conversion factor: pixels per millimeter at 300 DPI
                const root = document.documentElement;

                const cardWidthMM = parseFloat(getComputedStyle(root).getPropertyValue('--unIQue_cusTOmer_PsPt_Card-card-width'));
                const desiredWidth = cardWidthMM * MM_TO_PX;

                const sides = document.querySelectorAll('.unIQue_cusTOmer_PsPt_Card-side');

                const promises = Array.from(sides).map(side => {
                    const renderedWidth = side.offsetWidth; // Current width in pixels on screen
                    const scale = desiredWidth / renderedWidth; // Scale factor for 300 DPI output

                    return html2canvas(side, { scale: scale, useCORS: true }).then(canvas => {
                        const capturedWidth = canvas.width;
                        const capturedHeight = canvas.height;
                        const a4Width = 210 * MM_TO_PX; // A4 width in pixels at 300 DPI
                        const a4Height = 297 * MM_TO_PX; // A4 height in pixels at 300 DPI

                        // Create an A4-sized canvas
                        const a4Canvas = document.createElement('canvas');
                        a4Canvas.width = a4Width;
                        a4Canvas.height = a4Height;
                        const ctx = a4Canvas.getContext('2d');

                        // Fill A4 canvas with a white background
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, a4Width, a4Height);

                        // Center the captured card on the A4 canvas
                        const left = (a4Width - capturedWidth) / 2;
                        const top = (a4Height - capturedHeight) / 2;
                        ctx.drawImage(canvas, left, top);

                        return {
                            side: side.className.split(' ')[1], // Extract side identifier
                            canvas: a4Canvas
                        };
                    });
                });

                Promise.all(promises).then(results => {
                    results.forEach(({ side, canvas }) => {
                        const link = document.createElement('a');
                        link.href = canvas.toDataURL('image/png');
                        link.download = `namecard_${side}_a4.png`;
                        link.click();
                    });
                }).catch(err => {
                    console.error('Error generating images:', err);
                    alert('មានបញ្ហាក្នុងការបោះពុម្ពនាមប័ណ្ត។ សូមព្យាឯាមម្តងទៀត។');
                });
            }

            function unIQue_cusTOmer_PsPt_Card_init() {
                // Set up event listeners immediately, regardless of Font Awesome
                document.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_category-toggle').forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const targetId = toggle.getAttribute('data-toggle');
                        unIQue_cusTOmer_PsPt_Card_toggleCategory(targetId);
                    });
                });

                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundDropdown').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeBackgroundFromDropdown);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_uploadBackgroundBtn').addEventListener('click', () => {
                    document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundUpload').click();
                });
                document.getElementById('unIQue_cusTOmer_PsPt_Card_backgroundUpload').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeBackground);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_resetBackgroundBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_resetBackground);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_changeProfileBtn').addEventListener('click', () => {
                    document.getElementById('unIQue_cusTOmer_PsPt_Card_profileUpload').click();
                });
                document.getElementById('unIQue_cusTOmer_PsPt_Card_profileUpload').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeProfilePic);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_squareShapeBtn').addEventListener('click', () => unIQue_cusTOmer_PsPt_Card_toggleProfileShape(true));
                document.getElementById('unIQue_cusTOmer_PsPt_Card_circleShapeBtn').addEventListener('click', () => unIQue_cusTOmer_PsPt_Card_toggleProfileShape(false));
                document.getElementById('unIQue_cusTOmer_PsPt_Card_resetProfileBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_resetProfile);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_textButton').addEventListener('click', unIQue_cusTOmer_PsPt_Card_handleTextAction);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_textInput').addEventListener('input', unIQue_cusTOmer_PsPt_Card_updateTextButton);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_fontSizeDropdown').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeFontSize);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_customFontSize').addEventListener('input', unIQue_cusTOmer_PsPt_Card_changeFontSize);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_fontStyleDropdown').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeFontStyle);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_textColorPicker').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeTextColor);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_cancelTextBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_cancelText);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_addImageBtn').addEventListener('click', () => {
                    document.getElementById('unIQue_cusTOmer_PsPt_Card_imageUploadOverlay').click();
                });
                document.getElementById('unIQue_cusTOmer_PsPt_Card_imageUploadOverlay').addEventListener('change', unIQue_cusTOmer_PsPt_Card_addOverlayImage);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_changeImageBtn').addEventListener('click', () => {
                    document.getElementById('unIQue_cusTOmer_PsPt_Card_changeImageUpload').click();
                });
                document.getElementById('unIQue_cusTOmer_PsPt_Card_changeImageUpload').addEventListener('change', unIQue_cusTOmer_PsPt_Card_changeOverlayImage);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_imageSquareShapeBtn').addEventListener('click', () => unIQue_cusTOmer_PsPt_Card_toggleImageShape(true));
                document.getElementById('unIQue_cusTOmer_PsPt_Card_imageCircleShapeBtn').addEventListener('click', () => unIQue_cusTOmer_PsPt_Card_toggleImageShape(false));
                document.getElementById('unIQue_cusTOmer_PsPt_Card_bringForwardBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_bringForward);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_sendBackwardBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_sendBackward);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_deleteSelectedElementsBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_deleteSelectedElements);
                document.getElementById('unIQue_cusTOmer_PsPt_Card_resetAllBtn').addEventListener('click', () => {
                    unIQue_cusTOmer_PsPt_Card_resetLayout();
                });
                document.getElementById('unIQue_cusTOmer_PsPt_Card_downloadBtn').addEventListener('click', unIQue_cusTOmer_PsPt_Card_downloadCard);
                document.querySelector('.unIQue_cusTOmer_PsPt_Card-display-area').addEventListener('click', e => {
                    if (e.target === e.currentTarget || e.target.classList.contains('unIQue_cusTOmer_PsPt_Card-side')) {
                        unIQue_cusTOmer_PsPt_Card_deselectAll();
                    }
                });
                document.querySelectorAll('.unIQue_cusTOmer_PsPt_Card_draggable').forEach(item => {
                    unIQue_cusTOmer_PsPt_Card_makeDraggable(item);
                });

                // Handle initialization
                setTimeout(() => unIQue_cusTOmer_PsPt_Card_generateQRCode('unIQue_cusTOmer_PsPt_Card_qrCodeFront', 70), 500);
                unIQue_cusTOmer_PsPt_Card_loadState();
            }

            window.addEventListener('DOMContentLoaded', unIQue_cusTOmer_PsPt_Card_init);
        })();
    </script>
</body>
</html>