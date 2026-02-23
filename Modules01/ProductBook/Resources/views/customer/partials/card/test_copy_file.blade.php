<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Editor - Page 2</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --row-gap: 20px;
            --column-gap: 0px;
            --grid-padding: 10px;
            --card-margin: 0px;
            --card-padding: 19px;
            --card-width: 380px;
            --card-height: 560px;
        }
        .pspt_Page2_wrapper * {
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
        .pspt_Page2_wrapper {
            font-family: Arial, 'Battambang', sans-serif;
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            gap: 5px;
            background-color: #f5f5f5;
        }
        .pspt_Page2_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .pspt_Page2_filters-container h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }
        .pspt_Page2_filters-container hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }
        .pspt_Page2_category-toggle {
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
        .pspt_Page2_category-toggle:hover {
            background-color: #c8d8ff;
        }
        .pspt_Page2_category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }
        .pspt_Page2_category-content.active {
            max-height: 500px;
            padding: 10px;
        }
        .pspt_Page2_filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }
        .pspt_Page2_background-dropdown,
        .pspt_Page2_font-size-dropdown,
        .pspt_Page2_font-style-dropdown {
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
        .pspt_Page2_custom-button {
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
        .pspt_Page2_custom-button:hover {
            background-color: #138496;
        }
        .pspt_Page2_custom-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .pspt_Page2_reset-button {
            background-color: #dc3545;
            color: white;
        }
        .pspt_Page2_reset-button:hover {
            background-color: #c82333;
        }
        .pspt_Page2_secondary-button {
            background-color: #6c757d;
            color: white;
        }
        .pspt_Page2_secondary-button:hover {
            background-color: #5a6268;
        }
        .pspt_Page2_text-button {
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
        .pspt_Page2_text-button:hover {
            background-color: #138496;
        }
        .pspt_Page2_text-input,
        .pspt_Page2_color-picker {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .pspt_Page2_text-input:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }
        .pspt_Page2_hidden {
            display: none;
        }
        .pspt_Page2_arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }
        .pspt_Page2_category-toggle.active .pspt_Page2_arrow {
            transform: rotate(180deg);
        }
        .pspt_Page2_download-btn {
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
        .pspt_Page2_download-btn:hover {
            background-color: #138496;
        }
        .pspt_Page2_displayArea {
            width: 70%;
            padding: 5px;
            margin: 0;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .pspt_Page2_grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-gap: var(--row-gap) var(--column-gap);
            padding: var(--grid-padding);
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding));
            height: calc(2 * var(--card-height) + var(--row-gap) + 2 * var(--grid-padding));
        }
        .pspt_Page2_card {
            width: 100%;
            aspect-ratio: 380 / 560;
            margin: var(--card-margin);
            padding: var(--card-padding);
            border-radius: 8px;
            border: 2px solid #1C2526;
            background-color: #f5f5f5;
            position: relative;
            overflow: hidden;
            font-family: 'Battambang', sans-serif;
        }
        .pspt_Page2_card-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            pointer-events: none;
        }
        .pspt_Page2_profile-pic {
            position: absolute;
            width: 120px;
            height: 120px;
            overflow: hidden;
            cursor: move;
            pointer-events: auto;
            left: 250px;
            top: 20px;
            transition: left 0.1s, top 0.1s;
            will-change: transform;
        }
        .pspt_Page2_profile-pic.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }
        .pspt_Page2_profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .pspt_Page2_profile-pic.square img {
            border-radius: 0;
        }
        .pspt_Page2_profile-pic:not(.square) img {
            border-radius: 50%;
        }
        .pspt_Page2_text-element,
        .pspt_Page2_image-element {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-family: 'Battambang', sans-serif;
            transition: left 0.1s, top 0.1s;
            will-change: transform;
        }
        .pspt_Page2_text-element.dragging,
        .pspt_Page2_image-element.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }
        .pspt_Page2_text-element.selected,
        .pspt_Page2_image-element.selected,
        .pspt_Page2_profile-pic.selected,
        .pspt_Page2_qr-container.selected {
            border: 1px solid cyan;
        }
        .pspt_Page2_image-element {
            width: 100px;
            height: 100px;
        }
        .pspt_Page2_image-element img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .pspt_Page2_image-element.square img {
            border-radius: 0;
        }
        .pspt_Page2_image-element:not(.square) img {
            border-radius: 50%;
        }
        .pspt_Page2_resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
        }
        .pspt_Page2_resize-handle.top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }
        .pspt_Page2_resize-handle.top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }
        .pspt_Page2_resize-handle.bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }
        .pspt_Page2_resize-handle.bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }
        .pspt_Page2_qr-container {
            position: absolute;
            width: 80px;
            height: 80px;
            cursor: move;
            pointer-events: auto;
            background: white;
            border: 1px solid #ccc;
            transition: left 0.1s, top 0.1s;
            will-change: transform;
        }
        .pspt_Page2_qr-container.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }
        .pspt_Page2_qr-container div {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 3px;
        }
        #p2_card3, #p2_card4 {
            transform: rotate(180deg);
        }
        .passport-panel {
            position: relative;
            overflow: hidden;
        }
        .passport-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .verify-text {
            text-align: center;
            font-size: 11px;
        }
        .signature-box {
            width: 130px;
            min-height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0 auto;
        }
        .passport-white {
            background-color: white;
            padding: 20px;
            overflow-y: auto;
        }
        .bullet-list {
            list-style-type: none;
            
        }
        .bullet-list li {
            position: relative;
            padding-left: 15px;
            margin-bottom: 5px;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .bullet-list li:before {
            content: "-";
            position: absolute;
            left: 0;
            color: #2a3286;
        }
    </style>
</head>
<body>
    <div class="pspt_Page2_wrapper">
        <div class="pspt_Page2_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="pspt_Page2_category-toggle" onclick="pspt_Page2_toggleCategory('p2_background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="pspt_Page2_arrow">▼</span>
            </div>
            <div class="pspt_Page2_category-content" id="p2_background-controls">
                <div class="pspt_Page2_filter-group">
                    <select class="pspt_Page2_background-dropdown" id="p2_cardSelector"
                        onchange="pspt_Page2_updateCurrentCard()">
                        <option value="" data-background="" selected disabled>ជ្រើសរើសកញ្ចប់</option>
                         <option value="p2_card1" data-background="/docs/images/franchise_package/A.jpg">កញ្ចប់ A</option>
                        <option value="p2_card2" data-background="/docs/images/franchise_package/B.jpg">កញ្ចប់ B</option>
                        <option value="p2_card3" data-background="/docs/images/franchise_package/xs.jpg">កញ្ចប់ XS</option>
                    </select>
                    <select class="pspt_Page2_background-dropdown" id="p2_backgroundDropdown"
                        onchange="pspt_Page2_changeBackgroundFromDropdown()">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/1.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/2.png">ផ្ទៃខាងក្រោយ ២</option>
                        <option value="/docs/images/4.png">ផ្ទៃខាងក្រោយ ៣</option>
                    </select>
                    <button class="pspt_Page2_custom-button" id="p2_changeBackgroundBtn"
                        onclick="document.getElementById('p2_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="p2_imageUpload" accept="image/*"
                        onchange="pspt_Page2_changeBackground()" style="display: none;">
                    <button class="pspt_Page2_custom-button pspt_Page2_reset-button"
                        id="p2_resetBackgroundBtn"
                        onclick="pspt_Page2_resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="pspt_Page2_category-toggle" onclick="pspt_Page2_toggleCategory('p2_profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="pspt_Page2_arrow">▼</span>
            </div>
            <div class="pspt_Page2_category-content" id="p2_profile-controls">
                <div class="pspt_Page2_filter-group">
                    <button class="pspt_Page2_custom-button" id="p2_changeProfileBtn"
                        onclick="document.getElementById('p2_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="p2_profileUpload" accept="image/*"
                        onchange="pspt_Page2_changeProfilePic()" style="display: none;">
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_squareShapeBtn"
                        onclick="pspt_Page2_toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_circleShapeBtn"
                        onclick="pspt_Page2_toggleProfileShape(false)">រាងមូល</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_reset-button"
                        id="p2_resetProfileBtn"
                        onclick="pspt_Page2_resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="pspt_Page2_category-toggle" onclick="pspt_Page2_toggleCategory('p2_text-controls')">
                <i class="fas fa-edit"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="pspt_Page2_arrow">▼</span>
            </div>
            <div class="pspt_Page2_category-content" id="p2_text-controls">
                <div class="pspt_Page2_filter-group">
                    <input type="text" id="p2_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ"
                        class="pspt_Page2_text-input" oninput="pspt_Page2_updateTextButton()">
                    <input type="color" id="p2_textColorPicker" value="#000000"
                        class="pspt_Page2_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ"
                        onchange="pspt_Page2_changeTextColor()">
                    <select class="pspt_Page2_font-size-dropdown" id="p2_fontSizeDropdown"
                        onchange="pspt_Page2_changeFontSize()">
                        <option value="12">១២ ភីកសែល</option>
                        <option value="14">១៤ ភីកសែល</option>
                        <option value="16">១៦ ភីកសែល</option>
                        <option value="18">៧៨ ភីកសែល</option>
                        <option value="20">២០ ភីកសែល</option>
                        <option value="24">២៤ ភីកសែល</option>
                        <option value="30">៣៦ ភីកសែល</option>
                        <option value="36">៣៦ ភីកសែល</option>
                        <option value="custom">ផ្ទាល់ខ្លួន</option>
                    </select>
                    <input type="number" id="p2_customFontSize" class="pspt_Page2_hidden"
                        placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="pspt_Page2_font-style-dropdown" id="p2_fontStyleDropdown"
                        onchange="pspt_Page2_changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="pspt_Page2_text-button" id="p2_textButton"
                        onclick="pspt_Page2_handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_reset-button"
                        id="p2_cancelTextBtn" onclick="pspt_Page2_cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="pspt_Page2_category-toggle"
                onclick="pspt_Page2_toggleCategory('p2_image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="pspt_Page2_arrow">▼</span>
            </div>
            <div class="pspt_Page2_category-content" id="p2_image-overlay-controls">
                <div class="pspt_Page2_filter-group">
                    <button class="pspt_Page2_custom-button" id="p2_addImageBtn"
                        onclick="document.getElementById('p2_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="p2_imageUploadOverlay" accept="image/*"
                        onchange="pspt_Page2_addOverlayImage()" style="display: none;">
                    <button class="pspt_Page2_custom-button" id="p2_changeImageBtn"
                        onclick="document.getElementById('p2_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="p2_changeImageUpload" accept="image/*"
                        onchange="pspt_Page2_changeOverlayImage()" style="display: none;">
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_imageSquareShapeBtn"
                        onclick="pspt_Page2_toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_imageCircleShapeBtn"
                        onclick="pspt_Page2_toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="pspt_Page2_category-toggle" onclick="pspt_Page2_toggleCategory('p2_element-controls')">
                <i class="fas fa-trash-alt"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="pspt_Page2_arrow">▼</span>
            </div>
            <div class="pspt_Page2_category-content" id="p2_element-controls">
                <div class="pspt_Page2_filter-group">
                    <button class="pspt_Page2_custom-button pspt_Page2_reset-button"
                        id="p2_deleteSelectedElementsBtn"
                        onclick="pspt_Page2_deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_bringForwardBtn" onclick="pspt_Page2_bringForward()"
                        disabled>នាំមកមុខ</button>
                    <button class="pspt_Page2_custom-button pspt_Page2_secondary-button"
                        id="p2_sendBackwardBtn" onclick="pspt_Page2_sendBackward()"
                        disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="pspt_Page2_custom-button pspt_Page2_reset-button" id="p2_resetAllBtn"
                onclick="pspt_Page2_resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="pspt_Page2_download-btn" id="p2_downloadBtn"
                onclick="pspt_Page2_downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print"
                    style="margin-left: 10px;"></i></button>
        </div>
        <div class="pspt_Page2_displayArea">
            <div id="p2_cardsContainer" class="pspt_Page2_grid">
                <!-- Card 1 -->
                <div class="pspt_Page2_card" id="p2_card1">
                    <div class="pspt_Page2_card-content" style="background-image: url('/docs/images/1.png');">
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="pspt_Page2_card" id="p2_card2">
                    <div class="pspt_Page2_card-content" style="background-image: url('/docs/images/2.png');">
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="pspt_Page2_card" id="p2_card3">
                    <div class="pspt_Page2_card-content" style="background-image: url('/docs/images/4.png');">
                         <div class="verify-text" style="color: red; margin: 12px auto 8px;">
                            ពិន្ទុប្រចាំឆ្នាំ
                        </div>
                        <div class="signature-box"></div>
                        <div class="verify-text" style="margin: 20px auto 10px; color: black;">
                            ការប្រើប្រាស់
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; padding: 4px 0; color: black;">
                            ពិន្ទុ * ផ្កាយ1&ZeroWidthSpace;=1ពិន្ទុ, ផ្កាយ2=2ពិន្ទុ, ផ្កាយ3&ZeroWidthSpace;=3ពិន្ទុ, ផ្កាយ4&ZeroWidthSpace;=4ពិន្ទុ, ផ្កាយ5&ZeroWidthSpace;=5ពិន្ទុ <br>
                            រង្វាន់ * រង្វាន់គឺអាស្រ័យទៅតាមពិន្ទុរបស់លោកអ្នកប្រចាំឆ្នាំ តាមលក្ខ័ណ្ឌក្រុមហ៊ុន
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; margin-top: 60px;">
                            
                            <table cellpadding="5" cellspacing="0" style="font-size: 12.5px; color: #2a3286;" width="100%">
                                <tbody><tr>
                                  <td>យក្សាស្នាដៃរបស់កូនខ្មែរ</td>
                                  <td>យើងត្រូវតែរួមគ្នាថែរក្សា</td>
                                </tr>
                                <tr>
                                  <td>អោយគង់វង្សតទៅនៅលើលោកា</td>
                                  <td>ក្នុងរដ្ឋានៃដែនកម្ពុជា ។</td>
                                </tr>
                                <tr>
                                  <td>ត្រូវក្លាហានចេញមុខតតាំងប្រយុទ្ធ</td>
                                  <td>រួមសុខទុក្ខរីករាយជាមួយយក្សា</td>
                                </tr>
                                <tr>
                                  <td>ជួយស្ត្រី រឹងប៉ឹងនៅក្នុងគ្រួសារ</td>
                                  <td>ក្នុងមាគ៌ាសេដ្ឋកិច្ចដ៏រឹងមាំ ។</td>
                                </tr>
                                <tr>
                                  <td>រៀនសូត្រលត់ដំកុំអោយរួញរា</td>
                                  <td>យើងនាំគ្នាប្រឹងប្រែងជាប្រចាំ</td>
                                </tr>
                                <tr>
                                  <td>ទាំងក្រៅក្នុងជ្រោមជ្រែងអោយបានខ្លាំង</td>
                                  <td>កុំដេកចាំសំណាងពីលើមេឃ ។</td>
                                </tr>
                            </tbody></table>                              
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; color: black;">
                            <strong>ក្រុមហ៊ុន ឌឹហ្វក់សេស អាត ខូអិលធីឌី</strong> ជាក្រុមហ៊ុនគ្រប់គ្រង ចែកចាយ ផលិតផល 
                            នាំចេញ នាំចូល  ហ្វ្រែនឆាយគ្រប់ប្រភេទ។ យើងជាអ្នកផលិតផ្តាច់មុខនូវផលិតផល
                            យក្សា ដែលជាអ្នកជំនាញខាងសំអាត និង បោកអ៊ុត ផងដែរ ។
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; color: black;">
                            សៀវភៅនេះជាទ្រព្យសម្បត្តិរបស់ក្រុមហ៊ុន ឌឹហ្វក់សេស អាត ខូអិលធីឌី
                            ករណីរកឃើញសៀវភៅនេះដែលមិនមែនជាម្ចាស់សូមយកមករក្សាទុកនៅក្រុមហ៊ុនវិញ ឬ ទាក់ទង
                            ទូរស័ព្ទលេខ : 098 538 907 វិបសាយ: www.yeaksa.com
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="pspt_Page2_card" id="p2_card4">
                    <div class="pspt_Page2_card-content" style="background-color: white;">
                    </div>
                     <div class="pspt_Page2_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 5px;">ចក្ខុវិស័យ</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 33px; width: 340px">ធ្វើអោយគ្រួសារមានសុភមង្គលតាមរយៈអាជីវកម្មបោកអ៊ុតដែលប្រើប្រាស់ផលិតផល</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 48px; width: 340px">ខ្មែរ តម្លៃក្នុងស្រុក គុណភាពអន្តរជាតិ</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 71px;">បេសកកម្ម</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 99px; width: 340px">លើកកម្ពស់ម៉ាកយីហោ និង ផលិតផល ក្នុងស្រុក រួមចំណែកកសាងសេដ្ឋកិច្ចគ្រួសារ</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 114px; width: 340px">គ្រប់ផ្ទះបានប្រើប្រាស់ផលិតផលយក្សាដែលមានគុណភាពល្អបំផុត</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 129px; width: 340px">ភូមិមួយមានហាងបោកអ៊ុតមួយ</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 144px; width: 340px">ផ្តល់ការបណ្តុះបណ្តាលជំនាញបោកអ៊ុតស្តង់ដាដល់ស្ត្រីតាមសហគមន៍</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 159px; width: 340px">គ្រប់ហាងបោកសម្លៀកបំពាក់ទាំងអស់ប្រើប្រាស់ផលិតផលយក្សា</div>
                    <div class="pspt_Page2_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 182px;">ហេតុអ្វីបានជាចូលរួមសមាជិក?</div>
                    <ul class="pspt_Page2_text-element bullet-list" style="text-align: left; font-size: 12px; color: black; left: 10px; top: 210px; width: 340px">
                        <li style="text-align: left; font-size: 12px; color: black;">សម្រេចក្តីស្រមៃរបស់ខ្លួនដែលចង់ក្លាយជាសហគ្រិន</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ក្លាយជាម្ចាស់អាជីវកម្មយក្សាបោកអ៊ុតដ៏ល្បីល្បាញពេញពិភពលោក</li>
                        <li style="text-align: left; font-size: 12px; color: black;">មានទីប្រឹក្សាផ្ទាល់រហូតអស់មួយជីវិតអំពីអាជីវកម្មបោកអ៊ុត</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានការបង្ហាត់បង្រៀនបច្ចេកទេសថ្មីៗពេញលេញ</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ក្លាយជាភ្នាក់ងារហ្វ្រែនឆាយ និង តំណាងលក់ផលិតផល</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានវិញ្ញាបនបត្របញ្ជាក់សាខាពេញច្បាប់របស់យក្សា</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានប្រព័ន្ធគ្រប់គ្រងហិរញ្ញវត្ថុក្នុងហាង</li>
                        <li style="text-align: left; font-size: 12px; color: black;">មានអតិថិជនស្រាប់ក្នុងដៃដោយសារតែភាពល្បីល្បាញ</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ជួយក្នុងការផ្សព្វផ្សាយបន្ថែមពីក្រុមហ៊ុន</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ក្លាយខ្លួនជាអ្នកលក់ផលិតផលក្រុមហ៊ុនភ្លាមៗ</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានសិទ្ធិពិសេសក្នុងការទិញលក់ផលិតផល</li>
                        <li style="text-align: left; font-size: 12px; color: black;">បង្កើតទំនាក់ទំនងថ្មីៗ និងពង្រីកបណ្តាញសាធារណៈ</li>
                        <li style="text-align: left; font-size: 12px; color: black;">ចែករំលែកចំណេះដឹង និងបទពិសោធន៍ជាមួយសមាជិកដទៃទៀត</li>
                        <li style="text-align: left; font-size: 12px; color: black;">រួមចំណែកដល់សហគមន៍ដើម្បីសេដ្ឋកិច្ចគ្រួសារ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function pspt_Page2_toggleCategory(id) {
                const content = document.getElementById(id);
                const toggle = content.previousElementSibling;
                
                document.querySelectorAll('.pspt_Page2_category-content').forEach(item => {
                    if (item !== content) {
                        item.classList.remove('active');
                        item.previousElementSibling.classList.remove('active');
                    }
                });

                content.classList.toggle('active');
                toggle.classList.toggle('active');
            }
            window.pspt_Page2_toggleCategory = pspt_Page2_toggleCategory;
            let selectedElement = null;
            let currentCard = document.getElementById('p2_card1');
            let zIndexCounter = 10;
            let profilePics = {
                'p2_card1': null,
                'p2_card2': null,
                'p2_card3': null,
                'p2_card4': null
            };
            let textCounter = 24;

            function pspt_Page2_updateCurrentCard() {
                const cardSelector = document.getElementById('p2_cardSelector');
                const selectedOption = cardSelector.options[cardSelector.selectedIndex];
                const backgroundUrl = selectedOption.dataset.background;
                currentCard = document.getElementById(cardSelector.value);

                const card2 = document.getElementById('p2_card2');
                const card2Content = card2.querySelector('.pspt_Page2_card-content');

                if (backgroundUrl) {
                    card2Content.style.backgroundImage = `url(${backgroundUrl})`;
                    card2Content.style.backgroundColor = '';
                } else {
                    card2Content.style.backgroundImage = 'none';
                    card2Content.style.backgroundColor = 'white';
                }
            }

            window.pspt_Page2_updateCurrentCard = pspt_Page2_updateCurrentCard;

            function pspt_Page2_changeBackgroundFromDropdown() {
                const dropdown = document.getElementById('p2_backgroundDropdown');
                const cardContent = currentCard.querySelector('.pspt_Page2_card-content');
                if (dropdown.value) {
                    cardContent.style.backgroundImage = `url(${dropdown.value})`;
                }
            }
            window.pspt_Page2_changeBackgroundFromDropdown = pspt_Page2_changeBackgroundFromDropdown;

            function pspt_Page2_changeBackground() {
                const fileInput = document.getElementById('p2_imageUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const cardContent = currentCard.querySelector('.pspt_Page2_card-content');
                        cardContent.style.backgroundImage = `url(${e.target.result})`;
                    };
                    reader.onerror = function() {
                        alert('មិនអាចផ្ទុករូបភាពបានទេ។ សូមព្យាយាមម្តងទៀត។');
                    };
                    reader.readAsDataURL(file);
                }
            }
            window.pspt_Page2_changeBackground = pspt_Page2_changeBackground;

            function pspt_Page2_resetBackground() {
                const cardContent = currentCard.querySelector('.pspt_Page2_card-content');
                const cardId = currentCard.id;
                const defaultBackgrounds = {
                    'p2_card1': '/docs/images/1.png',
                    'p2_card2': '/docs/images/2.png',
                    'p2_card3': '/docs/images/4.png',
                    'p2_card4': 'white'
                };
                if(cardId === 'p2_card4'){
                    cardContent.style.backgroundImage = 'none';
                    cardContent.style.backgroundColor = defaultBackgrounds[cardId];
                } else {
                     cardContent.style.backgroundImage = `url(${defaultBackgrounds[cardId]})`;
                }
                document.getElementById('p2_backgroundDropdown').value = '';
                document.getElementById('p2_imageUpload').value = '';
            }
            window.pspt_Page2_resetBackground = pspt_Page2_resetBackground;

            function pspt_Page2_changeProfilePic() {
                pspt_Page2_updateCurrentCard();
                const fileInput = document.getElementById('p2_profileUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const cardId = currentCard.id;
                        let profilePic = currentCard.querySelector('.pspt_Page2_profile-pic');
                        if (!profilePic) {
                            profilePic = document.createElement('div');
                            profilePic.classList.add('pspt_Page2_profile-pic');
                            profilePic.id = `p2_profile-pic-${cardId}`;
                            const img = document.createElement('img');
                            img.alt = 'Profile';
                            profilePic.appendChild(img);
                            profilePic.style.left = '250px';
                            profilePic.style.top = '20px';
                            currentCard.appendChild(profilePic);
                            makeElementDraggable(profilePic);
                        }
                        profilePic.querySelector('img').src = e.target.result;
                        profilePics[cardId] = profilePic;
                        selectElement(profilePic);
                    };
                    reader.onerror = function() {
                        alert('មិនអាចផ្ទុករូបភាពប្រវត្តិរូបបានទេ។ សូមព្យាយាមម្តងទៀត។');
                    };
                    reader.readAsDataURL(file);
                }
            }
            window.pspt_Page2_changeProfilePic = pspt_Page2_changeProfilePic;

            function pspt_Page2_resetProfile() {
                pspt_Page2_updateCurrentCard();
                const cardId = currentCard.id;
                const profilePic = currentCard.querySelector('.pspt_Page2_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = 'https://via.placeholder.com/120';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    profilePics[cardId] = profilePic;
                    document.getElementById('p2_profileUpload').value = '';
                    selectElement(profilePic);
                }
            }
            window.pspt_Page2_resetProfile = pspt_Page2_resetProfile;

            function pspt_Page2_toggleProfileShape(isSquare) {
                pspt_Page2_updateCurrentCard();
                const cardId = currentCard.id;
                const profilePic = profilePics[cardId] || currentCard.querySelector('.pspt_Page2_profile-pic');
                if (profilePic) {
                    if (isSquare) {
                        profilePic.classList.add('square');
                    } else {
                        profilePic.classList.remove('square');
                    }
                }
            }
            window.pspt_Page2_toggleProfileShape = pspt_Page2_toggleProfileShape;

            function pspt_Page2_addOverlayImage() {
                pspt_Page2_updateCurrentCard();
                const fileInput = document.getElementById('p2_imageUploadOverlay');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageElement = document.createElement('div');
                        imageElement.classList.add('pspt_Page2_image-element');
                        imageElement.id = `p2_image-element-${textCounter++}`;
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        imageElement.appendChild(img);
                        imageElement.style.zIndex = zIndexCounter++;
                        imageElement.style.left = '50px';
                        imageElement.style.top = '50px';
                        currentCard.appendChild(imageElement);
                        makeElementDraggable(imageElement);
                        selectElement(imageElement);
                    };
                    reader.onerror = function() {
                        alert('មិនអាចបន្ថែមរូបភាពបានទេ១ សូមព្យាយាមម្តងទៀត១');
                    };
                    reader.readAsDataURL(file);
                }
            }
            window.pspt_Page2_addOverlayImage = pspt_Page2_addOverlayImage;

            function pspt_Page2_changeOverlayImage() {
                const fileInput = document.getElementById('p2_changeImageUpload');
                const file = fileInput.files[0];
                if (file && selectedElement && selectedElement.classList.contains('pspt_Page2_image-element')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        selectedElement.querySelector('img').src = e.target.result;
                    };
                    reader.onerror = function() {
                        alert('មិនអាចផ្លាស់ប្តូររូបភាពបានទេ១ សូមព្យាយាមម្តងទៀត១');
                    };
                    reader.readAsDataURL(file);
                }
            }
            window.pspt_Page2_changeOverlayImage = pspt_Page2_changeOverlayImage;

            function pspt_Page2_toggleImageShape(isSquare) {
                if (selectedElement && selectedElement.classList.contains('pspt_Page2_image-element')) {
                    if (isSquare) {
                        selectedElement.classList.add('square');
                    } else {
                        selectedElement.classList.remove('square');
                    }
                }
            }
            window.pspt_Page2_toggleImageShape = pspt_Page2_toggleImageShape;

            function pspt_Page2_updateTextButton() {
                const textInput = document.getElementById('p2_textInput');
                const textButton = document.getElementById('p2_textButton');
                textButton.textContent = textInput.value && selectedElement && selectedElement.classList.contains(
                    'pspt_Page2_text-element') ? 'ធ្វើបច្ចុប្បន្នភាពអត្ថបទ' : 'បន្ថែមអត្ថបទ';
            }
            window.pspt_Page2_updateTextButton = pspt_Page2_updateTextButton;

            function pspt_Page2_handleTextAction() {
                const textInput = document.getElementById('p2_textInput');
                const fontSizeDropdown = document.getElementById('p2_fontSizeDropdown');
                const fontStyleDropdown = document.getElementById('p2_fontStyleDropdown');
                const textColorPicker = document.getElementById('p2_textColorPicker');
                const customFontSize = document.getElementById('p2_customFontSize');
                pspt_Page2_updateCurrentCard();
                if (textInput.value) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown
                        .value + 'px';
                    let textElement;
                    if (selectedElement && selectedElement.classList.contains('pspt_Page2_text-element')) {
                        textElement = selectedElement;
                    } else {
                        textElement = document.createElement('p');
                        textElement.classList.add('pspt_Page2_text-element');
                        textElement.id = `p2_text-element-${textCounter++}`;
                        textElement.style.zIndex = zIndexCounter++;
                        textElement.style.left = '50px';
                        textElement.style.top = '50px';
                        currentCard.appendChild(textElement);
                        makeElementDraggable(textElement);
                    }
                    textElement.textContent = textInput.value;
                    textElement.style.fontSize = fontSize;
                    textElement.style.fontFamily = fontStyleDropdown.value;
                    textElement.style.color = textColorPicker.value;
                    textInput.value = '';
                    pspt_Page2_updateTextButton();
                    selectElement(textElement);
                }
            }
            window.pspt_Page2_handleTextAction = pspt_Page2_handleTextAction;

            function pspt_Page2_changeFontSize() {
                const fontSizeDropdown = document.getElementById('p2_fontSizeDropdown');
                const customFontSize = document.getElementById('p2_customFontSize');
                customFontSize.classList.toggle('pspt_Page2_hidden', fontSizeDropdown.value !== 'custom');
                if (selectedElement && selectedElement.classList.contains('pspt_Page2_text-element')) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown
                        .value + 'px';
                    selectedElement.style.fontSize = fontSize;
                }
            }
            window.pspt_Page2_changeFontSize = pspt_Page2_changeFontSize;

            function pspt_Page2_changeFontStyle() {
                const fontStyleDropdown = document.getElementById('p2_fontStyleDropdown');
                if (selectedElement && selectedElement.classList.contains('pspt_Page2_text-element')) {
                    selectedElement.style.fontFamily = fontStyleDropdown.value;
                }
            }
            window.pspt_Page2_changeFontStyle = pspt_Page2_changeFontStyle;

            function pspt_Page2_changeTextColor() {
                const textColorPicker = document.getElementById('p2_textColorPicker');
                if (selectedElement && selectedElement.classList.contains('pspt_Page2_text-element')) {
                    selectedElement.style.color = textColorPicker.value;
                }
            }
            window.pspt_Page2_changeTextColor = pspt_Page2_changeTextColor;

            function pspt_Page2_cancelText() {
                const textInput = document.getElementById('p2_textInput');
                textInput.value = '';
                pspt_Page2_updateTextButton();
                if (selectedElement) {
                    selectedElement.classList.remove('selected');
                    selectedElement = null;
                    updateElementControls();
                }
            }
            window.pspt_Page2_cancelText = pspt_Page2_cancelText;
            
            function makeElementDraggable(element) {
                let isDragging = false;
                let currentX = parseFloat(element.style.left) || 0;
                let currentY = parseFloat(element.style.top) || 0;
                let initialX, initialY;
                element.addEventListener('mousedown', startDragging);
                function startDragging(e) {
                    if (e.target.classList.contains('pspt_Page2_resize-handle')) return;
                    selectElement(element);
                    initialX = e.clientX - currentX;
                    initialY = e.clientY - currentY;
                    isDragging = true;
                    element.classList.add('dragging');
                    document.addEventListener('mousemove', drag);
                    document.addEventListener('mouseup', stopDragging);
                    e.preventDefault();
                }
                function drag(e) {
                    if (isDragging) {
                        e.preventDefault();
                        currentX = e.clientX - initialX;
                        currentY = e.clientY - initialY;
                        requestAnimationFrame(() => {
                            element.style.left = `${currentX}px`;
                            element.style.top = `${currentY}px`;
                        });
                    }
                }
                function stopDragging() {
                    if (isDragging) {
                        isDragging = false;
                        element.classList.remove('dragging');
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('mouseup', stopDragging);
                    }
                }
                element.style.position = 'absolute';
                if (!element.style.left) element.style.left = '0px';
                if (!element.style.top) element.style.top = '0px';
            }

            function selectElement(element) {
                if (selectedElement) {
                    selectedElement.classList.remove('selected');
                }
                selectedElement = element;
                if (element) {
                    element.classList.add('selected');
                    if (element.classList.contains('pspt_Page2_text-element')) {
                        const textInput = document.getElementById('p2_textInput');
                        textInput.value = element.textContent;
                        pspt_Page2_updateTextButton();
                    } else {
                        document.getElementById('p2_textInput').value = '';
                        pspt_Page2_updateTextButton();
                    }
                }
                updateElementControls();
            }

            function updateElementControls() {
                const bringForwardBtn = document.getElementById('p2_bringForwardBtn');
                const sendBackwardBtn = document.getElementById('p2_sendBackwardBtn');
                const deleteBtn = document.getElementById('p2_deleteSelectedElementsBtn');
                bringForwardBtn.disabled = !selectedElement;
                sendBackwardBtn.disabled = !selectedElement;
                deleteBtn.disabled = !selectedElement;
            }
            window.updateElementControls = updateElementControls;

            function pspt_Page2_bringForward() {
                if (selectedElement) {
                    selectedElement.style.zIndex = zIndexCounter++;
                }
            }
            window.pspt_Page2_bringForward = pspt_Page2_bringForward;

            function pspt_Page2_sendBackward() {
                if (selectedElement && parseInt(selectedElement.style.zIndex) > 10) {
                    selectedElement.style.zIndex = parseInt(selectedElement.style.zIndex) - 1;
                }
            }
            window.pspt_Page2_sendBackward = pspt_Page2_sendBackward;

            function pspt_Page2_deleteSelectedElements() {
                if (selectedElement) {
                    const cardId = currentCard.id;
                    if (selectedElement.classList.contains('pspt_Page2_profile-pic')) {
                        profilePics[cardId] = null;
                    }
                    selectedElement.remove();
                    selectedElement = null;
                    updateElementControls();
                    document.getElementById('p2_textInput').value = '';
                    pspt_Page2_updateTextButton();
                }
            }
            window.pspt_Page2_deleteSelectedElements = pspt_Page2_deleteSelectedElements;

            function pspt_Page2_resetLayout() {
                const cards = document.querySelectorAll('.pspt_Page2_card');
                const defaultBackgrounds = {
                    'p2_card1': '/docs/images/1.png',
                    'p2_card2': '/docs/images/2.png',
                    'p2_card3': '/docs/images/4.png',
                    'p2_card4': 'white'
                };
                cards.forEach((card, index) => {
                    const cardContent = card.querySelector('.pspt_Page2_card-content');
                    const cardId = card.id;
                    
                    if(cardId === 'p2_card4'){
                        cardContent.style.backgroundImage = 'none';
                        cardContent.style.backgroundColor = defaultBackgrounds[cardId];
                    } else {
                         cardContent.style.backgroundImage = `url(${defaultBackgrounds[cardId]})`;
                    }

                    const imageElements = card.querySelectorAll('.pspt_Page2_image-element');
                    imageElements.forEach(element => element.remove());
                    const profilePic = card.querySelector('.pspt_Page2_profile-pic');
                    if (profilePic) {
                        profilePic.remove();
                    }
                });
                
                document.getElementById('p2_backgroundDropdown').value = '';
                document.getElementById('p2_imageUpload').value = '';
                document.getElementById('p2_textInput').value = '';
                pspt_Page2_updateTextButton();
                document.getElementById('p2_profileUpload').value = '';
                document.getElementById('p2_imageUploadOverlay').value = '';
                document.getElementById('p2_changeImageUpload').value = '';
                selectedElement = null;
                updateElementControls();
            }
            window.pspt_Page2_resetLayout = pspt_Page2_resetLayout;

            async function pspt_Page2_downloadCards() {
                const downloadBtn = document.getElementById('p2_downloadBtn');
                const cardsContainer = document.getElementById('p2_cardsContainer');
                downloadBtn.innerHTML = 'កំពុងបង្កើត PNG...';
                downloadBtn.disabled = true;
                try {
                    const a4Canvas = document.createElement('canvas');
                    a4Canvas.width = 794 * 2;
                    a4Canvas.height = 1123 * 2;
                    const ctx = a4Canvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, a4Canvas.width, a4Canvas.height);
                    const gridCanvas = await html2canvas(cardsContainer, {
                        scale: 2,
                        backgroundColor: '#ffffff',
                        useCORS: true
                    });
                    const gridWidth = gridCanvas.width;
                    const gridHeight = gridCanvas.height;
                    const scale = Math.min((a4Canvas.width - 80) / gridWidth, (a4Canvas.height - 80) / gridHeight);
                    const scaledWidth = gridWidth * scale;
                    const scaledHeight = gridHeight * scale;
                    const offsetX = (a4Canvas.width - scaledWidth) / 2;
                    const offsetY = (a4Canvas.height - scaledHeight) / 2;
                    ctx.drawImage(gridCanvas, offsetX, offsetY, scaledWidth, scaledHeight);
                    const link = document.createElement('a');
                    link.download = 'id_cards_page2.png';
                    link.href = a4Canvas.toDataURL('image/png');
                    link.click();
                } catch (error) {
                    console.error('Error generating PNG:', error);
                    alert('បរាជ័យក្នុងការបង្កើត PNG');
                } finally {
                    downloadBtn.innerHTML = 'បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i>';
                    downloadBtn.disabled = false;
                }
            }
            window.pspt_Page2_downloadCards = pspt_Page2_downloadCards;

            const textElements = document.querySelectorAll('.pspt_Page2_text-element');
            textElements.forEach(element => makeElementDraggable(element));
            const profilePicsElements = document.querySelectorAll('.pspt_Page2_profile-pic');
            profilePicsElements.forEach(pic => {
                makeElementDraggable(pic);
                const cardId = pic.closest('.pspt_Page2_card').id;
                profilePics[cardId] = pic;
            });
            const qrContainers = document.querySelectorAll('.pspt_Page2_qr-container');
            qrContainers.forEach(container => {
                makeElementDraggable(container);
            });
            document.getElementById('p2_cardSelector').addEventListener('change',
                pspt_Page2_updateCurrentCard);
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.pspt_Page2_text-element') &&
                    !e.target.closest('.pspt_Page2_image-element') &&
                    !e.target.closest('.pspt_Page2_profile-pic') &&
                    !e.target.closest('.pspt_Page2_qr-container') &&
                    !e.target.closest('.pspt_Page2_filters-container')) {
                    selectElement(null);
                }
            });
            document.addEventListener('keydown', (e) => {
                if (selectedElement && ['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'].includes(e
                    .key)) {
                    e.preventDefault();
                    let currentX = parseFloat(selectedElement.style.left) || 0;
                    let currentY = parseFloat(selectedElement.style.top) || 0;
                    const moveBy = 1;
                    switch (e.key) {
                        case 'ArrowLeft':
                            currentX -= moveBy;
                            break;
                        case 'ArrowRight':
                            currentX += moveBy;
                            break;
                        case 'ArrowUp':
                            currentY -= moveBy;
                            break;
                        case 'ArrowDown':
                            currentY += moveBy;
                            break;
                    }
                    requestAnimationFrame(() => {
                        selectedElement.style.left = `${currentX}px`;
                        selectedElement.style.top = `${currentY}px`;
                    });
                }
            });
        });
    </script>