<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Editor</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --row-gap: 20px;
            --column-gap: 2px;
            --grid-padding: 10px;
            --card-margin: 0px;
            --card-padding: 19px;
            --card-width: 380px;
            --card-height: 560px;
        }
        .pk_20page_wrapper * {
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
        .pk_20page_wrapper {
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
        .pk_20page_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .pk_20page_filters-container h2 { text-align: center; margin: 0 0 10px 0; color: #333; }
        .pk_20page_filters-container hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }
        .pk_20page_category-toggle {
            padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center; font-size: 16px; color: #333; margin-bottom: 15px;
        }
        .pk_20page_category-toggle:hover { background-color: #c8d8ff; }
        .pk_20page_category-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease; }
        .pk_20page_category-content.active { max-height: 500px; padding: 10px; }
        .pk_20page_filter-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
        .pk_20page_background-dropdown,
        .pk_20page_font-size-dropdown,
        .pk_20page_font-style-dropdown {
            width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat; background-position: right 10px center; background-size: 18px;
        }
        .pk_20page_custom-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_20page_custom-button:hover { background-color: #138496; }
        .pk_20page_custom-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .pk_20page_reset-button { background-color: #dc3545; color: white; }
        .pk_20page_reset-button:hover { background-color: #c82333; }
        .pk_20page_download-btn { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_20page_download-btn:hover { background-color: #138496; }
        .pk_20page_secondary-button { background-color: #6c757d; color: white; }
        .pk_20page_secondary-button:hover { background-color: #5a6268; }
        .pk_20page_text-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_20page_text-button:hover { background-color: #138496; }
        .pk_20page_text-input, .pk_20page_color-picker { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        .pk_20page_text-input:focus { border-color: #17a2b8; outline: none; box-shadow: 0 0 5px rgba(23, 162, 184, 0.5); }
        .pk_20page_hidden { display: none; }
        .pk_20page_arrow { transition: transform 0.3s; font-size: 14px; }
        .pk_20page_category-toggle.active .pk_20page_arrow { transform: rotate(180deg); }
        .pk_20page_displayArea_container {
            width: 70%;
            overflow-y: auto;
            height: 100vh;
            padding: 5px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .pk_20page_displayArea { padding: 5px; margin: 0; box-sizing: border-box; border: 1px solid gray; border-radius: 10px; display: flex; justify-content: center; align-items: center; }
        .pk_20page_grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: var(--row-gap) var(--column-gap); padding: var(--grid-padding); background-color: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); width: 100%; max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding)); height: calc(2 * var(--card-height) + var(--row-gap) + 2 * var(--grid-padding)); }
        .pk_20page_card { width: 100%; aspect-ratio: 380 / 560; margin: var(--card-margin); padding: var(--card-padding); border: 2px solid #1C2526; background-color: #ffffff; position: relative; overflow: hidden; font-family: 'Battambang', sans-serif; }
        .pk_20page_card-content { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; display: flex; justify-content: center; align-items: center; }
        .pk_20page_card-placeholder-number { font-size: 5rem; color: #e0e0e0; font-weight: bold; user-select: none; }
        .pk_20page_card-content .pk_20page_card-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
        .pk_20page_profile-pic { position: absolute; width: 120px; height: 120px; overflow: hidden; cursor: move; pointer-events: auto; left: 250px; top: 20px; transition: left 0.1s, top 0.1s; will-change: transform; z-index: 5; }
        .pk_20page_profile-pic.dragging { transition: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); transform: scale(1.05); opacity: 0.9; }
        .pk_20page_profile-pic img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pk_20page_profile-pic.square img { border-radius: 0; }
        .pk_20page_profile-pic:not(.square) img { border-radius: 50%; }
        .pk_20page_text-element, .pk_20page_image-element { position: absolute; cursor: move; pointer-events: auto; user-select: none; font-family: 'Battambang', sans-serif; transition: left 0.1s, top 0.1s; will-change: transform; }
        .pk_20page_text-element.dragging, .pk_20page_image-element.dragging { transition: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transform: scale(1.05); opacity: 0.9; }
        .pk_20page_text-element.selected, .pk_20page_image-element.selected, .pk_20page_profile-pic.selected { border: 1px solid cyan; }
        .pk_20page_image-element { width: 100px; height: 100px; }
        .pk_20page_image-element img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pk_20page_image-element.square img { border-radius: 0; }
        .pk_20page_image-element:not(.square) img { border-radius: 50%; }
        .pk_20page_resize-handle { position: absolute; width: 10px; height: 10px; background: cyan; cursor: pointer; pointer-events: auto; }
        .pk_20page_resize-handle.top-left { top: -5px; left: -5px; cursor: nw-resize; }
        .pk_20page_resize-handle.top-right { top: -5px; right: -5px; cursor: ne-resize; }
        .pk_20page_resize-handle.bottom-left { bottom: -5px; left: -5px; cursor: sw-resize; }
        .pk_20page_resize-handle.bottom-right { bottom: -5px; right: -5px; cursor: se-resize; }
        
        /* .pk_20page_grid .pk_20page_card:nth-child(3),
        .pk_20page_grid .pk_20page_card:nth-child(4) { 
            transform: rotate(180deg); 
            transform-origin: center center; 
        } */
        #pk_20page_card19,
        #pk_20page_card20 { 
            transform: rotate(180deg); 
            transform-origin: center center; 
        }
    </style>
</head>
<body>
    <div class="pk_20page_wrapper">
        <div class="pk_20page_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="pk_20page_category-toggle" onclick="pk20pageApp.toggleCategory('pk_20page_background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="pk_20page_arrow">▼</span>
            </div>
            <div class="pk_20page_category-content" id="pk_20page_background-controls">
                <div class="pk_20page_filter-group">
                    <select class="pk_20page_background-dropdown" id="pk_20page_cardSelector" onchange="pk20pageApp.updateCurrentCard()">
                        <option value="pk_20page_card1">កាត ១</option>
                        <option value="pk_20page_card2">កាត ២</option>
                        <option value="pk_20page_card3">កាត ៣</option>
                        <option value="pk_20page_card4">កាត ៤</option>
                        <option value="pk_20page_card5">កាត ៥</option>
                        <option value="pk_20page_card6">កាត ៦</option>
                        <option value="pk_20page_card7">កាត ៧</option>
                        <option value="pk_20page_card8">កាត ៨</option>
                        <option value="pk_20page_card9">កាត ៩</option>
                        <option value="pk_20page_card10">កាត ១០</option>
                        <option value="pk_20page_card11">កាត ១១</option>
                        <option value="pk_20page_card12">កាត ១២</option>
                        <option value="pk_20page_card13">កាត ១៣</option>
                        <option value="pk_20page_card14">កាត ១៤</option>
                        <option value="pk_20page_card15">កាត ១៥</option>
                        <option value="pk_20page_card16">កាត ១៦</option>
                        <option value="pk_20page_card17">កាត ១៧</option>
                        <option value="pk_20page_card18">កាត ១៨</option>
                        <option value="pk_20page_card19">កាត ១៩</option>
                        <option value="pk_20page_card20">កាត ២០</option>
                        <option value="pk_20page_card21">កាត ២១</option>
                        <option value="pk_20page_card22">កាត ២២</option>
                        <option value="pk_20page_card23">កាត ២៣</option>
                        <option value="pk_20page_card24">កាត ២៤</option>
                        <option value="pk_20page_card25">កាត ២៥</option>
                        <option value="pk_20page_card26">កាត ២៦</option>
                        <option value="pk_20page_card27">កាត ២៧</option>
                        <option value="pk_20page_card28">កាត ២៨</option>
                        <option value="pk_20page_card29">កាត ២៩</option>
                        <option value="pk_20page_card30">កាត ៣០</option>
                        <option value="pk_20page_card31">កាត ៣១</option>
                        <option value="pk_20page_card32">កាត ៣២</option>
                    </select>
                    <select class="pk_20page_background-dropdown" id="pk_20page_backgroundDropdown" onchange="pk20pageApp.changeBackgroundFromDropdown()">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/5.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/6.png">ផ្ទៃខាងក្រោយ ២</option>
                        <option value="/docs/images/7.png">ផ្ទៃខាងក្រោយ ៣</option>
                        <option value="/docs/images/8.png">ផ្ទៃខាងក្រោយ ៤</option>
                    </select>
                    <select class="pk_20page_background-dropdown" id="pk_20page_rotationDropdown" onchange="pk20pageApp.rotateBackground()">
                        <option value="0">0 deg</option>
                        <option value="90">90 deg</option>
                        <option value="180">180 deg</option>
                        <option value="270">270 deg</option>
                    </select>
                    <button class="pk_20page_custom-button" id="pk_20page_changeBackgroundBtn" onclick="document.getElementById('pk_20page_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="pk_20page_imageUpload" accept="image/*" onchange="pk20pageApp.changeBackground()" style="display: none;">
                    <button class="pk_20page_custom-button pk_20page_reset-button" id="pk_20page_resetBackgroundBtn" onclick="pk20pageApp.resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="pk_20page_category-toggle" onclick="pk20pageApp.toggleCategory('pk_20page_profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="pk_20page_arrow">▼</span>
            </div>
            <div class="pk_20page_category-content" id="pk_20page_profile-controls">
                <div class="pk_20page_filter-group">
                    <button class="pk_20page_custom-button" id="pk_20page_changeProfileBtn" onclick="document.getElementById('pk_20page_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="pk_20page_profileUpload" accept="image/*" onchange="pk20pageApp.changeProfilePic()" style="display: none;">
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_squareShapeBtn" onclick="pk20pageApp.toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_circleShapeBtn" onclick="pk20pageApp.toggleProfileShape(false)">រាងមូល</button>
                    <button class="pk_20page_custom-button pk_20page_reset-button" id="pk_20page_resetProfileBtn" onclick="pk20pageApp.resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="pk_20page_category-toggle" onclick="pk20pageApp.toggleCategory('pk_20page_text-controls')">
                <i class="fas fa-edit"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="pk_20page_arrow">▼</span>
            </div>
            <div class="pk_20page_category-content" id="pk_20page_text-controls">
                <div class="pk_20page_filter-group">
                    <input type="text" id="pk_20page_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="pk_20page_text-input" oninput="pk20pageApp.updateTextButton()">
                    <input type="color" id="pk_20page_textColorPicker" value="#000000" class="pk_20page_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ" onchange="pk20pageApp.changeTextColor()">
                    <select class="pk_20page_font-size-dropdown" id="pk_20page_fontSizeDropdown" onchange="pk20pageApp.changeFontSize()">
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
                    <input type="number" id="pk_20page_customFontSize" class="pk_20page_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="pk_20page_font-style-dropdown" id="pk_20page_fontStyleDropdown" onchange="pk20pageApp.changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="pk_20page_text-button" id="pk_20page_textButton" onclick="pk20pageApp.handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="pk_20page_custom-button pk_20page_reset-button" id="pk_20page_cancelTextBtn" onclick="pk20pageApp.cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="pk_20page_category-toggle" onclick="pk20pageApp.toggleCategory('pk_20page_image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="pk_20page_arrow">▼</span>
            </div>
            <div class="pk_20page_category-content" id="pk_20page_image-overlay-controls">
                <div class="pk_20page_filter-group">
                    <button class="pk_20page_custom-button" id="pk_20page_addImageBtn" onclick="document.getElementById('pk_20page_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="pk_20page_imageUploadOverlay" accept="image/*" onchange="pk20pageApp.addOverlayImage()" style="display: none;">
                    <button class="pk_20page_custom-button" id="pk_20page_changeImageBtn" onclick="document.getElementById('pk_20page_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="pk_20page_changeImageUpload" accept="image/*" onchange="pk20pageApp.changeOverlayImage()" style="display: none;">
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_imageSquareShapeBtn" onclick="pk20pageApp.toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_imageCircleShapeBtn" onclick="pk20pageApp.toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="pk_20page_category-toggle" onclick="pk20pageApp.toggleCategory('pk_20page_element-controls')">
                <i class="fas fa-trash-alt"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="pk_20page_arrow">▼</span>
            </div>
            <div class="pk_20page_category-content" id="pk_20page_element-controls">
                <div class="pk_20page_filter-group">
                    <button class="pk_20page_custom-button pk_20page_reset-button" id="pk_20page_deleteSelectedElementsBtn" onclick="pk20pageApp.deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_bringForwardBtn" onclick="pk20pageApp.bringForward()" disabled>នាំមកមុខ</button>
                    <button class="pk_20page_custom-button pk_20page_secondary-button" id="pk_20page_sendBackwardBtn" onclick="pk20pageApp.sendBackward()" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="pk_20page_custom-button pk_20page_reset-button" id="pk_20page_resetAllBtn" onclick="pk20pageApp.resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="pk_20page_download-btn" id="pk_20page_downloadBtn" onclick="pk20pageApp.downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i></button>
        </div>
        <div class="pk_20page_displayArea_container">
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer1" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card1"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page20.png" alt="Card background 1"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card2"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page1.png" alt="Card background 2"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card3"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page18.png" alt="Card background 3"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card4"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page3.png" alt="Card background 4"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer2" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card5"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page2.png" alt="Card background 5"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card6"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page19.png" alt="Card background 6"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card7"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page4.png" alt="Card background 7"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card8"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page17.png" alt="Card background 8"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer3" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card9"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page16.png" alt="Card background 9"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card10"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page5.png" alt="Card background 10"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card11"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page14.png" alt="Card background 11"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card12"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page7.png" alt="Card background 12"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer4" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card13"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page6.png" alt="Card background 13"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card14"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page15.png" alt="Card background 14"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card15"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page8.png" alt="Card background 15"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card16"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page13.png" alt="Card background 16"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer5" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card17"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page12.png" alt="Card background 17"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card18"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page9.png" alt="Card background 18"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card19"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page11.png" alt="Card background 19"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card20"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page10.png" alt="Card background 20"></div></div>
                </div>
            </div>
            {{-- <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer6" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card21"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page21.png" alt="Card background 21"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card22"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page22.png" alt="Card background 22"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card23"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page23.png" alt="Card background 23"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card24"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page24.png" alt="Card background 24"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer7" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card25"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page25.png" alt="Card background 25"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card26"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page26.png" alt="Card background 26"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card27"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page27.png" alt="Card background 27"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card28"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page28.png" alt="Card background 28"></div></div>
                </div>
            </div>
            <div class="pk_20page_displayArea">
                <div id="pk_20page_cardsContainer8" class="pk_20page_grid">
                    <div class="pk_20page_card" id="pk_20page_card29"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page29.png" alt="Card background 29"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card30"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page30.png" alt="Card background 30"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card31"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page31.png" alt="Card background 31"></div></div>
                    <div class="pk_20page_card" id="pk_20page_card32"><div class="pk_20page_card-content"><img class="pk_20page_card-bg" src="/docs/images/franchise_package/package_a/page32.png" alt="Card background 32"></div></div>
                </div>
            </div> --}}
        </div>
    </div>
    <script>
    (function() {
        window.pk20pageApp = {
            initialized: false,
            selectedElement: null,
            currentCard: null,
            zIndexCounter: 10,
            profilePics: {},
            textCounter: 0,
            init: function() {
                if (this.initialized) return;
                this.initialized = true;
                this.currentCard = document.querySelector('.pk_20page_card');
                const textElements = document.querySelectorAll('.pk_20page_text-element');
                textElements.forEach(element => this.makeElementDraggable(element));
                const profilePicsElements = document.querySelectorAll('.pk_20page_profile-pic');
                profilePicsElements.forEach(pic => { 
                    this.makeElementDraggable(pic); 
                    const cardId = pic.closest('.pk_20page_card').id; 
                    this.profilePics[cardId] = pic; 
                });
                document.getElementById('pk_20page_cardSelector').addEventListener('change', () => this.updateCurrentCard());
                
                document.querySelector('.pk_20page_wrapper').addEventListener('click', (e) => {
                    if (!e.target.closest('.pk_20page_text-element') && !e.target.closest('.pk_20page_image-element') && !e.target.closest('.pk_20page_profile-pic') && !e.target.closest('.pk_20page_filters-container')) {
                        this.selectElement(null);
                    }
                });
                document.addEventListener('keydown', (e) => {
                    if (this.selectedElement && ['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'].includes(e.key)) {
                        e.preventDefault();
                        let currentX = parseFloat(this.selectedElement.style.left) || 0;
                        let currentY = parseFloat(this.selectedElement.style.top) || 0;
                        const moveBy = 1;
                        switch (e.key) {
                            case 'ArrowLeft': currentX -= moveBy; break;
                            case 'ArrowRight': currentX += moveBy; break;
                            case 'ArrowUp': currentY -= moveBy; break;
                            case 'ArrowDown': currentY += moveBy; break;
                        }
                        requestAnimationFrame(() => {
                            this.selectedElement.style.left = `${currentX}px`;
                            this.selectedElement.style.top = `${currentY}px`;
                        });
                    }
                });
            },
            toggleCategory: function(id) {
                document.querySelectorAll('.pk_20page_category-content').forEach(content => {
                    if (content.id !== id) {
                        content.classList.remove('active');
                        content.previousElementSibling.classList.remove('active');
                    }
                });
                const content = document.getElementById(id);
                const toggle = content.previousElementSibling;
                content.classList.toggle('active');
                toggle.classList.toggle('active');
            },
            updateCurrentCard: function() {
                const cardSelector = document.getElementById('pk_20page_cardSelector');
                this.currentCard = document.querySelector(`#${cardSelector.value}`);
            },
            changeBackgroundFromDropdown: function() {
                this.updateCurrentCard();
                const dropdown = document.getElementById('pk_20page_backgroundDropdown');
                if (dropdown.value) {
                    let bgImg = this.currentCard.querySelector('.pk_20page_card-content .pk_20page_card-bg');
                    if (!bgImg) {
                        bgImg = document.createElement('img');
                        bgImg.classList.add('pk_20page_card-bg');
                        this.currentCard.querySelector('.pk_20page_card-content').appendChild(bgImg);
                    }
                    bgImg.src = dropdown.value;
                    const placeholder = this.currentCard.querySelector('.pk_20page_card-placeholder-number');
                    if(placeholder) placeholder.style.display = 'none';
                }
            },
            rotateBackground: function() {
                this.updateCurrentCard();
                const dropdown = document.getElementById('pk_20page_rotationDropdown');
                const rotation = dropdown.value;
                const bgImg = this.currentCard.querySelector('.pk_20page_card-content .pk_20page_card-bg');
                if (bgImg) {
                    let transformValue = `rotate(${rotation}deg)`;
                    if (rotation === '90' || rotation === '270') {
                        const card = this.currentCard;
                        const cardAspectRatio = card.offsetHeight / card.offsetWidth;
                        if (cardAspectRatio > 1) {
                            transformValue += ` scale(${cardAspectRatio})`;
                        } else if (cardAspectRatio < 1) {
                            transformValue += ` scale(${1/cardAspectRatio})`;
                        }
                    }
                    bgImg.style.transform = transformValue;
                }
            },
            changeBackground: function() {
                this.updateCurrentCard();
                const fileInput = document.getElementById('pk_20page_imageUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        let bgImg = this.currentCard.querySelector('.pk_20page_card-content .pk_20page_card-bg');
                        if (!bgImg) {
                            bgImg = document.createElement('img');
                            bgImg.classList.add('pk_20page_card-bg');
                            this.currentCard.querySelector('.pk_20page_card-content').appendChild(bgImg);
                        }
                        bgImg.src = e.target.result;
                        const placeholder = this.currentCard.querySelector('.pk_20page_card-placeholder-number');
                        if(placeholder) placeholder.style.display = 'none';
                    };
                    reader.onerror = () => { alert('Could not load image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            resetBackground: function() {
                this.updateCurrentCard();
                const bgImg = this.currentCard.querySelector('.pk_20page_card-content .pk_20page_card-bg');
                if (bgImg) {
                    bgImg.remove();
                }
                const placeholder = this.currentCard.querySelector('.pk_20page_card-placeholder-number');
                if(placeholder) placeholder.style.display = 'block';
                document.getElementById('pk_20page_backgroundDropdown').value = '';
                document.getElementById('pk_20page_imageUpload').value = '';
                document.getElementById('pk_20page_rotationDropdown').value = '0';
            },
            changeProfilePic: function() {
                this.updateCurrentCard();
                const fileInput = document.getElementById('pk_20page_profileUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const cardId = this.currentCard.id;
                        let profilePic = this.currentCard.querySelector('.pk_20page_profile-pic');
                        if (!profilePic) {
                            profilePic = document.createElement('div');
                            profilePic.classList.add('pk_20page_profile-pic');
                            profilePic.id = `profile-pic-${cardId}`;
                            const img = document.createElement('img');
                            img.alt = 'Profile';
                            profilePic.appendChild(img);
                            profilePic.style.left = '250px';
                            profilePic.style.top = '20px';
                            this.currentCard.appendChild(profilePic);
                            this.makeElementDraggable(profilePic);
                        }
                        profilePic.querySelector('img').src = e.target.result;
                        this.profilePics[cardId] = profilePic;
                        this.selectElement(profilePic);
                    };
                    reader.onerror = () => { alert('Could not load profile image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            resetProfile: function() {
                this.updateCurrentCard();
                const cardId = this.currentCard.id;
                const profilePic = this.currentCard.querySelector('.pk_20page_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    this.profilePics[cardId] = profilePic;
                    document.getElementById('pk_20page_profileUpload').value = '';
                    this.selectElement(profilePic);
                }
            },
            toggleProfileShape: function(isSquare) {
                this.updateCurrentCard();
                const cardId = this.currentCard.id;
                const profilePic = this.profilePics[cardId] || this.currentCard.querySelector('.pk_20page_profile-pic');
                if (profilePic) {
                    if (isSquare) { profilePic.classList.add('square'); } else { profilePic.classList.remove('square'); }
                }
            },
            addOverlayImage: function() {
                this.updateCurrentCard();
                const fileInput = document.getElementById('pk_20page_imageUploadOverlay');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageElement = document.createElement('div');
                        imageElement.classList.add('pk_20page_image-element');
                        imageElement.id = `pk_20page_image-element-${this.textCounter++}`;
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        imageElement.appendChild(img);
                        imageElement.style.zIndex = this.zIndexCounter++;
                        imageElement.style.left = '50px';
                        imageElement.style.top = '50px';
                        this.currentCard.appendChild(imageElement);
                        this.makeElementDraggable(imageElement);
                        this.selectElement(imageElement);
                    };
                    reader.onerror = () => { alert('Could not add image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            changeOverlayImage: function() {
                const fileInput = document.getElementById('pk_20page_changeImageUpload');
                const file = fileInput.files[0];
                if (file && this.selectedElement && this.selectedElement.classList.contains('pk_20page_image-element')) {
                    const reader = new FileReader();
                    reader.onload = (e) => { this.selectedElement.querySelector('img').src = e.target.result; };
                    reader.onerror = () => { alert('Could not change image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            toggleImageShape: function(isSquare) {
                if (this.selectedElement && this.selectedElement.classList.contains('pk_20page_image-element')) {
                    if (isSquare) { this.selectedElement.classList.add('square'); } else { this.selectedElement.classList.remove('square'); }
                }
            },
            updateTextButton: function() {
                const textInput = document.getElementById('pk_20page_textInput');
                const textButton = document.getElementById('pk_20page_textButton');
                textButton.textContent = textInput.value && this.selectedElement && this.selectedElement.classList.contains('pk_20page_text-element') ? 'Update Text' : 'Add Text';
            },
            handleTextAction: function() {
                const textInput = document.getElementById('pk_20page_textInput');
                const fontSizeDropdown = document.getElementById('pk_20page_fontSizeDropdown');
                const fontStyleDropdown = document.getElementById('pk_20page_fontStyleDropdown');
                const textColorPicker = document.getElementById('pk_20page_textColorPicker');
                const customFontSize = document.getElementById('pk_20page_customFontSize');
                this.updateCurrentCard();
                if (textInput.value) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    let textElement;
                    if (this.selectedElement && this.selectedElement.classList.contains('pk_20page_text-element')) {
                        textElement = this.selectedElement;
                    } else {
                        textElement = document.createElement('p');
                        textElement.classList.add('pk_20page_text-element');
                        textElement.id = `pk_20page_text-element-${this.textCounter++}`;
                        textElement.style.zIndex = this.zIndexCounter++;
                        textElement.style.left = '50px';
                        textElement.style.top = '50px';
                        this.currentCard.appendChild(textElement);
                        this.makeElementDraggable(textElement);
                    }
                    textElement.textContent = textInput.value;
                    textElement.style.fontSize = fontSize;
                    textElement.style.fontFamily = fontStyleDropdown.value;
                    textElement.style.color = textColorPicker.value;
                    textInput.value = '';
                    this.updateTextButton();
                    this.selectElement(textElement);
                }
            },
            changeFontSize: function() {
                const fontSizeDropdown = document.getElementById('pk_20page_fontSizeDropdown');
                const customFontSize = document.getElementById('pk_20page_customFontSize');
                customFontSize.classList.toggle('pk_20page_hidden', fontSizeDropdown.value !== 'custom');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_20page_text-element')) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    this.selectedElement.style.fontSize = fontSize;
                }
            },
            changeFontStyle: function() {
                const fontStyleDropdown = document.getElementById('pk_20page_fontStyleDropdown');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_20page_text-element')) {
                    this.selectedElement.style.fontFamily = fontStyleDropdown.value;
                }
            },
            changeTextColor: function() {
                const textColorPicker = document.getElementById('pk_20page_textColorPicker');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_20page_text-element')) {
                    this.selectedElement.style.color = textColorPicker.value;
                }
            },
            cancelText: function() {
                const textInput = document.getElementById('pk_20page_textInput');
                textInput.value = '';
                this.updateTextButton();
                if (this.selectedElement) {
                    this.selectedElement.classList.remove('selected');
                    this.selectedElement = null;
                    this.updateElementControls();
                }
            },
            makeElementDraggable: function(element) {
                let isDragging = false;
                let currentX = parseFloat(element.style.left) || 0;
                let currentY = parseFloat(element.style.top) || 0;
                let initialX, initialY;
                
                const startDragging = (e) => {
                    if (e.target.classList.contains('pk_20page_resize-handle')) return;
                    this.selectElement(element);
                    initialX = e.clientX - currentX;
                    initialY = e.clientY - currentY;
                    isDragging = true;
                    element.classList.add('dragging');
                    document.addEventListener('mousemove', drag);
                    document.addEventListener('mouseup', stopDragging);
                    e.preventDefault();
                }
                const drag = (e) => {
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
                const stopDragging = () => {
                    if (isDragging) {
                        isDragging = false;
                        element.classList.remove('dragging');
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('mouseup', stopDragging);
                    }
                }
                element.addEventListener('mousedown', startDragging.bind(this));
                element.style.position = 'absolute';
                if (!element.style.left) element.style.left = '0px';
                if (!element.style.top) element.style.top = '0px';
            },
            selectElement: function(element) {
                if (this.selectedElement) { this.selectedElement.classList.remove('selected'); }
                this.selectedElement = element;
                if (element) {
                    element.classList.add('selected');
                    if (element.classList.contains('pk_20page_text-element')) {
                        const textInput = document.getElementById('pk_20page_textInput');
                        textInput.value = element.textContent;
                        this.updateTextButton();
                    } else {
                        document.getElementById('pk_20page_textInput').value = '';
                        this.updateTextButton();
                    }
                }
                this.updateElementControls();
            },
            updateElementControls: function() {
                const bringForwardBtn = document.getElementById('pk_20page_bringForwardBtn');
                const sendBackwardBtn = document.getElementById('pk_20page_sendBackwardBtn');
                const deleteBtn = document.getElementById('pk_20page_deleteSelectedElementsBtn');
                bringForwardBtn.disabled = !this.selectedElement;
                sendBackwardBtn.disabled = !this.selectedElement;
                deleteBtn.disabled = !this.selectedElement;
            },
            bringForward: function() {
                if (this.selectedElement) { this.selectedElement.style.zIndex = this.zIndexCounter++; }
            },
            sendBackward: function() {
                if (this.selectedElement && parseInt(this.selectedElement.style.zIndex) > 10) {
                    this.selectedElement.style.zIndex = parseInt(this.selectedElement.style.zIndex) - 1;
                }
            },
            deleteSelectedElements: function() {
                if (this.selectedElement) {
                    const cardId = this.currentCard.id;
                    if (this.selectedElement.classList.contains('pk_20page_profile-pic')) { this.profilePics[cardId] = null; }
                    this.selectedElement.remove();
                    this.selectedElement = null;
                    this.updateElementControls();
                    document.getElementById('pk_20page_textInput').value = '';
                    this.updateTextButton();
                }
            },
            resetLayout: function() {
                const cards = document.querySelectorAll('.pk_20page_card');
                cards.forEach((card) => {
                    const bgImg = card.querySelector('.pk_20page_card-content .pk_20page_card-bg');
                    if (bgImg) {
                        bgImg.remove();
                    }
                    const placeholder = card.querySelector('.pk_20page_card-placeholder-number');
                    if (placeholder) {
                        placeholder.style.display = 'block';
                    }
                    const textElements = card.querySelectorAll('.pk_20page_text-element');
                    textElements.forEach(element => element.remove());
                    const imageElements = card.querySelectorAll('.pk_20page_image-element');
                    imageElements.forEach(element => element.remove());
                    const qrContainers = card.querySelectorAll('.pk_20page_qr-container');
                    qrContainers.forEach(element => element.remove());
                    const cardId = card.id;
                    const profilePic = card.querySelector('.pk_20page_profile-pic');
                    if (profilePic) {
                        profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                        profilePic.style.left = '250px';
                        profilePic.style.top = '20px';
                        profilePic.classList.remove('square');
                        this.profilePics[cardId] = profilePic;
                    }
                });
                document.getElementById('pk_20page_backgroundDropdown').value = '';
                document.getElementById('pk_20page_imageUpload').value = '';
                document.getElementById('pk_20page_textInput').value = '';
                this.updateTextButton();
                document.getElementById('pk_20page_profileUpload').value = '';
                document.getElementById('pk_20page_imageUploadOverlay').value = '';
                document.getElementById('pk_20page_changeImageUpload').value = '';
                this.selectedElement = null;
                this.updateElementControls();
            },
            downloadCards: async function() {
                const downloadBtn = document.getElementById('pk_20page_downloadBtn');
                downloadBtn.innerHTML = 'Generating PDF...';
                downloadBtn.disabled = true;
                
                const allCards = document.querySelectorAll('.pk_20page_card');
                allCards.forEach(card => {
                    card.style.border = 'none';
                });
                try {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const containers = document.querySelectorAll('.pk_20page_grid');
                    for (let i = 0; i < containers.length; i++) {
                        const gridCanvas = await html2canvas(containers[i], { scale: 3, backgroundColor: '#ffffff', useCORS: true });
                        const imgData = gridCanvas.toDataURL('image/png');
                        if (i > 0) {
                            pdf.addPage();
                        }
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        const margin = 10; // mm
                        const maxWidth = pageWidth - margin * 2;
                        const maxHeight = pageHeight - margin * 2;
                        let renderWidth = maxWidth;
                        let renderHeight = (gridCanvas.height * renderWidth) / gridCanvas.width;
                        if (renderHeight > maxHeight) {
                            renderHeight = maxHeight;
                            renderWidth = (gridCanvas.width * renderHeight) / gridCanvas.height;
                        }
                        const x = (pageWidth - renderWidth) / 2;
                        const y = (pageHeight - renderHeight) / 2;
                        pdf.addImage(imgData, 'PNG', x, y, renderWidth, renderHeight, undefined, 'FAST');
                    }
                    pdf.save('id_cards.pdf');
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    alert('Failed to generate PDF');
                } finally {
                    allCards.forEach(card => {
                        card.style.border = '';
                    });
                    downloadBtn.innerHTML = 'Print <i class="fas fa-print" style="margin-left: 10px;"></i>';
                    downloadBtn.disabled = false;
                }
            }
        };
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', pk20pageApp.init.bind(pk20pageApp));
        } else {
            pk20pageApp.init();
        }
    })();
    </script>
</body>
</html>