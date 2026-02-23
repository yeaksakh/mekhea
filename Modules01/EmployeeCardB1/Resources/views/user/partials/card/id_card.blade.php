<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee ID Card Editor</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Battambang&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arial|Times+New+Roman|Courier+New|Georgia|Verdana|Helvetica" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .unIQue_empLOyee_Id_Card_card {
            font-family: 'Battambang', 'Montserrat', Arial, sans-serif;
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            gap: 5px;
            background-color: #f5f5f5;
        }

        .unIQue_empLOyee_Id_Card_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }

        .unIQue_empLOyee_Id_Card_filters-container h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }

        .unIQue_empLOyee_Id_Card_filters-container hr {
            border: 0;
            border-top: 1px #ccc;
            margin: 10px 0;
        }

        .unIQue_empLOyee_Id_Card_category-toggle {
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

        .unIQue_empLOyee_Id_Card_category-toggle:hover {
            background-color: #c8d8ff;
        }

        .unIQue_empLOyee_Id_Card_category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .unIQue_empLOyee_Id_Card_category-content.active {
            max-height: 500px;
            padding: 10px;
        }

        .unIQue_empLOyee_Id_Card_filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .unIQue_empLOyee_Id_Card_background-dropdown,
        .unIQue_empLOyee_Id_Card_font-size-dropdown,
        .unIQue_empLOyee_Id_Card_font-style-dropdown {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
        }

        .unIQue_empLOyee_Id_Card_text-input,
        .unIQue_empLOyee_Id_Card_color-picker,
        .unIQue_empLOyee_Id_Card_custom-font-size {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .unIQue_empLOyee_Id_Card_text-input:focus,
        .unIQue_empLOyee_Id_Card_color-picker:focus,
        .unIQue_empLOyee_Id_Card_custom-font-size:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }

        .unIQue_empLOyee_Id_Card_custom-button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Battambang', sans-serif;
            font-size: 16px;
            transition: background-color 0.2s;
            background-color: #17a2b8;
            color: white;
        }

        .unIQue_empLOyee_Id_Card_custom-button:hover {
            background-color: #138496;
        }

        .unIQue_empLOyee_Id_Card_custom-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .unIQue_empLOyee_Id_Card_reset-button {
            background-color: #dc3545;
            color: white;
        }

        .unIQue_empLOyee_Id_Card_reset-button:hover {
            background-color: #c82333;
        }

        .unIQue_empLOyee_Id_Card_secondary-button {
            background-color: #6c757d;
            color: white;
        }

        .unIQue_empLOyee_Id_Card_secondary-button:hover {
            background-color: #5a6268;
        }

        .unIQue_empLOyee_Id_Card_text-button {
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

        .unIQue_empLOyee_Id_Card_text-button:hover {
            background-color: #138496;
        }

        .unIQue_empLOyee_Id_Card_hidden {
            display: none;
        }

        .unIQue_empLOyee_Id_Card_download-btn {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .unIQue_empLOyee_Id_Card_download-btn:hover {
            background-color: #138496;
        }

        .unIQue_empLOyee_Id_Card_arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }

        .unIQue_empLOyee_Id_Card_category-toggle.active .unIQue_empLOyee_Id_Card_arrow {
            transform: rotate(180deg);
        }

        .unIQue_empLOyee_Id_Card_card-inner {
            width: 70%;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .unIQue_empLOyee_Id_Card_card-front,
        .unIQue_empLOyee_Id_Card_card-back {
            position: relative;
            width: 55mm;
            height: 85mm;
            overflow: hidden;
            background-color: transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 0 20px;
            padding: 0;
            border: 1px solid black;
            border-radius: 8px;
        }

        .unIQue_empLOyee_Id_Card_card-front {
            background: url('/docs/images/employee_idcard_front.png') no-repeat center center;
            background-size: cover;
        }

        .unIQue_empLOyee_Id_Card_card-back {
            background: url('/docs/images/employee_idcard_back.png') no-repeat center center;
            background-size: cover;
        }

        .unIQue_empLOyee_Id_Card_profile-user-img {
            width: 6rem;
            height: 6rem;
            border: 2px solid #17a2b8;
            position: absolute;
            pointer-events: auto;
            cursor: move;
            object-fit: cover;
        }

        .unIQue_empLOyee_Id_Card_profile-user-img.square {
            border-radius: 0;
        }

        .unIQue_empLOyee_Id_Card_profile-user-img:not(.square) {
            border-radius: 50%;
        }

        .unIQue_empLOyee_Id_Card_card-text {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-size: 10px;
            color: #000;
            font-family: 'Battambang', 'Montserrat', Arial, sans-serif;
            white-space: nowrap;
        }

        .unIQue_empLOyee_Id_Card_qr-container {
            position: absolute;
            width: 120px;
            height: 120px;
            pointer-events: auto;
            cursor: move;
        }

        #unIQue_empLOyee_Id_Card_employee_qrcode1 {
            width: 100%;
            height: 100%;
            border-radius: 3mm;
        }

        .unIQue_empLOyee_Id_Card_draggable {
            position: absolute;
            cursor: move;
            user-select: none;
            pointer-events: auto;
        }

        .unIQue_empLOyee_Id_Card_selected {
            outline: 2px solid #007bff;
        }

        .unIQue_empLOyee_Id_Card_overlay-image {
            width: auto;
            height: auto;
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            display: block;
        }

        .unIQue_empLOyee_Id_Card_overlay-image.square {
            border-radius: 0;
        }

        .unIQue_empLOyee_Id_Card_overlay-image:not(.square) {
            border-radius: 50%;
        }

        .unIQue_empLOyee_Id_Card_image-container {
            position: absolute;
            display: inline-block;
            pointer-events: auto;
        }

        .unIQue_empLOyee_Id_Card_resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #007bff;
            cursor: pointer;
            pointer-events: auto;
            display: none;
        }

        .unIQue_empLOyee_Id_Card_selected .unIQue_empLOyee_Id_Card_resize-handle {
            display: block;
        }

        .unIQue_empLOyee_Id_Card_resize-handle.unIQue_empLOyee_Id_Card_bottom-right {
            right: -5px;
            bottom: -5px;
            cursor: se-resize;
        }

        .unIQue_empLOyee_Id_Card_resize-handle.unIQue_empLOyee_Id_Card_bottom-left {
            left: -5px;
            bottom: -5px;
            cursor: sw-resize;
        }

        .unIQue_empLOyee_Id_Card_resize-handle.unIQue_empLOyee_Id_Card_top-right {
            right: -5px;
            top: -5px;
            cursor: ne-resize;
        }

        .unIQue_empLOyee_Id_Card_resize-handle.unIQue_empLOyee_Id_Card_top-left {
            left: -5px;
            top: -5px;
            cursor: nw-resize;
        }
    </style>
