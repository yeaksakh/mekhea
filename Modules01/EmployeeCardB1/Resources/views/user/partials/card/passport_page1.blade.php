<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Editor - Page 1</title>
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

        .pspt_Employee_wrapper * {
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

        .pspt_Employee_wrapper {
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

        .pspt_Employee_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }

        .pspt_Employee_filters-container h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }

        .pspt_Employee_filters-container hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }

        .pspt_Employee_category-toggle {
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

        .pspt_Employee_category-toggle:hover {
            background-color: #c8d8ff;
        }

        .pspt_Employee_category-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .pspt_Employee_category-content.active {
            max-height: 500px;
            padding: 10px;
        }

        .pspt_Employee_filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .pspt_Employee_background-dropdown,
        .pspt_Employee_font-size-dropdown,
        .pspt_Employee_font-style-dropdown {
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

        .pspt_Employee_custom-button {
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

        .pspt_Employee_custom-button:hover {
            background-color: #138496;
        }

        .pspt_Employee_custom-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .pspt_Employee_reset-button {
            background-color: #dc3545;
            color: white;
        }

        .pspt_Employee_reset-button:hover {
            background-color: #c82333;
        }

        .pspt_Employee_secondary-button {
            background-color: #6c757d;
            color: white;
        }

        .pspt_Employee_secondary-button:hover {
            background-color: #5a6268;
        }

        .pspt_Employee_text-button {
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

        .pspt_Employee_text-button:hover {
            background-color: #138496;
        }

        .pspt_Employee_text-input,
        .pspt_Employee_color-picker {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .pspt_Employee_text-input:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }

        .pspt_Employee_hidden {
            display: none;
        }

        .pspt_Employee_arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }

        .pspt_Employee_category-toggle.active .pspt_Employee_arrow {
            transform: rotate(180deg);
        }

        .pspt_Employee_download-btn {
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

        .pspt_Employee_download-btn:hover {
            background-color: #138496;
        }

        .pspt_Employee_displayArea {
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

        .pspt_Employee_grid {
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

        .pspt_Employee_card {
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

        .pspt_Employee_card-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            pointer-events: none;
        }

        .pspt_Employee_profile-pic {
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

        .pspt_Employee_profile-pic.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }

        .pspt_Employee_profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pspt_Employee_profile-pic.square img {
            border-radius: 0;
        }

        .pspt_Employee_profile-pic:not(.square) img {
            border-radius: 50%;
        }

        .pspt_Employee_text-element,
        .pspt_Employee_image-element {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-family: 'Battambang', sans-serif;
            transition: left 0.1s, top 0.1s;
            will-change: transform;
        }

        .pspt_Employee_text-element.dragging,
        .pspt_Employee_image-element.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }

        .pspt_Employee_text-element.selected,
        .pspt_Employee_image-element.selected,
        .pspt_Employee_profile-pic.selected,
        .pspt_Employee_qr-container.selected {
            border: 1px solid cyan;
        }

        .pspt_Employee_image-element {
            width: 100px;
            height: 100px;
        }

        .pspt_Employee_image-element img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pspt_Employee_image-element.square img {
            border-radius: 0;
        }

        .pspt_Employee_image-element:not(.square) img {
            border-radius: 50%;
        }

        .pspt_Employee_resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
        }

        .pspt_Employee_resize-handle.top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }

        .pspt_Employee_resize-handle.top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }

        .pspt_Employee_resize-handle.bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }

        .pspt_Employee_resize-handle.bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }

        .pspt_Employee_qr-container {
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

        .pspt_Employee_qr-container.dragging {
            transition: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
            opacity: 0.9;
        }

        .pspt_Employee_qr-container div {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="pspt_Employee_wrapper">
        <div class="pspt_Employee_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="pspt_Employee_category-toggle" onclick="pspt_Employee_toggleCategory('background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="pspt_Employee_arrow">▼</span>
            </div>
            <div class="pspt_Employee_category-content" id="background-controls">
                <div class="pspt_Employee_filter-group">
                    <select class="pspt_Employee_background-dropdown" id="pspt_Employee_cardSelector"
                        onchange="pspt_Employee_updateCurrentCard()">
                        <option value="card1">កាត ១</option>
                        <option value="card2">កាត ២</option>
                        <option value="card3">កាត ៣</option>
                        <option value="card4">កាត ៤</option>
                    </select>
                    <select class="pspt_Employee_background-dropdown" id="pspt_Employee_backgroundDropdown"
                        onchange="pspt_Employee_changeBackgroundFromDropdown()">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/5.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/6.png">ផ្ទៃខាងក្រោយ ២</option>
                        <option value="/docs/images/7.png">ផ្ទៃខាងក្រោយ ៣</option>
                        <option value="/docs/images/8.png">ផ្ទៃខាងក្រោយ ៤</option>
                    </select>
                    <button class="pspt_Employee_custom-button" id="pspt_Employee_changeBackgroundBtn"
                        onclick="document.getElementById('pspt_Employee_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="pspt_Employee_imageUpload" accept="image/*"
                        onchange="pspt_Employee_changeBackground()" style="display: none;">
                    <button class="pspt_Employee_custom-button pspt_Employee_reset-button"
                        id="pspt_Employee_resetBackgroundBtn"
                        onclick="pspt_Employee_resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="pspt_Employee_category-toggle" onclick="pspt_Employee_toggleCategory('profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="pspt_Employee_arrow">▼</span>
            </div>
            <div class="pspt_Employee_category-content" id="profile-controls">
                <div class="pspt_Employee_filter-group">
                    <button class="pspt_Employee_custom-button" id="pspt_Employee_changeProfileBtn"
                        onclick="document.getElementById('pspt_Employee_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="pspt_Employee_profileUpload" accept="image/*"
                        onchange="pspt_Employee_changeProfilePic()" style="display: none;">
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_squareShapeBtn"
                        onclick="pspt_Employee_toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_circleShapeBtn"
                        onclick="pspt_Employee_toggleProfileShape(false)">រាងមូល</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_reset-button"
                        id="pspt_Employee_resetProfileBtn"
                        onclick="pspt_Employee_resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="pspt_Employee_category-toggle" onclick="pspt_Employee_toggleCategory('text-controls')">
                <i class="fas fa-font"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="pspt_Employee_arrow">▼</span>
            </div>
            <div class="pspt_Employee_category-content" id="text-controls">
                <div class="pspt_Employee_filter-group">
                    <input type="text" id="pspt_Employee_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ"
                        class="pspt_Employee_text-input" oninput="pspt_Employee_updateTextButton()">
                    <input type="color" id="pspt_Employee_textColorPicker" value="#000000"
                        class="pspt_Employee_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ"
                        onchange="pspt_Employee_changeTextColor()">
                    <select class="pspt_Employee_font-size-dropdown" id="pspt_Employee_fontSizeDropdown"
                        onchange="pspt_Employee_changeFontSize()">
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
                    <input type="number" id="pspt_Employee_customFontSize" class="pspt_Employee_hidden"
                        placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="pspt_Employee_font-style-dropdown" id="pspt_Employee_fontStyleDropdown"
                        onchange="pspt_Employee_changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="pspt_Employee_text-button" id="pspt_Employee_textButton"
                        onclick="pspt_Employee_handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_reset-button"
                        id="pspt_Employee_cancelTextBtn" onclick="pspt_Employee_cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="pspt_Employee_category-toggle"
                onclick="pspt_Employee_toggleCategory('image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="pspt_Employee_arrow">▼</span>
            </div>
            <div class="pspt_Employee_category-content" id="image-overlay-controls">
                <div class="pspt_Employee_filter-group">
                    <button class="pspt_Employee_custom-button" id="pspt_Employee_addImageBtn"
                        onclick="document.getElementById('pspt_Employee_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="pspt_Employee_imageUploadOverlay" accept="image/*"
                        onchange="pspt_Employee_addOverlayImage()" style="display: none;">
                    <button class="pspt_Employee_custom-button" id="pspt_Employee_changeImageBtn"
                        onclick="document.getElementById('pspt_Employee_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="pspt_Employee_changeImageUpload" accept="image/*"
                        onchange="pspt_Employee_changeOverlayImage()" style="display: none;">
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_imageSquareShapeBtn"
                        onclick="pspt_Employee_toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_imageCircleShapeBtn"
                        onclick="pspt_Employee_toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="pspt_Employee_category-toggle" onclick="pspt_Employee_toggleCategory('element-controls')">
                <i class="fas fa-cog"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="pspt_Employee_arrow">▼</span>
            </div>
            <div class="pspt_Employee_category-content" id="element-controls">
                <div class="pspt_Employee_filter-group">
                    <button class="pspt_Employee_custom-button pspt_Employee_reset-button"
                        id="pspt_Employee_deleteSelectedElementsBtn"
                        onclick="pspt_Employee_deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_bringForwardBtn" onclick="pspt_Employee_bringForward()"
                        disabled>នាំមកមុខ</button>
                    <button class="pspt_Employee_custom-button pspt_Employee_secondary-button"
                        id="pspt_Employee_sendBackwardBtn" onclick="pspt_Employee_sendBackward()"
                        disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="pspt_Employee_custom-button pspt_Employee_reset-button" id="pspt_Employee_resetAllBtn"
                onclick="pspt_Employee_resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="pspt_Employee_download-btn" id="pspt_Employee_downloadBtn"
                onclick="pspt_Employee_downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print"
                    style="margin-left: 10px;"></i></button>
        </div>
        <div class="pspt_Employee_displayArea">
            <div id="pspt_Employee_cardsContainer" class="pspt_Employee_grid">
                <!-- Card 1 -->
                <div class="pspt_Employee_card" id="card1">
                    <div class="pspt_Employee_card-content" style="background-image: url('/docs/images/5.png');">
                    </div>
                    <div class="pspt_Employee_profile-pic" id="profile-pic-card1" style="left: 250px; top: 20px;">
                        <img src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile">
                    </div>
                    <h3 class="pspt_Employee_text-element" id="pspt_Employee_text-element-0"
                        style="left: 20px; top: 20px; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>
                        {{ $user->user_full_name ?? 'John Doe' }}
                    </h3>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-1"
                        style="left: 20px; top: 60px; font-size: 14px;">
                        តួនាទី ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-2"
                        style="left: 20px; top: 90px; font-size: 14px;">
                        <i class="fas fa-phone-square-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $user->contact_number ?? '+855 123 456 789' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-3"
                        style="left: 20px; top: 120px; font-size: 14px;">
                        <i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->name ?? 'Tech Solutions' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-4"
                        style="left: 20px; top: 150px; font-size: 14px;">
                        <i class="fas fa-map-marked-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->landmark ?? '456 Elm St' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-5"
                        style="left: 20px; top: 180px; font-size: 12px;">
                        {{ $work_location->city ?? 'Phnom Penh' }}
                    </p>
                    <div class="pspt_Employee_qr-container" style="left: 280px; bottom: 20px;">
                        <div id="pspt_Employee_qrCode1"></div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="pspt_Employee_card" id="card2">
                    <div class="pspt_Employee_card-content" style="background-image: url('/docs/images/6.png');">
                    </div>
                    <div class="pspt_Employee_profile-pic" id="profile-pic-card2" style="left: 250px; top: 20px;">
                        <img src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile">
                    </div>
                    <h3 class="pspt_Employee_text-element" id="pspt_Employee_text-element-6"
                        style="left: 20px; top: 20px; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>
                        {{ $user->user_full_name ?? 'John Doe' }}
                    </h3>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-7"
                        style="left: 20px; top: 60px; font-size: 14px;">
                        តួនាទី ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-8"
                        style="left: 20px; top: 90px; font-size: 14px;">
                        <i class="fas fa-phone-square-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $user->contact_number ?? '+855 123 456 789' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-9"
                        style="left: 20px; top: 120px; font-size: 14px;">
                        <i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->name ?? 'Tech Solutions' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-10"
                        style="left: 20px; top: 150px; font-size: 14px;">
                        <i class="fas fa-map-marked-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->landmark ?? '456 Elm St' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-11"
                        style="left: 20px; top: 180px; font-size: 12px;">
                        {{ $work_location->city ?? 'Phnom Penh' }}
                    </p>
                    <div class="pspt_Employee_qr-container" style="left: 280px; bottom: 20px;">
                        <div id="pspt_Employee_qrCode2"></div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="pspt_Employee_card" id="card3">
                    <div class="pspt_Employee_card-content" style="background-image: url('/docs/images/7.png');">
                    </div>
                    <div class="pspt_Employee_profile-pic" id="profile-pic-card3" style="left: 250px; top: 20px;">
                        <img src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile">
                    </div>
                    <h3 class="pspt_Employee_text-element" id="pspt_Employee_text-element-12"
                        style="left: 20px; top: 20px; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>
                        {{ $user->user_full_name ?? 'John Doe' }}
                    </h3>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-13"
                        style="left: 20px; top: 60px; font-size: 14px;">
                        តួនាទី ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-14"
                        style="left: 20px; top: 90px; font-size: 14px;">
                        <i class="fas fa-phone-square-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $user->contact_number ?? '+855 123 456 789' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-15"
                        style="left: 20px; top: 120px; font-size: 14px;">
                        <i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->name ?? 'Tech Solutions' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-16"
                        style="left: 20px; top: 150px; font-size: 14px;">
                        <i class="fas fa-map-marked-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->landmark ?? '456 Elm St' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-17"
                        style="left: 20px; top: 180px; font-size: 12px;">
                        {{ $work_location->city ?? 'Phnom Penh' }}
                    </p>
                    <div class="pspt_Employee_qr-container" style="left: 280px; bottom: 20px;">
                        <div id="pspt_Employee_qrCode3"></div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="pspt_Employee_card" id="card4">
                    <div class="pspt_Employee_card-content" style="background-image: url('/docs/images/8.png');">
                    </div>
                    <div class="pspt_Employee_profile-pic" id="profile-pic-card4" style="left: 250px; top: 20px;">
                        <img src="{{ $img_src ?? 'https://via.placeholder.com/120' }}" alt="Profile">
                    </div>
                    <h3 class="pspt_Employee_text-element" id="pspt_Employee_text-element-18"
                        style="left: 20px; top: 20px; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>
                        {{ $user->user_full_name ?? 'John Doe' }}
                    </h3>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-19"
                        style="left: 20px; top: 60px; font-size: 14px;">
                        តួនាទី ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-20"
                        style="left: 20px; top: 90px; font-size: 14px;">
                        <i class="fas fa-phone-square-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $user->contact_number ?? '+855 123 456 789' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-21"
                        style="left: 20px; top: 120px; font-size: 14px;">
                        <i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->name ?? 'Tech Solutions' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-22"
                        style="left: 20px; top: 150px; font-size: 14px;">
                        <i class="fas fa-map-marked-alt" style="margin-right: 10px; color: yellow;"></i>
                        {{ $work_location->landmark ?? '456 Elm St' }}
                    </p>
                    <p class="pspt_Employee_text-element" id="pspt_Employee_text-element-23"
                        style="left: 20px; top: 180px; font-size: 12px;">
                        {{ $work_location->city ?? 'Phnom Penh' }}
                    </p>
                    <div class="pspt_Employee_qr-container" style="left: 280px; bottom: 20px;">
                        <div id="pspt_Employee_qrCode4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let selectedElement = null;
        let currentCard = document.getElementById('card1');
        let zIndexCounter = 10;
        let profilePics = {
            'card1': null,
            'card2': null,
            'card3': null,
            'card4': null
        };
        let textCounter = 24; // Start after initial text elements

        function pspt_Employee_toggleCategory(id) {
            document.querySelectorAll('.pspt_Employee_category-content').forEach(content => {
                if (content.id !== id) {
                    content.classList.remove('active');
                    content.previousElementSibling.classList.remove('active');
                }
            });
            const content = document.getElementById(id);
            const toggle = content.previousElementSibling;
            content.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        function pspt_Employee_updateCurrentCard() {
            const cardSelector = document.getElementById('pspt_Employee_cardSelector');
            currentCard = document.getElementById(cardSelector.value);
        }

        function pspt_Employee_changeBackgroundFromDropdown() {
            pspt_Employee_updateCurrentCard();
            const dropdown = document.getElementById('pspt_Employee_backgroundDropdown');
            const cardContent = currentCard.querySelector('.pspt_Employee_card-content');
            if (dropdown.value) {
                cardContent.style.backgroundImage = `url(${dropdown.value})`;
            }
        }

        function pspt_Employee_changeBackground() {
            pspt_Employee_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Employee_imageUpload');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const cardContent = currentCard.querySelector('.pspt_Employee_card-content');
                    cardContent.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.onerror = function() {
                    alert('មិនអាចផ្ទុករូបភាពបានទេ។ សូមព្យាយាមម្តងទៀត។');
                };
                reader.readAsDataURL(file);
            }
        }

        function pspt_Employee_resetBackground() {
            pspt_Employee_updateCurrentCard();
            const cardContent = currentCard.querySelector('.pspt_Employee_card-content');
            const cardId = currentCard.id;
            const defaultBackgrounds = {
                'card1': '/docs/images/5.png',
                'card2': '/docs/images/6.png',
                'card3': '/docs/images/7.png',
                'card4': '/docs/images/8.png'
            };
            cardContent.style.backgroundImage = `url(${defaultBackgrounds[cardId]})`;
            document.getElementById('pspt_Employee_backgroundDropdown').value = '';
            document.getElementById('pspt_Employee_imageUpload').value = '';
        }

        function pspt_Employee_changeProfilePic() {
            pspt_Employee_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Employee_profileUpload');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const cardId = currentCard.id;
                    let profilePic = currentCard.querySelector('.pspt_Employee_profile-pic');
                    if (!profilePic) {
                        profilePic = document.createElement('div');
                        profilePic.classList.add('pspt_Employee_profile-pic');
                        profilePic.id = `profile-pic-${cardId}`;
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

        function pspt_Employee_resetProfile() {
            pspt_Employee_updateCurrentCard();
            const cardId = currentCard.id;
            const profilePic = currentCard.querySelector('.pspt_Employee_profile-pic');
            if (profilePic) {
                profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                profilePic.style.left = '250px';
                profilePic.style.top = '20px';
                profilePic.classList.remove('square');
                profilePics[cardId] = profilePic;
                document.getElementById('pspt_Employee_profileUpload').value = '';
                selectElement(profilePic);
            }
        }

        function pspt_Employee_toggleProfileShape(isSquare) {
            pspt_Employee_updateCurrentCard();
            const cardId = currentCard.id;
            const profilePic = profilePics[cardId] || currentCard.querySelector('.pspt_Employee_profile-pic');
            if (profilePic) {
                if (isSquare) {
                    profilePic.classList.add('square');
                } else {
                    profilePic.classList.remove('square');
                }
            }
        }

        function pspt_Employee_addOverlayImage() {
            pspt_Employee_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Employee_imageUploadOverlay');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageElement = document.createElement('div');
                    imageElement.classList.add('pspt_Employee_image-element');
                    imageElement.id = `pspt_Employee_image-element-${textCounter++}`;
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

        function pspt_Employee_changeOverlayImage() {
            const fileInput = document.getElementById('pspt_Employee_changeImageUpload');
            const file = fileInput.files[0];
            if (file && selectedElement && selectedElement.classList.contains('pspt_Employee_image-element')) {
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

        function pspt_Employee_toggleImageShape(isSquare) {
            if (selectedElement && selectedElement.classList.contains('pspt_Employee_image-element')) {
                if (isSquare) {
                    selectedElement.classList.add('square');
                } else {
                    selectedElement.classList.remove('square');
                }
            }
        }

        function pspt_Employee_updateTextButton() {
            const textInput = document.getElementById('pspt_Employee_textInput');
            const textButton = document.getElementById('pspt_Employee_textButton');
            textButton.textContent = textInput.value && selectedElement && selectedElement.classList.contains(
                'pspt_Employee_text-element') ? 'ធ្វើបច្ចុប្បន្នភាពអត្ថបទ' : 'បន្ថែមអត្ថបទ';
        }

        function pspt_Employee_handleTextAction() {
            const textInput = document.getElementById('pspt_Employee_textInput');
            const fontSizeDropdown = document.getElementById('pspt_Employee_fontSizeDropdown');
            const fontStyleDropdown = document.getElementById('pspt_Employee_fontStyleDropdown');
            const textColorPicker = document.getElementById('pspt_Employee_textColorPicker');
            const customFontSize = document.getElementById('pspt_Employee_customFontSize');

            pspt_Employee_updateCurrentCard();

            if (textInput.value) {
                let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown
                    .value + 'px';
                let textElement;
                if (selectedElement && selectedElement.classList.contains('pspt_Employee_text-element')) {
                    textElement = selectedElement;
                } else {
                    textElement = document.createElement('p');
                    textElement.classList.add('pspt_Employee_text-element');
                    textElement.id = `pspt_Employee_text-element-${textCounter++}`;
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
                pspt_Employee_updateTextButton();
                selectElement(textElement);
            }
        }

        function pspt_Employee_changeFontSize() {
            const fontSizeDropdown = document.getElementById('pspt_Employee_fontSizeDropdown');
            const customFontSize = document.getElementById('pspt_Employee_customFontSize');
            customFontSize.classList.toggle('pspt_Employee_hidden', fontSizeDropdown.value !== 'custom');
            if (selectedElement && selectedElement.classList.contains('pspt_Employee_text-element')) {
                let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown
                    .value + 'px';
                selectedElement.style.fontSize = fontSize;
            }
        }

        function pspt_Employee_changeFontStyle() {
            const fontStyleDropdown = document.getElementById('pspt_Employee_fontStyleDropdown');
            if (selectedElement && selectedElement.classList.contains('pspt_Employee_text-element')) {
                selectedElement.style.fontFamily = fontStyleDropdown.value;
            }
        }

        function pspt_Employee_changeTextColor() {
            const textColorPicker = document.getElementById('pspt_Employee_textColorPicker');
            if (selectedElement && selectedElement.classList.contains('pspt_Employee_text-element')) {
                selectedElement.style.color = textColorPicker.value;
            }
        }

        function pspt_Employee_cancelText() {
            const textInput = document.getElementById('pspt_Employee_textInput');
            textInput.value = '';
            pspt_Employee_updateTextButton();
            if (selectedElement) {
                selectedElement.classList.remove('selected');
                selectedElement = null;
                updateElementControls();
            }
        }

        function pspt_Employee_generateQRCode(containerId, size) {
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

        function makeElementDraggable(element) {
            let isDragging = false;
            let currentX = parseFloat(element.style.left) || 0;
            let currentY = parseFloat(element.style.top) || 0;
            let initialX, initialY;

            element.addEventListener('mousedown', startDragging);

            function startDragging(e) {
                if (e.target.classList.contains('pspt_Employee_resize-handle')) return;
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
                if (element.classList.contains('pspt_Employee_text-element')) {
                    const textInput = document.getElementById('pspt_Employee_textInput');
                    textInput.value = element.textContent;
                    pspt_Employee_updateTextButton();
                } else {
                    document.getElementById('pspt_Employee_textInput').value = '';
                    pspt_Employee_updateTextButton();
                }
            }
            updateElementControls();
        }

        function updateElementControls() {
            const bringForwardBtn = document.getElementById('pspt_Employee_bringForwardBtn');
            const sendBackwardBtn = document.getElementById('pspt_Employee_sendBackwardBtn');
            const deleteBtn = document.getElementById('pspt_Employee_deleteSelectedElementsBtn');
            bringForwardBtn.disabled = !selectedElement;
            sendBackwardBtn.disabled = !selectedElement;
            deleteBtn.disabled = !selectedElement;
        }

        function pspt_Employee_bringForward() {
            if (selectedElement) {
                selectedElement.style.zIndex = zIndexCounter++;
            }
        }

        function pspt_Employee_sendBackward() {
            if (selectedElement && parseInt(selectedElement.style.zIndex) > 10) {
                selectedElement.style.zIndex = parseInt(selectedElement.style.zIndex) - 1;
            }
        }

        function pspt_Employee_deleteSelectedElements() {
            if (selectedElement) {
                const cardId = currentCard.id;
                if (selectedElement.classList.contains('pspt_Employee_profile-pic')) {
                    profilePics[cardId] = null;
                }
                selectedElement.remove();
                selectedElement = null;
                updateElementControls();
                document.getElementById('pspt_Employee_textInput').value = '';
                pspt_Employee_updateTextButton();
            }
        }

        function pspt_Employee_resetLayout() {
            const cards = document.querySelectorAll('.pspt_Employee_card');
            const defaultBackgrounds = ['/docs/images/5.png', '/docs/images/6.png', '/docs/images/7.png',
                '/docs/images/8.png'
            ];
            cards.forEach((card, index) => {
                const cardContent = card.querySelector('.pspt_Employee_card-content');
                cardContent.style.backgroundImage = `url(${defaultBackgrounds[index]})`;
                const textElements = card.querySelectorAll('.pspt_Employee_text-element');
                textElements.forEach(element => element.remove());
                const imageElements = card.querySelectorAll('.pspt_Employee_image-element');
                imageElements.forEach(element => element.remove());
                const qrContainers = card.querySelectorAll('.pspt_Employee_qr-container');
                qrContainers.forEach(element => element.remove());
                const cardId = card.id;
                const profilePic = card.querySelector('.pspt_Employee_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    profilePics[cardId] = profilePic;
                }
                // Re-add default text elements
                const texts = [{
                        id: `pspt_Employee_text-element-${index * 6}`,
                        tag: 'h3',
                        text: `<i class="fas fa-user" style="margin-right: 12px; color: yellow;"></i>{{ $user->user_full_name ?? 'John Doe' }}`,
                        style: 'left: 20px; top: 20px; font-size: 20px; font-weight: 600;'
                    },
                    {
                        id: `pspt_Employee_text-element-${index * 6 + 1}`,
                        tag: 'p',
                        text: `តួនាទី ៖ {{ $user_designation->name ?? '' }}{{ $user_department->name ?? '' }}`,
                        style: 'left: 20px; top: 60px; font-size: 14px;'
                    },
                    {
                        id: `pspt_Employee_text-element-${index * 6 + 2}`,
                        tag: 'p',
                        text: `<i class="fas fa-phone-square-alt" style="margin-right: 10px; color: yellow;"></i>{{ $user->contact_number ?? '+855 123 456 789' }}`,
                        style: 'left: 20px; top: 90px; font-size: 14px;'
                    },
                    {
                        id: `pspt_Employee_text-element-${index * 6 + 3}`,
                        tag: 'p',
                        text: `<i class="fas fa-briefcase" style="margin-right: 10px; color: yellow;"></i>{{ $work_location->name ?? 'Tech Solutions' }}`,
                        style: 'left: 20px; top: 120px; font-size: 14px;'
                    },
                    {
                        id: `pspt_Employee_text-element-${index * 6 + 4}`,
                        tag: 'p',
                        text: `<i class="fas fa-map-marked-alt" style="margin-right: 10px; color: yellow;"></i>{{ $work_location->landmark ?? '456 Elm St' }}`,
                        style: 'left: 20px; top: 150px; font-size: 14px;'
                    },
                    {
                        id: `pspt_Employee_text-element-${index * 6 + 5}`,
                        tag: 'p',
                        text: `{{ $work_location->city ?? 'Phnom Penh' }}`,
                        style: 'left: 20px; top: 180px; font-size: 12px;'
                    }
                ];
                texts.forEach(({
                    id,
                    tag,
                    text,
                    style
                }) => {
                    const el = document.createElement(tag);
                    el.classList.add('pspt_Employee_text-element');
                    el.id = id;
                    el.innerHTML = text;
                    el.style.cssText = style;
                    el.style.zIndex = zIndexCounter++;
                    card.appendChild(el);
                    makeElementDraggable(el);
                });
                // Re-add QR code
                const qrContainer = document.createElement('div');
                qrContainer.classList.add('pspt_Employee_qr-container');
                qrContainer.style.left = '280px';
                qrContainer.style.bottom = '20px';
                qrContainer.style.top = '';
                qrContainer.style.zIndex = zIndexCounter++;
                const qrDiv = document.createElement('div');
                qrDiv.id = `pspt_Employee_qrCode${cardId.replace('card', '')}`;
                qrContainer.appendChild(qrDiv);
                card.appendChild(qrContainer);
                makeElementDraggable(qrContainer);
                pspt_Employee_generateQRCode(qrDiv.id, 80);
            });
            document.getElementById('pspt_Employee_backgroundDropdown').value = '';
            document.getElementById('pspt_Employee_imageUpload').value = '';
            document.getElementById('pspt_Employee_textInput').value = '';
            pspt_Employee_updateTextButton();
            document.getElementById('pspt_Employee_profileUpload').value = '';
            document.getElementById('pspt_Employee_imageUploadOverlay').value = '';
            document.getElementById('pspt_Employee_changeImageUpload').value = '';
            selectedElement = null;
            updateElementControls();
        }

        async function pspt_Employee_downloadCards() {
            const downloadBtn = document.getElementById('pspt_Employee_downloadBtn');
            const cardsContainer = document.getElementById('pspt_Employee_cardsContainer');

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
                link.download = 'id_cards_page1.png';
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

        document.addEventListener('DOMContentLoaded', () => {
            const textElements = document.querySelectorAll('.pspt_Employee_text-element');
            textElements.forEach(element => makeElementDraggable(element));
            const profilePicsElements = document.querySelectorAll('.pspt_Employee_profile-pic');
            profilePicsElements.forEach(pic => {
                makeElementDraggable(pic);
                const cardId = pic.closest('.pspt_Employee_card').id;
                profilePics[cardId] = pic;
            });
            const qrContainers = document.querySelectorAll('.pspt_Employee_qr-container');
            qrContainers.forEach(container => {
                makeElementDraggable(container);
                const qrDiv = container.querySelector('div');
                if (qrDiv) pspt_Employee_generateQRCode(qrDiv.id, 80);
            });

            document.getElementById('pspt_Employee_cardSelector').addEventListener('change',
                pspt_Employee_updateCurrentCard);
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.pspt_Employee_text-element') &&
                    !e.target.closest('.pspt_Employee_image-element') &&
                    !e.target.closest('.pspt_Employee_profile-pic') &&
                    !e.target.closest('.pspt_Employee_qr-container') &&
                    !e.target.closest('.pspt_Employee_filters-container')) {
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
</body>

</html>
