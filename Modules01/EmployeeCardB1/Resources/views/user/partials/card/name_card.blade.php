<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Namecard Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --unIQue_empLOyee_Name_Card-card-width: 120mm;
            --unIQue_empLOyee_Name_Card-card-height: 70mm;
        }

        .unIQue_empLOyee_Name_Card-editor-container * {
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

        .unIQue_empLOyee_Name_Card-editor-container {
            font-family: Arial, 'Battambang', sans-serif;
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            gap: 5px;
            background-color: #f5f5f5;
        }

        .unIQue_empLOyee_Name_Card_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }

        .unIQue_empLOyee_Name_Card_filters-container h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }

        .unIQue_empLOyee_Name_Card_filters-container hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }

        .unIQue_empLOyee_Name_Card_category-toggle {
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

        .unIQue_empLOyee_Name_Card_category-toggle:hover {
            background-color: #c8d8ff;
        }

        .unIQue_empLOyee_Name_Card_category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .unIQue_empLOyee_Name_Card_category-content.unIQue_empLOyee_Name_Card_active {
            max-height: 500px;
            padding: 10px;
        }

        .unIQue_empLOyee_Name_Card_filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .unIQue_empLOyee_Name_Card_background-dropdown,
        .unIQue_empLOyee_Name_Card_font-size-dropdown,
        .unIQue_empLOyee_Name_Card_font-style-dropdown {
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

        .unIQue_empLOyee_Name_Card_custom-button {
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

        .unIQue_empLOyee_Name_Card_custom-button:hover {
            background-color: #138496;
        }

        .unIQue_empLOyee_Name_Card_custom-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .unIQue_empLOyee_Name_Card_reset-button {
            background-color: #dc3545;
            color: white;
        }

        .unIQue_empLOyee_Name_Card_reset-button:hover {
            background-color: #c82333;
        }

        .unIQue_empLOyee_Name_Card_secondary-button {
            background-color: #6c757d;
            color: white;
        }

        .unIQue_empLOyee_Name_Card_secondary-button:hover {
            background-color: #5a6268;
        }

        .unIQue_empLOyee_Name_Card_text-input,
        .unIQue_empLOyee_Name_Card_color-picker,
        .unIQue_empLOyee_Name_Card_number-input {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .unIQue_empLOyee_Name_Card_text-input:focus,
        .unIQue_empLOyee_Name_Card_number-input:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }

        .unIQue_empLOyee_Name_Card_hidden {
            display: none;
        }

        .unIQue_empLOyee_Name_Card_arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }

        .unIQue_empLOyee_Name_Card_category-toggle.unIQue_empLOyee_Name_Card_active .unIQue_empLOyee_Name_Card_arrow {
            transform: rotate(180deg);
        }

        .unIQue_empLOyee_Name_Card_download-btn {
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

        .unIQue_empLOyee_Name_Card_download-btn:hover {
            background-color: #138496;
        }

        .unIQue_empLOyee_Name_Card-display-area {
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

        .unIQue_empLOyee_Name_Card-side {
            position: relative;
            width: var(--unIQue_empLOyee_Name_Card-card-width);
            height: var(--unIQue_empLOyee_Name_Card-card-height);
            overflow: hidden;
            background-color: transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid black;
            border-radius: 8px;
        }

        .unIQue_empLOyee_Name_Card-front {
            background: url('/docs/images/employee_namecard_landscape_front.png') no-repeat center center;
            background-size: cover;
        }

        .unIQue_empLOyee_Name_Card-back {
            background: url('/docs/images/employee_namecard_landscape_back.png') no-repeat center center;
            background-size: cover;
        }

        .unIQue_empLOyee_Name_Card-profile-pic {
            width: 43.5mm;
            height: 43.5mm;
            border-radius: 50%;
            border: 2px solid #17a2b8;
            position: absolute;
            pointer-events: auto;
            cursor: move;
            object-fit: cover;
        }

        .unIQue_empLOyee_Name_Card-text {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-size: 4mm;
            color: #000;
            font-family: 'Times New Roman', 'Battambang', serif;
        }

        .unIQue_empLOyee_Name_Card-front .unIQue_empLOyee_Name_Card-text {
            color: #fff;
        }

        .unIQue_empLOyee_Name_Card_qr-container {
            position: absolute;
            width: 70px;
            height: 70px;
            pointer-events: auto;
            cursor: move;
        }

        .unIQue_empLOyee_Name_Card_qr-container div {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 8px;
        }

        .unIQue_empLOyee_Name_Card_draggable {
            position: absolute;
            cursor: move;
            user-select: none;
            pointer-events: auto;
        }

        .unIQue_empLOyee_Name_Card_selected {
            border: 1px solid cyan;
        }

        .unIQue_empLOyee_Name_Card_qr-container.unIQue_empLOyee_Name_Card_selected {
            border: 0.5px solid cyan;
        }

        .unIQue_empLOyee_Name_Card_image-container {
            position: absolute;
            display: inline-block;
            pointer-events: auto;
        }

        .unIQue_empLOyee_Name_Card_overlay-image {
            width: auto;
            height: auto;
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            display: block;
        }

        .unIQue_empLOyee_Name_Card_overlay-image.unIQue_empLOyee_Name_Card_square {
            border-radius: 0;
        }

        .unIQue_empLOyee_Name_Card_overlay-image:not(.unIQue_empLOyee_Name_Card_square) {
            border-radius: 50%;
        }

        .unIQue_empLOyee_Name_Card_resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
            display: none;
        }

        .unIQue_empLOyee_Name_Card_image-container.unIQue_empLOyee_Name_Card_selected .unIQue_empLOyee_Name_Card_resize-handle {
            display: block;
        }

        .unIQue_empLOyee_Name_Card_resize-handle.unIQue_empLOyee_Name_Card_top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }

        .unIQue_empLOyee_Name_Card_resize-handle.unIQue_empLOyee_Name_Card_top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }

        .unIQue_empLOyee_Name_Card_resize-handle.unIQue_empLOyee_Name_Card_bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }

        .unIQue_empLOyee_Name_Card_resize-handle.unIQue_empLOyee_Name_Card_bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }
    </style>
