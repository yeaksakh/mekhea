<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Editor</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap">
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
        .pk_A_wrapper * {
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
        .pk_A_wrapper {
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
        .pk_A_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .pk_A_filters-container h2 { text-align: center; margin: 0 0 10px 0; color: #333; }
        .pk_A_filters-container hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }
        .pk_A_category-toggle {
            padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center; font-size: 16px; color: #333; margin-bottom: 15px;
        }
        .pk_A_category-toggle:hover { background-color: #c8d8ff; }
        .pk_A_category-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease; }
        .pk_A_category-content.active { max-height: 500px; padding: 10px; }
        .pk_A_filter-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
        .pk_A_background-dropdown,
        .pk_A_font-size-dropdown,
        .pk_A_font-style-dropdown {
            width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat; background-position: right 10px center; background-size: 18px;
        }
        .pk_A_custom-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_A_custom-button:hover { background-color: #138496; }
        .pk_A_custom-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .pk_A_reset-button { background-color: #dc3545; color: white; }
        .pk_A_reset-button:hover { background-color: #c82333; }
        .pk_A_download-btn { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_A_download-btn:hover { background-color: #138496; }
        .pk_A_secondary-button { background-color: #6c757d; color: white; }
        .pk_A_secondary-button:hover { background-color: #5a6268; }
        .pk_A_text-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pk_A_text-button:hover { background-color: #138496; }
        .pk_A_text-input, .pk_A_color-picker { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        .pk_A_text-input:focus { border-color: #17a2b8; outline: none; box-shadow: 0 0 5px rgba(23, 162, 184, 0.5); }
        .pk_A_hidden { display: none; }
        .pk_A_arrow { transition: transform 0.3s; font-size: 14px; }
        .pk_A_category-toggle.active .pk_A_arrow { transform: rotate(180deg); }
        .pk_A_displayArea_container {
            width: 70%;
            overflow-y: auto;
            height: 100vh;
            padding: 5px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .pk_A_displayArea { padding: 5px; margin: 0; box-sizing: border-box; border: 1px solid gray; border-radius: 10px; display: flex; justify-content: center; align-items: center; }
        .pk_A_grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: var(--row-gap) var(--column-gap); padding: var(--grid-padding); background-color: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); width: 100%; max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding)); height: calc(2 * var(--card-height) + var(--row-gap) + 2 * var(--grid-padding)); }
        .pk_A_card { width: 100%; aspect-ratio: 380 / 560; margin: var(--card-margin); padding: var(--card-padding); border: 2px solid #1C2526; background-color: #ffffff; position: relative; overflow: hidden; font-family: 'Battambang', sans-serif; cursor: pointer; }
        .pk_A_card.selected { border: 2px solid cyan; }
        .pk_A_card-content { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; display: flex; justify-content: center; align-items: center; }
        .pk_A_card-placeholder-number { font-size: 5rem; color: #e0e0e0; font-weight: bold; user-select: none; }
        .pk_A_card-content .pk_A_card-bg { position: absolute; inset: 0; width: 100%; height: 100%; background-size: cover; background-position: center; background-repeat: no-repeat; pointer-events: none; }
        .pk_A_profile-pic { position: absolute; width: 120px; height: 120px; overflow: hidden; cursor: move; pointer-events: auto; left: 250px; top: 20px; transition: left 0.1s, top 0.1s; will-change: transform; z-index: 5; }
        .pk_A_profile-pic.dragging { transition: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); transform: scale(1.05); opacity: 0.9; }
        .pk_A_profile-pic img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pk_A_profile-pic.square img { border-radius: 0; }
        .pk_A_profile-pic:not(.square) img { border-radius: 50%; }
        .pk_A_text-element, .pk_A_image-element { position: absolute; cursor: move; pointer-events: auto; user-select: none; font-family: 'Battambang', sans-serif; transition: left 0.1s, top 0.1s; will-change: transform; }
        .pk_A_text-element.dragging, .pk_A_image-element.dragging { transition: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transform: scale(1.05); opacity: 0.9; }
        .pk_A_text-element.selected, .pk_A_image-element.selected, .pk_A_profile-pic.selected { border: 1px solid cyan; }
        .pk_A_image-element { width: 100px; height: 100px; }
        .pk_A_image-element img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pk_A_image-element.square img { border-radius: 0; }
        .pk_A_image-element:not(.square) img { border-radius: 50%; }
        .pk_A_resize-handle { position: absolute; width: 10px; height: 10px; background: cyan; cursor: pointer; pointer-events: auto; }
        .pk_A_resize-handle.top-left { top: -5px; left: -5px; cursor: nw-resize; }
        .pk_A_resize-handle.top-right { top: -5px; right: -5px; cursor: ne-resize; }
        .pk_A_resize-handle.bottom-left { bottom: -5px; left: -5px; cursor: sw-resize; }
        .pk_A_resize-handle.bottom-right { bottom: -5px; right: -5px; cursor: se-resize; }
        
        .signature-box {
            width: 130px;
            min-height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="pk_A_wrapper">
        <div class="pk_A_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="pk_A_category-toggle" onclick="pkAApp.toggleCategory('pk_A_background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="pk_A_arrow">▼</span>
            </div>
            <div class="pk_A_category-content" id="pk_A_background-controls">
                <div class="pk_A_filter-group">
                    <select class="pk_A_background-dropdown" id="pk_A_rotationDropdown" onchange="pkAApp.rotateBackground()">
                        <option value="0">0 deg</option>
                        <option value="90">90 deg</option>
                        <option value="180">180 deg</option>
                        <option value="270">270 deg</option>
                    </select>
                    <button class="pk_A_custom-button" id="pk_A_changeBackgroundBtn" onclick="document.getElementById('pk_A_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="pk_A_imageUpload" accept="image/*" onchange="pkAApp.changeBackground()" style="display: none;">
                    <button class="pk_A_custom-button pk_A_reset-button" id="pk_A_resetBackgroundBtn" onclick="pkAApp.resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="pk_A_category-toggle" onclick="pkAApp.toggleCategory('pk_A_profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="pk_A_arrow">▼</span>
            </div>
            <div class="pk_A_category-content" id="pk_A_profile-controls">
                <div class="pk_A_filter-group">
                    <button class="pk_A_custom-button" id="pk_A_changeProfileBtn" onclick="document.getElementById('pk_A_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="pk_A_profileUpload" accept="image/*" onchange="pkAApp.changeProfilePic()" style="display: none;">
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_squareShapeBtn" onclick="pkAApp.toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_circleShapeBtn" onclick="pkAApp.toggleProfileShape(false)">រាងមូល</button>
                    <button class="pk_A_custom-button pk_A_reset-button" id="pk_A_resetProfileBtn" onclick="pkAApp.resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="pk_A_category-toggle" onclick="pkAApp.toggleCategory('pk_A_text-controls')">
                <i class="fas fa-edit"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="pk_A_arrow">▼</span>
            </div>
            <div class="pk_A_category-content" id="pk_A_text-controls">
                <div class="pk_A_filter-group">
                    <input type="text" id="pk_A_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="pk_A_text-input" oninput="pkAApp.updateTextButton()">
                    <input type="color" id="pk_A_textColorPicker" value="#000000" class="pk_A_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ" onchange="pkAApp.changeTextColor()">
                    <select class="pk_A_font-size-dropdown" id="pk_A_fontSizeDropdown" onchange="pkAApp.changeFontSize()">
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
                    <input type="number" id="pk_A_customFontSize" class="pk_A_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="pk_A_font-style-dropdown" id="pk_A_fontStyleDropdown" onchange="pkAApp.changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="pk_A_text-button" id="pk_A_textButton" onclick="pkAApp.handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="pk_A_custom-button pk_A_reset-button" id="pk_A_cancelTextBtn" onclick="pkAApp.cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="pk_A_category-toggle" onclick="pkAApp.toggleCategory('pk_A_image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="pk_A_arrow">▼</span>
            </div>
            <div class="pk_A_category-content" id="pk_A_image-overlay-controls">
                <div class="pk_A_filter-group">
                    <button class="pk_A_custom-button" id="pk_A_addImageBtn" onclick="document.getElementById('pk_A_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="pk_A_imageUploadOverlay" accept="image/*" onchange="pkAApp.addOverlayImage()" style="display: none;">
                    <button class="pk_A_custom-button" id="pk_A_changeImageBtn" onclick="document.getElementById('pk_A_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="pk_A_changeImageUpload" accept="image/*" onchange="pkAApp.changeOverlayImage()" style="display: none;">
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_imageSquareShapeBtn" onclick="pkAApp.toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_imageCircleShapeBtn" onclick="pkAApp.toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="pk_A_category-toggle" onclick="pkAApp.toggleCategory('pk_A_element-controls')">
                <i class="fas fa-trash-alt"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="pk_A_arrow">▼</span>
            </div>
            <div class="pk_A_category-content" id="pk_A_element-controls">
                <div class="pk_A_filter-group">
                    <button class="pk_A_custom-button pk_A_reset-button" id="pk_A_deleteSelectedElementsBtn" onclick="pkAApp.deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_bringForwardBtn" onclick="pkAApp.bringForward()" disabled>នាំមកមុខ</button>
                    <button class="pk_A_custom-button pk_A_secondary-button" id="pk_A_sendBackwardBtn" onclick="pkAApp.sendBackward()" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="pk_A_custom-button pk_A_reset-button" id="pk_A_resetAllBtn" onclick="pkAApp.resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="pk_A_download-btn" id="pk_A_downloadBtn" onclick="pkAApp.downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i></button>
        </div>
        <div class="pk_A_displayArea_container">
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer1" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card1"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/1.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card2"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/A.jpg')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card3"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/1.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card4"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/XS.jpg')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer2" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card5">
                        <div class="pk_A_card-content" style="background-color: white;">
                        </div>
                         <div class="pk_A_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 5px;">ចក្ខុវិស័យ</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 33px; width: 340px">ធ្វើអោយគ្រួសារមានសុភមង្គលតាមរយៈអាជីវកម្មបោកអ៊ុតដែលប្រើប្រាស់ផលិតផល</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 48px; width: 340px">ខ្មែរ តម្លៃក្នុងស្រុក គុណភាពអន្តរជាតិ</div>
                        <div class="pk_A_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 71px;">បេសកកម្ម</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 99px; width: 340px">លើកកម្ពស់ម៉ាកយីហោ និង ផលិតផល ក្នុងស្រុក រួមចំណែកកសាងសេដ្ឋកិច្ចគ្រួសារ</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 114px; width: 340px">គ្រប់ផ្ទះបានប្រើប្រាស់ផលិតផលយក្សាដែលមានគុណភាពល្អបំផុត</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 129px; width: 340px">ភូមិមួយមានហាងបោកអ៊ុតមួយ</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 144px; width: 340px">ផ្តល់ការបណ្តុះបណ្តាលជំនាញបោកអ៊ុតស្តង់ដាដល់ស្ត្រីតាមសហគមន៍</div>
                        <div class="pk_A_text-element" style="text-align: left; color: black; font-size: 12px; left: 10px; top: 159px; width: 340px">គ្រប់ហាងបោកសម្លៀកបំពាក់ទាំងអស់ប្រើប្រាស់ផលិតផលយក្សា</div>
                        <div class="pk_A_text-element" style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0; left: 10px; top: 182px;">ហេតុអ្វីបានជាចូលរួមសមាជិក?</div>
                        <ul class="pk_A_text-element bullet-list" style="text-align: left; font-size: 12px; color: black; left: 10px; top: 210px; width: 340px">
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
                    <div class="pk_A_card" id="pk_A_card6">
                        <div class="pk_A_card-content" style="padding: 5px;">
                            <div class="pk_A_card-bg" style="background-image: url('/docs/images/4.png')"></div>
                        </div>
                        <div style="position: absolute; top: 19px; left: 19px; right: 19px; bottom: 19px; z-index: 1;">
                        <div class="verify-text" style="color: red; margin: 12px auto 8px;">
                            ពិន្ទុប្រចាំឆ្នាំ
                        </div>
                        <div class="signature-box"></div>
                        <div class="verify-text" style="margin: 20px auto 10px; color: black;">
                            ការប្រើប្រាស់
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; padding: 10px 0; color: black;">
                            ពិន្ទុ * ផ្កាយ1&ZeroWidthSpace;=1ពិន្ទុ, ផ្កាយ2=2ពិន្ទុ, ផ្កាយ3&ZeroWidthSpace;=3ពិន្ទុ, ផ្កាយ4&ZeroWidthSpace;=4ពិន្ទុ, ផ្កាយ5&ZeroWidthSpace;=5ពិន្ទុ <br>
                            រង្វាន់ * រង្វាន់គឺអាស្រ័យទៅតាមពិន្ទុរបស់លោកអ្នកប្រចាំឆ្នាំ តាមលក្ខ័ណ្ឌក្រុមហ៊ុន
                        </div>
                        <div style="margin-top: 110px;"></div>
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
                    <div class="pk_A_card" id="pk_A_card7"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/yeaksa.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card8"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/laundry_flow.jpg')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer3" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card9"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page24.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card10"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page1.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card11"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page22.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card12"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page3.png')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer4" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card13"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page2.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card14"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page23.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card15"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page4.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card16"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page21.png')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer5" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card17"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page20.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card18"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page5.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card19"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page18.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card20"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page7.png')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer6" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card21"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page6.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card22"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page19.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card23"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page8.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card24"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page17.png')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer7" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card25"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page16.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card26"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page9.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card27"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page14.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card28"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page11.png')"></div></div></div>
                </div>
            </div>
            <div class="pk_A_displayArea">
                <div id="pk_A_cardsContainer8" class="pk_A_grid">
                    <div class="pk_A_card" id="pk_A_card29"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page10.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card30"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page15.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card31"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page12.png')"></div></div></div>
                    <div class="pk_A_card" id="pk_A_card32"><div class="pk_A_card-content"><div class="pk_A_card-bg" style="background-image: url('/docs/images/franchise_package/packageA/page13.png')"></div></div></div>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function() {
        window.pkAApp = {
            initialized: false,
            selectedElement: null,
            selectedCard: null,
            zIndexCounter: 10,
            profilePics: {},
            textCounter: 0,
            defaultCard5HTML: '',
            defaultBackgrounds: {
                'pk_A_card1': '/docs/images/1.png',
                'pk_A_card2': '/docs/images/franchise_package/A.jpg',
                'pk_A_card3': '/docs/images/1.png',
                'pk_A_card4': '/docs/images/franchise_package/XS.jpg',
                'pk_A_card5': null, // White background
                'pk_A_card6': '/docs/images/4.png',
                'pk_A_card7': '/docs/images/franchise_package/yeaksa.png',
                'pk_A_card8': '/docs/images/franchise_package/laundry_flow.jpg',
                'pk_A_card9': '/docs/images/franchise_package/packageA/page24.png',
                'pk_A_card10': '/docs/images/franchise_package/packageA/page1.png',
                'pk_A_card11': '/docs/images/franchise_package/packageA/page22.png',
                'pk_A_card12': '/docs/images/franchise_package/packageA/page3.png',
                'pk_A_card13': '/docs/images/franchise_package/packageA/page2.png',
                'pk_A_card14': '/docs/images/franchise_package/packageA/page23.png',
                'pk_A_card15': '/docs/images/franchise_package/packageA/page4.png',
                'pk_A_card16': '/docs/images/franchise_package/packageA/page21.png',
                'pk_A_card17': '/docs/images/franchise_package/packageA/page20.png',
                'pk_A_card18': '/docs/images/franchise_package/packageA/page5.png',
                'pk_A_card19': '/docs/images/franchise_package/packageA/page18.png',
                'pk_A_card20': '/docs/images/franchise_package/packageA/page7.png',
                'pk_A_card21': '/docs/images/franchise_package/packageA/page6.png',
                'pk_A_card22': '/docs/images/franchise_package/packageA/page19.png',
                'pk_A_card23': '/docs/images/franchise_package/packageA/page8.png',
                'pk_A_card24': '/docs/images/franchise_package/packageA/page17.png',
                'pk_A_card25': '/docs/images/franchise_package/packageA/page16.png',
                'pk_A_card26': '/docs/images/franchise_package/packageA/page9.png',
                'pk_A_card27': '/docs/images/franchise_package/packageA/page14.png',
                'pk_A_card28': '/docs/images/franchise_package/packageA/page11.png',
                'pk_A_card29': '/docs/images/franchise_package/packageA/page10.png',
                'pk_A_card30': '/docs/images/franchise_package/packageA/page15.png',
                'pk_A_card31': '/docs/images/franchise_package/packageA/page12.png',
                'pk_A_card32': '/docs/images/franchise_package/packageA/page13.png'
            },
            init: function() {
                if (this.initialized) return;
                this.initialized = true;
                this.defaultCard5HTML = document.getElementById('pk_A_card5').innerHTML;
                
                // Load saved backgrounds from localStorage
                this.loadBackgrounds();
                
                this.selectedCard = document.querySelector('.pk_A_card');
                const textElements = document.querySelectorAll('.pk_A_text-element');
                textElements.forEach(element => this.makeElementDraggable(element));
                const profilePicsElements = document.querySelectorAll('.pk_A_profile-pic');
                profilePicsElements.forEach(pic => { 
                    this.makeElementDraggable(pic); 
                    const cardId = pic.closest('.pk_A_card').id; 
                    this.profilePics[cardId] = pic; 
                });
                // Add click event listeners to all cards
                const cards = document.querySelectorAll('.pk_A_card');
                cards.forEach(card => {
                    card.addEventListener('click', (e) => {
                        // Prevent selecting card if clicking on draggable elements
                        if (!e.target.closest('.pk_A_text-element') && 
                            !e.target.closest('.pk_A_image-element') && 
                            !e.target.closest('.pk_A_profile-pic')) {
                            this.selectCard(card);
                        }
                    });
                });
                document.querySelector('.pk_A_wrapper').addEventListener('click', (e) => {
                    if (!e.target.closest('.pk_A_text-element') && 
                        !e.target.closest('.pk_A_image-element') && 
                        !e.target.closest('.pk_A_profile-pic') && 
                        !e.target.closest('.pk_A_filters-container') && 
                        !e.target.closest('.pk_A_card')) {
                        this.selectCard(null);
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
            // Load backgrounds from localStorage
            loadBackgrounds: function() {
                const savedBackgrounds = localStorage.getItem('cardBackgrounds');
                if (savedBackgrounds) {
                    const backgrounds = JSON.parse(savedBackgrounds);
                    for (const cardId in backgrounds) {
                        const card = document.getElementById(cardId);
                        if (card) {
                            let bgImg = card.querySelector('.pk_A_card-content .pk_A_card-bg');
                            if (!bgImg) {
                                bgImg = document.createElement('div');
                                bgImg.classList.add('pk_A_card-bg');
                                card.querySelector('.pk_A_card-content').appendChild(bgImg);
                            }
                            bgImg.style.backgroundImage = `url('${backgrounds[cardId].src}')`;
                            
                            // Apply rotation if saved
                            if (backgrounds[cardId].rotation) {
                                bgImg.style.transform = `rotate(${backgrounds[cardId].rotation}deg)`;
                            }
                            
                            const placeholder = card.querySelector('.pk_A_card-placeholder-number');
                            if(placeholder) placeholder.style.display = 'none';
                        }
                    }
                }
            },
            // Save background to localStorage
            saveBackground: function(cardId, src, rotation = null) {
                let savedBackgrounds = localStorage.getItem('cardBackgrounds');
                let backgrounds = savedBackgrounds ? JSON.parse(savedBackgrounds) : {};
                
                backgrounds[cardId] = {
                    src: src,
                    rotation: rotation
                };
                
                localStorage.setItem('cardBackgrounds', JSON.stringify(backgrounds));
            },
            // Remove background from localStorage
            removeBackground: function(cardId) {
                let savedBackgrounds = localStorage.getItem('cardBackgrounds');
                if (savedBackgrounds) {
                    let backgrounds = JSON.parse(savedBackgrounds);
                    if (backgrounds[cardId]) {
                        delete backgrounds[cardId];
                        localStorage.setItem('cardBackgrounds', JSON.stringify(backgrounds));
                    }
                }
            },
            selectCard: function(card) {
                // Deselect previous card
                if (this.selectedCard) {
                    this.selectedCard.classList.remove('selected');
                }
                this.selectedCard = card;
                if (card) {
                    card.classList.add('selected');
                }
            },
            toggleCategory: function(id) {
                document.querySelectorAll('.pk_A_category-content').forEach(content => {
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
            changeBackgroundFromDropdown: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const dropdown = document.getElementById('pk_A_backgroundDropdown');
                if (dropdown.value) {
                    let bgImg = this.selectedCard.querySelector('.pk_A_card-content .pk_A_card-bg');
                    if (!bgImg) {
                        bgImg = document.createElement('div');
                        bgImg.classList.add('pk_A_card-bg');
                        this.selectedCard.querySelector('.pk_A_card-content').appendChild(bgImg);
                    }
                    bgImg.style.backgroundImage = `url('${dropdown.value}')`;
                    const placeholder = this.selectedCard.querySelector('.pk_A_card-placeholder-number');
                    if(placeholder) placeholder.style.display = 'none';
                    
                    // Save to localStorage
                    this.saveBackground(this.selectedCard.id, dropdown.value);
                }
            },
            rotateBackground: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const dropdown = document.getElementById('pk_A_rotationDropdown');
                const rotation = dropdown.value;
                const bgImg = this.selectedCard.querySelector('.pk_A_card-content .pk_A_card-bg');
                if (bgImg) {
                    let transformValue = `rotate(${rotation}deg)`;
                    if (rotation === '90' || rotation === '270') {
                        const card = this.selectedCard;
                        const cardAspectRatio = card.offsetHeight / card.offsetWidth;
                        if (cardAspectRatio > 1) {
                            transformValue += ` scale(${cardAspectRatio})`;
                        } else if (cardAspectRatio < 1) {
                            transformValue += ` scale(${1/cardAspectRatio})`;
                        }
                    }
                    bgImg.style.transform = transformValue;
                    
                    // Save to localStorage
                    const savedBackgrounds = localStorage.getItem('cardBackgrounds');
                    let backgrounds = savedBackgrounds ? JSON.parse(savedBackgrounds) : {};
                    if (backgrounds[this.selectedCard.id]) {
                        backgrounds[this.selectedCard.id].rotation = rotation;
                        localStorage.setItem('cardBackgrounds', JSON.stringify(backgrounds));
                    }
                }
            },
            changeBackground: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fileInput = document.getElementById('pk_A_imageUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        let bgImg = this.selectedCard.querySelector('.pk_A_card-content .pk_A_card-bg');
                        if (!bgImg) {
                            bgImg = document.createElement('div');
                            bgImg.classList.add('pk_A_card-bg');
                            this.selectedCard.querySelector('.pk_A_card-content').appendChild(bgImg);
                        }
                        bgImg.style.backgroundImage = `url('${e.target.result}')`;
                        const placeholder = this.selectedCard.querySelector('.pk_A_card-placeholder-number');
                        if(placeholder) placeholder.style.display = 'none';
                        
                        // Save to localStorage
                        this.saveBackground(this.selectedCard.id, e.target.result);
                    };
                    reader.onerror = () => { alert('Could not load image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            resetBackground: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const bgImg = this.selectedCard.querySelector('.pk_A_card-content .pk_A_card-bg');
                if (bgImg) {
                    bgImg.style.backgroundImage = '';
                }
                const placeholder = this.selectedCard.querySelector('.pk_A_card-placeholder-number');
                if(placeholder) placeholder.style.display = 'block';
                document.getElementById('pk_A_backgroundDropdown').value = '';
                document.getElementById('pk_A_imageUpload').value = '';
                document.getElementById('pk_A_rotationDropdown').value = '0';
                
                // Remove from localStorage
                this.removeBackground(this.selectedCard.id);
            },
            changeProfilePic: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fileInput = document.getElementById('pk_A_profileUpload');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const cardId = this.selectedCard.id;
                        let profilePic = this.selectedCard.querySelector('.pk_A_profile-pic');
                        if (!profilePic) {
                            profilePic = document.createElement('div');
                            profilePic.classList.add('pk_A_profile-pic');
                            profilePic.id = `profile-pic-${cardId}`;
                            const img = document.createElement('img');
                            img.alt = 'Profile';
                            profilePic.appendChild(img);
                            profilePic.style.left = '250px';
                            profilePic.style.top = '20px';
                            this.selectedCard.appendChild(profilePic);
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
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const cardId = this.selectedCard.id;
                const profilePic = this.selectedCard.querySelector('.pk_A_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    this.profilePics[cardId] = profilePic;
                    document.getElementById('pk_A_profileUpload').value = '';
                    this.selectElement(profilePic);
                }
            },
            toggleProfileShape: function(isSquare) {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const cardId = this.selectedCard.id;
                const profilePic = this.profilePics[cardId] || this.selectedCard.querySelector('.pk_A_profile-pic');
                if (profilePic) {
                    if (isSquare) { profilePic.classList.add('square'); } else { profilePic.classList.remove('square'); }
                }
            },
            addOverlayImage: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fileInput = document.getElementById('pk_A_imageUploadOverlay');
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imageElement = document.createElement('div');
                        imageElement.classList.add('pk_A_image-element');
                        imageElement.id = `pk_A_image-element-${this.textCounter++}`;
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        imageElement.appendChild(img);
                        imageElement.style.zIndex = this.zIndexCounter++;
                        imageElement.style.left = '50px';
                        imageElement.style.top = '50px';
                        this.selectedCard.appendChild(imageElement);
                        this.makeElementDraggable(imageElement);
                        this.selectElement(imageElement);
                    };
                    reader.onerror = () => { alert('Could not add image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            changeOverlayImage: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fileInput = document.getElementById('pk_A_changeImageUpload');
                const file = fileInput.files[0];
                if (file && this.selectedElement && this.selectedElement.classList.contains('pk_A_image-element')) {
                    const reader = new FileReader();
                    reader.onload = (e) => { this.selectedElement.querySelector('img').src = e.target.result; };
                    reader.onerror = () => { alert('Could not change image. Please try again.'); };
                    reader.readAsDataURL(file);
                }
            },
            toggleImageShape: function(isSquare) {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                if (this.selectedElement && this.selectedElement.classList.contains('pk_A_image-element')) {
                    if (isSquare) { this.selectedElement.classList.add('square'); } else { this.selectedElement.classList.remove('square'); }
                }
            },
            updateTextButton: function() {
                const textInput = document.getElementById('pk_A_textInput');
                const textButton = document.getElementById('pk_A_textButton');
                textButton.textContent = textInput.value && this.selectedElement && this.selectedElement.classList.contains('pk_A_text-element') ? 'Update Text' : 'Add Text';
            },
            handleTextAction: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const textInput = document.getElementById('pk_A_textInput');
                const fontSizeDropdown = document.getElementById('pk_A_fontSizeDropdown');
                const fontStyleDropdown = document.getElementById('pk_A_fontStyleDropdown');
                const textColorPicker = document.getElementById('pk_A_textColorPicker');
                const customFontSize = document.getElementById('pk_A_customFontSize');
                if (textInput.value) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    let textElement;
                    if (this.selectedElement && this.selectedElement.classList.contains('pk_A_text-element')) {
                        textElement = this.selectedElement;
                    } else {
                        textElement = document.createElement('p');
                        textElement.classList.add('pk_A_text-element');
                        textElement.id = `pk_A_text-element-${this.textCounter++}`;
                        textElement.style.zIndex = this.zIndexCounter++;
                        textElement.style.left = '50px';
                        textElement.style.top = '50px';
                        this.selectedCard.appendChild(textElement);
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
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fontSizeDropdown = document.getElementById('pk_A_fontSizeDropdown');
                const customFontSize = document.getElementById('pk_A_customFontSize');
                customFontSize.classList.toggle('pk_A_hidden', fontSizeDropdown.value !== 'custom');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_A_text-element')) {
                    let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                    this.selectedElement.style.fontSize = fontSize;
                }
            },
            changeFontStyle: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const fontStyleDropdown = document.getElementById('pk_A_fontStyleDropdown');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_A_text-element')) {
                    this.selectedElement.style.fontFamily = fontStyleDropdown.value;
                }
            },
            changeTextColor: function() {
                if (!this.selectedCard) {
                    alert('Please select a card first by clicking on it.');
                    return;
                }
                const textColorPicker = document.getElementById('pk_A_textColorPicker');
                if (this.selectedElement && this.selectedElement.classList.contains('pk_A_text-element')) {
                    this.selectedElement.style.color = textColorPicker.value;
                }
            },
            cancelText: function() {
                const textInput = document.getElementById('pk_A_textInput');
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
                    if (e.target.classList.contains('pk_A_resize-handle')) return;
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
                    if (element.classList.contains('pk_A_text-element')) {
                        const textInput = document.getElementById('pk_A_textInput');
                        textInput.value = element.textContent;
                        this.updateTextButton();
                    } else {
                        document.getElementById('pk_A_textInput').value = '';
                        this.updateTextButton();
                    }
                }
                this.updateElementControls();
            },
            updateElementControls: function() {
                const bringForwardBtn = document.getElementById('pk_A_bringForwardBtn');
                const sendBackwardBtn = document.getElementById('pk_A_sendBackwardBtn');
                const deleteBtn = document.getElementById('pk_A_deleteSelectedElementsBtn');
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
                    const cardId = this.selectedCard ? this.selectedCard.id : null;
                    if (cardId && this.selectedElement.classList.contains('pk_A_profile-pic')) { 
                        this.profilePics[cardId] = null; 
                    }
                    this.selectedElement.remove();
                    this.selectedElement = null;
                    this.updateElementControls();
                    document.getElementById('pk_A_textInput').value = '';
                    this.updateTextButton();
                }
            },
            resetLayout: function() {
                const cards = document.querySelectorAll('.pk_A_card');
                cards.forEach((card) => {
                    if (card.id === 'pk_A_card5') {
                        card.innerHTML = this.defaultCard5HTML;
                        const textElements = card.querySelectorAll('.pk_A_text-element');
                        textElements.forEach(element => this.makeElementDraggable(element));
                    } else {
                        card.classList.remove('selected');
                        
                        // Reset background to default
                        const cardId = card.id;
                        const defaultBg = this.defaultBackgrounds[cardId];
                        
                        // Remove existing background
                        const bgImg = card.querySelector('.pk_A_card-content .pk_A_card-bg');
                        if (bgImg) {
                            bgImg.remove();
                        }
                        
                        // Set default background if exists
                        if (defaultBg) {
                            let newBgImg = document.createElement('div');
                            newBgImg.classList.add('pk_A_card-bg');
                            newBgImg.style.backgroundImage = `url('${defaultBg}')`;
                            card.querySelector('.pk_A_card-content').appendChild(newBgImg);
                        } else {
                            // For cards without default background, set white background
                            card.querySelector('.pk_A_card-content').style.backgroundColor = 'white';
                        }
                        
                        const placeholder = card.querySelector('.pk_A_card-placeholder-number');
                        if (placeholder) {
                            placeholder.style.display = 'block';
                        }
                        
                        // Remove text elements
                        const textElements = card.querySelectorAll('.pk_A_text-element');
                        textElements.forEach(element => element.remove());
                        
                        // Remove image elements
                        const imageElements = card.querySelectorAll('.pk_A_image-element');
                        imageElements.forEach(element => element.remove());
                        
                        // Remove QR containers
                        const qrContainers = card.querySelectorAll('.pk_A_qr-container');
                        qrContainers.forEach(element => element.remove());
                        
                        // Reset profile picture
                        const profilePic = card.querySelector('.pk_A_profile-pic');
                        if (profilePic) {
                            profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                            profilePic.style.left = '250px';
                            profilePic.style.top = '20px';
                            profilePic.classList.remove('square');
                            this.profilePics[cardId] = profilePic;
                        }
                        
                        // Remove from localStorage
                        this.removeBackground(cardId);
                    }
                });
                this.selectedCard = null;
                document.getElementById('pk_A_textInput').value = '';
                this.updateTextButton();
                document.getElementById('pk_A_profileUpload').value = '';
                document.getElementById('pk_A_imageUploadOverlay').value = '';
                document.getElementById('pk_A_changeImageUpload').value = '';
                this.selectedElement = null;
                this.updateElementControls();
            },
            downloadCards: async function() {
                const downloadBtn = document.getElementById('pk_A_downloadBtn');
                downloadBtn.innerHTML = 'Generating PDF...';
                downloadBtn.disabled = true;
                
                const allCards = document.querySelectorAll('.pk_A_card');
                allCards.forEach(card => {
                    card.style.border = 'none';
                });
                try {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const containers = document.querySelectorAll('.pk_A_grid');
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
                    pdf.save('PackageA.pdf');
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    alert('Failed to generate PDF');
                } finally {
                    allCards.forEach(card => {
                        card.style.border = '';
                        if (card === this.selectedCard) {
                            card.classList.add('selected');
                        }
                    });
                    downloadBtn.innerHTML = 'Print <i class="fas fa-print" style="margin-left: 10px;"></i>';
                    downloadBtn.disabled = false;
                }
            }
        };
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', pkAApp.init.bind(pkAApp));
        } else {
            pkAApp.init();
        }
    })();
    </script>
</body>
</html>