<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Editor - Page 1</title>
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
        .pspt_Customer_wrapper * {
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
        .pspt_Customer_wrapper {
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
        .pspt_Customer_filters-container {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .pspt_Customer_filters-container h2 { text-align: center; margin: 0 0 10px 0; color: #333; }
        .pspt_Customer_filters-container hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }
        .pspt_Customer_category-toggle {
            padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center; font-size: 16px; color: #333; margin-bottom: 15px;
        }
        .pspt_Customer_category-toggle:hover { background-color: #c8d8ff; }
        .pspt_Customer_category-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease; }
        .pspt_Customer_category-content.active { max-height: 500px; padding: 10px; }
        .pspt_Customer_filter-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
        .pspt_Customer_background-dropdown,
        .pspt_Customer_font-size-dropdown,
        .pspt_Customer_font-style-dropdown {
            width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat; background-position: right 10px center; background-size: 18px;
        }
        .pspt_Customer_custom-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pspt_Customer_custom-button:hover { background-color: #138496; }
        .pspt_Customer_custom-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .pspt_Customer_reset-button { background-color: #dc3545; color: white; }
        .pspt_Customer_reset-button:hover { background-color: #c82333; }
        .pspt_Customer_download-btn { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pspt_Customer_download-btn:hover { background-color: #138496; }
        .pspt_Customer_secondary-button { background-color: #6c757d; color: white; }
        .pspt_Customer_secondary-button:hover { background-color: #5a6268; }
        .pspt_Customer_text-button { width: 100%; padding: 10px; margin: 8px 0; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; background-color: #17a2b8; color: white; }
        .pspt_Customer_text-button:hover { background-color: #138496; }
        .pspt_Customer_text-input, .pspt_Customer_color-picker { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        .pspt_Customer_text-input:focus { border-color: #17a2b8; outline: none; box-shadow: 0 0 5px rgba(23, 162, 184, 0.5); }
        .pspt_Customer_hidden { display: none; }
        .pspt_Customer_arrow { transition: transform 0.3s; font-size: 14px; }
        .pspt_Customer_category-toggle.active .pspt_Customer_arrow { transform: rotate(180deg); }
        .pspt_Customer_displayArea { width: 70%; padding: 5px; margin: 0; box-sizing: border-box; border: 1px solid gray; border-radius: 10px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .pspt_Customer_grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: var(--row-gap) var(--column-gap); padding: var(--grid-padding); background-color: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); width: 100%; max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding)); height: calc(2 * var(--card-height) + var(--row-gap) + 2 * var(--grid-padding)); }
        .pspt_Customer_card { width: 100%; aspect-ratio: 380 / 560; margin: var(--card-margin); padding: var(--card-padding); border: 2px solid #1C2526; background-color: #f5f5f5; position: relative; overflow: hidden; font-family: 'Battang', sans-serif; }
        .pspt_Customer_card-content { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .pspt_Customer_card-content .pspt_Customer_card-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
        .pspt_Customer_profile-pic { position: absolute; width: 120px; height: 120px; overflow: hidden; cursor: move; pointer-events: auto; left: 250px; top: 20px; transition: left 0.1s, top 0.1s; will-change: transform; z-index: 5; }
        .pspt_Customer_profile-pic.dragging { transition: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); transform: scale(1.05); opacity: 0.9; }
        .pspt_Customer_profile-pic img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pspt_Customer_profile-pic.square img { border-radius: 0; }
        .pspt_Customer_profile-pic:not(.square) img { border-radius: 50%; }
        .pspt_Customer_text-element, .pspt_Customer_image-element { position: absolute; cursor: move; pointer-events: auto; user-select: none; font-family: 'Battambang', sans-serif; transition: left 0.1s, top 0.1s; will-change: transform; }
        .pspt_Customer_text-element.dragging, .pspt_Customer_image-element.dragging { transition: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transform: scale(1.05); opacity: 0.9; }
        .pspt_Customer_text-element.selected, .pspt_Customer_image-element.selected, .pspt_Customer_profile-pic.selected { border: 1px solid cyan; }
        .pspt_Customer_image-element { width: 100px; height: 100px; }
        .pspt_Customer_image-element img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pspt_Customer_image-element.square img { border-radius: 0; }
        .pspt_Customer_image-element:not(.square) img { border-radius: 50%; }
        .pspt_Customer_resize-handle { position: absolute; width: 10px; height: 10px; background: cyan; cursor: pointer; pointer-events: auto; }
        .pspt_Customer_resize-handle.top-left { top: -5px; left: -5px; cursor: nw-resize; }
        .pspt_Customer_resize-handle.top-right { top: -5px; right: -5px; cursor: ne-resize; }
        .pspt_Customer_resize-handle.bottom-left { bottom: -5px; left: -5px; cursor: sw-resize; }
        .pspt_Customer_resize-handle.bottom-right { bottom: -5px; right: -5px; cursor: se-resize; }
        
        /* Rotate card 4 by 180 degrees */
        #card4, #card3 { 
            transform: rotate(180deg); 
            transform-origin: center center; 
        }
    </style>
</head>
<body>
    <div class="pspt_Customer_wrapper">
        <div class="pspt_Customer_filters-container">
            <h2>ការកែតម្រូវ</h2>
            <hr>
            <!-- Background Controls -->
            <div class="pspt_Customer_category-toggle" onclick="pspt_Customer_toggleCategory('background-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងផ្ទៃខាងក្រោយ</span>
                <span class="pspt_Customer_arrow">▼</span>
            </div>
            <div class="pspt_Customer_category-content" id="background-controls">
                <div class="pspt_Customer_filter-group">
                    <select class="pspt_Customer_background-dropdown" id="pspt_Customer_cardSelector" onchange="pspt_Customer_updateCurrentCard()">
                        <option value="card1">កាត ១</option>
                        <option value="card2">កាត ២</option>
                        <option value="card3">កាត ៣</option>
                        <option value="card4">កាត ៤</option>
                    </select>
                    <select class="pspt_Customer_background-dropdown" id="pspt_Customer_backgroundDropdown" onchange="pspt_Customer_changeBackgroundFromDropdown()">
                        <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                        <option value="/docs/images/5.png">ផ្ទៃខាងក្រោយ ១</option>
                        <option value="/docs/images/6.png">ផ្ទៃខាងក្រោយ ២</option>
                        <option value="/docs/images/7.png">ផ្ទៃខាងក្រោយ ៣</option>
                        <option value="/docs/images/8.png">ផ្ទៃខាងក្រោយ ៤</option>
                    </select>
                    <select class="pspt_Customer_background-dropdown" id="pspt_Customer_rotationDropdown" onchange="pspt_Customer_rotateBackground()">
                        <option value="0">0 deg</option>
                        <option value="90">90 deg</option>
                        <option value="180">180 deg</option>
                        <option value="270">270 deg</option>
                    </select>
                    <button class="pspt_Customer_custom-button" id="pspt_Customer_changeBackgroundBtn" onclick="document.getElementById('pspt_Customer_imageUpload').click();">ផ្ទុកផ្ទៃខាងក្រោយ</button>
                    <input type="file" id="pspt_Customer_imageUpload" accept="image/*" onchange="pspt_Customer_changeBackground()" style="display: none;">
                    <button class="pspt_Customer_custom-button pspt_Customer_reset-button" id="pspt_Customer_resetBackgroundBtn" onclick="pspt_Customer_resetBackground()">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                </div>
            </div>
            <!-- Profile Picture Controls -->
            <div class="pspt_Customer_category-toggle" onclick="pspt_Customer_toggleCategory('profile-controls')">
                <i class="fas fa-user"></i>
                <span>ការគ្រប់គ្រងរូបភាពប្រវត្តិរូប</span>
                <span class="pspt_Customer_arrow">▼</span>
            </div>
            <div class="pspt_Customer_category-content" id="profile-controls">
                <div class="pspt_Customer_filter-group">
                    <button class="pspt_Customer_custom-button" id="pspt_Customer_changeProfileBtn" onclick="document.getElementById('pspt_Customer_profileUpload').click();">ផ្លាស់ប្តូររូបភាពប្រវត្តិរូប</button>
                    <input type="file" id="pspt_Customer_profileUpload" accept="image/*" onchange="pspt_Customer_changeProfilePic()" style="display: none;">
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_squareShapeBtn" onclick="pspt_Customer_toggleProfileShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_circleShapeBtn" onclick="pspt_Customer_toggleProfileShape(false)">រាងមូល</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_reset-button" id="pspt_Customer_resetProfileBtn" onclick="pspt_Customer_resetProfile()">កំណត់រូបភាពឡើងវិញ</button>
                </div>
            </div>
            <!-- Text Input Controls -->
            <div class="pspt_Customer_category-toggle" onclick="pspt_Customer_toggleCategory('text-controls')">
                <i class="fas fa-edit"></i>
                <span>ការគ្រប់គ្រងអត្ថបទ</span>
                <span class="pspt_Customer_arrow">▼</span>
            </div>
            <div class="pspt_Customer_category-content" id="text-controls">
                <div class="pspt_Customer_filter-group">
                    <input type="text" id="pspt_Customer_textInput" placeholder="បញ្ចូលអត្ថបទនៅទីនេះ" class="pspt_Customer_text-input" oninput="pspt_Customer_updateTextButton()">
                    <input type="color" id="pspt_Customer_textColorPicker" value="#000000" class="pspt_Customer_color-picker" title="ជ្រើសរើសពណ៌អត្ថបទ" onchange="pspt_Customer_changeTextColor()">
                    <select class="pspt_Customer_font-size-dropdown" id="pspt_Customer_fontSizeDropdown" onchange="pspt_Customer_changeFontSize()">
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
                    <input type="number" id="pspt_Customer_customFontSize" class="pspt_Customer_hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                    <select class="pspt_Customer_font-style-dropdown" id="pspt_Customer_fontStyleDropdown" onchange="pspt_Customer_changeFontStyle()">
                        <option value="Arial">អេរីយ៉ាល</option>
                        <option value="Times New Roman">ថាមស៍ នូវ រ៉ូម៉ាន</option>
                        <option value="Courier New">គូរីយ៉េ នូវ</option>
                        <option value="Georgia">ជីអរជីយ៉ា</option>
                        <option value="Verdana">វ៉ើរដានា</option>
                        <option value="Helvetica">ហែលវេទីកា</option>
                        <option value="Battambang">បាត់ដំបង</option>
                    </select>
                    <button class="pspt_Customer_text-button" id="pspt_Customer_textButton" onclick="pspt_Customer_handleTextAction()">បន្ថែមអត្ថបទ</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_reset-button" id="pspt_Customer_cancelTextBtn" onclick="pspt_Customer_cancelText()">បោះបង់</button>
                </div>
            </div>
            <!-- Image Overlay Controls -->
            <div class="pspt_Customer_category-toggle" onclick="pspt_Customer_toggleCategory('image-overlay-controls')">
                <i class="fas fa-image"></i>
                <span>ការគ្រប់គ្រងរូបភាពបន្ថែម</span>
                <span class="pspt_Customer_arrow">▼</span>
            </div>
            <div class="pspt_Customer_category-content" id="image-overlay-controls">
                <div class="pspt_Customer_filter-group">
                    <button class="pspt_Customer_custom-button" id="pspt_Customer_addImageBtn" onclick="document.getElementById('pspt_Customer_imageUploadOverlay').click();">បន្ថែមរូបភាព</button>
                    <input type="file" id="pspt_Customer_imageUploadOverlay" accept="image/*" onchange="pspt_Customer_addOverlayImage()" style="display: none;">
                    <button class="pspt_Customer_custom-button" id="pspt_Customer_changeImageBtn" onclick="document.getElementById('pspt_Customer_changeImageUpload').click();">ផ្លាស់ប្តូររូបភាព</button>
                    <input type="file" id="pspt_Customer_changeImageUpload" accept="image/*" onchange="pspt_Customer_changeOverlayImage()" style="display: none;">
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_imageSquareShapeBtn" onclick="pspt_Customer_toggleImageShape(true)">រាងការ៉េ</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_imageCircleShapeBtn" onclick="pspt_Customer_toggleImageShape(false)">រាងមូល</button>
                </div>
            </div>
            <!-- Element Controls -->
            <div class="pspt_Customer_category-toggle" onclick="pspt_Customer_toggleCategory('element-controls')">
                <i class="fas fa-trash-alt"></i>
                <span>ការគ្រប់គ្រងធាតុ</span>
                <span class="pspt_Customer_arrow">▼</span>
            </div>
            <div class="pspt_Customer_category-content" id="element-controls">
                <div class="pspt_Customer_filter-group">
                    <button class="pspt_Customer_custom-button pspt_Customer_reset-button" id="pspt_Customer_deleteSelectedElementsBtn" onclick="pspt_Customer_deleteSelectedElements()">លុបធាតុជ្រើសរើស</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_bringForwardBtn" onclick="pspt_Customer_bringForward()" disabled>នាំមកមុខ</button>
                    <button class="pspt_Customer_custom-button pspt_Customer_secondary-button" id="pspt_Customer_sendBackwardBtn" onclick="pspt_Customer_sendBackward()" disabled>បញ្ជូនទៅក្រោយ</button>
                </div>
            </div>
            <!-- Reset and Download -->
            <button class="pspt_Customer_custom-button pspt_Customer_reset-button" id="pspt_Customer_resetAllBtn" onclick="pspt_Customer_resetLayout()">កំណត់ឡើងវិញទាំងអស់</button>
            <button class="pspt_Customer_download-btn" id="pspt_Customer_downloadBtn" onclick="pspt_Customer_downloadCards()">បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i></button>
        </div>
        <div class="pspt_Customer_displayArea">
            <div id="pspt_Customer_cardsContainer" class="pspt_Customer_grid">
                <!-- Card 1 -->
                <div class="pspt_Customer_card" id="card1">
                    <div class="pspt_Customer_card-content">
                        <img class="pspt_Customer_card-bg" src="/docs/images/franchise_package/Shop_b.jpg" alt="Card background 1">
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="pspt_Customer_card" id="card2">
                    <div class="pspt_Customer_card-content">
                        <img class="pspt_Customer_card-bg" src="/docs/images/franchise_package/yeaksa.png" alt="Card background 2">
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="pspt_Customer_card" id="card3">
                    <div class="pspt_Customer_card-content">
                        <img class="pspt_Customer_card-bg" src="/docs/images/franchise_package/laundry_flow.jpg" alt="Card background 3">
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="pspt_Customer_card" id="card4">
                    <div class="pspt_Customer_card-content">
                        <img class="pspt_Customer_card-bg" src="/docs/images/1.png" alt="Card background 4">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let selectedElement = null;
        let currentCard = document.getElementById('card1');
        let zIndexCounter = 10;
        let profilePics = { 'card1': null, 'card2': null, 'card3': null, 'card4': null };
        let textCounter = 0;

        function pspt_Customer_toggleCategory(id) {
            document.querySelectorAll('.pspt_Customer_category-content').forEach(content => {
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
        function pspt_Customer_updateCurrentCard() {
            const cardSelector = document.getElementById('pspt_Customer_cardSelector');
            currentCard = document.getElementById(cardSelector.value);
        }
        function pspt_Customer_changeBackgroundFromDropdown() {
            pspt_Customer_updateCurrentCard();
            const dropdown = document.getElementById('pspt_Customer_backgroundDropdown');
            const bgImg = currentCard.querySelector('.pspt_Customer_card-content .pspt_Customer_card-bg');
            if (dropdown.value && bgImg) {
                bgImg.src = dropdown.value;
            }
        }

        function pspt_Customer_rotateBackground() {
            pspt_Customer_updateCurrentCard();
            const dropdown = document.getElementById('pspt_Customer_rotationDropdown');
            const rotation = dropdown.value;
            const bgImg = currentCard.querySelector('.pspt_Customer_card-content .pspt_Customer_card-bg');
            if (bgImg) {
                let transformValue = `rotate(${rotation}deg)`;
                if (rotation === '90' || rotation === '270') {
                    const card = currentCard;
                    const cardAspectRatio = card.offsetHeight / card.offsetWidth;
                    if (cardAspectRatio > 1) {
                        transformValue += ` scale(${cardAspectRatio})`;
                    } else if (cardAspectRatio < 1) {
                        transformValue += ` scale(${1/cardAspectRatio})`;
                    }
                }
                bgImg.style.transform = transformValue;
            }
        }

        function pspt_Customer_changeBackground() {
            pspt_Customer_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Customer_imageUpload');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const bgImg = currentCard.querySelector('.pspt_Customer_card-content .pspt_Customer_card-bg');
                    if (bgImg) { bgImg.src = e.target.result; }
                };
                reader.onerror = function() { alert('មិនអាចផ្ទុករូបភាពបានទេ។ សូមព្យាយាមម្តងទៀត។'); };
                reader.readAsDataURL(file);
            }
        }
        function pspt_Customer_resetBackground() {
            pspt_Customer_updateCurrentCard();
            const bgImg = currentCard.querySelector('.pspt_Customer_card-content .pspt_Customer_card-bg');
            const defaultSrc = bgImg ? (bgImg.dataset.defaultSrc || bgImg.src) : null;
            if (bgImg && defaultSrc) {
                bgImg.src = defaultSrc;
                bgImg.style.transform = '';
            }
            document.getElementById('pspt_Customer_backgroundDropdown').value = '';
            document.getElementById('pspt_Customer_imageUpload').value = '';
            document.getElementById('pspt_Customer_rotationDropdown').value = '0';
        }
        function pspt_Customer_changeProfilePic() {
            pspt_Customer_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Customer_profileUpload');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const cardId = currentCard.id;
                    let profilePic = currentCard.querySelector('.pspt_Customer_profile-pic');
                    if (!profilePic) {
                        profilePic = document.createElement('div');
                        profilePic.classList.add('pspt_Customer_profile-pic');
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
                reader.onerror = function() { alert('មិនអាចផ្ទុករូបភាពប្រវត្តិរូបបានទេ។ សូមព្យាយាមម្តងទៀត។'); };
                reader.readAsDataURL(file);
            }
        }
        function pspt_Customer_resetProfile() {
            pspt_Customer_updateCurrentCard();
            const cardId = currentCard.id;
            const profilePic = currentCard.querySelector('.pspt_Customer_profile-pic');
            if (profilePic) {
                profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                profilePic.style.left = '250px';
                profilePic.style.top = '20px';
                profilePic.classList.remove('square');
                profilePics[cardId] = profilePic;
                document.getElementById('pspt_Customer_profileUpload').value = '';
                selectElement(profilePic);
            }
        }
        function pspt_Customer_toggleProfileShape(isSquare) {
            pspt_Customer_updateCurrentCard();
            const cardId = currentCard.id;
            const profilePic = profilePics[cardId] || currentCard.querySelector('.pspt_Customer_profile-pic');
            if (profilePic) {
                if (isSquare) { profilePic.classList.add('square'); } else { profilePic.classList.remove('square'); }
            }
        }
        function pspt_Customer_addOverlayImage() {
            pspt_Customer_updateCurrentCard();
            const fileInput = document.getElementById('pspt_Customer_imageUploadOverlay');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageElement = document.createElement('div');
                    imageElement.classList.add('pspt_Customer_image-element');
                    imageElement.id = `pspt_Customer_image-element-${textCounter++}`;
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
                reader.onerror = function() { alert('មិនអាចបន្ថែមរូបភាពបានទេ១ សូមព្យាយាមម្តងទៀត១'); };
                reader.readAsDataURL(file);
            }
        }
        function pspt_Customer_changeOverlayImage() {
            const fileInput = document.getElementById('pspt_Customer_changeImageUpload');
            const file = fileInput.files[0];
            if (file && selectedElement && selectedElement.classList.contains('pspt_Customer_image-element')) {
                const reader = new FileReader();
                reader.onload = function(e) { selectedElement.querySelector('img').src = e.target.result; };
                reader.onerror = function() { alert('មិនអាចផ្លាស់ប្តូររូបភាពបានទេ១ សូមព្យាយាមម្តងទៀត១'); };
                reader.readAsDataURL(file);
            }
        }
        function pspt_Customer_toggleImageShape(isSquare) {
            if (selectedElement && selectedElement.classList.contains('pspt_Customer_image-element')) {
                if (isSquare) { selectedElement.classList.add('square'); } else { selectedElement.classList.remove('square'); }
            }
        }
        function pspt_Customer_updateTextButton() {
            const textInput = document.getElementById('pspt_Customer_textInput');
            const textButton = document.getElementById('pspt_Customer_textButton');
            textButton.textContent = textInput.value && selectedElement && selectedElement.classList.contains('pspt_Customer_text-element') ? 'ធ្វើបច្ចុប្បន្នភាពអត្ថបទ' : 'បន្ថែមអត្ថបទ';
        }
        function pspt_Customer_handleTextAction() {
            const textInput = document.getElementById('pspt_Customer_textInput');
            const fontSizeDropdown = document.getElementById('pspt_Customer_fontSizeDropdown');
            const fontStyleDropdown = document.getElementById('pspt_Customer_fontStyleDropdown');
            const textColorPicker = document.getElementById('pspt_Customer_textColorPicker');
            const customFontSize = document.getElementById('pspt_Customer_customFontSize');
            pspt_Customer_updateCurrentCard();
            if (textInput.value) {
                let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                let textElement;
                if (selectedElement && selectedElement.classList.contains('pspt_Customer_text-element')) {
                    textElement = selectedElement;
                } else {
                    textElement = document.createElement('p');
                    textElement.classList.add('pspt_Customer_text-element');
                    textElement.id = `pspt_Customer_text-element-${textCounter++}`;
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
                pspt_Customer_updateTextButton();
                selectElement(textElement);
            }
        }
        function pspt_Customer_changeFontSize() {
            const fontSizeDropdown = document.getElementById('pspt_Customer_fontSizeDropdown');
            const customFontSize = document.getElementById('pspt_Customer_customFontSize');
            customFontSize.classList.toggle('pspt_Customer_hidden', fontSizeDropdown.value !== 'custom');
            if (selectedElement && selectedElement.classList.contains('pspt_Customer_text-element')) {
                let fontSize = fontSizeDropdown.value === 'custom' ? (customFontSize.value || 12) + 'px' : fontSizeDropdown.value + 'px';
                selectedElement.style.fontSize = fontSize;
            }
        }
        function pspt_Customer_changeFontStyle() {
            const fontStyleDropdown = document.getElementById('pspt_Customer_fontStyleDropdown');
            if (selectedElement && selectedElement.classList.contains('pspt_Customer_text-element')) {
                selectedElement.style.fontFamily = fontStyleDropdown.value;
            }
        }
        function pspt_Customer_changeTextColor() {
            const textColorPicker = document.getElementById('pspt_Customer_textColorPicker');
            if (selectedElement && selectedElement.classList.contains('pspt_Customer_text-element')) {
                selectedElement.style.color = textColorPicker.value;
            }
        }
        function pspt_Customer_cancelText() {
            const textInput = document.getElementById('pspt_Customer_textInput');
            textInput.value = '';
            pspt_Customer_updateTextButton();
            if (selectedElement) {
                selectedElement.classList.remove('selected');
                selectedElement = null;
                updateElementControls();
            }
        }
        function makeElementDraggable(element) {
            let isDragging = false;
            let currentX = parseFloat(element.style.left) || 0;
            let currentY = parseFloat(element.style.top) || 0;
            let initialX, initialY;
            element.addEventListener('mousedown', startDragging);
            function startDragging(e) {
                if (e.target.classList.contains('pspt_Customer_resize-handle')) return;
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
            if (selectedElement) { selectedElement.classList.remove('selected'); }
            selectedElement = element;
            if (element) {
                element.classList.add('selected');
                if (element.classList.contains('pspt_Customer_text-element')) {
                    const textInput = document.getElementById('pspt_Customer_textInput');
                    textInput.value = element.textContent;
                    pspt_Customer_updateTextButton();
                } else {
                    document.getElementById('pspt_Customer_textInput').value = '';
                    pspt_Customer_updateTextButton();
                }
            }
            updateElementControls();
        }
        function updateElementControls() {
            const bringForwardBtn = document.getElementById('pspt_Customer_bringForwardBtn');
            const sendBackwardBtn = document.getElementById('pspt_Customer_sendBackwardBtn');
            const deleteBtn = document.getElementById('pspt_Customer_deleteSelectedElementsBtn');
            bringForwardBtn.disabled = !selectedElement;
            sendBackwardBtn.disabled = !selectedElement;
            deleteBtn.disabled = !selectedElement;
        }
        function pspt_Customer_bringForward() {
            if (selectedElement) { selectedElement.style.zIndex = zIndexCounter++; }
        }
        function pspt_Customer_sendBackward() {
            if (selectedElement && parseInt(selectedElement.style.zIndex) > 10) {
                selectedElement.style.zIndex = parseInt(selectedElement.style.zIndex) - 1;
            }
        }
        function pspt_Customer_deleteSelectedElements() {
            if (selectedElement) {
                const cardId = currentCard.id;
                if (selectedElement.classList.contains('pspt_Customer_profile-pic')) { profilePics[cardId] = null; }
                selectedElement.remove();
                selectedElement = null;
                updateElementControls();
                document.getElementById('pspt_Customer_textInput').value = '';
                pspt_Customer_updateTextButton();
            }
        }
        function pspt_Customer_resetLayout() {
            const cards = document.querySelectorAll('.pspt_Customer_card');
            cards.forEach((card) => {
                const bgImg = card.querySelector('.pspt_Customer_card-content .pspt_Customer_card-bg');
                const defaultSrc = bgImg ? (bgImg.dataset.defaultSrc || bgImg.src) : null;
                if (bgImg && defaultSrc) { bgImg.src = defaultSrc; }
                const textElements = card.querySelectorAll('.pspt_Customer_text-element');
                textElements.forEach(element => element.remove());
                const imageElements = card.querySelectorAll('.pspt_Customer_image-element');
                imageElements.forEach(element => element.remove());
                const qrContainers = card.querySelectorAll('.pspt_Customer_qr-container');
                qrContainers.forEach(element => element.remove());
                const cardId = card.id;
                const profilePic = card.querySelector('.pspt_Customer_profile-pic');
                if (profilePic) {
                    profilePic.querySelector('img').src = '{{ $img_src ?? 'https://via.placeholder.com/120' }}';
                    profilePic.style.left = '250px';
                    profilePic.style.top = '20px';
                    profilePic.classList.remove('square');
                    profilePics[cardId] = profilePic;
                }
            });
            document.getElementById('pspt_Customer_backgroundDropdown').value = '';
            document.getElementById('pspt_Customer_imageUpload').value = '';
            document.getElementById('pspt_Customer_textInput').value = '';
            pspt_Customer_updateTextButton();
            document.getElementById('pspt_Customer_profileUpload').value = '';
            document.getElementById('pspt_Customer_imageUploadOverlay').value = '';
            document.getElementById('pspt_Customer_changeImageUpload').value = '';
            selectedElement = null;
            updateElementControls();
        }
        async function pspt_Customer_downloadCards() {
            const downloadBtn = document.getElementById('pspt_Customer_downloadBtn');
            const cardsContainer = document.getElementById('pspt_Customer_cardsContainer');
            downloadBtn.innerHTML = 'កំពុងបង្កើត PDF...';
            downloadBtn.disabled = true;

            const allCards = document.querySelectorAll('.pspt_Customer_card');
            allCards.forEach(card => {
                card.style.border = 'none';
            });

            try {
                const gridCanvas = await html2canvas(cardsContainer, { scale: 3, backgroundColor: '#ffffff', useCORS: true });
                const imgData = gridCanvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
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
                pdf.save('id_cards_page1.pdf');
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('បរាជ័យក្នុងការបង្កើត PDF');
            } finally {
                allCards.forEach(card => {
                    card.style.border = '';
                });
                downloadBtn.innerHTML = 'បោះពុម្ព (Print)<i class="fas fa-print" style="margin-left: 10px;"></i>';
                downloadBtn.disabled = false;
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            // Cache each card background <img> initial src as default
            document.querySelectorAll('.pspt_Customer_card .pspt_Customer_card-bg').forEach((imgEl) => {
                if (!imgEl.dataset.defaultSrc) {
                    imgEl.dataset.defaultSrc = imgEl.getAttribute('src') || '';
                }
            });
            const textElements = document.querySelectorAll('.pspt_Customer_text-element');
            textElements.forEach(element => makeElementDraggable(element));
            const profilePicsElements = document.querySelectorAll('.pspt_Customer_profile-pic');
            profilePicsElements.forEach(pic => { makeElementDraggable(pic); const cardId = pic.closest('.pspt_Customer_card').id; profilePics[cardId] = pic; });
            document.getElementById('pspt_Customer_cardSelector').addEventListener('change', pspt_Customer_updateCurrentCard);
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.pspt_Customer_text-element') && !e.target.closest('.pspt_Customer_image-element') && !e.target.closest('.pspt_Customer_profile-pic') && !e.target.closest('.pspt_Customer_filters-container')) {
                    selectElement(null);
                }
            });
            document.addEventListener('keydown', (e) => {
                if (selectedElement && ['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'].includes(e.key)) {
                    e.preventDefault();
                    let currentX = parseFloat(selectedElement.style.left) || 0;
                    let currentY = parseFloat(selectedElement.style.top) || 0;
                    const moveBy = 1;
                    switch (e.key) {
                        case 'ArrowLeft': currentX -= moveBy; break;
                        case 'ArrowRight': currentX += moveBy; break;
                        case 'ArrowUp': currentY -= moveBy; break;
                        case 'ArrowDown': currentY += moveBy; break;
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