</head>
<body>
    <div class="unIQue_empLOyee_Name_Card-editor-container">
        <div class="unIQue_empLOyee_Name_Card_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Profile Picture Controls -->
            <div class="unIQue_empLOyee_Name_Card_category-toggle" data-unIQue_empLOyee_Name_Card-toggle="unIQue_empLOyee_Name_Card_profile-controls">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="unIQue_empLOyee_Name_Card_arrow">▼</span>
            </div>
            <div class="unIQue_empLOyee_Name_Card_category-content" id="unIQue_empLOyee_Name_Card_profile-controls">
                <div class="unIQue_empLOyee_Name_Card_filter-group">
                    <button class="unIQue_empLOyee_Name_Card_custom-button" id="unIQue_empLOyee_Name_Card_changeProfileBtn">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="unIQue_empLOyee_Name_Card_profileUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_squareShapeBtn">រាងការ៉េ</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_circleShapeBtn">រាងមូល</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_reset-button" id="unIQue_empLOyee_Name_Card_resetProfileBtn">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Background Controls -->
            <div class="unIQue_empLOyee_Name_Card_category-toggle" data-unIQue_empLOyee_Name_Card-toggle="unIQue_empLOyee_Name_Card_background-controls">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="unIQue_empLOyee_Name_Card_arrow">▼</span>
            </div>
            <div class="unIQue_empLOyee_Name_Card_category-content" id="unIQue_empLOyee_Name_Card_background-controls">
                <div class="unIQue_empLOyee_Name_Card_filter-group">
                    <select class="unIQue_empLOyee_Name_Card_background-dropdown" id="unIQue_empLOyee_Name_Card_sideSelector">
                        <option value="front">ផ្នែកខាងមុខ</option>
                        <option value="back">ផ្នែកខាងក្រោយ</option>
                    </select>
                    <select class="unIQue_empLOyee_Name_Card_background-dropdown" id="unIQue_empLOyee_Name_Card_backgroundDropdown">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/background1.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/background2.png">ផ្ទៃខាងក្រោយ ២</option>
                    </select>
                    <button class="unIQue_empLOyee_Name_Card_custom-button" id="unIQue_empLOyee_Name_Card_uploadBackgroundBtn">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="unIQue_empLOyee_Name_Card_backgroundUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_reset-button" id="unIQue_empLOyee_Name_Card_resetBackgroundBtn">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Controls -->
            <div class="unIQue_empLOyee_Name_Card_category-toggle" data-unIQue_empLOyee_Name_Card-toggle="unIQue_empLOyee_Name_Card_text-controls">
                <i class="fas fa-font"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="unIQue_empLOyee_Name_Card_arrow">▼</span>
            </div>
            <div class="unIQue_empLOyee_Name_Card_category-content" id="unIQue_empLOyee_Name_Card_text-controls">
                <div class="unIQue_empLOyee_Name_Card_filter-group">
                    <input type="text" id="unIQue_empLOyee_Name_Card_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="unIQue_empLOyee_Name_Card_text-input">
                    <input type="color" id="unIQue_empLOyee_Name_Card_textColorPicker" value="#000000" class="unIQue_empLOyee_Name_Card_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ">
                    <select class="unIQue_empLOyee_Name_Card_font-size-dropdown" id="unIQue_empLOyee_Name_Card_fontSizeDropdown">
                        <option value="12">១២ ភីកសែល</option>
                        <option value="14">១៤ ភីកសែល</option>
                        <option value="16">១៦ ភីកសែល</option>
                        <option value="18">១៨ ភីកសែល</option>
                        <option value="20">២០ ភីកសែល</option>
                        <option value="24">២៤ ភីកសែល</option>
                        <option value="30">៣០ ភីកសែល</option>
                        <option value="36">៣៦ ភីកសែល</option>
                        <option value="custom">ផ្ទាល់ខ្លួន</option>
                    </select>
                    <input type="number" id="unIQue_empLOyee_Name_Card_customFontSize" class="unIQue_empLOyee_Name_Card_number-input unIQue_empLOyee_Name_Card_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)" min="8">
                    <select class="unIQue_empLOyee_Name_Card_font-style-dropdown" id="unIQue_empLOyee_Name_Card_fontStyleDropdown">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="unIQue_empLOyee_Name_Card_custom-button" id="unIQue_empLOyee_Name_Card_textButton">បន្ថែមអត្ថបទ</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_reset-button" id="unIQue_empLOyee_Name_Card_cancelTextBtn">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="unIQue_empLOyee_Name_Card_category-toggle" data-unIQue_empLOyee_Name_Card-toggle="unIQue_empLOyee_Name_Card_image-overlay-controls">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="unIQue_empLOyee_Name_Card_arrow">▼</span>
            </div>
            <div class="unIQue_empLOyee_Name_Card_category-content" id="unIQue_empLOyee_Name_Card_image-overlay-controls">
                <div class="unIQue_empLOyee_Name_Card_filter-group">
                    <button class="unIQue_empLOyee_Name_Card_custom-button" id="unIQue_empLOyee_Name_Card_addImageBtn">បន្ថែមរូបភាព</button>
                    <input type="file" id="unIQue_empLOyee_Name_Card_imageUploadOverlay" accept="image/*" style="display: none;">
                    <button class="unIQue_empLOyee_Name_Card_custom-button" id="unIQue_empLOyee_Name_Card_changeImageBtn">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="unIQue_empLOyee_Name_Card_changeImageUpload" accept="image/*" style="display: none;">
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_imageSquareShapeBtn">រាងការ៉េ</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_imageCircleShapeBtn">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="unIQue_empLOyee_Name_Card_category-toggle" data-unIQue_empLOyee_Name_Card-toggle="unIQue_empLOyee_Name_Card_element-controls">
                <i class="fas fa-cog"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="unIQue_empLOyee_Name_Card_arrow">▼</span>
            </div>
            <div class="unIQue_empLOyee_Name_Card_category-content" id="unIQue_empLOyee_Name_Card_element-controls">
                <div class="unIQue_empLOyee_Name_Card_filter-group">
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_reset-button" id="unIQue_empLOyee_Name_Card_deleteSelectedElementsBtn" disabled>លុបធាតុជ្រើសរើស</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_bringForwardBtn" disabled>នាំមកមុខ</button>
                    <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_secondary-button" id="unIQue_empLOyee_Name_Card_sendBackwardBtn" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="unIQue_empLOyee_Name_Card_custom-button unIQue_empLOyee_Name_Card_reset-button" id="unIQue_empLOyee_Name_Card_resetAllBtn">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="unIQue_empLOyee_Name_Card_download-btn" id="unIQue_empLOyee_Name_Card_downloadBtn">បោះពុម្ពនាមប័ណ្ត<i class="fas fa-print" style="margin-left: 8px;"></i></button>
        </div>
        <div class="unIQue_empLOyee_Name_Card-display-area">
            <div class="unIQue_empLOyee_Name_Card-side unIQue_empLOyee_Name_Card-front">
                <img class="unIQue_empLOyee_Name_Card-profile-pic unIQue_empLOyee_Name_Card_draggable" src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile" style="left: 5mm; top: 5mm;">
                <h3 class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 60mm; top: 5mm; font-size: 5.5mm; font-weight: 600;">
                    <i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>
                    {{ $user->user_full_name ?? 'John Doe' }}
                </h3>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 60mm; top: 20mm; font-size: 4mm; color: #fff;" id="unIQue_empLOyee_Name_Card_designation_front">
                    <?php
                        $designation_front = __('lang_v1.designation') . '៖ ';
                        $designation_front .= ($user_designation->name ?? '') . ($user_department->name ?? '');
                        echo $designation_front;
                    ?>
                </p>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 60mm; top: 27mm; font-size: 4mm;">
                    <i class="fas fa-phone-square" style="margin-right: 10px; color: yellow;"></i>
                    {{ $user->contact_number ?? '+855 123 456 789' }}
                </p>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 60mm; top: 34mm; font-size: 4mm;">
                    <i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>
                    {{ $work_location->name ?? '' }}
                </p>
            </div>
            <div class="unIQue_empLOyee_Name_Card-side unIQue_empLOyee_Name_Card-back">
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 5mm; top: 16.5mm; font-size: 6mm;">
                    {{ $user->user_full_name ?? 'John Doe' }}
                </p>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 5mm; top: 33.5mm; font-size: 4mm;">
                    @lang('lang_v1.designation') ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}
                </p>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 5mm; top: 40.5mm; font-size: 4mm;">
                    <i class="fas fa-phone-square" style="margin-right: 10px; color: blue;"></i>
                    {{ $user->contact_number ?? '+855 123 456 789' }}
                </p>
                <p class="unIQue_empLOyee_Name_Card-text unIQue_empLOyee_Name_Card_draggable" style="left: 5mm; top: 47.5mm; font-size: 4mm;">
                    <i class="fas fa-briefcase" style="margin-right: 2mm; color: blue;"></i>
                    {{ $work_location->name ?? '' }}
                </p>
                <div class="unIQue_empLOyee_Name_Card_qr-container unIQue_empLOyee_Name_Card_draggable" style="left: 90mm; top: 45mm;">
                    <div id="uniIQue_cusTOmer_Name_Card_qrCodeBack"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            let unIQue_empLOyee_Name_Card_selectedElements = [];
            let unIQue_empLOyee_Name_Card_textCounter = 0;
            let unIQue_empLOyee_Name_Card_editingTextElement = null;
            const STORAGE_KEY = 'unIQue_empLOyee_Name_Card_state';
            const MOVE_STEP = 1;
            let unIQue_empLOyee_Name_Card_zIndexCounter = 10;
        
            function unIQue_empLOyee_Name_Card_toggleCategory(categoryId) {
                const content = document.getElementById(categoryId);
                const toggle = content.previousElementSibling;
                const allContents = document.querySelectorAll('.unIQue_empLOyee_Name_Card-editor-container .unIQue_empLOyee_Name_Card_category-content');
                const allToggles = document.querySelectorAll('.unIQue_empLOyee_Name_Card-editor-container .unIQue_empLOyee_Name_Card_category-toggle');
                const isActive = content.classList.contains('unIQue_empLOyee_Name_Card_active');
        
                allContents.forEach(c => {
                    c.classList.remove('unIQue_empLOyee_Name_Card_active');
                    c.previousElementSibling.classList.remove('unIQue_empLOyee_Name_Card_active');
                });
        
                if (!isActive) {
                    content.classList.add('unIQue_empLOyee_Name_Card_active');
                    toggle.classList.add('unIQue_empLOyee_Name_Card_active');
                }
            }
        
            function unIQue_empLOyee_Name_Card_getSelectedSide() {
                return document.getElementById('unIQue_empLOyee_Name_Card_sideSelector').value === 'front' ? '.unIQue_empLOyee_Name_Card-front' : '.unIQue_empLOyee_Name_Card-back';
            }
        
            function unIQue_empLOyee_Name_Card_updateTextButton() {
                const textInput = document.getElementById('unIQue_empLOyee_Name_Card_textInput');
                const textButton = document.getElementById('unIQue_empLOyee_Name_Card_textButton');
                if (textInput.value.trim() === '' && unIQue_empLOyee_Name_Card_editingTextElement) {
                    textButton.textContent = 'បន្ថែមអត្ថបទ';
                    unIQue_empLOyee_Name_Card_editingTextElement = null;
                } else if (unIQue_empLOyee_Name_Card_editingTextElement) {
                    textButton.textContent = 'កែសម្រួលអត្ថបទ';
                } else {
                    textButton.textContent = 'បន្ថែមអត្ថបទ';
                }
            }
        
            function unIQue_empLOyee_Name_Card_handleTextAction() {
                const textInput = document.getElementById('unIQue_empLOyee_Name_Card_textInput');
                const text = textInput.value.trim();
                const color = document.getElementById('unIQue_empLOyee_Name_Card_textColorPicker').value;
                const fontSize = document.getElementById('unIQue_empLOyee_Name_Card_fontSizeDropdown').value;
                const fontStyle = document.getElementById('unIQue_empLOyee_Name_Card_fontStyleDropdown').value;
                const customFontSize = document.getElementById('unIQue_empLOyee_Name_Card_customFontSize').value;
        
                if (!text) {
                    alert('សូមបញ្ចូលអត្ថបទ!');
                    return;
                }
        
                const size = fontSize === 'custom' ? (customFontSize || 12) + 'px' : fontSize + 'px';
                const side = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide());
        
                if (unIQue_empLOyee_Name_Card_editingTextElement) {
                    const icon = unIQue_empLOyee_Name_Card_editingTextElement.querySelector('i');                                             
                    if (icon) {                                                                                                               
                        unIQue_empLOyee_Name_Card_editingTextElement.innerHTML = icon.outerHTML + '' + text;                                 
                    } else {                                                                                                                  
                        unIQue_empLOyee_Name_Card_editingTextElement.textContent = text;                                                      
                    }
                    unIQue_empLOyee_Name_Card_editingTextElement.style.color = color;
                    unIQue_empLOyee_Name_Card_editingTextElement.style.fontSize = size;
                    unIQue_empLOyee_Name_Card_editingTextElement.style.fontFamily = fontStyle;
                    textInput.value = '';
                    unIQue_empLOyee_Name_Card_editingTextElement = null;
                } else {
                    const textElement = document.createElement('p');
                    textElement.id = `unIQue_empLOyee_Name_Card_text-element-${unIQue_empLOyee_Name_Card_textCounter++}`;
                    textElement.classList.add('unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable', 'unIQue_empLOyee_Name_Card_custom-element');
                    textElement.style.left = '50px';
                    textElement.style.top = '50px';
                    textElement.style.color = color;
                    textElement.style.fontSize = size;
                    textElement.style.fontFamily = fontStyle;
                    textElement.style.zIndex = unIQue_empLOyee_Name_Card_zIndexCounter++;
                    textElement.textContent = text;
                    side.appendChild(textElement);
                    unIQue_empLOyee_Name_Card_makeDraggable(textElement);
                    textInput.value = '';
                }
                unIQue_empLOyee_Name_Card_updateTextButton();
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_generateQRCode(containerId, size) {
                const employeeLink = "{{ url('/employee') }}/{{ $user->id ?? 0 }}";
                const qrOptions = {
                    text: employeeLink,
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
                }
            }
        
            function unIQue_empLOyee_Name_Card_changeBackgroundFromDropdown() {
                const selectedBg = document.getElementById('unIQue_empLOyee_Name_Card_backgroundDropdown').value;
                if (selectedBg) {
                    document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide()).style.backgroundImage = `url(${selectedBg})`;
                    unIQue_empLOyee_Name_Card_saveState();
                }
            }
        
            function unIQue_empLOyee_Name_Card_changeBackground() {
                const file = document.getElementById('unIQue_empLOyee_Name_Card_backgroundUpload').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide()).style.backgroundImage = `url(${e.target.result})`;
                        unIQue_empLOyee_Name_Card_saveState();
                    };
                    reader.readAsDataURL(file);
                }
            }
        
            function unIQue_empLOyee_Name_Card_resetBackground() {
                const side = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide());
                const defaultBg = unIQue_empLOyee_Name_Card_getSelectedSide() === '.unIQue_empLOyee_Name_Card-front' ? '/docs/images/employee_namecard_landscape_front.png' : '/docs/images/employee_namecard_landscape_back.png';
                side.style.backgroundImage = `url(${defaultBg})`;
                document.getElementById('unIQue_empLOyee_Name_Card_backgroundDropdown').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_backgroundUpload').value = '';
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_changeProfilePic() {
                const file = document.getElementById('unIQue_empLOyee_Name_Card_profileUpload').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const profilePic = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide() + ' .unIQue_empLOyee_Name_Card-profile-pic');
                        if (profilePic) {
                            profilePic.src = e.target.result;
                            unIQue_empLOyee_Name_Card_selectElement(profilePic);
                            unIQue_empLOyee_Name_Card_saveState();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }
        
            function unIQue_empLOyee_Name_Card_resetProfile() {
                const profilePic = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide() + ' .unIQue_empLOyee_Name_Card-profile-pic');
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
                    document.getElementById('unIQue_empLOyee_Name_Card_profileUpload').value = '';
                    unIQue_empLOyee_Name_Card_selectElement(profilePic);
                    unIQue_empLOyee_Name_Card_saveState();
                }
            }
        
            function unIQue_empLOyee_Name_Card_toggleProfileShape(isSquare) {
                const profilePic = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide() + ' .unIQue_empLOyee_Name_Card-profile-pic');
                if (profilePic) {
                    profilePic.style.borderRadius = isSquare ? '0' : '50%';
                    unIQue_empLOyee_Name_Card_saveState();
                }
            }
        
            function unIQue_empLOyee_Name_Card_addOverlayImage() {
                const file = document.getElementById('unIQue_empLOyee_Name_Card_imageUploadOverlay').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const side = document.querySelector(unIQue_empLOyee_Name_Card_getSelectedSide());
                        const imageContainer = document.createElement('div');
                        imageContainer.className = 'unIQue_empLOyee_Name_Card_image-container unIQue_empLOyee_Name_Card_custom-element unIQue_empLOyee_Name_Card_draggable';
                        imageContainer.id = `unIQue_empLOyee_Name_Card_image-container-${unIQue_empLOyee_Name_Card_textCounter++}`;
                        imageContainer.style.left = '100px';
                        imageContainer.style.top = '100px';
                        imageContainer.style.zIndex = unIQue_empLOyee_Name_Card_zIndexCounter++;
        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'unIQue_empLOyee_Name_Card_overlay-image';
                        img.style.width = 'auto';
                        img.style.height = 'auto';
                        img.style.maxWidth = '200px';
                        img.style.maxHeight = '200px';
                        imageContainer.appendChild(img);
        
                        const createResizeHandle = className => {
                            const handle = document.createElement('div');
                            handle.className = `unIQue_empLOyee_Name_Card_resize-handle unIQue_empLOyee_Name_Card_${className}`;
                            handle.addEventListener('mousedown', unIQue_empLOyee_Name_Card_initResize);
                            return handle;
                        };
        
                        imageContainer.appendChild(createResizeHandle('bottom-right'));
                        imageContainer.appendChild(createResizeHandle('bottom-left'));
                        imageContainer.appendChild(createResizeHandle('top-right'));
                        imageContainer.appendChild(createResizeHandle('top-left'));
        
                        side.appendChild(imageContainer);
                        unIQue_empLOyee_Name_Card_makeDraggable(imageContainer);
                        unIQue_empLOyee_Name_Card_selectElement(imageContainer);
                        document.getElementById('unIQue_empLOyee_Name_Card_imageUploadOverlay').value = '';
                        unIQue_empLOyee_Name_Card_saveState();
                    };
                    reader.readAsDataURL(file);
                }
            }
        
            function unIQue_empLOyee_Name_Card_changeOverlayImage() {
                const file = document.getElementById('unIQue_empLOyee_Name_Card_changeImageUpload').files[0];
                if (file && unIQue_empLOyee_Name_Card_selectedElements.length === 1 && unIQue_empLOyee_Name_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Name_Card_image-container')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = unIQue_empLOyee_Name_Card_selectedElements[0].querySelector('img');
                        if (img) {
                            img.src = e.target.result;
                            unIQue_empLOyee_Name_Card_saveState();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }
        
            function unIQue_empLOyee_Name_Card_toggleImageShape(isSquare) {
                if (unIQue_empLOyee_Name_Card_selectedElements.length === 1 && unIQue_empLOyee_Name_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Name_Card_image-container')) {
                    const img = unIQue_empLOyee_Name_Card_selectedElements[0].querySelector('img');
                    if (img) {
                        img.classList.toggle('unIQue_empLOyee_Name_Card_square', isSquare);
                        unIQue_empLOyee_Name_Card_saveState();
                    }
                }
            }
        
            function unIQue_empLOyee_Name_Card_changeFontSize() {
                const fontSize = document.getElementById('unIQue_empLOyee_Name_Card_fontSizeDropdown').value;
                const customFontSize = document.getElementById('unIQue_empLOyee_Name_Card_customFontSize');
                customFontSize.classList.toggle('unIQue_empLOyee_Name_Card_hidden', fontSize !== 'custom');
                if (unIQue_empLOyee_Name_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_empLOyee_Name_Card-text')) {
                        const size = fontSize === 'custom' ? (customFontSize.value || 6) + 'px' : fontSize + 'px';
                        element.style.fontSize = size;
                    }
                });
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_changeFontStyle() {
                const style = document.getElementById('unIQue_empLOyee_Name_Card_fontStyleDropdown').value;
                if (unIQue_empLOyee_Name_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_empLOyee_Name_Card-text')) {
                        element.style.fontFamily = style;
                    }
                });
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_changeTextColor() {
                const color = document.getElementById('unIQue_empLOyee_Name_Card_textColorPicker').value;
                if (unIQue_empLOyee_Name_Card_selectedElements.length === 0) return alert('Please select an element first!');
                unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                    if (element.classList.contains('unIQue_empLOyee_Name_Card-text')) {
                        element.style.color = color;
                    }
                });
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_cancelText() {
                const textInput = document.getElementById('unIQue_empLOyee_Name_Card_textInput');
                textInput.value = '';
                unIQue_empLOyee_Name_Card_editingTextElement = null;
                unIQue_empLOyee_Name_Card_updateTextButton();
                unIQue_empLOyee_Name_Card_deselectAll();
            }
        
            function unIQue_empLOyee_Name_Card_bringForward() {
                unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                    const currentZ = parseInt(element.style.zIndex) || 5;
                    element.style.zIndex = currentZ + 1;
                });
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_sendBackward() {
                unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                    const currentZ = parseInt(element.style.zIndex) || 5;
                    if (currentZ > 1) {
                        element.style.zIndex = currentZ - 1;
                    }
                });
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_deleteSelectedElements() {
                if (unIQue_empLOyee_Name_Card_selectedElements.length === 0) return alert('Please select elements to delete!');
                if (confirm('Are you sure you want to delete the selected elements?')) {
                    unIQue_empLOyee_Name_Card_selectedElements.forEach(element => {
                        if (element === unIQue_empLOyee_Name_Card_editingTextElement) {
                            document.getElementById('unIQue_empLOyee_Name_Card_textInput').value = '';
                            unIQue_empLOyee_Name_Card_updateTextButton();
                            unIQue_empLOyee_Name_Card_editingTextElement = null;
                        }
                        element.remove();
                    });
                    unIQue_empLOyee_Name_Card_deselectAll();
                    unIQue_empLOyee_Name_Card_saveState();
                }
            }
        
            function unIQue_empLOyee_Name_Card_deselectAll() {
                unIQue_empLOyee_Name_Card_selectedElements.forEach(el => el.classList.remove('unIQue_empLOyee_Name_Card_selected'));
                unIQue_empLOyee_Name_Card_selectedElements = [];
                document.getElementById('unIQue_empLOyee_Name_Card_bringForwardBtn').disabled = true;
                document.getElementById('unIQue_empLOyee_Name_Card_sendBackwardBtn').disabled = true;
                document.getElementById('unIQue_empLOyee_Name_Card_deleteSelectedElementsBtn').disabled = true;
                document.getElementById('unIQue_empLOyee_Name_Card_textInput').value = '';
                unIQue_empLOyee_Name_Card_updateTextButton();
                unIQue_empLOyee_Name_Card_editingTextElement = null;
            }
        
            function unIQue_empLOyee_Name_Card_makeDraggable(item) {
                let isDragging = false;
                let startX, startY, deltaX = 0, deltaY = 0;
        
                const updatePosition = () => {
                    item.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                };
        
                const startDrag = (x, y, e) => {
                    if (e.target.classList.contains('unIQue_empLOyee_Name_Card_resize-handle')) return;
                    isDragging = true;
                    startX = x;
                    startY = y;
                    deltaX = parseFloat(item.dataset.deltaX) || 0;
                    deltaY = parseFloat(item.dataset.deltaY) || 0;
                    unIQue_empLOyee_Name_Card_selectElement(item, e.ctrlKey || e.metaKey);
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
                        unIQue_empLOyee_Name_Card_saveState();
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
                    if (!unIQue_empLOyee_Name_Card_selectedElements.includes(item)) return;
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
        
                    unIQue_empLOyee_Name_Card_selectElement(item, e.ctrlKey || e.metaKey);
                    updatePosition();
                    item.dataset.deltaX = deltaX;
                    item.dataset.deltaY = deltaY;
                    unIQue_empLOyee_Name_Card_saveState();
                });
        
                item.addEventListener('click', e => {
                    unIQue_empLOyee_Name_Card_selectElement(item, e.ctrlKey || e.metaKey);
                    item.focus();
                    e.stopPropagation();
                });
            }
        
            function unIQue_empLOyee_Name_Card_selectElement(element, addToSelection = false) {
                if (!element) {
                    unIQue_empLOyee_Name_Card_deselectAll();
                    return;
                }
                if (!addToSelection) {
                    unIQue_empLOyee_Name_Card_selectedElements.forEach(el => el.classList.remove('unIQue_empLOyee_Name_Card_selected'));
                    unIQue_empLOyee_Name_Card_selectedElements = [element];
                } else {
                    if (unIQue_empLOyee_Name_Card_selectedElements.includes(element)) {
                        unIQue_empLOyee_Name_Card_selectedElements = unIQue_empLOyee_Name_Card_selectedElements.filter(el => el !== element);
                        element.classList.remove('unIQue_empLOyee_Name_Card_selected');
                    } else {
                        unIQue_empLOyee_Name_Card_selectedElements.push(element);
                    }
                }
                unIQue_empLOyee_Name_Card_selectedElements.forEach(el => el.classList.add('unIQue_empLOyee_Name_Card_selected'));
        
                document.getElementById('unIQue_empLOyee_Name_Card_bringForwardBtn').disabled = unIQue_empLOyee_Name_Card_selectedElements.length === 0;
                document.getElementById('unIQue_empLOyee_Name_Card_sendBackwardBtn').disabled = unIQue_empLOyee_Name_Card_selectedElements.length === 0;
                document.getElementById('unIQue_empLOyee_Name_Card_deleteSelectedElementsBtn').disabled = unIQue_empLOyee_Name_Card_selectedElements.length === 0;
        
                const textInput = document.getElementById('unIQue_empLOyee_Name_Card_textInput');
                const colorPicker = document.getElementById('unIQue_empLOyee_Name_Card_textColorPicker');
                const fontSizeDropdown = document.getElementById('unIQue_empLOyee_Name_Card_fontSizeDropdown');
                const customFontSizeInput = document.getElementById('unIQue_empLOyee_Name_Card_customFontSize');
                const fontStyleDropdown = document.getElementById('unIQue_empLOyee_Name_Card_fontStyleDropdown');

                if (element.classList.contains('unIQue_empLOyee_Name_Card-text') && !addToSelection) {
                    unIQue_empLOyee_Name_Card_editingTextElement = element;
                    
                    const clone = element.cloneNode(true);
                    const icon = clone.querySelector('i');
                    if (icon) {
                        icon.remove();
                    }
                    textInput.value = clone.textContent.trim();

                    const computedStyle = window.getComputedStyle(element);
                    
                    const rgbToHex = (rgb) => {
                        if (!rgb || !rgb.startsWith('rgb')) return '#000000';
                        const sep = rgb.includes(",") ? "," : " ";
                        const rgbValues = rgb.substr(4).split(")")[0].split(sep);

                        let r = (+rgbValues[0]).toString(16),
                            g = (+rgbValues[1]).toString(16),
                            b = (+rgbValues[2]).toString(16);

                        if (r.length === 1) r = "0" + r;
                        if (g.length === 1) g = "0" + g;
                        if (b.length === 1) b = "0" + b;

                        return "#" + r + g + b;
                    };
                    colorPicker.value = rgbToHex(computedStyle.color);

                    const fontSize = Math.round(parseFloat(computedStyle.fontSize));
                    const options = Array.from(fontSizeDropdown.options).map(opt => parseInt(opt.value, 10));
                    if (options.includes(fontSize)) {
                        fontSizeDropdown.value = fontSize;
                        customFontSizeInput.classList.add('unIQue_empLOyee_Name_Card_hidden');
                    } else {
                        fontSizeDropdown.value = 'custom';
                        customFontSizeInput.value = fontSize;
                        customFontSizeInput.classList.remove('unIQue_empLOyee_Name_Card_hidden');
                    }

                    const fontFamily = computedStyle.fontFamily.split(',')[0].replace(/['"]/g, '').trim();
                    let fontFound = false;
                    for (const option of fontStyleDropdown.options) {
                        if (option.value.toLowerCase() === fontFamily.toLowerCase()) {
                            fontStyleDropdown.value = option.value;
                            fontFound = true;
                            break;
                        }
                    }
                    if (!fontFound) {
                        const newOption = new Option(fontFamily, fontFamily, true, true);
                        fontStyleDropdown.add(newOption);
                    }

                    unIQue_empLOyee_Name_Card_updateTextButton();
                } else if (!unIQue_empLOyee_Name_Card_selectedElements.some(el => el.classList.contains('unIQue_empLOyee_Name_Card-text'))) {
                    textInput.value = '';
                    unIQue_empLOyee_Name_Card_editingTextElement = null;
                    unIQue_empLOyee_Name_Card_updateTextButton();
                    // Reset controls
                    colorPicker.value = '#000000';
                    fontSizeDropdown.value = '12';
                    customFontSizeInput.classList.add('unIQue_empLOyee_Name_Card_hidden');
                    customFontSizeInput.value = '';
                    fontStyleDropdown.value = 'Arial';
                }
            }
        
            function unIQue_empLOyee_Name_Card_initResize(e) {
                e.stopPropagation();
                const container = e.target.parentElement;
                const img = container.querySelector('img');
                const startX = e.clientX;
                const startY = e.clientY;
                const startWidth = parseInt(img.style.width) || img.offsetWidth;
                const startHeight = parseInt(img.style.height) || img.offsetHeight;
                const startLeft = parseInt(container.style.left) || 0;
                const startTop = parseInt(container.style.top) || 0;
                const handleClass = e.target.className.split(' ')[1].replace('unIQue_empLOyee_Name_Card_', '');
        
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
                    unIQue_empLOyee_Name_Card_saveState();
                }
        
                document.addEventListener('mousemove', doResize);
                document.addEventListener('mouseup', stopResize);
            }
        
            function unIQue_empLOyee_Name_Card_resetLayout() {
                const frontCard = document.querySelector('.unIQue_empLOyee_Name_Card-front');
                const backCard = document.querySelector('.unIQue_empLOyee_Name_Card-back');
                unIQue_empLOyee_Name_Card_zIndexCounter = 10;

                // Clear all existing draggable elements from both sides to prevent duplication
                frontCard.querySelectorAll('.unIQue_empLOyee_Name_Card_draggable').forEach(el => el.remove());
                backCard.querySelectorAll('.unIQue_empLOyee_Name_Card_draggable').forEach(el => el.remove());

                const defaultFrontElements = [
                    {
                        tag: 'IMG',
                        classes: ['unIQue_empLOyee_Name_Card-profile-pic', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '5mm', top: '5mm', width: '43.5mm', height: '43.5mm', borderRadius: '50%', border: '2px solid #17a2b8' },
                        src: '{{ $img_src ?? "https://via.placeholder.com/120" }}',
                        isProfile: true
                    },
                    {
                        tag: 'H3',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '60mm', top: '5mm', fontSize: '5.5mm', fontWeight: '600', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++, color: '#fff' },
                        text: '<i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>{{ $user->user_full_name ?? "John Doe" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '60mm', top: '20mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++, color: '#fff' },
                        text: '@lang("lang_v1.designation") ៖ {{ $user_designation->name ?? "" }}{{ $user_department->name ?? "" }}',
                        isDesignation: true
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '60mm', top: '27mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++, color: '#fff' },
                        text: '<i class="fas fa-phone-square" style="margin-right: 10px; color: yellow;"></i>{{ $user->contact_number ?? "+855 123 456 789" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '60mm', top: '34mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++, color: '#fff' },
                        text: '<i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>{{ $work_location->name ?? "" }}'
                    }
                ];

                const defaultBackElements = [
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '5mm', top: '16.5mm', fontSize: '6mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++ },
                        text: '{{ $user->user_full_name ?? "John Doe" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '5mm', top: '33.5mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++ },
                        text: '@lang("lang_v1.designation") ៖ {{ $user_designation->name ?? "" }}{{ $user_department->name ?? "" }}',
                        isDesignation: true
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '5mm', top: '40.5mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++ },
                        text: '<i class="fas fa-phone-square" style="margin-right: 10px; color: blue;"></i>{{ $user->contact_number ?? "+855 123 456 789" }}'
                    },
                    {
                        tag: 'P',
                        classes: ['unIQue_empLOyee_Name_Card-text', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '5mm', top: '47.5mm', fontSize: '4mm', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++ },
                        text: '<i class="fas fa-briefcase" style="margin-right: 2mm; color: blue;"></i>{{ $work_location->name ?? "" }}'
                    },
                    {
                        tag: 'DIV',
                        classes: ['unIQue_empLOyee_Name_Card_qr-container', 'unIQue_empLOyee_Name_Card_draggable'],
                        style: { left: '90mm', top: '45mm', width: '70px', height: '70px', zIndex: unIQue_empLOyee_Name_Card_zIndexCounter++ },
                        isQR: true,
                        id: 'unIQue_empLOyee_Name_Card_qrCodeBack'
                    }
                ];

                // Ensure no duplicate designation elements by checking existing elements
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
                        setTimeout(() => unIQue_empLOyee_Name_Card_generateQRCode(data.id, 70), 500);
                    } else {
                        // Check if a designation element already exists in the container
                        if (data.isDesignation) {
                            const existingDesignation = Array.from(container.querySelectorAll('.unIQue_empLOyee_Name_Card-text'))
                                .find(el => el.textContent.trim().startsWith('@lang("lang_v1.designation")'));
                            if (existingDesignation) return; // Skip adding if designation already exists
                        }
                        element = document.createElement(data.tag);
                        element.innerHTML = data.text;
                    }
                    element.className = data.classes.join(' ');
                    Object.assign(element.style, data.style);
                    container.appendChild(element);
                    unIQue_empLOyee_Name_Card_makeDraggable(element);
                };

                // Add front elements
                defaultFrontElements.forEach(data => createElement(data, frontCard));

                // Add back elements
                defaultBackElements.forEach(data => createElement(data, backCard));

                frontCard.style.backgroundImage = `url('/docs/images/employee_namecard_landscape_front.png')`;
                backCard.style.backgroundImage = `url('/docs/images/employee_namecard_landscape_back.png')`;

                document.getElementById('unIQue_empLOyee_Name_Card_backgroundDropdown').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_backgroundUpload').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_profileUpload').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_imageUploadOverlay').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_changeImageUpload').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_textInput').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_textColorPicker').value = '#000000';
                document.getElementById('unIQue_empLOyee_Name_Card_fontSizeDropdown').value = '12';
                document.getElementById('unIQue_empLOyee_Name_Card_customFontSize').value = '';
                document.getElementById('unIQue_empLOyee_Name_Card_customFontSize').classList.add('unIQue_empLOyee_Name_Card_hidden');
                document.getElementById('unIQue_empLOyee_Name_Card_fontStyleDropdown').value = 'Arial';
                unIQue_empLOyee_Name_Card_deselectAll();
                unIQue_empLOyee_Name_Card_saveState();
            }
        
            function unIQue_empLOyee_Name_Card_saveState() {
                const state = {
                    front: { backgroundImage: document.querySelector('.unIQue_empLOyee_Name_Card-front').style.backgroundImage, elements: [] },
                    back: { backgroundImage: document.querySelector('.unIQue_empLOyee_Name_Card-back').style.backgroundImage, elements: [] }
                };

                function saveElementData(el) {
                    const computedStyle = window.getComputedStyle(el);
                    const transform = computedStyle.transform;
                    let translateX = 0, translateY = 0;
                    if (transform && transform !== 'none') {
                        const matrix = transform.match(/matrix\((.+)\)/);
                        if (matrix) {
                            const values = matrix[1].split(',').map(parseFloat);
                            translateX = values[4];
                            translateY = values[5];
                        }
                    }
                    const leftPx = parseFloat(computedStyle.left) || 0;
                    const topPx = parseFloat(computedStyle.top) || 0;
                    const finalLeft = leftPx + translateX;
                    const finalTop = topPx + translateY;

                    const elementData = {
                        id: el.id || null,
                        tagName: el.tagName,
                        classList: Array.from(el.classList),
                        left: finalLeft,
                        top: finalTop,
                        zIndex: parseInt(computedStyle.zIndex) || 5,
                        fontSize: computedStyle.fontSize,
                        fontFamily: computedStyle.fontFamily,
                        fontWeight: computedStyle.fontWeight,
                        color: computedStyle.color,
                        textContent: el.textContent
                    };

                    if (el.classList.contains('unIQue_empLOyee_Name_Card-profile-pic')) {
                        // Do not save src to localStorage
                        elementData.width = computedStyle.width;
                        elementData.height = computedStyle.height;
                        elementData.borderRadius = computedStyle.borderRadius;
                        elementData.border = computedStyle.border;
                    } else if (el.classList.contains('unIQue_empLOyee_Name_Card_qr-container')) {
                        const qrCode = el.querySelector('div');
                        elementData.qrSize = qrCode ? qrCode.style.width : '70px';
                    } else if (el.classList.contains('unIQue_empLOyee_Name_Card_image-container')) {
                        const img = el.querySelector('img');
                        if (img) {
                            elementData.imageSrc = img.src;
                            elementData.imageWidth = img.style.width || computedStyle.width;
                            elementData.imageHeight = img.style.height || computedStyle.height;
                        }
                    }

                    return elementData;
                }

                document.querySelectorAll('.unIQue_empLOyee_Name_Card-front .unIQue_empLOyee_Name_Card_draggable').forEach(el => state.front.elements.push(saveElementData(el)));
                document.querySelectorAll('.unIQue_empLOyee_Name_Card-back .unIQue_empLOyee_Name_Card_draggable').forEach(el => state.back.elements.push(saveElementData(el)));

                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    console.error('Error saving to localStorage:', e);
                }
            }

            function unIQue_empLOyee_Name_Card_loadState() {
                const state = localStorage.getItem(STORAGE_KEY);
                if (!state) {
                    unIQue_empLOyee_Name_Card_resetLayout();
                    return;
                }

                try {
                    const parsedState = JSON.parse(state);
                    const frontSide = document.querySelector('.unIQue_empLOyee_Name_Card-front');
                    const backSide = document.querySelector('.unIQue_empLOyee_Name_Card-back');

                    document.querySelectorAll('.unIQue_empLOyee_Name_Card_custom-element').forEach(el => el.remove());

                    if (parsedState.front.backgroundImage) frontSide.style.backgroundImage = parsedState.front.backgroundImage;
                    if (parsedState.back.backgroundImage) backSide.style.backgroundImage = parsedState.back.backgroundImage;

                    function applyElementStyles(element, elData) {
                        element.style.position = 'absolute';
                        element.style.left = `${elData.left}px`;
                        element.style.top = `${elData.top}px`;
                        element.style.zIndex = elData.zIndex || '5';
                        element.style.fontSize = elData.fontSize || '';
                        element.style.fontFamily = elData.fontFamily || '';
                        element.style.fontWeight = elData.fontWeight || '';
                        element.style.color = elData.color || '';
                        element.style.transform = 'none';
                        element.dataset.deltaX = '0';
                        element.dataset.deltaY = '0';
                        unIQue_empLOyee_Name_Card_makeDraggable(element);
                    }

                    function processElements(elements, sideElement, sideSelector) {
                        const matchedElements = new Set();

                        elements.forEach(elData => {
                            let element;

                            if (elData.classList.includes('unIQue_empLOyee_Name_Card-profile-pic')) {
                                element = document.querySelector(sideSelector + ' .unIQue_empLOyee_Name_Card-profile-pic');
                                if (element && !matchedElements.has(element)) {
                                    // Do not override src; keep current src
                                    element.style.width = elData.width || '43.5mm';
                                    element.style.height = elData.height || '43.5mm';
                                    element.style.borderRadius = elData.borderRadius || '50%';
                                    element.style.border = elData.border || '2px solid #17a2b8';
                                    applyElementStyles(element, elData);
                                    matchedElements.add(element);
                                }
                            } else if (elData.classList.includes('unIQue_empLOyee_Name_Card_qr-container')) {
                                element = document.querySelector(sideSelector + ' .unIQue_empLOyee_Name_Card_qr-container');
                                if (element && !matchedElements.has(element)) {
                                    const qrCode = element.querySelector('div');
                                    if (qrCode) {
                                        const qrSize = parseFloat(elData.qrSize) || 70;
                                        qrCode.style.width = `${qrSize}px`;
                                        qrCode.style.height = `${qrSize}px`;
                                        unIQue_empLOyee_Name_Card_generateQRCode(qrCode.id, qrSize);
                                    }
                                    applyElementStyles(element, elData);
                                    matchedElements.add(element);
                                }
                            } else if (elData.classList.includes('unIQue_empLOyee_Name_Card-text') && !elData.classList.includes('unIQue_empLOyee_Name_Card_custom-element')) {
                                if (elData.id) {
                                    element = document.getElementById(elData.id);
                                }
                                if (!element) {
                                    const textElements = document.querySelectorAll(`${sideSelector} .unIQue_empLOyee_Name_Card-text:not(.unIQue_empLOyee_Name_Card_custom-element)`);
                                    element = Array.from(textElements).find(el => el.textContent.trim() === elData.textContent.trim() && !matchedElements.has(el));
                                }
                                if (element) {
                                    applyElementStyles(element, elData);
                                    matchedElements.add(element);
                                }
                            } else if (elData.classList.includes('unIQue_empLOyee_Name_Card_custom-element')) {
                                if (elData.classList.includes('unIQue_empLOyee_Name_Card_image-container')) {
                                    element = document.createElement('div');
                                    element.id = elData.id || `unIQue_empLOyee_Name_Card_image-container-${Date.now()}`;
                                    element.className = elData.classList.join(' ');
                                    const img = document.createElement('img');
                                    img.src = elData.imageSrc || '';
                                    img.className = 'unIQue_empLOyee_Name_Card_overlay-image';
                                    img.style.width = elData.imageWidth || 'auto';
                                    img.style.height = elData.imageHeight || 'auto';
                                    img.style.maxWidth = '200px';
                                    img.style.maxHeight = '200px';
                                    element.appendChild(img);

                                    const createResizeHandle = className => {
                                        const handle = document.createElement('div');
                                        handle.className = `unIQue_empLOyee_Name_Card_resize-handle unIQue_empLOyee_Name_Card_${className}`;
                                        handle.addEventListener('mousedown', unIQue_empLOyee_Name_Card_initResize);
                                        return handle;
                                    };

                                    element.appendChild(createResizeHandle('bottom-right'));
                                    element.appendChild(createResizeHandle('bottom-left'));
                                    element.appendChild(createResizeHandle('top-right'));
                                    element.appendChild(createResizeHandle('top-left'));

                                    sideElement.appendChild(element);
                                    applyElementStyles(element, elData);
                                } else if (elData.classList.includes('unIQue_empLOyee_Name_Card-text')) {
                                    element = document.createElement(elData.tagName || 'p');
                                    element.id = elData.id || `unIQue_empLOyee_Name_Card_text-element-${unIQue_empLOyee_Name_Card_textCounter++}`;
                                    element.className = elData.classList.join(' ');
                                    element.textContent = elData.textContent || '';
                                    sideElement.appendChild(element);
                                    applyElementStyles(element, elData);
                                }
                            }
                        });
                    }

                    processElements(parsedState.front.elements, frontSide, '.unIQue_empLOyee_Name_Card-front');
                    processElements(parsedState.back.elements, backSide, '.unIQue_empLOyee_Name_Card-back');
                } catch (e) {
                    console.error('Error loading from localStorage:', e);
                    unIQue_empLOyee_Name_Card_resetLayout();
                }
            }

            function unIQue_empLOyee_Name_Card_cloneElementWithStyles(sourceElement, scaleX, scaleY) {
                const newElement = sourceElement.cloneNode(true);
                newElement.className = sourceElement.className.replace('unIQue_empLOyee_Name_Card_selected', '');
                newElement.style.transition = 'none';

                const computedStyle = window.getComputedStyle(sourceElement);
                const leftPx = parseFloat(computedStyle.left) || 0;
                const topPx = parseFloat(computedStyle.top) || 0;
                const transform = computedStyle.transform;
                let translateX = 0, translateY = 0;
                if (transform && transform !== 'none') {
                    const matrix = transform.match(/matrix\((.+)\)/);
                    if (matrix) {
                        const values = matrix[1].split(',').map(parseFloat);
                        translateX = values[4];
                        translateY = values[5];
                    }
                }

                const finalLeftPx = leftPx + translateX;
                const finalTopPx = topPx + translateY;

                newElement.style.position = 'absolute';
                newElement.style.left = `${finalLeftPx * scaleX}px`;
                newElement.style.top = `${finalTopPx * scaleY}px`;
                newElement.style.zIndex = computedStyle.zIndex || '5';
                newElement.style.fontSize = computedStyle.fontSize ? `${parseFloat(computedStyle.fontSize) * scaleX}px` : '';
                newElement.style.fontFamily = computedStyle.fontFamily;
                newElement.style.fontWeight = computedStyle.fontWeight;
                newElement.style.color = computedStyle.color;
                newElement.style.margin = '0';

                if (sourceElement.classList.contains('unIQue_empLOyee_Name_Card-profile-pic')) {
                    const computedWidth = parseFloat(computedStyle.width);
                    const scale = Math.min(scaleX, scaleY);
                    newElement.style.width = `${computedWidth * scale}px`;
                    newElement.style.height = `${computedWidth * scale}px`;
                    newElement.style.borderRadius = computedStyle.borderRadius;
                    newElement.style.border = `${2 * scale}px solid #17a2b8`;
                    newElement.style.objectFit = 'cover';
                    newElement.src = sourceElement.src; // Use the current src of the profile picture
                } else if (sourceElement.classList.contains('unIQue_empLOyee_Name_Card_qr-container')) {
                    const qrSizePx = parseFloat(computedStyle.width) * scaleX;
                    const qrDiv = newElement.querySelector('div');
                    if (qrDiv) {
                        qrDiv.id = `unIQue_empLOyee_Name_Card_qrCode_clone_${Date.now()}`;
                        qrDiv.style.width = `${qrSizePx}px`;
                        qrDiv.style.height = `${qrSizePx}px`;
                        setTimeout(() => unIQue_empLOyee_Name_Card_generateQRCode(qrDiv.id, qrSizePx), 0);
                    }
                } else if (sourceElement.classList.contains('unIQue_empLOyee_Name_Card_image-container')) {
                    const img = newElement.querySelector('img');
                    if (img) {
                        const imgWidth = parseFloat(img.style.width || computedStyle.width);
                        const imgHeight = parseFloat(img.style.height || computedStyle.height);
                        img.style.width = `${imgWidth * scaleX}px`;
                        img.style.height = `${imgHeight * scaleY}px`;
                        img.style.maxWidth = `${200 * scaleX}px`;
                        img.style.maxHeight = `${200 * scaleY}px`;
                        img.style.borderRadius = computedStyle.borderRadius;
                    }
                }

                return newElement;
            }
        
            function unIQue_empLOyee_Name_Card_downloadCards() {
                const frontCard = document.querySelector('.unIQue_empLOyee_Name_Card-front');
                const backCard = document.querySelector('.unIQue_empLOyee_Name_Card-back');
                if (!frontCard || !backCard) {
                    alert('Error: Card sides not found!');
                    return;
                }
        
                const dpi = 300;
                const mmToPx = mm => mm * dpi / 25.4;
                const a4WidthPx = mmToPx(210);
                const a4HeightPx = mmToPx(297);
                const cardWidthPx = mmToPx(90);
                const cardHeightPx = mmToPx(55);
                const paddingPx = mmToPx(5.875);
                const columnGapPx = mmToPx(17);
                const rowGapPx = mmToPx(3);
        
                const originalWidthPx = frontCard.offsetWidth;
                const originalHeightPx = frontCard.offsetHeight;
                const scaleX = cardWidthPx / originalWidthPx;
                const scaleY = cardHeightPx / originalHeightPx;
        
                const container = document.createElement('div');
                container.style.width = `${a4WidthPx}px`;
                container.style.height = `${a4HeightPx}px`;
                container.style.backgroundColor = '#ffffff';
                container.style.position = 'absolute';
                container.style.left = '-9999px';
                container.style.top = '-9999px';
                container.style.padding = `${paddingPx}px`;
                container.style.boxSizing = 'border-box';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                document.body.appendChild(container);
        
                try {
                    for (let i = 0; i < 5; i++) {
                        const row = document.createElement('div');
                        row.style.height = `${cardHeightPx}px`;
                        row.style.display = 'flex';
                        row.style.justifyContent = 'flex-start';
                        row.style.gap = `${columnGapPx}px`;
                        row.style.marginBottom = i < 4 ? `${rowGapPx}px` : '0';
        
                        const frontClone = document.createElement('div');
                        frontClone.style.width = `${cardWidthPx}px`;
                        frontClone.style.height = `${cardHeightPx}px`;
                        frontClone.style.position = 'relative';
                        frontClone.style.overflow = 'hidden';
                        frontClone.style.backgroundImage = frontCard.style.backgroundImage || 'url(/docs/images/employee_namecard_landscape_front.png)';
                        frontClone.style.backgroundSize = 'cover';
                        frontClone.style.backgroundPosition = 'center';
                        frontClone.style.border = '1px solid black';
                        frontClone.style.borderRadius = '8px';
        
                        const backClone = document.createElement('div');
                        backClone.style.width = `${cardWidthPx}px`;
                        backClone.style.height = `${cardHeightPx}px`;
                        backClone.style.position = 'relative';
                        backClone.style.overflow = 'hidden';
                        backClone.style.backgroundImage = backCard.style.backgroundImage || 'url(/docs/images/employee_namecard_landscape_back.png)';
                        backClone.style.backgroundSize = 'cover';
                        backClone.style.backgroundPosition = 'center';
                        backClone.style.border = '1px solid black';
                        backClone.style.borderRadius = '8px';
        
                        const frontElements = frontCard.querySelectorAll('.unIQue_empLOyee_Name_Card_draggable');
                        frontElements.forEach(el => frontClone.appendChild(unIQue_empLOyee_Name_Card_cloneElementWithStyles(el, scaleX, scaleY)));
        
                        const backElements = backCard.querySelectorAll('.unIQue_empLOyee_Name_Card_draggable');
                        backElements.forEach(el => backClone.appendChild(unIQue_empLOyee_Name_Card_cloneElementWithStyles(el, scaleX, scaleY)));
        
                        row.appendChild(frontClone);
                        row.appendChild(backClone);
                        container.appendChild(row);
                    }
        
                    setTimeout(() => {
                        html2canvas(container, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
                            const link = document.createElement('a');
                            link.download = 'namecard-a4.png';
                            link.href = canvas.toDataURL('image/png', 1.0);
                            link.click();
                            document.body.removeChild(container);
                        }).catch(error => {
                            alert('Failed to generate PNG: ' + error.message);
                            document.body.removeChild(container);
                        });
                    }, 1500);
                } catch (error) {
                    alert('Error during download: ' + error.message);
                    document.body.removeChild(container);
                }
            }
        
            document.addEventListener('DOMContentLoaded', () => {
                unIQue_empLOyee_Name_Card_generateQRCode('unIQue_empLOyee_Name_Card_qrCodeFront', 70);
                unIQue_empLOyee_Name_Card_generateQRCode('unIQue_empLOyee_Name_Card_qrCodeBack', 70);
        
                const defaultElements = document.querySelectorAll('.unIQue_empLOyee_Name_Card-front .unIQue_empLOyee_Name_Card_draggable, .unIQue_empLOyee_Name_Card-back .unIQue_empLOyee_Name_Card_draggable');
                defaultElements.forEach(el => {
                    unIQue_empLOyee_Name_Card_makeDraggable(el);
                });
        
                if (localStorage.getItem(STORAGE_KEY)) {
                    unIQue_empLOyee_Name_Card_loadState();
                } else {
                    unIQue_empLOyee_Name_Card_resetLayout();
                }
        
                document.getElementById('unIQue_empLOyee_Name_Card_sideSelector').addEventListener('change', () => {});
                document.getElementById('unIQue_empLOyee_Name_Card_uploadBackgroundBtn').addEventListener('click', () => document.getElementById('unIQue_empLOyee_Name_Card_backgroundUpload').click());
                document.getElementById('unIQue_empLOyee_Name_Card_backgroundUpload').addEventListener('change', unIQue_empLOyee_Name_Card_changeBackground);
                document.getElementById('unIQue_empLOyee_Name_Card_backgroundDropdown').addEventListener('change', unIQue_empLOyee_Name_Card_changeBackgroundFromDropdown);
                document.getElementById('unIQue_empLOyee_Name_Card_resetBackgroundBtn').addEventListener('click', unIQue_empLOyee_Name_Card_resetBackground);
                document.getElementById('unIQue_empLOyee_Name_Card_changeProfileBtn').addEventListener('click', () => document.getElementById('unIQue_empLOyee_Name_Card_profileUpload').click());
                document.getElementById('unIQue_empLOyee_Name_Card_profileUpload').addEventListener('change', unIQue_empLOyee_Name_Card_changeProfilePic);
                document.getElementById('unIQue_empLOyee_Name_Card_squareShapeBtn').addEventListener('click', () => unIQue_empLOyee_Name_Card_toggleProfileShape(true));
                document.getElementById('unIQue_empLOyee_Name_Card_circleShapeBtn').addEventListener('click', () => unIQue_empLOyee_Name_Card_toggleProfileShape(false));
                document.getElementById('unIQue_empLOyee_Name_Card_resetProfileBtn').addEventListener('click', unIQue_empLOyee_Name_Card_resetProfile);
                document.getElementById('unIQue_empLOyee_Name_Card_textInput').addEventListener('input', unIQue_empLOyee_Name_Card_updateTextButton);
                document.getElementById('unIQue_empLOyee_Name_Card_textButton').addEventListener('click', unIQue_empLOyee_Name_Card_handleTextAction);
                document.getElementById('unIQue_empLOyee_Name_Card_fontSizeDropdown').addEventListener('change', unIQue_empLOyee_Name_Card_changeFontSize);
                document.getElementById('unIQue_empLOyee_Name_Card_fontStyleDropdown').addEventListener('change', unIQue_empLOyee_Name_Card_changeFontStyle);
                document.getElementById('unIQue_empLOyee_Name_Card_textColorPicker').addEventListener('change', unIQue_empLOyee_Name_Card_changeTextColor);
                document.getElementById('unIQue_empLOyee_Name_Card_cancelTextBtn').addEventListener('click', unIQue_empLOyee_Name_Card_cancelText);
                document.getElementById('unIQue_empLOyee_Name_Card_addImageBtn').addEventListener('click', () => document.getElementById('unIQue_empLOyee_Name_Card_imageUploadOverlay').click());
                document.getElementById('unIQue_empLOyee_Name_Card_imageUploadOverlay').addEventListener('change', unIQue_empLOyee_Name_Card_addOverlayImage);
                document.getElementById('unIQue_empLOyee_Name_Card_changeImageBtn').addEventListener('click', () => document.getElementById('unIQue_empLOyee_Name_Card_changeImageUpload').click());
                document.getElementById('unIQue_empLOyee_Name_Card_changeImageUpload').addEventListener('change', unIQue_empLOyee_Name_Card_changeOverlayImage);
                document.getElementById('unIQue_empLOyee_Name_Card_imageSquareShapeBtn').addEventListener('click', () => unIQue_empLOyee_Name_Card_toggleImageShape(true));
                document.getElementById('unIQue_empLOyee_Name_Card_imageCircleShapeBtn').addEventListener('click', () => unIQue_empLOyee_Name_Card_toggleImageShape(false));
                document.getElementById('unIQue_empLOyee_Name_Card_deleteSelectedElementsBtn').addEventListener('click', unIQue_empLOyee_Name_Card_deleteSelectedElements);
                document.getElementById('unIQue_empLOyee_Name_Card_bringForwardBtn').addEventListener('click', unIQue_empLOyee_Name_Card_bringForward);
                document.getElementById('unIQue_empLOyee_Name_Card_sendBackwardBtn').addEventListener('click', unIQue_empLOyee_Name_Card_sendBackward);
                document.getElementById('unIQue_empLOyee_Name_Card_resetAllBtn').addEventListener('click', unIQue_empLOyee_Name_Card_resetLayout);
                document.getElementById('unIQue_empLOyee_Name_Card_downloadBtn').addEventListener('click', unIQue_empLOyee_Name_Card_downloadCards);
        
                document.querySelectorAll('.unIQue_empLOyee_Name_Card_category-toggle').forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const categoryId = toggle.getAttribute('data-unIQue_empLOyee_Name_Card-toggle');
                        unIQue_empLOyee_Name_Card_toggleCategory(categoryId);
                    });
                });
        
                const cardSides = document.querySelectorAll('.unIQue_empLOyee_Name_Card-front, .unIQue_empLOyee_Name_Card-back');
                cardSides.forEach(side => {
                    side.addEventListener('click', e => {
                        if (e.target === side) {
                            unIQue_empLOyee_Name_Card_deselectAll();
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const designationFront = document.getElementById('unIQue_empLOyee_Name_Card_designation_front');
                if (designationFront) {
                    unIQue_empLOyee_Name_Card_makeDraggable(designationFront);
                }
            });
        
            window.unIQue_empLOyee_Name_Card_toggleCategory = unIQue_empLOyee_Name_Card_toggleCategory;
            window.unIQue_empLOyee_Name_Card_getSelectedSide = unIQue_empLOyee_Name_Card_getSelectedSide;
            window.unIQue_empLOyee_Name_Card_updateTextButton = unIQue_empLOyee_Name_Card_updateTextButton;
            window.unIQue_empLOyee_Name_Card_handleTextAction = unIQue_empLOyee_Name_Card_handleTextAction;
            window.unIQue_empLOyee_Name_Card_generateQRCode = unIQue_empLOyee_Name_Card_generateQRCode;
            window.unIQue_empLOyee_Name_Card_changeBackgroundFromDropdown = unIQue_empLOyee_Name_Card_changeBackgroundFromDropdown;
            window.unIQue_empLOyee_Name_Card_changeBackground = unIQue_empLOyee_Name_Card_changeBackground;
            window.unIQue_empLOyee_Name_Card_resetBackground = unIQue_empLOyee_Name_Card_resetBackground;
            window.unIQue_empLOyee_Name_Card_changeProfilePic = unIQue_empLOyee_Name_Card_changeProfilePic;
            window.unIQue_empLOyee_Name_Card_resetProfile = unIQue_empLOyee_Name_Card_resetProfile;
            window.unIQue_empLOyee_Name_Card_toggleProfileShape = unIQue_empLOyee_Name_Card_toggleProfileShape;
            window.unIQue_empLOyee_Name_Card_addOverlayImage = unIQue_empLOyee_Name_Card_addOverlayImage;
            window.unIQue_empLOyee_Name_Card_changeOverlayImage = unIQue_empLOyee_Name_Card_changeOverlayImage;
            window.unIQue_empLOyee_Name_Card_toggleImageShape = unIQue_empLOyee_Name_Card_toggleImageShape;
            window.unIQue_empLOyee_Name_Card_changeFontSize = unIQue_empLOyee_Name_Card_changeFontSize;
            window.unIQue_empLOyee_Name_Card_changeFontStyle = unIQue_empLOyee_Name_Card_changeFontStyle;
            window.unIQue_empLOyee_Name_Card_changeTextColor = unIQue_empLOyee_Name_Card_changeTextColor;
            window.unIQue_empLOyee_Name_Card_cancelText = unIQue_empLOyee_Name_Card_cancelText;
            window.unIQue_empLOyee_Name_Card_bringForward = unIQue_empLOyee_Name_Card_bringForward;
            window.unIQue_empLOyee_Name_Card_sendBackward = unIQue_empLOyee_Name_Card_sendBackward;
            window.unIQue_empLOyee_Name_Card_deleteSelectedElements = unIQue_empLOyee_Name_Card_deleteSelectedElements;
            window.unIQue_empLOyee_Name_Card_deselectAll = unIQue_empLOyee_Name_Card_deselectAll;
            window.unIQue_empLOyee_Name_Card_makeDraggable = unIQue_empLOyee_Name_Card_makeDraggable;
            window.unIQue_empLOyee_Name_Card_selectElement = unIQue_empLOyee_Name_Card_selectElement;
            window.unIQue_empLOyee_Name_Card_initResize = unIQue_empLOyee_Name_Card_initResize;
            window.unIQue_empLOyee_Name_Card_resetLayout = unIQue_empLOyee_Name_Card_resetLayout;
            window.unIQue_empLOyee_Name_Card_saveState = unIQue_empLOyee_Name_Card_saveState;
            window.unIQue_empLOyee_Name_Card_loadState = unIQue_empLOyee_Name_Card_loadState;
            window.unIQue_empLOyee_Name_Card_cloneElementWithStyles = unIQue_empLOyee_Name_Card_cloneElementWithStyles;
            window.unIQue_empLOyee_Name_Card_downloadCards = unIQue_empLOyee_Name_Card_downloadCards;
        })();
    </script>
</body>
</html>