</head>
<body>
    <div class="unIQue_empLOyee_Id_Card_card">
        <div class="unIQue_empLOyee_Id_Card_filters-container" id="unIQue_empLOyee_Id_Card_controls">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Profile Picture Controls -->
            <div class="unIQue_empLOyee_Id_Card_change-profile-container">
                <div class="unIQue_empLOyee_Id_Card_category-toggle" onclick="unIQue_empLOyee_Id_Card_toggleCategory('unIQue_empLOyee_Id_Card_profile-controls')">
                    <i class="fas fa-user"></i>
                    <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                    <span class="unIQue_empLOyee_Id_Card_arrow">▼</span>
                </div>
                <div class="unIQue_empLOyee_Id_Card_category-content" id="unIQue_empLOyee_Id_Card_profile-controls">
                    <div class="unIQue_empLOyee_Id_Card_filter-group">
                        <button class="unIQue_empLOyee_Id_Card_custom-button" onclick="document.getElementById('unIQue_empLOyee_Id_Card_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                        <input type="file" id="unIQue_empLOyee_Id_Card_profileUpload" accept="image/*" onchange="unIQue_empLOyee_Id_Card_changeProfilePic()" style="display: none;">
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" onclick="unIQue_empLOyee_Id_Card_toggleProfileShape(true)">រាងការ៉េ</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" onclick="unIQue_empLOyee_Id_Card_toggleProfileShape(false)">រាងមូល</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_reset-button" onclick="unIQue_empLOyee_Id_Card_resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                    </div>
                </div>
            </div>
            <!-- Background Controls -->
            <div class="unIQue_empLOyee_Id_Card_background-input-container">
                <div class="unIQue_empLOyee_Id_Card_category-toggle" onclick="unIQue_empLOyee_Id_Card_toggleCategory('unIQue_empLOyee_Id_Card_background-controls')">
                    <i class="fas fa-image"></i>
                    <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                    <span class="unIQue_empLOyee_Id_Card_arrow">▼</span>
                </div>
                <div class="unIQue_empLOyee_Id_Card_category-content" id="unIQue_empLOyee_Id_Card_background-controls">
                    <div class="unIQue_empLOyee_Id_Card_filter-group">
                        <select class="unIQue_empLOyee_Id_Card_background-dropdown" id="unIQue_empLOyee_Id_Card_cardSelector" onchange="unIQue_empLOyee_Id_Card_updateCurrentCard()">
                            <option value="front">កាតខាងមុខ</option>
                            <option value="back">កាតខាងក្រោយ</option>
                        </select>
                        <select class="unIQue_empLOyee_Id_Card_background-dropdown" id="unIQue_empLOyee_Id_Card_backgroundDropdown" onchange="unIQue_empLOyee_Id_Card_changeBackgroundFromDropdown()">
                            <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                            <option value="/docs/images/background1.png">ផ្ទៃខាងក្រោយ ១</option>
                            <option value="/docs/images/background2.png">ផ្ទៃខាងក្រោយ ២</option>
                        </select>
                        <button class="unIQue_empLOyee_Id_Card_custom-button" onclick="document.getElementById('unIQue_empLOyee_Id_Card_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                        <input type="file" id="unIQue_empLOyee_Id_Card_imageUpload" accept="image/*" onchange="unIQue_empLOyee_Id_Card_changeBackground()" style="display: none;">
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_reset-button" onclick="unIQue_empLOyee_Id_Card_resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                    </div>
                </div>
            </div>
            <!-- Text Controls -->
            <div class="unIQue_empLOyee_Id_Card_insert-text-container">
                <div class="unIQue_empLOyee_Id_Card_category-toggle" onclick="unIQue_empLOyee_Id_Card_toggleCategory('unIQue_empLOyee_Id_Card_text-controls')">
                    <i class="fas fa-font"></i>
                    <span>ការគ្រប់គ្រងអត្ថបទ</span>
                    <span class="unIQue_empLOyee_Id_Card_arrow">▼</span>
                </div>
                <div class="unIQue_empLOyee_Id_Card_category-content" id="unIQue_empLOyee_Id_Card_text-controls">
                    <div class="unIQue_empLOyee_Id_Card_filter-group">
                        <input type="text" id="unIQue_empLOyee_Id_Card_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="unIQue_empLOyee_Id_Card_text-input" oninput="unIQue_empLOyee_Id_Card_updateTextButton()">
                        <input type="color" id="unIQue_empLOyee_Id_Card_textColorPicker" value="#000000" class="unIQue_empLOyee_Id_Card_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ" onchange="unIQue_empLOyee_Id_Card_changeTextColor()">
                        <select class="unIQue_empLOyee_Id_Card_font-size-dropdown" id="unIQue_empLOyee_Id_Card_fontSizeDropdown" onchange="unIQue_empLOyee_Id_Card_changeFontSize()">
                            <option value="10">១០ ភីកសែល</option>
                            <option value="12">១២ ភីកសែល</option>
                            <option value="14">១៤ ភី���សែល</option>
                            <option value="16">១៦ ភីកសែល</option>
                            <option value="18">៱៨ ភីកសែល</option>
                            <option value="20">៲០ ភីកសែល</option>
                            <option value="custom">ផ្ទាល់ខ្លួន</option>
                        </select>
                        <input type="number" id="unIQue_empLOyee_Id_Card_customFontSize" class="unIQue_empLOyee_Id_Card_custom-font-size unIQue_empLOyee_Id_Card_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                        <select class="unIQue_empLOyee_Id_Card_font-style-dropdown" id="unIQue_empLOyee_Id_Card_fontStyleDropdown" onchange="unIQue_empLOyee_Id_Card_changeFontStyle()">
                            <option value="Arial">អេរីយ៉ាល</option>
                            <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                            <option value="Courier New">គូរីយ៉េ នូវ</option>
                            <option value="Georgia">ជីអរជីយ៉ា</option>
                            <option value="Verdana">វ៉ើរដានា</option>
                            <option value="Helvetica">ហែលវេទីកា</option>
                            <option value="Montserrat">ម៉ុងសឺរ៉ាត</option>
                            <option value="Moul">មូល</option>
                            <option value="Battambang">បាត់ដំបង</option>
                        </select>
                        <button class="unIQue_empLOyee_Id_Card_text-button" id="unIQue_empLOyee_Id_Card_textButton" onclick="unIQue_empLOyee_Id_Card_handleTextAction()">បន្ថែមអត្ថបទ</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_reset-button" onclick="unIQue_empLOyee_Id_Card_cancelText()">បោះបង់</button>
                    </div>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="unIQue_empLOyee_Id_Card_add-image-container">
                <div class="unIQue_empLOyee_Id_Card_category-toggle" onclick="unIQue_empLOyee_Id_Card_toggleCategory('unIQue_empLOyee_Id_Card_image-overlay-controls')">
                    <i class="fas fa-image"></i>
                    <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                    <span class="unIQue_empLOyee_Id_Card_arrow">▼</span>
                </div>
                <div class="unIQue_empLOyee_Id_Card_category-content" id="unIQue_empLOyee_Id_Card_image-overlay-controls">
                    <div class="unIQue_empLOyee_Id_Card_filter-group">
                        <button class="unIQue_empLOyee_Id_Card_custom-button" onclick="document.getElementById('unIQue_empLOyee_Id_Card_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                        <input type="file" id="unIQue_empLOyee_Id_Card_imageUploadOverlay" accept="image/*" onchange="unIQue_empLOyee_Id_Card_addOverlayImage()" style="display: none;">
                        <button class="unIQue_empLOyee_Id_Card_custom-button" onclick="document.getElementById('unIQue_empLOyee_Id_Card_changeImageUpload').click();">ផ្លាស់ប្តូររូប���ាព</button>
                        <input type="file" id="unIQue_empLOyee_Id_Card_changeImageUpload" accept="image/*" onchange="unIQue_empLOyee_Id_Card_changeOverlayImage()" style="display: none;">
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" onclick="unIQue_empLOyee_Id_Card_toggleImageShape(true)">រាងការ៉េ</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" onclick="unIQue_empLOyee_Id_Card_toggleImageShape(false)">រាងមូល</button>
                    </div>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="unIQue_empLOyee_Id_Card_element-controls-container">
                <div class="unIQue_empLOyee_Id_Card_category-toggle" onclick="unIQue_empLOyee_Id_Card_toggleCategory('unIQue_empLOyee_Id_Card_element-controls')">
                    <i class="fas fa-cog"></i>
                    <span>ការគ្រប់គ្រងធាតុ</span>
                    <span class="unIQue_empLOyee_Id_Card_arrow">▼</span>
                </div>
                <div class="unIQue_empLOyee_Id_Card_category-content" id="unIQue_empLOyee_Id_Card_element-controls">
                    <div class="unIQue_empLOyee_Id_Card_filter-group">
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_reset-button" onclick="unIQue_empLOyee_Id_Card_deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" id="unIQue_empLOyee_Id_Card_bringForwardBtn" onclick="unIQue_empLOyee_Id_Card_bringForward()" disabled>នាំមកមុខ</button>
                        <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_secondary-button" id="unIQue_empLOyee_Id_Card_sendBackwardBtn" onclick="unIQue_empLOyee_Id_Card_sendBackward()" disabled>បញ្ជូនទៅក្រោយ</button>
                    </div>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="unIQue_empLOyee_Id_Card_custom-button unIQue_empLOyee_Id_Card_reset-button" onclick="unIQue_empLOyee_Id_Card_resetLayout()">កំណត់ឡើងវិញ</button>
            <button class="unIQue_empLOyee_Id_Card_download-btn" onclick="unIQue_empLOyee_Id_Card_downloadCards()">បោះពុម្ពប័ណ្ណសំគាល់ខ្លួន<i class="fas fa-print" style="margin-left: 10px;"></i></button>
        </div>
        <div class="unIQue_empLOyee_Id_Card_card-inner">
            <div class="unIQue_empLOyee_Id_Card_card-front" id="front">
                @php
                    $textLength = mb_strlen(($user_designation->name ?? '') . ($user_department->name ?? ''));
                @endphp
                <img class="unIQue_empLOyee_Id_Card_profile-user-img unIQue_empLOyee_Id_Card_draggable" src="{{ $img_src ?? 'https://via.placeholder.com/100' }}" alt="Photo" style="left: 50%; transform: translateX(-50%); top: 45px;">
                <h3 class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 137px; font-size: 16px; font-weight: bolder; white-space: nowrap;">{{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') }}</h3>
                {{-- <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 190px; font-size: 13px; font-weight: bolder; white-space: wrap;">{{__('lang_v1.designation')}}៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}</p> --}}
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 177px; font-size: 13px; font-weight: bolder; white-space: wrap;">{{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}</p>
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: {{ $textLength < 30 ? '215px' : '230px' }}; font-size: 12px; font-weight: bolder; white-space: nowrap;">Tel: {{ $user->contact_number ?? '' }}</p>
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: {{ $textLength < 30 ? '235px' : '250px' }}; font-size: 12px; font-weight: bolder; white-space: nowrap;">@lang('employeecardb1::user.id'): {{ $user->id ?? '' }}</p>
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: {{ $textLength < 30 ? '255px' : '270px' }}; font-size: 12px; font-weight: bolder; white-space: nowrap;">@lang('employeecardb1::lang.end_date'): {{ (isset($userShift) && $userShift->end_date) ? \Carbon\Carbon::parse($userShift->end_date)->format('d-m-Y') : '' }}</p>
            </div>
            <div class="unIQue_empLOyee_Id_Card_card-back" id="back">
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 40px; font-size: 14px; font-weight: bold; white-space: nowrap;">@lang('employeecardb1::user.scan_here'):</p>
                <div class="unIQue_empLOyee_Id_Card_qr-container unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 100px;">
                    <div id="unIQue_empLOyee_Id_Card_employee_qrcode1"></div>
                </div>
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 240px; font-size: 14px; white-space: nowrap;">@lang('employeecardb1::user.property_of'):</p>
                <p class="unIQue_empLOyee_Id_Card_card-text unIQue_empLOyee_Id_Card_draggable" style="left: 50%; transform: translateX(-50%); top: 260px; font-size: 14px; font-weight: bold; white-space: nowrap;">{{ $business->name ?? '' }}</p>
            </div>
        </div>
    </div>
    <script>
        let unIQue_empLOyee_Id_Card_selectedElements = [];
        let unIQue_empLOyee_Id_Card_textCounter = 0;
        let unIQue_empLOyee_Id_Card_zIndexCounter = 10;
        let unIQue_empLOyee_Id_Card_currentCard = null;
        const employeeId = {{ $user->id ?? '0' }};
        const STORAGE_KEY = `unIQue_empLOyee_Id_Card_layout_${employeeId}`;
    
        function unIQue_empLOyee_Id_Card_toggleCategory(categoryId) {
            const content = document.getElementById(categoryId);
            if (!content) return console.warn(`Category ${categoryId} not found`);
            const toggle = content.previousElementSibling;
            const isActive = content.classList.contains('active');
    
            document.querySelectorAll('.unIQue_empLOyee_Id_Card_category-content').forEach(otherContent => {
                if (otherContent.id !== categoryId && otherContent.classList.contains('active')) {
                    otherContent.classList.remove('active');
                    otherContent.previousElementSibling?.classList.remove('active');
                }
            });
    
            content.classList.toggle('active', !isActive);
            toggle?.classList.toggle('active', !isActive);
        }
    
        function unIQue_empLOyee_Id_Card_updateCurrentCard() {
            const cardSelector = document.getElementById('unIQue_empLOyee_Id_Card_cardSelector');
            if (!cardSelector) return console.warn('Card selector not found');
            unIQue_empLOyee_Id_Card_currentCard = document.querySelector(`.unIQue_empLOyee_Id_Card_card-${cardSelector.value}`);
            if (!unIQue_empLOyee_Id_Card_currentCard) console.warn(`Card .unIQue_empLOyee_Id_Card_card-${cardSelector.value} not found`);
        }
    
        function unIQue_empLOyee_Id_Card_getSelectedSide() {
            const cardSelector = document.getElementById('unIQue_empLOyee_Id_Card_cardSelector');
            return cardSelector ? `.unIQue_empLOyee_Id_Card_card-${cardSelector.value}` : '.unIQue_empLOyee_Id_Card_card-front';
        }
    
        function unIQue_empLOyee_Id_Card_generateQRCode(containerId, size) {
            try {
                const employeeLink = "{{ url('/employee') }}/" + employeeId;
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
                    new QRCode(qrContainer, qrOptions);
                }
            } catch (error) {
                console.error('Failed to generate QR code:', error);
            }
        }
    
        function unIQue_empLOyee_Id_Card_saveToLocalStorage() {
            const frontCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-front');
            const backCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-back');
            if (!frontCard || !backCard) {
                console.warn('Front or back card not found');
                return;
            }
    
            const layoutData = {
                front: {
                    backgroundImage: frontCard.style.backgroundImage,
                    backgroundSize: frontCard.style.backgroundSize || '100% 100%',
                    elements: []
                },
                back: {
                    backgroundImage: backCard.style.backgroundImage,
                    backgroundSize: backCard.style.backgroundSize || '100% 100%',
                    elements: []
                }
            };
    
            const styleProperties = [
                'left', 'top', 'zIndex', 'transform',
                'fontSize', 'fontWeight', 'color', 'fontFamily', 'whiteSpace',
                'width', 'height', 'maxWidth', 'maxHeight'
            ];
    
            frontCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                if (!el.id) {
                    el.id = `unIQue_empLOyee_Id_Card_el_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    console.debug(`Assigned ID ${el.id} to element`);
                }
                const computedStyle = window.getComputedStyle(el);
                const style = {};
                styleProperties.forEach(prop => {
                    style[prop] = el.style[prop] || computedStyle[prop];
                });
                layoutData.front.elements.push({ id: el.id, style });
            });
    
            backCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                if (!el.id) {
                    el.id = `unIQue_empLOyee_Id_Card_el_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    console.debug(`Assigned ID ${el.id} to element`);
                }
                const computedStyle = window.getComputedStyle(el);
                const style = {};
                styleProperties.forEach(prop => {
                    style[prop] = el.style[prop] || computedStyle[prop];
                });
                layoutData.back.elements.push({ id: el.id, style });
            });
    
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(layoutData));
                console.debug('Saved to localStorage:', layoutData);
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
        }
    
        function unIQue_empLOyee_Id_Card_loadFromLocalStorage() {
            const frontCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-front');
            const backCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-back');
            if (!frontCard || !backCard) {
                console.warn('Front or back card not found during load');
                return;
            }
    
            let layoutData;
            try {
                const savedData = localStorage.getItem(STORAGE_KEY);
                layoutData = savedData ? JSON.parse(savedData) : null;
                console.debug('Loaded from localStorage:', layoutData);
            } catch (e) {
                console.error('Error parsing localStorage:', e);
                return;
            }
    
            if (!layoutData || !layoutData.front || !layoutData.back) {
                console.warn('No valid layout data found');
                return;
            }
    
            // Apply front card background
            if (layoutData.front.backgroundImage) {
                frontCard.style.backgroundImage = layoutData.front.backgroundImage;
                frontCard.style.backgroundSize = layoutData.front.backgroundSize;
                console.debug('Applied front card background');
            }
    
            // Apply styles to front card elements
            frontCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                if (!el.id) {
                    el.id = `unIQue_empLOyee_Id_Card_el_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    console.debug(`Assigned ID ${el.id} to element during load`);
                }
                const savedElement = layoutData.front.elements.find(data => data.id === el.id);
                if (savedElement?.style) {
                    Object.assign(el.style, savedElement.style);
                    console.debug(`Applied styles to front element ${el.id}`);
                } else {
                    console.warn(`No saved styles for front element ${el.id}`);
                }
            });
    
            // Apply back card background
            if (layoutData.back.backgroundImage) {
                backCard.style.backgroundImage = layoutData.back.backgroundImage;
                backCard.style.backgroundSize = layoutData.back.backgroundSize;
                console.debug('Applied back card background');
            }
    
            // Apply styles to back card elements
            backCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                if (!el.id) {
                    el.id = `unIQue_empLOyee_Id_Card_el_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    console.debug(`Assigned ID ${el.id} to element during load`);
                }
                const savedElement = layoutData.back.elements.find(data => data.id === el.id);
                if (savedElement?.style) {
                    Object.assign(el.style, savedElement.style);
                    console.debug(`Applied styles to back element ${el.id}`);
                } else {
                    console.warn(`No saved styles for back element ${el.id}`);
                }
            });
    
            // Regenerate QR codes
            document.querySelectorAll('.unIQue_empLOyee_Id_Card_qr-container').forEach(container => {
                const qrDiv = container.querySelector('div');
                if (qrDiv?.id) {
                    unIQue_empLOyee_Id_Card_generateQRCode(qrDiv.id, 120);
                }
            });
        }
    
        function unIQue_empLOyee_Id_Card_initializeDefaultLayout() {
            const frontSide = document.querySelector('.unIQue_empLOyee_Id_Card_card-front');
            const backSide = document.querySelector('.unIQue_empLOyee_Id_Card_card-back');
            if (!frontSide || !backSide) {
                console.warn('Front or back card not found during initialization');
                return;
            }
    
            // Clear existing elements
            frontSide.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => el.remove());
            backSide.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => el.remove());
    
            // Set default backgrounds
            frontSide.style.backgroundImage = `url('/docs/images/employee_idcard_front.png')`;
            frontSide.style.backgroundSize = '100% 100%';
            backSide.style.backgroundImage = `url('/docs/images/employee_idcard_back.png')`;
            backSide.style.backgroundSize = '100% 100%';
    
            const defaultFrontElements = [
                {
                    type: 'img',
                    id: 'unIQue_empLOyee_Id_Card_profile_img',
                    classes: ['unIQue_empLOyee_Id_Card_profile-user-img', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '45px', width: '6rem', height: '6rem', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    src: '{{ $img_src ?? "https://via.placeholder.com/100" }}',
                    alt: 'Photo'
                },
                {
                    type: 'h3',
                    id: 'unIQue_empLOyee_Id_Card_text_1',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '137px', fontSize: '16px', fontWeight: 'bolder', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '{{ ($user->first_name ?? "") . " " . ($user->last_name ?? "") }}'
                },
                // {
                //     type: 'p',
                //     id: 'unIQue_empLOyee_Id_Card_text_3',
                //     classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                //     style: { left: '50%', transform: 'translateX(-50%)', top: '190px', fontSize: '13px', fontWeight: 'bolder', whiteSpace: 'wrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                //     textContent: '{{__("lang_v1.designation")}}៖ {{ $user_designation->name ?? "" }}{{ $user_department->name ?? "" }}'
                // },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_3',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '177px', fontSize: '13px', fontWeight: 'bolder', whiteSpace: 'wrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '{{ $user_designation->name ?? "" }}{{ $user_department->name ?? "" }}'
                },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_4',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '{{ $textLength < 30 ? "215px" : "230px" }}', fontSize: '12px', fontWeight: 'bolder', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: 'Tel: {{ $user->contact_number ?? "" }}'
                },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_5',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '{{ $textLength < 30 ? "235px" : "250px" }}', fontSize: '12px', fontWeight: 'bolder', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '@lang("employeecardb1::user.id"): {{ $user->id ?? "" }}'
                },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_6',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '{{ $textLength < 30 ? "255px" : "270px" }}', fontSize: '12px', fontWeight: 'bolder', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '@lang("employeecardb1::lang.end_date"): {{ (isset($userShift) && $userShift->end_date) ? \Carbon\Carbon::parse($userShift->end_date)->format("d-m-Y") : "" }}'
                }
            ];
    
            const defaultBackElements = [
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_7',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '40px', fontSize: '14px', fontWeight: 'bold', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '@lang("employeecardb1::user.scan_here"):'
                },
                {
                    type: 'div',
                    id: 'unIQue_empLOyee_Id_Card_qr_container',
                    classes: ['unIQue_empLOyee_Id_Card_qr-container', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '100px', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    isQR: true,
                    qrId: 'unIQue_empLOyee_Id_Card_employee_qrcode'
                },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_8',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '240px', fontSize: '14px', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '@lang("employeecardb1::user.property_of"):'
                },
                {
                    type: 'p',
                    id: 'unIQue_empLOyee_Id_Card_text_9',
                    classes: ['unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable'],
                    style: { left: '50%', transform: 'translateX(-50%)', top: '260px', fontSize: '14px', fontWeight: 'bold', whiteSpace: 'nowrap', zIndex: unIQue_empLOyee_Id_Card_zIndexCounter++ },
                    textContent: '{{ $business->name ?? "" }}'
                }
            ];
    
            defaultFrontElements.forEach(data => {
                let element;
                if (data.type === 'img') {
                    element = document.createElement('img');
                    element.src = data.src;
                    element.alt = data.alt;
                } else {
                    element = document.createElement(data.type);
                    element.textContent = data.textContent;
                }
                element.id = data.id;
                element.className = data.classes.join(' ');
                Object.assign(element.style, data.style);
                frontSide.appendChild(element);
                unIQue_empLOyee_Id_Card_makeDraggable(element);
            });
    
            defaultBackElements.forEach(data => {
                let element;
                if (data.isQR) {
                    element = document.createElement('div');
                    element.id = data.id;
                    element.className = data.classes.join(' ');
                    const qrDiv = document.createElement('div');
                    qrDiv.id = data.qrId;
                    element.appendChild(qrDiv);
                    setTimeout(() => unIQue_empLOyee_Id_Card_generateQRCode(data.qrId, 120), 100);
                } else {
                    element = document.createElement(data.type);
                    element.textContent = data.textContent;
                    element.id = data.id;
                    element.className = data.classes.join(' ');
                }
                Object.assign(element.style, data.style);
                backSide.appendChild(element);
                unIQue_empLOyee_Id_Card_makeDraggable(element);
            });
    
            console.debug('Default layout initialized');
        }
    
        window.unIQue_empLOyee_Id_Card_changeBackgroundFromDropdown = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const selectedBg = document.getElementById('unIQue_empLOyee_Id_Card_backgroundDropdown')?.value;
            if (selectedBg) {
                unIQue_empLOyee_Id_Card_currentCard.style.backgroundImage = `url(${selectedBg})`;
                unIQue_empLOyee_Id_Card_currentCard.style.backgroundSize = '100% 100%';
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_changeBackground = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const file = document.getElementById('unIQue_empLOyee_Id_Card_imageUpload')?.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    unIQue_empLOyee_Id_Card_currentCard.style.backgroundImage = `url(${e.target.result})`;
                    unIQue_empLOyee_Id_Card_currentCard.style.backgroundSize = '100% 100%';
                    unIQue_empLOyee_Id_Card_saveToLocalStorage();
                };
                reader.readAsDataURL(file);
            }
        };
    
        window.unIQue_empLOyee_Id_Card_resetBackground = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const defaultBackgrounds = {
                'unIQue_empLOyee_Id_Card_card-front': '/docs/images/employee_idcard_front.png',
                'unIQue_empLOyee_Id_Card_card-back': '/docs/images/employee_idcard_back.png'
            };
            unIQue_empLOyee_Id_Card_currentCard.style.backgroundImage = `url(${defaultBackgrounds[unIQue_empLOyee_Id_Card_currentCard.classList[1]]})`;
            unIQue_empLOyee_Id_Card_currentCard.style.backgroundSize = '100% 100%';
            const bgDropdown = document.getElementById('unIQue_empLOyee_Id_Card_backgroundDropdown');
            const imgUpload = document.getElementById('unIQue_empLOyee_Id_Card_imageUpload');
            if (bgDropdown) bgDropdown.value = '';
            if (imgUpload) imgUpload.value = '';
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_changeProfilePic = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const file = document.getElementById('unIQue_empLOyee_Id_Card_profileUpload')?.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    let profileImg = unIQue_empLOyee_Id_Card_currentCard.querySelector('.unIQue_empLOyee_Id_Card_profile-user-img');
                    if (!profileImg) {
                        profileImg = document.createElement('img');
                        profileImg.className = 'unIQue_empLOyee_Id_Card_profile-user-img unIQue_empLOyee_Id_Card_draggable';
                        profileImg.id = 'unIQue_empLOyee_Id_Card_profile_img';
                        profileImg.style.left = '50%';
                        profileImg.style.transform = 'translateX(-50%)';
                        profileImg.style.top = '45px';
                        profileImg.style.width = '6rem';
                        profileImg.style.height = '6rem';
                        unIQue_empLOyee_Id_Card_currentCard.appendChild(profileImg);
                        unIQue_empLOyee_Id_Card_makeDraggable(profileImg);
                    }
                    profileImg.src = e.target.result;
                    unIQue_empLOyee_Id_Card_selectElement(profileImg);
                    unIQue_empLOyee_Id_Card_saveToLocalStorage();
                };
                reader.readAsDataURL(file);
            }
        };
    
        window.unIQue_empLOyee_Id_Card_resetProfile = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const profileImg = unIQue_empLOyee_Id_Card_currentCard.querySelector('.unIQue_empLOyee_Id_Card_profile-user-img');
            if (profileImg) {
                profileImg.src = '{{ $img_src ?? 'https://via.placeholder.com/100' }}';
                profileImg.style.left = '50%';
                profileImg.style.transform = 'translateX(-50%)';
                profileImg.style.top = '45px';
                profileImg.style.width = '6rem';
                profileImg.style.height = '6rem';
                profileImg.classList.remove('square');
                const profileUpload = document.getElementById('unIQue_empLOyee_Id_Card_profileUpload');
                if (profileUpload) profileUpload.value = '';
                unIQue_empLOyee_Id_Card_selectElement(profileImg);
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_toggleProfileShape = function(isSquare) {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const profileImg = unIQue_empLOyee_Id_Card_currentCard.querySelector('.unIQue_empLOyee_Id_Card_profile-user-img');
            if (profileImg) {
                profileImg.classList.toggle('square', isSquare);
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_updateTextButton = function() {
            const textInput = document.getElementById('unIQue_empLOyee_Id_Card_textInput');
            const textButton = document.getElementById('unIQue_empLOyee_Id_Card_textButton');
            if (!textInput || !textButton) return;
            textButton.textContent = textInput.value && unIQue_empLOyee_Id_Card_selectedElements.length === 1 && unIQue_empLOyee_Id_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Id_Card_card-text') ? 'ធ្វើបច្ចុប្បន្នភាពអត្ថបទ' : 'បន្ថែមអត្ថបទ';
        };
    
        window.unIQue_empLOyee_Id_Card_handleTextAction = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const textInput = document.getElementById('unIQue_empLOyee_Id_Card_textInput');
            const fontSizeDropdown = document.getElementById('unIQue_empLOyee_Id_Card_fontSizeDropdown');
            const customFontSize = document.getElementById('unIQue_empLOyee_Id_Card_customFontSize');
            const fontStyleDropdown = document.getElementById('unIQue_empLOyee_Id_Card_fontStyleDropdown');
            const textColorPicker = document.getElementById('unIQue_empLOyee_Id_Card_textColorPicker');
            if (!textInput || !fontSizeDropdown || !fontStyleDropdown || !textColorPicker) return;
            const text = textInput.value.trim();
            if (!text) return alert('សូមបញ្ចូលអត្ថបទ!');
            let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 10) + 'px' : fontSizeDropdown.value + 'px';
            let textElement;
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 1 && unIQue_empLOyee_Id_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Id_Card_card-text')) {
                textElement = unIQue_empLOyee_Id_Card_selectedElements[0];
                textElement.textContent = text;
            } else {
                textElement = document.createElement('p');
                textElement.id = `unIQue_empLOyee_Id_Card_text_${unIQue_empLOyee_Id_Card_textCounter++}`;
                textElement.classList.add('unIQue_empLOyee_Id_Card_card-text', 'unIQue_empLOyee_Id_Card_draggable', 'unIQue_empLOyee_Id_Card_custom-element');
                textElement.style.left = '50%';
                textElement.style.top = '50px';
                textElement.style.transform = 'translateX(-50%)';
                textElement.style.zIndex = unIQue_empLOyee_Id_Card_zIndexCounter++;
                textElement.textContent = text;
                textElement.style.fontSize = fontSize;
                textElement.style.fontFamily = fontStyleDropdown.value;
                textElement.style.color = textColorPicker.value;
                unIQue_empLOyee_Id_Card_currentCard.appendChild(textElement);
                unIQue_empLOyee_Id_Card_makeDraggable(textElement);
            }

            textInput.value = '';
            unIQue_empLOyee_Id_Card_updateTextButton();
            unIQue_empLOyee_Id_Card_selectElement(textElement);
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_cancelText = function() {
            const textInput = document.getElementById('unIQue_empLOyee_Id_Card_textInput');
            if (!textInput) return;
            textInput.value = '';
            unIQue_empLOyee_Id_Card_updateTextButton();
            if (unIQue_empLOyee_Id_Card_selectedElements.length > 0) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => el.classList.remove('unIQue_empLOyee_Id_Card_selected'));
                unIQue_empLOyee_Id_Card_selectedElements = [];
                unIQue_empLOyee_Id_Card_updateElementControls();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_changeFontSize = function() {
            const fontSizeDropdown = document.getElementById('unIQue_empLOyee_Id_Card_fontSizeDropdown');
            const customFontSize = document.getElementById('unIQue_empLOyee_Id_Card_customFontSize');
            if (!fontSizeDropdown || !customFontSize) return;
            customFontSize.classList.toggle('unIQue_empLOyee_Id_Card_hidden', fontSizeDropdown.value !== 'custom');
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 0) return alert('សូមជ្រើសរើសធាតុជាមុន។');
            unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                if (el.tagName === 'H3' || el.tagName === 'P') {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 10) + 'px' : fontSizeDropdown.value + 'px';
                    el.style.fontSize = fontSize;
                }
            });
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_changeFontStyle = function() {
            const fontStyleDropdown = document.getElementById('unIQue_empLOyee_Id_Card_fontStyleDropdown');
            if (!fontStyleDropdown) return;
            const style = fontStyleDropdown.value;
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 0) return alert('សូមជ្រើសរើសធាតុជាមុន។');
            unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                if (el.tagName === 'H3' || el.tagName === 'P') el.style.fontFamily = style;
            });
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_changeTextColor = function() {
            const textColorPicker = document.getElementById('unIQue_empLOyee_Id_Card_textColorPicker');
            if (!textColorPicker) return;
            const color = textColorPicker.value;
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 0) return alert('សូមជ្រើសរើសធាតុជាមុន។');
            unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                if (el.tagName === 'H3' || el.tagName === 'P') el.style.color = color;
            });
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_addOverlayImage = function() {
            unIQue_empLOyee_Id_Card_updateCurrentCard();
            if (!unIQue_empLOyee_Id_Card_currentCard) return;
            const file = document.getElementById('unIQue_empLOyee_Id_Card_imageUploadOverlay')?.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'unIQue_empLOyee_Id_Card_image-container unIQue_empLOyee_Id_Card_custom-element unIQue_empLOyee_Id_Card_draggable';
                imageContainer.id = `unIQue_empLOyee_Id_Card_image_${Date.now()}`;
                imageContainer.style.left = '50%';
                imageContainer.style.top = '50px';
                imageContainer.style.transform = 'translateX(-50%)';
                imageContainer.style.zIndex = unIQue_empLOyee_Id_Card_zIndexCounter++;
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'unIQue_empLOyee_Id_Card_overlay-image';
                img.style.width = 'auto';
                img.style.height = 'auto';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                imageContainer.appendChild(img);
                ['bottom-right', 'bottom-left', 'top-right', 'top-left'].forEach(cls => {
                    const handle = document.createElement('div');
                    handle.className = `unIQue_empLOyee_Id_Card_resize-handle unIQue_empLOyee_Id_Card_${cls}`;
                    handle.addEventListener('mousedown', unIQue_empLOyee_Id_Card_initResize);
                    imageContainer.appendChild(handle);
                });
                unIQue_empLOyee_Id_Card_currentCard.appendChild(imageContainer);
                unIQue_empLOyee_Id_Card_selectElement(imageContainer);
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            };
            reader.readAsDataURL(file);
        };
    
        window.unIQue_empLOyee_Id_Card_changeOverlayImage = function() {
            const file = document.getElementById('unIQue_empLOyee_Id_Card_changeImageUpload')?.files[0];
            if (file && unIQue_empLOyee_Id_Card_selectedElements.length === 1 && unIQue_empLOyee_Id_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Id_Card_image-container')) {
                const reader = new FileReader();
                reader.onload = e => {
                    unIQue_empLOyee_Id_Card_selectedElements[0].querySelector('img').src = e.target.result;
                    unIQue_empLOyee_Id_Card_saveToLocalStorage();
                };
                reader.readAsDataURL(file);
            }
        };
    
        window.unIQue_empLOyee_Id_Card_toggleImageShape = function(isSquare) {
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 1 && unIQue_empLOyee_Id_Card_selectedElements[0].classList.contains('unIQue_empLOyee_Id_Card_image-container')) {
                const img = unIQue_empLOyee_Id_Card_selectedElements[0].querySelector('img');
                img.classList.toggle('square', isSquare);
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_deleteSelectedElements = function() {
            if (unIQue_empLOyee_Id_Card_selectedElements.length === 0) return alert('សូមជ្រើសរើសធាតុដើម្បីលុប។');
            if (confirm('តើអ្នកប្រាកដថាចង់លុបធាតុដែលបានជ្រើសរើសទេ?')) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => el.remove());
                unIQue_empLOyee_Id_Card_selectedElements = [];
                unIQue_empLOyee_Id_Card_updateElementControls();
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_editSelectedElement = function() {
            if (unIQue_empLOyee_Id_Card_selectedElements.length !== 1) return alert('សូមជ្រើសរើសអត្ថបទតែមួយដើម្បីកែសម្រួល។');
            const element = unIQue_empLOyee_Id_Card_selectedElements[0];
            if (element.tagName !== 'P' && element.tagName !== 'H3') return alert('សូមជ្រើសរើសអត្ថបទដើម្បីកែសម្រួល');
            const newText = prompt('បញ្ចូលអត្ថបទថ្មី:', element.textContent);
            if (newText !== null) {
                element.textContent = newText;
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_bringForward = function() {
            if (unIQue_empLOyee_Id_Card_selectedElements.length > 0) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                    el.style.zIndex = unIQue_empLOyee_Id_Card_zIndexCounter++;
                });
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        window.unIQue_empLOyee_Id_Card_sendBackward = function() {
            if (unIQue_empLOyee_Id_Card_selectedElements.length > 0) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                    const currentZ = parseInt(el.style.zIndex) || 10;
                    if (currentZ > 10) el.style.zIndex = currentZ - 1;
                });
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        };
    
        function unIQue_empLOyee_Id_Card_updateElementControls() {
            const bringForwardBtn = document.getElementById('unIQue_empLOyee_Id_Card_bringForwardBtn');
            const sendBackwardBtn = document.getElementById('unIQue_empLOyee_Id_Card_sendBackwardBtn');
            if (bringForwardBtn) bringForwardBtn.disabled = unIQue_empLOyee_Id_Card_selectedElements.length === 0;
            if (sendBackwardBtn) sendBackwardBtn.disabled = unIQue_empLOyee_Id_Card_selectedElements.length === 0;
        }
    
        function unIQue_empLOyee_Id_Card_makeDraggable(item) {
            let isDragging = false, startX, startY, initialLeft, initialTop;
    
            item.addEventListener('mousedown', e => {
                if (e.target.classList.contains('unIQue_empLOyee_Id_Card_resize-handle')) return;
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                initialLeft = parseFloat(item.style.left) || 0;
                initialTop = parseFloat(item.style.top) || 0;
                unIQue_empLOyee_Id_Card_selectElement(item, e.ctrlKey);
                document.body.style.cursor = 'move';
                e.preventDefault();
    
                const onMouseMove = e => {
                    if (isDragging) {
                        const newLeft = initialLeft + (e.clientX - startX);
                        const newTop = initialTop + (e.clientY - startY);
                        item.style.left = `${newLeft}px`;
                        item.style.top = `${newTop}px`;
                        item.style.transform = '';
                    }
                };
    
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', () => {
                    isDragging = false;
                    document.body.style.cursor = 'default';
                    document.removeEventListener('mousemove', onMouseMove);
                    unIQue_empLOyee_Id_Card_saveToLocalStorage();
                }, { once: true });
            });
        }
    
        function unIQue_empLOyee_Id_Card_moveElementWithArrowKeys(e) {
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.isContentEditable)) {
                return;
            }

            if (unIQue_empLOyee_Id_Card_selectedElements.length === 0) return;

            if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                return;
            }

            e.preventDefault();
            
            const moveStep = 5;
            unIQue_empLOyee_Id_Card_selectedElements.forEach(el => {
                let left = parseFloat(el.style.left) || 0;
                let top = parseFloat(el.style.top) || 0;
                switch (e.key) {
                    case 'ArrowUp': top -= moveStep; break;
                    case 'ArrowDown': top += moveStep; break;
                    case 'ArrowLeft': left -= moveStep; break;
                    case 'ArrowRight': left += moveStep; break;
                }
                el.style.left = `${left}px`;
                el.style.top = `${top}px`;
                el.style.transform = '';
            });
            
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        }
    
        function unIQue_empLOyee_Id_Card_selectElement(element, addToSelection = false) {
            if (!addToSelection) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => el.classList.remove('unIQue_empLOyee_Id_Card_selected'));
                unIQue_empLOyee_Id_Card_selectedElements = element ? [element] : [];
            } else if (unIQue_empLOyee_Id_Card_selectedElements.includes(element)) {
                unIQue_empLOyee_Id_Card_selectedElements = unIQue_empLOyee_Id_Card_selectedElements.filter(el => el !== element);
                element.classList.remove('unIQue_empLOyee_Id_Card_selected');
            } else if (element) {
                unIQue_empLOyee_Id_Card_selectedElements.push(element);
            }
            unIQue_empLOyee_Id_Card_selectedElements.forEach(el => el.classList.add('unIQue_empLOyee_Id_Card_selected'));
            const textInput = document.getElementById('unIQue_empLOyee_Id_Card_textInput');
            if (textInput) {
                if (element && element.classList.contains('unIQue_empLOyee_Id_Card_card-text')) {
                    textInput.value = element.textContent;
                } else {
                    textInput.value = '';
                }
            }
            unIQue_empLOyee_Id_Card_updateTextButton();
            unIQue_empLOyee_Id_Card_updateElementControls();
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        }
    
        document.addEventListener('click', e => {
            if (!e.target.closest('.unIQue_empLOyee_Id_Card_draggable') && !e.target.closest('.unIQue_empLOyee_Id_Card_filters-container')) {
                unIQue_empLOyee_Id_Card_selectedElements.forEach(el => el.classList.remove('unIQue_empLOyee_Id_Card_selected'));
                unIQue_empLOyee_Id_Card_selectedElements = [];
                const textInput = document.getElementById('unIQue_empLOyee_Id_Card_textInput');
                if (textInput) textInput.value = '';
                unIQue_empLOyee_Id_Card_updateTextButton();
                unIQue_empLOyee_Id_Card_updateElementControls();
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }
        });
    
        function unIQue_empLOyee_Id_Card_initResize(e) {
            e.stopPropagation();
            const container = e.target.parentElement;
            const img = container.querySelector('img');
            if (!img) return;
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = parseInt(img.style.width) || img.offsetWidth;
            const startHeight = parseInt(img.style.height) || img.offsetHeight;
            const startLeft = parseInt(container.style.left) || 0;
            const startTop = parseInt(container.style.top) || 0;
            const handleClass = e.target.className.split(' ')[1].replace('unIQue_empLOyee_Id_Card_', '');
    
            const onMouseMove = e => {
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                if (handleClass === 'bottom-right') {
                    img.style.width = `${startWidth + dx}px`;
                    img.style.height = `${startHeight + dy}px`;
                } else if (handleClass === 'bottom-left') {
                    img.style.width = `${startWidth - dx}px`;
                    img.style.height = `${startHeight + dy}px`;
                    container.style.left = `${startLeft + dx}px`;
                } else if (handleClass === 'top-right') {
                    img.style.width = `${startWidth + dx}px`;
                    img.style.height = `${startHeight - dy}px`;
                    container.style.top = `${startTop + dy}px`;
                } else if (handleClass === 'top-left') {
                    img.style.width = `${startWidth - dx}px`;
                    img.style.height = `${startHeight - dy}px`;
                    container.style.left = `${startLeft + dx}px`;
                    container.style.top = `${startTop + dy}px`;
                }
            };
    
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', () => {
                document.removeEventListener('mousemove', onMouseMove);
                unIQue_empLOyee_Id_Card_saveToLocalStorage();
            }, { once: true });
        }
    
        window.unIQue_empLOyee_Id_Card_resetLayout = function() {
            unIQue_empLOyee_Id_Card_initializeDefaultLayout();
            unIQue_empLOyee_Id_Card_selectedElements = [];
            const controls = [
                'unIQue_empLOyee_Id_Card_fontSizeDropdown',
                'unIQue_empLOyee_Id_Card_customFontSize',
                'unIQue_empLOyee_Id_Card_fontStyleDropdown',
                'unIQue_empLOyee_Id_Card_textColorPicker',
                'unIQue_empLOyee_Id_Card_textInput',
                'unIQue_empLOyee_Id_Card_backgroundDropdown',
                'unIQue_empLOyee_Id_Card_imageUpload',
                'unIQue_empLOyee_Id_Card_profileUpload',
                'unIQue_empLOyee_Id_Card_imageUploadOverlay',
                'unIQue_empLOyee_Id_Card_changeImageUpload'
            ];
            controls.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    if (id === 'unIQue_empLOyee_Id_Card_fontSizeDropdown') el.value = '10';
                    else if (id === 'unIQue_empLOyee_Id_Card_fontStyleDropdown') el.value = 'Arial';
                    else if (id === 'unIQue_empLOyee_Id_Card_textColorPicker') el.value = '#000000';
                    else if (id === 'unIQue_empLOyee_Id_Card_customFontSize') {
                        el.value = '';
                        el.classList.add('unIQue_empLOyee_Id_Card_hidden');
                    } else {
                        el.value = '';
                    }
                }
            });
            unIQue_empLOyee_Id_Card_updateTextButton();
            unIQue_empLOyee_Id_Card_updateElementControls();
            unIQue_empLOyee_Id_Card_saveToLocalStorage();
        };
    
        window.unIQue_empLOyee_Id_Card_downloadCards = async function() {
            const downloadBtn = document.querySelector('.unIQue_empLOyee_Id_Card_download-btn');
            const frontCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-front');
            const backCard = document.querySelector('.unIQue_empLOyee_Id_Card_card-back');
            if (!downloadBtn || !frontCard || !backCard) return console.warn('Download button or cards not found');
    
            const cardWidth = frontCard.offsetWidth;
            const cardHeight = frontCard.offsetHeight;
            const frontBgStyle = window.getComputedStyle(frontCard).backgroundImage;
            const backBgStyle = window.getComputedStyle(backCard).backgroundImage;
    
            downloadBtn.innerHTML = 'កំពុងបង្កើត PNG...';
            downloadBtn.disabled = true;
    
            try {
                const a4Width = 794;
                const a4Height = 1123;
                const padding = 20;
                const gap = 5;
    
                const container = document.createElement('div');
                container.style.width = `${a4Width}px`;
                container.style.height = `${a4Height}px`;
                container.style.backgroundColor = '#ffffff';
                container.style.position = 'fixed';
                container.style.left = '-9999px';
                container.style.top = '0';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.alignItems = 'center';
                container.style.justifyContent = 'flex-start';
                container.style.padding = `${padding}px`;
                container.style.gap = `${gap}px`;
                container.style.boxSizing = 'border-box';
                document.body.appendChild(container);
    
                const frontClone = document.createElement('div');
                frontClone.style.width = `${cardWidth}px`;
                frontClone.style.height = `${cardHeight}px`;
                frontClone.style.position = 'relative';
                frontClone.style.overflow = 'hidden';
                frontClone.style.borderRadius = '8px';
                frontClone.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
                frontClone.style.border = '1px solid black';
                frontClone.style.padding = '15px';
    
                const frontBgDiv = document.createElement('div');
                frontBgDiv.style.position = 'absolute';
                frontBgDiv.style.top = '0';
                frontBgDiv.style.left = '0';
                frontBgDiv.style.width = '100%';
                frontBgDiv.style.height = '100%';
                frontBgDiv.style.backgroundImage = frontBgStyle;
                frontBgDiv.style.backgroundSize = '100% 100%';
                frontBgDiv.style.backgroundPosition = 'center center';
                frontBgDiv.style.backgroundRepeat = 'no-repeat';
                frontBgDiv.style.zIndex = '0';
                frontClone.appendChild(frontBgDiv);
    
                const backClone = document.createElement('div');
                backClone.style.width = `${cardWidth}px`;
                backClone.style.height = `${cardHeight}px`;
                backClone.style.position = 'relative';
                backClone.style.overflow = 'hidden';
                backClone.style.borderRadius = '8px';
                backClone.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
                backClone.style.border = '1px solid black';
                backClone.style.padding = '15px';
                backClone.style.transform = 'rotate(180deg)';
    
                const backBgDiv = document.createElement('div');
                backBgDiv.style.position = 'absolute';
                backBgDiv.style.top = '0';
                backBgDiv.style.left = '0';
                backBgDiv.style.width = '100%';
                backBgDiv.style.height = '100%';
                backBgDiv.style.backgroundImage = backBgStyle;
                backBgDiv.style.backgroundSize = '100% 100%';
                backBgDiv.style.backgroundPosition = 'center center';
                backBgDiv.style.backgroundRepeat = 'no-repeat';
                backBgDiv.style.zIndex = '0';
                backClone.appendChild(backBgDiv);
    
                frontCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                    const clone = el.cloneNode(true);
                    clone.classList.remove('unIQue_empLOyee_Id_Card_selected');
                    const computedStyle = window.getComputedStyle(el);
                    for (let i = 0; i < computedStyle.length; i++) {
                        const property = computedStyle[i];
                        clone.style[property] = computedStyle.getPropertyValue(property);
                    }
                    clone.style.position = 'absolute';
                    clone.style.left = computedStyle.left;
                    clone.style.top = computedStyle.top;
                    clone.style.transform = computedStyle.transform;
                    clone.style.zIndex = computedStyle.zIndex;
                    if (clone.classList.contains('unIQue_empLOyee_Id_Card_profile-user-img')) {
                        clone.src = el.src;
                    } else if (clone.classList.contains('unIQue_empLOyee_Id_Card_qr-container')) {
                        const qrDiv = clone.querySelector('div');
                        if (qrDiv) {
                            qrDiv.innerHTML = '';
                            qrDiv.id = `unIQue_empLOyee_Id_Card_qr_clone_${Date.now()}`;
                            setTimeout(() => unIQue_empLOyee_Id_Card_generateQRCode(qrDiv.id, 120), 100);
                        }
                    } else if (clone.classList.contains('unIQue_empLOyee_Id_Card_image-container')) {
                        const img = clone.querySelector('img');
                        if (img) img.src = el.querySelector('img').src;
                    }
                    frontClone.appendChild(clone);
                });
    
                backCard.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                    const clone = el.cloneNode(true);
                    clone.classList.remove('unIQue_empLOyee_Id_Card_selected');
                    const computedStyle = window.getComputedStyle(el);
                    for (let i = 0; i < computedStyle.length; i++) {
                        const property = computedStyle[i];
                        clone.style[property] = computedStyle.getPropertyValue(property);
                    }
                    clone.style.position = 'absolute';
                    clone.style.left = computedStyle.left;
                    clone.style.top = computedStyle.top;
                    clone.style.transform = computedStyle.transform;
                    clone.style.zIndex = computedStyle.zIndex;
                    if (clone.classList.contains('unIQue_empLOyee_Id_Card_qr-container')) {
                        const qrDiv = clone.querySelector('div');
                        if (qrDiv) {
                            qrDiv.innerHTML = '';
                            qrDiv.id = `unIQue_empLOyee_Id_Card_qr_clone_${Date.now()}`;
                            setTimeout(() => unIQue_empLOyee_Id_Card_generateQRCode(qrDiv.id, 120), 100);
                        }
                    } else if (clone.classList.contains('unIQue_empLOyee_Id_Card_image-container')) {
                        const img = clone.querySelector('img');
                        if (img) img.src = el.querySelector('img').src;
                    }
                    backClone.appendChild(clone);
                });
    
                container.appendChild(frontClone);
                container.appendChild(backClone);
    
                const preloadImages = () => {
                    return new Promise(resolve => {
                        const imagesToLoad = [];
                        const extractUrl = bgStyle => {
                            if (!bgStyle || bgStyle === 'none') return null;
                            const match = bgStyle.match(/url\(['"]?(.*?)['"]?\)/);
                            return match ? match[1] : null;
                        };
    
                        const frontBgUrl = extractUrl(frontBgStyle);
                        const backBgUrl = extractUrl(backBgStyle);
                        if (frontBgUrl) imagesToLoad.push(frontBgUrl);
                        if (backBgUrl) imagesToLoad.push(backBgUrl);
    
                        document.querySelectorAll('.unIQue_empLOyee_Id_Card_profile-user-img, .unIQue_empLOyee_Id_Card_overlay-image').forEach(img => {
                            if (img.src) imagesToLoad.push(img.src);
                        });
    
                        if (imagesToLoad.length === 0) return resolve();
    
                        let loadedCount = 0;
                        imagesToLoad.forEach(src => {
                            const img = new Image();
                            img.onload = img.onerror = () => {
                                loadedCount++;
                                if (loadedCount === imagesToLoad.length) resolve();
                            };
                            img.src = src;
                        });
                    });
                };
    
                await preloadImages();
    
                setTimeout(() => {
                    html2canvas(container, {
                        scale: 2,
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        logging: false
                    }).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'employee_id_card.png';
                        link.href = canvas.toDataURL('image/png', 1.0);
                        link.click();
                        document.body.removeChild(link);
                        document.body.appendChild(container);
                    }).catch(err => {
                        console.error('Download failed:', error);
                        document.body.removeChild(container);
                        alert('Download បរាជ័ failed. Please try again.');
                    }).finally(() => {
                        downloadBtn.innerHTML = 'ទាញ download ប័ណ្ណសំគាល់ខ្លួន<i class="fas fa-print" style="margin-left: 10px;"></i>';
                        downloadBtn.disabled = false;
                    });
                }, 500);
            } catch (error) {
                console.error('Error generating PNG:', error);
                alert('Download failed. Please try again.');
                downloadBtn.innerHTML = 'ទាញ download ប័ណ្ណសំគាល់ខ្លួន<i class="fas fa-print" style="margin-left: 10px;"></i>';
                downloadBtn.disabled = false;
            }
        };
    
        document.addEventListener('DOMContentLoaded', () => {
            unIQue_empLOyee_Id_Card_selectedElements = [];
            unIQue_empLOyee_Id_Card_updateTextButton();
            unIQue_empLOyee_Id_Card_updateElementControls();
            
            // Initialize default layout
            unIQue_empLOyee_Id_Card_initializeDefaultLayout();
            
            // Load saved styles
            unIQue_empLOyee_Id_Card_loadFromLocalStorage();
            
            // Make existing elements draggable
            document.querySelectorAll('.unIQue_empLOyee_Id_Card_draggable').forEach(el => {
                unIQue_empLOyee_Id_Card_makeDraggable(el);
                if (el.classList.contains('unIQue_empLOyee_Id_Card_image-container')) {
                    ['bottom-right', 'bottom-left', 'top-right', 'top-left'].forEach(cls => {
                        if (!el.querySelector(`.unIQue_empLOyee_Id_Card_${cls}`)) {
                            const handle = document.createElement('div');
                            handle.className = `unIQue_empLOyee_Id_Card_resize-handle unIQue_empLOyee_Id_Card_${cls}`;
                            handle.addEventListener('mousedown', unIQue_empLOyee_Id_Card_initResize);
                            el.appendChild(handle);
                        }
                    });
                }
            });
    
            // Initialize card selector
            const cardSelector = document.getElementById('unIQue_empLOyee_Id_Card_cardSelector');
            if (cardSelector) {
                cardSelector.addEventListener('change', unIQue_empLOyee_Id_Card_updateCurrentCard);
                unIQue_empLOyee_Id_Card_updateCurrentCard();
            }
    
            // Add arrow key movement
            document.addEventListener('keydown', unIQue_empLOyee_Id_Card_moveElementWithArrowKeys);
            
            console.debug('Initialization complete');
        });
    </script>
</body>
</html>
