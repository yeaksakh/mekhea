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
        .sbp_wrapper * {
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
        .sbp_wrapper {
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
        .sbp_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .sbp_filters-container h2 { text-align: center; margin: 0 0 10px 0; color: #333; }
        .sbp_filters-container hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }
        .sbp_category-toggle {
            padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center; font-size: 16px; color: #333; margin-bottom: 15px;
        }
        .sbp_category-toggle:hover { background-color: #c8d8ff; }
        .sbp_category-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease; }
        .sbp_category-content.active { max-height: 500px; padding: 10px; }
        .sbp_filter-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
        .sbp_background-dropdown,
        .sbp_font-size-dropdown,
        .sbp_font-style-dropdown {
            width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat; background-position: right 10px center; background-size: 18px;
        }
        .sbp_custom-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .sbp_custom-button:hover { background-color: #138496; }
        .sbp_custom-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .sbp_reset-button { background-color: #dc3545; color: white; }
        .sbp_reset-button:hover { background-color: #c82333; }
        .sbp_download-btn { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .sbp_download-btn:hover { background-color: #138496; }
        .sbp_secondary-button { background-color: #6c757d; color: white; }
        .sbp_secondary-button:hover { background-color: #5a6268; }
        .sbp_text-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .sbp_text-button:hover { background-color: #138496; }
        .sbp_text-input, .sbp_color-picker { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        .sbp_text-input:focus { border-color: #17a2b8; outline: none; box-shadow: 0 0 5px rgba(23, 162, 184, 0.5); }
        .sbp_hidden { display: none; }
        .sbp_arrow { transition: transform 0.3s; font-size: 14px; }
        .sbp_category-toggle.active .sbp_arrow { transform: rotate(180deg); }
        .sbp_displayArea_container {
            width: 70%;
            overflow-y: auto;
            height: 100vh;
            padding: 5px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sbp_displayArea { padding: 5px; margin: 0; box-sizing: border-box; border: 1px solid gray; border-radius: 10px; display: flex; justify-content: center; align-items: center; }
        .sbp_grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: var(--row-gap) var(--column-gap); padding: var(--grid-padding); background-color: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); width: 100%; max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding)); height: calc(2 * var(--card-height) + var(--row-gap) + 2 * var(--grid-padding)); }
        .sbp_card { width: 100%; aspect-ratio: 380 / 560; margin: var(--card-margin); padding: var(--card-padding); border: 2px solid #1C2526; background-color: #ffffff; position: relative; overflow: hidden; font-family: 'Battambang', sans-serif; }
        .sbp_card-content { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; display: flex; justify-content: center; align-items: center; }
        .sbp_card-placeholder-number { font-size: 5rem; color: #e0e0e0; font-weight: bold; user-select: none; }
        .sbp_card-content .sbp_card-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
        .sbp_profile-pic { position: absolute; width: 120px; height: 120px; overflow: hidden; cursor: move; pointer-events: auto; left: 250px; top: 20px; transition: left 0.1s, top 0.1s; will-change: transform; z-index: 5; }
        .sbp_profile-pic.dragging { transition: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); transform: scale(1.05); opacity: 0.9; }
        .sbp_profile-pic img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .sbp_profile-pic.square img { border-radius: 0; }
        .sbp_profile-pic:not(.square) img { border-radius: 50%; }
        .sbp_text-element, .sbp_image-element { position: absolute; cursor: move; pointer-events: auto; user-select: none; font-family: 'Battambang', sans-serif; transition: left 0.1s, top 0.1s; will-change: transform; }
        .sbp_text-element.dragging, .sbp_image-element.dragging { transition: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transform: scale(1.05); opacity: 0.9; }
        .sbp_text-element.selected, .sbp_image-element.selected, .sbp_profile-pic.selected { border: 1px solid cyan; }
        .sbp_image-element { width: 100px; height: 100px; }
        .sbp_image-element img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .sbp_image-element.square img { border-radius: 0; }
        .sbp_image-element:not(.square) img { border-radius: 50%; }
        .sbp_resize-handle { position: absolute; width: 10px; height: 10px; background: cyan; cursor: pointer; pointer-events: auto; }
        .sbp_resize-handle.top-left { top: -5px; left: -5px; cursor: nw-resize; }
        .sbp_resize-handle.top-right { top: -5px; right: -5px; cursor: ne-resize; }
        .sbp_resize-handle.bottom-left { bottom: -5px; left: -5px; cursor: sw-resize; }
        .sbp_resize-handle.bottom-right { bottom: -5px; right: -5px; cursor: se-resize; }
        
        .sbp_grid .sbp_card:nth-child(3),
        .sbp_grid .sbp_card:nth-child(4) { 
            transform: rotate(180deg); 
            transform-origin: center center; 
        }
    </style>
</head>
<body>
    <div class="sbp_wrapper">
        <div class="sbp_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="sbp_category-toggle" onclick="sbpApp.toggleCategory('sbp_background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="sbp_arrow">▼</span>
            </div>
            <div class="sbp_category-content" id="sbp_background-controls">
                <div class="sbp_filter-group">
                    <select class="sbp_background-dropdown" id="sbp_cardSelector" onchange="sbpApp.updateCurrentCard()">
                        <option value="sbp_card1">កាត ១</option>
                        <option value="sbp_card2">កាត ២</option>
                        <option value="sbp_card3">កាត ៣</option>
                        <option value="sbp_card4">កាត ៤</option>
                        <option value="sbp_card5">កាត ៥</option>
                        <option value="sbp_card6">កាត ៦</option>
                        <option value="sbp_card7">កាត ៧</option>
                        <option value="sbp_card8">កាត ៨</option>
                        <option value="sbp_card9">កាត ៩</option>
                        <option value="sbp_card10">កាត ១០</option>
                        <option value="sbp_card11">កាត ១១</option>
                        <option value="sbp_card12">កាត ១២</option>
                        <option value="sbp_card13">កាត ១៣</option>
                        <option value="sbp_card14">កាត ១៤</option>
                        <option value="sbp_card15">កាត ១៥</option>
                        <option value="sbp_card16">កាត ១៦</option>
                        <option value="sbp_card17">កាត ១៧</option>
                        <option value="sbp_card18">កាត ១៨</option>
                        <option value="sbp_card19">កាត ១៩</option>
                        <option value="sbp_card20">កាត ២០</option>
                        <option value="sbp_card21">កាត ២១</option>
                        <option value="sbp_card22">កាត ២២</option>
                        <option value="sbp_card23">កាត ២៣</option>
                        <option value="sbp_card24">កាត ២៤</option>
                        <option value="sbp_card25">កាត ២៥</option>
                        <option value="sbp_card26">កាត ២៦</option>
                        <option value="sbp_card27">កាត ២៧</option>
                        <option value="sbp_card28">កាត ២៨</option>
                        <option value="sbp_card29">កាត ២៩</option>
                        <option value="sbp_card30">កាត ៣០</option>
                        <option value="sbp_card31">កាត ៣១</option>
                        <option value="sbp_card32">កាត ៣២</option>
                    </select>
                    <select class="sbp_background-dropdown" id="sbp_backgroundDropdown" onchange="sbpApp.changeBackgroundFromDropdown()">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/5.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/6.png">ផ្ទៃខាងក្រោយ ២</option>
                        <option value="/docs/images/7.png">ផ្ទៃខាងក្រោយ ៣</option>
                        <option value="/docs/images/8.png">ផ្ទៃខាងក្រោយ ៤</option>
                    </select>
                    <select class="sbp_background-dropdown" id="sbp_rotationDropdown" onchange="sbpApp.rotateBackground()">
                        <option value="0">0 deg</option>
                        <option value="90">90 deg</option>
                        <option value="180">180 deg</option>
                        <option value="270">270 deg</option>
                    </select>
                    <button class="sbp_custom-button" id="sbp_changeBackgroundBtn" onclick="document.getElementById('sbp_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="sbp_imageUpload" accept="image/*" onchange="sbpApp.changeBackground()" style="display: none;">
                    <button class="sbp_custom-button sbp_reset-button" id="sbp_resetBackgroundBtn" onclick="sbpApp.resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="sbp_category-toggle" onclick="sbpApp.toggleCategory('sbp_profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="sbp_arrow">▼</span>
            </div>
            <div class="sbp_category-content" id="sbp_profile-controls">
                <div class="sbp_filter-group">
                    <button class="sbp_custom-button" id="sbp_changeProfileBtn" onclick="document.getElementById('sbp_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="sbp_profileUpload" accept="image/*" onchange="sbpApp.changeProfilePic()" style="display: none;">
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_squareShapeBtn" onclick="sbpApp.toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_circleShapeBtn" onclick="sbpApp.toggleProfileShape(false)">រាងមូល</button>
                    <button class="sbp_custom-button sbp_reset-button" id="sbp_resetProfileBtn" onclick="sbpApp.resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="sbp_category-toggle" onclick="sbpApp.toggleCategory('sbp_text-controls')">
                <i class="fas fa-edit"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="sbp_arrow">▼</span>
            </div>
            <div class="sbp_category-content" id="sbp_text-controls">
                <div class="sbp_filter-group">
                    <input type="text" id="sbp_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="sbp_text-input" oninput="sbpApp.updateTextButton()">
                    <input type="color" id="sbp_textColorPicker" value="#000000" class="sbp_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ" onchange="sbpApp.changeTextColor()">
                    <select class="sbp_font-size-dropdown" id="sbp_fontSizeDropdown" onchange="sbpApp.changeFontSize()">
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
                    <input type="number" id="sbp_customFontSize" class="sbp_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="sbp_font-style-dropdown" id="sbp_fontStyleDropdown" onchange="sbpApp.changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="sbp_text-button" id="sbp_textButton" onclick="sbpApp.handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="sbp_custom-button sbp_reset-button" id="sbp_cancelTextBtn" onclick="sbpApp.cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="sbp_category-toggle" onclick="sbpApp.toggleCategory('sbp_image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="sbp_arrow">▼</span>
            </div>
            <div class="sbp_category-content" id="sbp_image-overlay-controls">
                <div class="sbp_filter-group">
                    <button class="sbp_custom-button" id="sbp_addImageBtn" onclick="document.getElementById('sbp_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="sbp_imageUploadOverlay" accept="image/*" onchange="sbpApp.addOverlayImage()" style="display: none;">
                    <button class="sbp_custom-button" id="sbp_changeImageBtn" onclick="document.getElementById('sbp_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="sbp_changeImageUpload" accept="image/*" onchange="sbpApp.changeOverlayImage()" style="display: none;">
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_imageSquareShapeBtn" onclick="sbpApp.toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_imageCircleShapeBtn" onclick="sbpApp.toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="sbp_category-toggle" onclick="sbpApp.toggleCategory('sbp_element-controls')">
                <i class="fas fa-trash-alt"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="sbp_arrow">▼</span>
            </div>
            <div class="sbp_category-content" id="sbp_element-controls">
                <div class="sbp_filter-group">
                    <button class="sbp_custom-button sbp_reset-button" id="sbp_deleteSelectedElementsBtn" onclick="sbpApp.deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_bringForwardBtn" onclick="sbpApp.bringForward()" disabled>នាំមកមុខ</button>
                    <button class="sbp_custom-button sbp_secondary-button" id="sbp_sendBackwardBtn" onclick="sbpApp.sendBackward()" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="sbp_custom-button sbp_reset-button" id="sbp_resetAllBtn" onclick="sbpApp.resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="sbp_download-btn" id="sbp_downloadBtn" onclick="sbpApp.downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i></button>
        </div>
        <div class="sbp_displayArea_container">
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer1" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card1"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">1</span></div></div>
                    <div class="sbp_card" id="sbp_card2"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">2</span></div></div>
                    <div class="sbp_card" id="sbp_card3"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">3</span></div></div>
                    <div class="sbp_card" id="sbp_card4"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">4</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer2" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card5"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">5</span></div></div>
                    <div class="sbp_card" id="sbp_card6"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">6</span></div></div>
                    <div class="sbp_card" id="sbp_card7"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">7</span></div></div>
                    <div class="sbp_card" id="sbp_card8"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">8</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer3" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card9"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">9</span></div></div>
                    <div class="sbp_card" id="sbp_card10"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">10</span></div></div>
                    <div class="sbp_card" id="sbp_card11"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">11</span></div></div>
                    <div class="sbp_card" id="sbp_card12"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">12</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer4" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card13"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">13</span></div></div>
                    <div class="sbp_card" id="sbp_card14"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">14</span></div></div>
                    <div class="sbp_card" id="sbp_card15"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">15</span></div></div>
                    <div class="sbp_card" id="sbp_card16"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">16</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer5" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card17"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">17</span></div></div>
                    <div class="sbp_card" id="sbp_card18"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">18</span></div></div>
                    <div class="sbp_card" id="sbp_card19"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">19</span></div></div>
                    <div class="sbp_card" id="sbp_card20"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">20</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer6" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card21"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">21</span></div></div>
                    <div class="sbp_card" id="sbp_card22"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">22</span></div></div>
                    <div class="sbp_card" id="sbp_card23"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">23</span></div></div>
                    <div class="sbp_card" id="sbp_card24"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">24</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer7" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card25"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">25</span></div></div>
                    <div class="sbp_card" id="sbp_card26"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">26</span></div></div>
                    <div class="sbp_card" id="sbp_card27"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">27</span></div></div>
                    <div class="sbp_card" id="sbp_card28"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">28</span></div></div>
                </div>
            </div>
            <div class="sbp_displayArea">
                <div id="sbp_cardsContainer8" class="sbp_grid">
                    <div class="sbp_card" id="sbp_card29"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">29</span></div></div>
                    <div class="sbp_card" id="sbp_card30"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">30</span></div></div>
                    <div class="sbp_card" id="sbp_card31"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">31</span></div></div>
                    <div class="sbp_card" id="sbp_card32"><div class="sbp_card-content"><span class="sbp_card-placeholder-number">32</span></div></div>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function() {
        window.sbpApp = {
            initialized: false,
            selectedElement: null,
            currentCard: null,
            zIndexCounter: 10,
            profilePics: {},
            textCounter: 0,

            init: function() {
                if (this.initialized) return;
                this.initialized = true;

                this.currentCard = document.querySelector('.sbp_card');

                const textElements = document.querySelectorAll('.sbp_text-element');
                textElements.forEach(element => this.makeElementDraggable(element));

                const profilePicsElements = document.querySelectorAll('.sbp_profile-pic');
                profilePicsElements.forEach(pic => { 
                    this.makeElementDraggable(pic); 
                    const cardId = pic.closest('.sbp_card').id; 
                    this.profilePics[cardId] = pic; 
                });

                document.getElementById('sbp_cardSelector').addEventListener('change', () => this.updateCurrentCard());
                
                document.querySelector('.sbp_wrapper').addEventListener('click', (e) => {
                    if (!e.target.closest('.sbp_text-element') && !e.target.closest('.sbp_image-element') && !e.target.closest('.sbp_profile-pic') && !e.target.closest('.sbp_filters-container')) {
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
                const targetContent = document.getElementById(id);
                const targetToggle = targetContent.previousElementSibling;

                // If the clicked one is already open, just close it and do nothing else.
                if (targetContent.classList.contains('active')) {
                    targetContent.classList.remove('active');
                    targetToggle.classList.remove('active');
                    return;
                }

                // If it's not open, then close all others...
                document.querySelectorAll('.sbp_category-content').forEach(content => {
                    content.classList.remove('active');
                    content.previousElementSibling.classList.remove('active');
                });

                // ...and then open the clicked one.
                targetContent.classList.add('active');
                targetToggle.classList.add('active');
            },

            updateCurrentCard: function() {
                const cardSelector = document.getElementById('sbp_cardSelector');
                this.currentCard = document.querySelector(`#${cardSelector.value}`);
            },

            changeBackgroundFromDropdown: function() {
                this.updateCurrentCard();
                const dropdown = document.getElementById('sbp_backgroundDropdown');
                if (dropdown.value) {
                    let bgImg = this.currentCard.querySelector('.sbp_card-content .sbp_card-bg');
                    if (!bgImg) {
                        bgImg = document.createElement('img');
                        bgImg.classList.add('sbp_card-bg');
                        this.currentCard.querySelector('.sbp_card-content').appendChild(bgImg);
                    }
                    bgImg.src = dropdown.value;
                    const placeholder = this.currentCard.querySelector('.sbp_card-placeholder-number');
                    if(placeholder) placeholder.style.display = 'none';
                }
            },

            rotateBackground: function() {
                this.updateCurrentCard();
                const dropdown = document.getElementById('sbp_rotationDropdown');
                const rotation = dropdown.value;
                const bgImg = this.currentCard.querySelector('.sbp_card-content .sbp_card-bg');
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
                const fileInput = document.getElementById('sbp_imageUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        let bgImg = this.currentCard.querySelector('.sbp_card-content .sbp_card-bg');
                        if (!bgImg) {
                            bgImg = document.createElement('img');
                            bgImg.classList.add('sbp_card-bg');
                            this.currentCard.querySelector('.sbp_card-content').appendChild(bgImg);
                        }
                        bgImg.src = e.target.result;
                        const placeholder = this.currentCard.querySelector('.sbp_card-placeholder-number');
                        if(placeholder) placeholder.style.display = 'none';
                    };
                    reader.onerror = () => { alert('Could not load image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },

            resetBackground: function() {
                this.updateCurrentCard();
                const bgImg = this.currentCard.querySelector('.sbp_card-content .sbp_card-bg');
                if (bgImg) {
                    bgImg.remove();
                }
                const placeholder = this.currentCard.querySelector('.sbp_card-placeholder-number');
                if(placeholder) placeholder.style.display = 'block';

                document.getElementById('sbp_backgroundDropdown').value = '';
                document.getElementById('sbp_imageUpload').value = '';
                document.getElementById('sbp_rotationDropdown').value = '0';
            },

            changeProfilePic: function() {
                this.updateCurrentCard();
                const fileInput = document.getElementById('sbp_profileUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const cardId = this.currentCard.id;
                        let profilePic = this.currentCard.querySelector('.sbp_profile-pic');
                        if (!profilePic) {
                            profilePic = document.createElement('div');
                            profilePic.classList.add('sbp_profile-pic');
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
                const profilePic = this.currentCard.querySelector('.sbp_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    this.profilePics[cardId] = profilePic;
                    document.getElementById('sbp_profileUpload').value = '';
                    this.selectElement(profilePic);
                }
            },

            toggleProfileShape: function(isSquare) {
                this.updateCurrentCard();
                const cardId = this.currentCard.id;
                const profilePic = this.profilePics[cardId] || this.currentCard.querySelector('.sbp_profile-pic');
                if (profilePic) {
                    if (isSquare) { profilePic.classList.add('square'); } else { profilePic.classList.remove('square'); }
                }
            },

            addOverlayImage: function() {
                this.updateCurrentCard();
                const fileInput = document.getElementById('sbp_imageUploadOverlay');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageElement = document.createElement('div');
                        imageElement.classList.add('sbp_image-element');
                        imageElement.id = `sbp_image-element-${this.textCounter++}`;
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
                const fileInput = document.getElementById('sbp_changeImageUpload');
                const file = fileInput.files[0];
                if (file && this.selectedElement && this.selectedElement.classList.contains('sbp_image-element')) {
                    const reader = new FileReader();
                    reader.onload = (e) => { this.selectedElement.querySelector('img').src = e.target.result; };
                    reader.onerror = () => { alert('Could not change image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },

            toggleImageShape: function(isSquare) {
                if (this.selectedElement && this.selectedElement.classList.contains('sbp_image-element')) {
                    if (isSquare) { this.selectedElement.classList.add('square'); } else { this.selectedElement.classList.remove('square'); }
                }
            },

            updateTextButton: function() {
                const textInput = document.getElementById('sbp_textInput');
                const textButton = document.getElementById('sbp_textButton');
                textButton.textContent = textInput.value && this.selectedElement && this.selectedElement.classList.contains('sbp_text-element') ? 'Update Text' : 'Add Text';
            },

            handleTextAction: function() {
                const textInput = document.getElementById('sbp_textInput');
                const fontSizeDropdown = document.getElementById('sbp_fontSizeDropdown');
                const fontStyleDropdown = document.getElementById('sbp_fontStyleDropdown');
                const textColorPicker = document.getElementById('sbp_textColorPicker');
                const customFontSize = document.getElementById('sbp_customFontSize');
                this.updateCurrentCard();
                if (textInput.value) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    let textElement;
                    if (this.selectedElement && this.selectedElement.classList.contains('sbp_text-element')) {
                        textElement = this.selectedElement;
                    } else {
                        textElement = document.createElement('p');
                        textElement.classList.add('sbp_text-element');
                        textElement.id = `sbp_text-element-${this.textCounter++}`;
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
                const fontSizeDropdown = document.getElementById('sbp_fontSizeDropdown');
                const customFontSize = document.getElementById('sbp_customFontSize');
                customFontSize.classList.toggle('sbp_hidden', fontSizeDropdown.value !== 'custom');
                if (this.selectedElement && this.selectedElement.classList.contains('sbp_text-element')) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    this.selectedElement.style.fontSize = fontSize;
                }
            },

            changeFontStyle: function() {
                const fontStyleDropdown = document.getElementById('sbp_fontStyleDropdown');
                if (this.selectedElement && this.selectedElement.classList.contains('sbp_text-element')) {
                    this.selectedElement.style.fontFamily = fontStyleDropdown.value;
                }
            },

            changeTextColor: function() {
                const textColorPicker = document.getElementById('sbp_textColorPicker');
                if (this.selectedElement && this.selectedElement.classList.contains('sbp_text-element')) {
                    this.selectedElement.style.color = textColorPicker.value;
                }
            },

            cancelText: function() {
                const textInput = document.getElementById('sbp_textInput');
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
                    if (e.target.classList.contains('sbp_resize-handle')) return;
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
                    if (element.classList.contains('sbp_text-element')) {
                        const textInput = document.getElementById('sbp_textInput');
                        textInput.value = element.textContent;
                        this.updateTextButton();
                    } else {
                        document.getElementById('sbp_textInput').value = '';
                        this.updateTextButton();
                    }
                }
                this.updateElementControls();
            },

            updateElementControls: function() {
                const bringForwardBtn = document.getElementById('sbp_bringForwardBtn');
                const sendBackwardBtn = document.getElementById('sbp_sendBackwardBtn');
                const deleteBtn = document.getElementById('sbp_deleteSelectedElementsBtn');
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
                    if (this.selectedElement.classList.contains('sbp_profile-pic')) { this.profilePics[cardId] = null; }
                    this.selectedElement.remove();
                    this.selectedElement = null;
                    this.updateElementControls();
                    document.getElementById('sbp_textInput').value = '';
                    this.updateTextButton();
                }
            },

            resetLayout: function() {
                const cards = document.querySelectorAll('.sbp_card');
                cards.forEach((card) => {
                    const bgImg = card.querySelector('.sbp_card-content .sbp_card-bg');
                    if (bgImg) {
                        bgImg.remove();
                    }
                    const placeholder = card.querySelector('.sbp_card-placeholder-number');
                    if (placeholder) {
                        placeholder.style.display = 'block';
                    }
                    const textElements = card.querySelectorAll('.sbp_text-element');
                    textElements.forEach(element => element.remove());
                    const imageElements = card.querySelectorAll('.sbp_image-element');
                    imageElements.forEach(element => element.remove());
                    const qrContainers = card.querySelectorAll('.sbp_qr-container');
                    qrContainers.forEach(element => element.remove());
                    const cardId = card.id;
                    const profilePic = card.querySelector('.sbp_profile-pic');
                    if (profilePic) {
                        profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                        profilePic.style.left = '250px';
                        profilePic.style.top = '20px';
                        profilePic.classList.remove('square');
                        this.profilePics[cardId] = profilePic;
                    }
                });
                document.getElementById('sbp_backgroundDropdown').value = '';
                document.getElementById('sbp_imageUpload').value = '';
                document.getElementById('sbp_textInput').value = '';
                this.updateTextButton();
                document.getElementById('sbp_profileUpload').value = '';
                document.getElementById('sbp_imageUploadOverlay').value = '';
                document.getElementById('sbp_changeImageUpload').value = '';
                this.selectedElement = null;
                this.updateElementControls();
            },

            downloadCards: async function() {
                const downloadBtn = document.getElementById('sbp_downloadBtn');
                downloadBtn.innerHTML = 'Generating PDF...';
                downloadBtn.disabled = true;
                
                const allCards = document.querySelectorAll('.sbp_card');
                allCards.forEach(card => {
                    card.style.border = 'none';
                });

                try {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const containers = document.querySelectorAll('.sbp_grid');
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
            document.addEventListener('DOMContentLoaded', sbpApp.init.bind(sbpApp));
        } else {
            sbpApp.init();
        }
    })();
    </script>
</body>
</html>