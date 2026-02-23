<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Card Yeaksa Employee Editor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&family=Arial&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/easyqrcodejs@4.5.0/dist/easy.qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .welcome_card-yeaksa-employee-editor {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }
        .welcome_card-yeaksa-employee-editor-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            gap: 5px;
        }
        .welcome_card-yeaksa-employee-editor-left-side {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .welcome_card-yeaksa-employee-editor-right-side {
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
            min-width: 794px;
        }
        .welcome_card-yeaksa-employee-editor-display-area {
            position: relative;
            width: 794px;
            height: 1123px;
            overflow: hidden;
            background-color: transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 0;
            padding: 0;
        }
        .welcome_card-yeaksa-employee-editor-letter-page {
            position: absolute;
            top: 0;
            left: 0;
            width: 794px;
            height: 1123px;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        .welcome_card-yeaksa-employee-editor #welcome_card-yeaksa-employee-editor-textContainer, 
        .welcome_card-yeaksa-employee-editor #welcome_card-yeaksa-employee-editor-imageContainer, 
        .welcome_card-yeaksa-employee-editor #welcome_card-yeaksa-employee-editor-qrContainer {
            position: absolute;
            top: 0;
            left: 0;
            width: 794px;
            height: 1123px;
            pointer-events: none;
        }
        .welcome_card-yeaksa-employee-editor-text-element,
        .welcome_card-yeaksa-employee-editor-image-element,
        .welcome_card-yeaksa-employee-editor-qr-element {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-size: 18px;
            color: #000000;
            font-family: 'Times New Roman', Moul, serif;
        }
        .welcome_card-yeaksa-employee-editor-text-element.selected,
        .welcome_card-yeaksa-employee-editor-image-element.selected,
        .welcome_card-yeaksa-employee-editor-qr-element.selected {
            border: 1px solid cyan;
        }
        .welcome_card-yeaksa-employee-editor-image-element {
            width: 100px;
            height: 100px;
        }
        .welcome_card-yeaksa-employee-editor-image-element img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .welcome_card-yeaksa-employee-editor-image-element.square img {
            border-radius: 0;
        }
        .welcome_card-yeaksa-employee-editor-image-element:not(.square) img {
            border-radius: 50%;
        }
        .welcome_card-yeaksa-employee-editor-qr-element {
            width: 128px;
            height: 128px;
        }
        .welcome_card-yeaksa-employee-editor-qr-element canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .welcome_card-yeaksa-employee-editor-resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
        }
        .welcome_card-yeaksa-employee-editor-resize-handle.top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }
        .welcome_card-yeaksa-employee-editor-resize-handle.top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }
        .welcome_card-yeaksa-employee-editor-resize-handle.bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }
        .welcome_card-yeaksa-employee-editor-resize-handle.bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }
        .welcome_card-yeaksa-employee-editor-element-hidden {
            display: none;
            opacity: 0;
        }
        .welcome_card-yeaksa-employee-editor-left-side h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }
        .welcome_card-yeaksa-employee-editor-left-side hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-container,
        .welcome_card-yeaksa-employee-editor-background-input-container,
        .welcome_card-yeaksa-employee-editor-insert-text-container,
        .welcome_card-yeaksa-employee-editor-add-image-container,
        .welcome_card-yeaksa-employee-editor-element-control-container,
        .welcome_card-yeaksa-employee-editor-toggle-elements-container {
            margin-bottom: 15px;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-header,
        .welcome_card-yeaksa-employee-editor-background-input-header,
        .welcome_card-yeaksa-employee-editor-insert-text-header,
        .welcome_card-yeaksa-employee-editor-add-image-header,
        .welcome_card-yeaksa-employee-editor-element-control-header,
        .welcome_card-yeaksa-employee-editor-toggle-elements-header {
            padding: 10px;
            background-color: #d4e6ff;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            color: #333;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-header:hover,
        .welcome_card-yeaksa-employee-editor-background-input-header:hover,
        .welcome_card-yeaksa-employee-editor-insert-text-header:hover,
        .welcome_card-yeaksa-employee-editor-add-image-header:hover,
        .welcome_card-yeaksa-employee-editor-element-control-header:hover,
        .welcome_card-yeaksa-employee-editor-toggle-elements-header:hover {
            background-color: #c8d8ff;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-content,
        .welcome_card-yeaksa-employee-editor-background-input-content,
        .welcome_card-yeaksa-employee-editor-insert-text-content,
        .welcome_card-yeaksa-employee-editor-add-image-content,
        .welcome_card-yeaksa-employee-editor-element-control-content,
        .welcome_card-yeaksa-employee-editor-toggle-elements-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-container.active .welcome_card-yeaksa-employee-editor-change-profile-content,
        .welcome_card-yeaksa-employee-editor-background-input-container.active .welcome_card-yeaksa-employee-editor-background-input-content,
        .welcome_card-yeaksa-employee-editor-insert-text-container.active .welcome_card-yeaksa-employee-editor-insert-text-content,
        .welcome_card-yeaksa-employee-editor-add-image-container.active .welcome_card-yeaksa-employee-editor-add-image-content,
        .welcome_card-yeaksa-employee-editor-element-control-container.active .welcome_card-yeaksa-employee-editor-element-control-content,
        .welcome_card-yeaksa-employee-editor-toggle-elements-container.active .welcome_card-yeaksa-employee-editor-toggle-elements-content {
            max-height: 500px;
            padding: 10px;
        }
        .welcome_card-yeaksa-employee-editor-change-profile-container.active .welcome_card-yeaksa-employee-editor-arrow,
        .welcome_card-yeaksa-employee-editor-background-input-container.active .welcome_card-yeaksa-employee-editor-arrow,
        .welcome_card-yeaksa-employee-editor-insert-text-container.active .welcome_card-yeaksa-employee-editor-arrow,
        .welcome_card-yeaksa-employee-editor-add-image-container.active .welcome_card-yeaksa-employee-editor-arrow,
        .welcome_card-yeaksa-employee-editor-element-control-container.active .welcome_card-yeaksa-employee-editor-arrow,
        .welcome_card-yeaksa-employee-editor-toggle-elements-container.active .welcome_card-yeaksa-employee-editor-arrow {
            transform: rotate(180deg);
        }
        .welcome_card-yeaksa-employee-editor-arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }
        .welcome_card-yeaksa-employee-editor select, 
        .welcome_card-yeaksa-employee-editor input[type="checkbox"], 
        .welcome_card-yeaksa-employee-editor input[type="text"], 
        .welcome_card-yeaksa-employee-editor input[type="color"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .welcome_card-yeaksa-employee-editor input[type="text"]:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }
        .welcome_card-yeaksa-employee-editor input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        .welcome_card-yeaksa-employee-editor input[type="file"] {
            display: none;
        }
        .welcome_card-yeaksa-employee-editor-btn {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .welcome_card-yeaksa-employee-editor-btn-primary {
            background-color: #17a2b8;
            color: white;
        }
        .welcome_card-yeaksa-employee-editor-btn-primary:hover {
            background-color: #138496;
        }
        .welcome_card-yeaksa-employee-editor-btn-reset {
            background-color: #dc3545;
            color: white;
        }
        .welcome_card-yeaksa-employee-editor-btn-reset:hover {
            background-color: #c82333;
        }
        .welcome_card-yeaksa-employee-editor-btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .welcome_card-yeaksa-employee-editor-btn-secondary:hover {
            background-color: #5a6268;
        }
        .welcome_card-yeaksa-employee-editor-hidden {
            display: none;
        }
        @media print {
            .welcome_card-yeaksa-employee-editor {
                display: none;
            }
        }
        @media (max-width: 1200px) {
            .welcome_card-yeaksa-employee-editor-right-side {
                min-width: auto;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="welcome_card-yeaksa-employee-editor">
        <div class="welcome_card-yeaksa-employee-editor-container">
            <div class="welcome_card-yeaksa-employee-editor-left-side">
                <h2>កែសម្រួល</h2>
                <hr>
                <!-- Profile Image Controls -->
                <div class="welcome_card-yeaksa-employee-editor-change-profile-container">
                    <div class="welcome_card-yeaksa-employee-editor-change-profile-header">
                        <i class="fas fa-user"></i>
                        <span>រូបភាពប្រវត្តិរូប</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-change-profile-content">
                        <button id="welcome_card-yeaksa-employee-editor-changeProfileBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">ផ្លាស់ប្តូររូបភាព</button>
                        <input type="file" id="welcome_card-yeaksa-employee-editor-profileFile" accept="image/*">
                        <button id="welcome_card-yeaksa-employee-editor-squareShapeBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary">រាងការ៉េ</button>
                        <button id="welcome_card-yeaksa-employee-editor-circleShapeBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary">រាងមូល</button>
                        <button id="welcome_card-yeaksa-employee-editor-resetProfileBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset">កំណត់រូបភាពឡើងវិញ</button>
                    </div>
                </div>
                <!-- Background Controls -->
                <div class="welcome_card-yeaksa-employee-editor-background-input-container">
                    <div class="welcome_card-yeaksa-employee-editor-background-input-header">
                        <i class="fas fa-image"></i>
                        <span>រូបភាពផ្ទៃខាងក្រោយ</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-background-input-content">
                        <select id="welcome_card-yeaksa-employee-editor-backgroundSelect">
                            <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                            <option value="https://via.placeholder.com/794x1123?text=YeaksaEmployeeCertificate">លិខិតបញ្ជាក់និយោជិតយក្សា</option>
                            <option value="https://via.placeholder.com/794x1123?text=TrainingCertificate">វិញ្ញាបនបត្របញ្ជាក់ការសិក្សា</option>
                        </select>
                        <button id="welcome_card-yeaksa-employee-editor-changeBackgroundBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">ផ្លាស់ប្តូរផ្ទៃខាងក្រោយ</button>
                        <input type="file" id="welcome_card-yeaksa-employee-editor-backgroundFile" accept="image/*">
                        <button id="welcome_card-yeaksa-employee-editor-resetBackgroundBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                    </div>
                </div>
                <!-- Text Input Controls -->
                <div class="welcome_card-yeaksa-employee-editor-insert-text-container">
                    <div class="welcome_card-yeaksa-employee-editor-insert-text-header">
                        <i class="fas fa-font"></i>
                        <span>បញ្ចូលអត្ថបទ</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-insert-text-content">
                        <input type="text" id="welcome_card-yeaksa-employee-editor-textInput" placeholder="បញ្ចូលអត្ថបទ">
                        <input type="color" id="welcome_card-yeaksa-employee-editor-textColor" value="#000000">
                        <select id="welcome_card-yeaksa-employee-editor-fontSizeSelect">
                            <option value="12">12px</option>
                            <option value="16" selected>16px</option>
                            <option value="24">24px</option>
                            <option value="32">32px</option>
                            <option value="36">36px</option>
                            <option value="custom">ផ្ទាល់ខ្លួន</option>
                        </select>
                        <input type="number" id="welcome_card-yeaksa-employee-editor-customFontSize" class="welcome_card-yeaksa-employee-editor-hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                        <select id="welcome_card-yeaksa-employee-editor-fontStyleSelect">
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Moul', serif">Moul</option>
                            <option value="'Arial', sans-serif">Arial</option>
                            <option value="'Courier New', monospace">Courier New</option>
                        </select>
                        <button id="welcome_card-yeaksa-employee-editor-textBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">បញ្ចូលអត្ថបទ</button>
                        <button id="welcome_card-yeaksa-employee-editor-cancelTextBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset">បោះបង់</button>
                    </div>
                </div>
                <!-- Image Upload Controls -->
                <div class="welcome_card-yeaksa-employee-editor-add-image-container">
                    <div class="welcome_card-yeaksa-employee-editor-add-image-header">
                        <i class="fas fa-image"></i>
                        <span>បញ្ចូលរូបភាព</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-add-image-content">
                        <button id="welcome_card-yeaksa-employee-editor-addImageBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">បញ្ចូលរូបភាព</button>
                        <input type="file" id="welcome_card-yeaksa-employee-editor-imageFile" accept="image/*">
                        <button id="welcome_card-yeaksa-employee-editor-changeImageBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">ផ្លាស់ប្តូររូបភាព</button>
                        <input type="file" id="welcome_card-yeaksa-employee-editor-changeImageFile" accept="image/*">
                        <button id="welcome_card-yeaksa-employee-editor-imageSquareShapeBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary">រាងការ៉េ</button>
                        <button id="welcome_card-yeaksa-employee-editor-imageCircleShapeBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary">រាងមូល</button>
                    </div>
                </div>
                <!-- Element Control -->
                <div class="welcome_card-yeaksa-employee-editor-element-control-container">
                    <div class="welcome_card-yeaksa-employee-editor-element-control-header">
                        <i class="fas fa-cog"></i>
                        <span>គ្រប់គ្រងធាតុ</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-element-control-content">
                        <button id="welcome_card-yeaksa-employee-editor-deleteElementBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset" disabled>លុបធាតុ</button>
                        <button id="welcome_card-yeaksa-employee-editor-bringForwardBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary" disabled>នាំមកមុខ</button>
                        <button id="welcome_card-yeaksa-employee-editor-sendBackwardBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-secondary" disabled>បញ្ជូនទៅក្រោយ</button>
                    </div>
                </div>
                <!-- Toggle Elements -->
                <div class="welcome_card-yeaksa-employee-editor-toggle-elements-container">
                    <div class="welcome_card-yeaksa-employee-editor-toggle-elements-header">
                        <i class="fas fa-toggle-on"></i>
                        <span>ប្តូរព័ត៌មាននិយោជិត</span>
                        <span class="welcome_card-yeaksa-employee-editor-arrow">▼</span>
                    </div>
                    <div class="welcome_card-yeaksa-employee-editor-toggle-elements-content">
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-togglePersonalDetails" data-category="welcome_cardpersonalDetails" checked> ព័ត៌មានផ្ទាល់ខ្លួន</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleContactDetails" data-category="welcome_cardcontactDetails"> ព័ត៌មានទំនាក់ទំនង</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleBusinessMetrics" data-category="welcome_cardbusinessMetrics"> ព័ត៌មានការងារ</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleCustomerPreferences" data-category="welcome_cardcustomerPreferences"> ចំណូលចិត្ត</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleAccountActivity" data-category="welcome_cardaccountActivity"> សកម្មភាពគណនី</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleQRCode" data-category="welcome_cardqrCode" checked> កូដ QR</label>
                        <label><input type="checkbox" id="welcome_card-yeaksa-employee-editor-toggleProfileImage" data-category="welcome_cardprofileImage" checked> រូបភាពប្រវត្តិរូប</label>
                        <button id="welcome_card-yeaksa-employee-editor-selectAllBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">ជ្រើសរើសទាំងអស់</button>
                        <button id="welcome_card-yeaksa-employee-editor-deselectAllBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset">លុបជម្រើសទាំងអស់</button>
                    </div>
                </div>
                <!-- Reset and Download -->
                <button id="welcome_card-yeaksa-employee-editor-resetAllBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-reset">កំណត់ឡើងវិញទាំងអស់</button>
                <button id="welcome_card-yeaksa-employee-editor-downloadBtn" class="welcome_card-yeaksa-employee-editor-btn welcome_card-yeaksa-employee-editor-btn-primary">ទាញយក (PNG)</button>
            </div>
            <div class="welcome_card-yeaksa-employee-editor-right-side">
                <div class="welcome_card-yeaksa-employee-editor-display-area">
                    <div class="welcome_card-yeaksa-employee-editor-letter-page" style="background-image: url('https://via.placeholder.com/794x1123?text=YeaksaEmployeeCertificate');">
                        <div id="welcome_card-yeaksa-employee-editor-textContainer"></div>
                        <div id="welcome_card-yeaksa-employee-editor-imageContainer"></div>
                        <div id="welcome_card-yeaksa-employee-editor-qrContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
    use Carbon\Carbon;

    $carbonDate = now();
    
    $englishDate = $carbonDate->format('j - F - Y');
    @endphp
    <p class="welcome_card-yeaksa-employee-editor">ធ្វើនៅ ភ្នំពេញ ថ្ងៃទី ២២ ខែ ឧសភា ឆ្នាំ ២០២៥</p>
    <p class="welcome_card-yeaksa-employee-editor">Date 22 May 2025</p>
    <script>
        (function() {
            const welcome_cardYeaksaEmployeeEditor = {
                selectedElement: null,
                isDragging: false,
                isResizing: false,
                resizeHandleType: '',
                startX: 0,
                startY: 0,
                startWidth: 0,
                startHeight: 0,
                startLeft: 0,
                startTop: 0,
                mouseOffsetX: 0,
                mouseOffsetY: 0,
                originalTransform: 'none',
                lastAddedImage: null,
                previousImage: null,
                imgSrc: "{{ $user->media->display_url ?? 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png' }}",
                categoryToggles: {
                    welcome_cardpersonalDetails: true,
                    welcome_cardcontactDetails: false,
                    welcome_cardbusinessMetrics: false,
                    welcome_cardcustomerPreferences: false,
                    welcome_cardaccountActivity: false,
                    welcome_cardqrCode: true,
                    welcome_cardprofileImage: true
                },
                defaultPositions: {
                    welcome_cardprofileImage: {
                        tag: 'div',
                        class: 'welcome_card-yeaksa-employee-editor-image-element welcome_card-yeaksa-employee-editor-category-welcome_cardprofileImage',
                        style: { left: '50%', top: '430px', width: '170px', height: '170px', zIndex: '1', transform: 'translateX(-50%)' },
                        id: 'welcome_card-yeaksa-employee-editor-welcome_cardprofileImage',
                        innerHTML: '<img src="{{ $user->media->display_url ?? "https://ulm.webstudio.co.zw/themes/adminlte/img/user.png" }}" alt="Profile Image" style="display: block; opacity: 1; width: 100%; height: 100%; object-fit: cover;">'
                    },
                    welcome_cardpersonalDetails: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '269px', fontSize: '36px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)', whiteSpace: 'nowrap' }, text: "{{__('employeecardb1::lang.welcome_card')}}", id: 'welcome_cardpersonalDetails-0' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '330px', fontSize: '24px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)', whiteSpace: 'nowrap' }, text: "<b>CERTIFICATE OF YEAKSA EMPLOYEE</b>", id: 'welcome_cardpersonalDetails-1' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '377px', fontSize: '24px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "សូមបញ្ជាក់ថានិយោជិតឈ្មោះ", id: 'welcome_cardpersonalDetails-2' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '630px', fontSize: '36px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)', whiteSpace: 'nowrap' }, text: "ឈ្មោះ: <?php echo htmlspecialchars(implode(' ', array_filter([$user->prefix, $user->first_name, $user->middle_name, $user->last_name], fn($value) => !is_null($value) && $value !== '')) ?: '-'); ?>", id: 'welcome_cardpersonalDetails-3' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '700px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "@lang('user.member_date'): {{ $user->member_date ? \Carbon\Carbon::parse($user->member_date)->format('d/m/Y') : __('user.no_data') }}", id: 'welcome_cardpersonalDetails-4' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '745px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "@lang('lang_v1.designation'): {{ $user_designation->name ?? '' }} {{ $user_department->name ?? '' }}", id: 'welcome_cardpersonalDetails-5' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '815px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "<?php echo htmlspecialchars(now()->format('j - F - Y')); ?>", id: 'welcome_cardpersonalDetails-6' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '854px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "ប្រធាន", id: 'welcome_cardpersonalDetails-7' },
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardpersonalDetails', style: { left: '50%', top: '1020px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "________________", id: 'welcome_cardpersonalDetails-8' }
                    ],
                    welcome_cardcontactDetails: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardcontactDetails', style: { left: '300px', top: '200px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'none' }, text: "Email: <?php echo htmlspecialchars($user->email ?? '-'); ?>", id: 'welcome_cardcontactDetails-0' }
                    ],
                    welcome_cardbusinessMetrics: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardbusinessMetrics', style: { left: '550px', top: '200px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'none' }, text: "DOB: <?php echo htmlspecialchars($user->dob ? \Carbon\Carbon::parse($user->dob)->format('d-m-Y') : '-'); ?>", id: 'welcome_cardbusinessMetrics-0' }
                    ],
                    welcome_cardcustomerPreferences: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardcustomerPreferences', style: { left: '50px', top: '400px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'none' }, text: "Type: -1", id: 'welcome_cardcustomerPreferences-0' }
                    ],
                    welcome_cardaccountActivity: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-welcome_cardaccountActivity', style: { left: '300px', top: '400px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000', zIndex: '1', transform: 'none' }, text: "User ID: <?php echo htmlspecialchars($user->user_id ?? '-'); ?>", id: 'welcome_cardaccountActivity-0' }
                    ],
                    welcome_cardqrCode: [
                        { tag: 'div', class: 'welcome_card-yeaksa-employee-editor-qr-element welcome_card-yeaksa-employee-editor-category-welcome_cardqrCode', style: { left: '610px', top: '870px', width: '128px', height: '128px', zIndex: '1', transform: 'none' }, id: 'welcome_card-yeaksa-employee-editor-qrElement', innerHTML: '' }
                    ]
                },

                generateUniqueId: function(prefix) {
                    const timestamp = Date.now();
                    const random = Math.floor(Math.random() * 1000);
                    return `${prefix}-${timestamp}-${random}`;
                },

                initializeEditor: function() {
                    console.log('Initializing Yeaksa Employee Editor');
                    const welcome_cardContainer = document.querySelector('.welcome_card-yeaksa-employee-editor-letter-page');
                    const defaultBackground = '/docs/images/certificate_of_yeaksa.png';
                    if (welcome_cardContainer) {
                        welcome_cardContainer.style.backgroundImage = `url('${defaultBackground}')`;
                        welcome_cardContainer.style.backgroundSize = 'cover';
                        welcome_cardContainer.style.backgroundPosition = 'center';
                        welcome_cardContainer.onclick = (e) => {
                            if (e.target === welcome_cardContainer) {
                                this.resetSelection();
                            }
                        };
                    } else {
                        console.error('Certificate container not found');
                    }

                    const containers = document.querySelectorAll(
                        '.welcome_card-yeaksa-employee-editor-change-profile-container, ' +
                        '.welcome_card-yeaksa-employee-editor-background-input-container, ' +
                        '.welcome_card-yeaksa-employee-editor-insert-text-container, ' +
                        '.welcome_card-yeaksa-employee-editor-add-image-container, ' +
                        '.welcome_card-yeaksa-employee-editor-element-control-container, ' +
                        '.welcome_card-yeaksa-employee-editor-toggle-elements-container'
                    );

                    containers.forEach(container => {
                        const header = container.querySelector(
                            '.welcome_card-yeaksa-employee-editor-change-profile-header, ' +
                            '.welcome_card-yeaksa-employee-editor-background-input-header, ' +
                            '.welcome_card-yeaksa-employee-editor-insert-text-header, ' +
                            '.welcome_card-yeaksa-employee-editor-add-image-header, ' +
                            '.welcome_card-yeaksa-employee-editor-element-control-header, ' +
                            '.welcome_card-yeaksa-employee-editor-toggle-elements-header'
                        );
                        if (header) {
                            header.onclick = (e) => {
                                e.preventDefault();
                                container.classList.toggle('active');
                                containers.forEach(otherContainer => {
                                    if (otherContainer !== container) {
                                        otherContainer.classList.remove('active');
                                    }
                                });
                            };
                        }
                    });

                    const textContainer = document.getElementById('welcome_card-yeaksa-employee-editor-textContainer');
                    const imageContainer = document.getElementById('welcome_card-yeaksa-employee-editor-imageContainer');
                    const qrContainer = document.getElementById('welcome_card-yeaksa-employee-editor-qrContainer');
                    if (!textContainer || !imageContainer || !qrContainer) {
                        console.error('One or more containers (text, image, qr) not found');
                        return;
                    }

                    const toggles = [
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-togglePersonalDetails'), category: 'welcome_cardpersonalDetails' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleContactDetails'), category: 'welcome_cardcontactDetails' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleBusinessMetrics'), category: 'welcome_cardbusinessMetrics' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleCustomerPreferences'), category: 'welcome_cardcustomerPreferences' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleAccountActivity'), category: 'welcome_cardaccountActivity' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleQRCode'), category: 'welcome_cardqrCode' },
                        { element: document.getElementById('welcome_card-yeaksa-employee-editor-toggleProfileImage'), category: 'welcome_cardprofileImage' }
                    ];

                    toggles.forEach(({ element, category }) => {
                        if (!element) return;
                        element.onchange = () => {
                            this.categoryToggles[category] = element.checked;
                            if (category === 'welcome_cardqrCode') {
                                const qrElement = document.getElementById('welcome_card-yeaksa-employee-editor-qrElement');
                                if (element.checked && !qrElement) {
                                    this.initializeQRCode();
                                } else if (!element.checked && qrElement) {
                                    this.removeResizeHandles(qrElement);
                                    qrElement.remove();
                                }
                            } else if (category === 'welcome_cardprofileImage') {
                                const profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                                if (element.checked && !profileElement) {
                                    this.initializeProfileImage();
                                } else if (!element.checked && profileElement) {
                                    this.removeResizeHandles(profileElement);
                                    profileElement.remove();
                                }
                            } else {
                                textContainer.querySelectorAll(`.welcome_card-yeaksa-employee-editor-category-${category}`).forEach(el => {
                                    this.removeResizeHandles(el);
                                    el.remove();
                                });
                                if (element.checked) {
                                    this.initializeCategoryElements(category);
                                }
                            }
                            this.updateElementVisibility();
                        };
                    });

                    const selectAllBtn = document.getElementById('welcome_card-yeaksa-employee-editor-selectAllBtn');
                    if (selectAllBtn) {
                        selectAllBtn.onclick = () => {
                            toggles.forEach(({ element, category }) => {
                                if (!element) return;
                                element.checked = true;
                                this.categoryToggles[category] = true;
                            });
                            textContainer.innerHTML = '';
                            imageContainer.innerHTML = '';
                            qrContainer.innerHTML = '';
                            Object.keys(this.categoryToggles).forEach(category => {
                                if (category === 'welcome_cardprofileImage') {
                                    this.initializeProfileImage();
                                } else if (category === 'welcome_cardqrCode') {
                                    this.initializeQRCode();
                                } else if (this.categoryToggles[category]) {
                                    this.initializeCategoryElements(category);
                                }
                            });
                            this.updateElementVisibility();
                        };
                    }

                    const deselectAllBtn = document.getElementById('welcome_card-yeaksa-employee-editor-deselectAllBtn');
                    if (deselectAllBtn) {
                        deselectAllBtn.onclick = () => {
                            toggles.forEach(({ element, category }) => {
                                if (!element) return;
                                element.checked = false;
                                this.categoryToggles[category] = false;
                            });
                            textContainer.innerHTML = '';
                            imageContainer.innerHTML = '';
                            qrContainer.innerHTML = '';
                            this.lastAddedImage = null;
                            this.updateElementVisibility();
                        };
                    }

                    const resetAllBtn = document.getElementById('welcome_card-yeaksa-employee-editor-resetAllBtn');
                    if (resetAllBtn) {
                        resetAllBtn.onclick = () => {
                            textContainer.innerHTML = '';
                            imageContainer.innerHTML = '';
                            qrContainer.innerHTML = '';
                            this.lastAddedImage = null;
                            this.resetSelection();
                            if (welcome_cardContainer) {
                                welcome_cardContainer.style.backgroundImage = `url('${defaultBackground}')`;
                                welcome_cardContainer.style.backgroundSize = 'cover';
                                welcome_cardContainer.style.backgroundPosition = 'center';
                            }
                            const backgroundSelect = document.getElementById('welcome_card-yeaksa-employee-editor-backgroundSelect');
                            const backgroundFile = document.getElementById('welcome_card-yeaksa-employee-editor-backgroundFile');
                            const profileFile = document.getElementById('welcome_card-yeaksa-employee-editor-profileFile');
                            if (backgroundSelect) backgroundSelect.value = '';
                            if (backgroundFile) backgroundFile.value = '';
                            if (profileFile) profileFile.value = '';
                            this.categoryToggles = {
                                welcome_cardpersonalDetails: true,
                                welcome_cardcontactDetails: false,
                                welcome_cardbusinessMetrics: false,
                                welcome_cardcustomerPreferences: false,
                                welcome_cardaccountActivity: false,
                                welcome_cardqrCode: true,
                                welcome_cardprofileImage: true
                            };
                            toggles.forEach(({ element, category }) => {
                                if (!element) return;
                                element.checked = this.categoryToggles[category];
                            });
                            if (this.categoryToggles.welcome_cardprofileImage) this.initializeProfileImage();
                            if (this.categoryToggles.welcome_cardqrCode) this.initializeQRCode();
                            if (this.categoryToggles.welcome_cardpersonalDetails) this.initializeCategoryElements('welcome_cardpersonalDetails');
                            this.updateElementVisibility();
                            try {
                                localStorage.removeItem('yeaksaEmployeeWelcome_CardEditorState');
                                console.log('localStorage cleared');
                            } catch (err) {
                                console.error('Failed to clear localStorage:', err);
                            }
                            this.debouncedSave();
                        };
                    }

                    const downloadBtn = document.getElementById('welcome_card-yeaksa-employee-editor-downloadBtn');
                    if (downloadBtn) {
                        downloadBtn.onclick = () => {
                            const displayArea = document.querySelector('.welcome_card-yeaksa-employee-editor-display-area');
                            const clone = displayArea.cloneNode(true);
                            clone.classList.add('welcome_card-yeaksa-employee-editor-download-clone');
                            document.body.appendChild(clone);

                            clone.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));
                            clone.querySelectorAll('.welcome_card-yeaksa-employee-editor-resize-handle').forEach(handle => handle.remove());

                            const container = clone.querySelector('.welcome_card-yeaksa-employee-editor-letter-page');
                            const containerWidth = 794; // Fixed width for A4 at 96 DPI
                            const containerHeight = 1123; // Fixed height for A4 at 96 DPI

                            clone.querySelectorAll('.welcome_card-yeaksa-employee-editor-text-element, .welcome_card-yeaksa-employee-editor-image-element, .welcome_card-yeaksa-employee-editor-qr-element').forEach(el => {
                                const isTextElement = el.classList.contains('welcome_card-yeaksa-employee-editor-text-element');
                                const fontSize = isTextElement ? (parseFloat(el.style.fontSize) || 18) : 0;
                                const offset = isTextElement ? -fontSize * 0.3 : 1.5;

                                let left = el.style.left || '0px';
                                let top = parseFloat(el.style.top) || 0;
                                let width = parseFloat(el.style.width) || el.offsetWidth;
                                let height = parseFloat(el.style.height) || el.offsetHeight;

                                if (left.includes('%')) {
                                    const percentage = parseFloat(left) / 100;
                                    left = percentage * containerWidth;
                                    if (el.dataset.useTransform === 'true' || el.style.transform.includes('translateX(-50%)')) {
                                        left -= width / 2;
                                    }
                                } else {
                                    left = parseFloat(left) || 0;
                                }

                                el.style.position = 'absolute';
                                el.style.left = `${left}px`;
                                el.style.top = `${top + offset}px`;
                                el.style.width = `${width}px`;
                                el.style.height = `${height}px`;
                                el.style.transform = 'none';
                                el.style.margin = '0';
                                el.style.padding = '0';

                                if (isTextElement) {
                                    el.style.fontFamily = el.style.fontFamily || "'Times New Roman', Moul, serif";
                                    el.style.fontSize = `${fontSize}px`;
                                    el.style.color = el.style.color || '#000000';
                                    el.style.lineHeight = '1.2';

                                    if (el.id === 'welcome_cardpersonalDetails-0' || el.id === 'welcome_cardpersonalDetails-1' || el.id === 'welcome_cardpersonalDetails-3') {
                                        el.style.whiteSpace = 'nowrap';
                                        const textLength = el.textContent.length;
                                        const approxWidth = fontSize * textLength * 0.6;
                                        el.style.width = `${approxWidth}px`;
                                    }
                                }
                            });

                            try {
                                html2canvas(clone, {
                                    scale: 2,
                                    useCORS: true,
                                    logging: false,
                                    width: containerWidth,
                                    height: containerHeight
                                }).then(canvas => {
                                    const link = document.createElement('a');
                                    link.download = 'welcome_card.png';
                                    link.href = canvas.toDataURL('image/png');
                                    link.click();
                                    document.body.removeChild(clone);
                                }).catch(err => {
                                    console.error('Failed to generate welcome_card:', err);
                                    alert('Failed to generate welcome_card. Please try again.');
                                    document.body.removeChild(clone);
                                });
                            } catch (err) {
                                console.error('Failed to initiate download:', err);
                                alert('Failed to initiate welcome_card download.');
                                document.body.removeChild(clone);
                            }
                        };
                    }

                    const changeProfileBtn = document.getElementById('welcome_card-yeaksa-employee-editor-changeProfileBtn');
                    const profileFile = document.getElementById('welcome_card-yeaksa-employee-editor-profileFile');
                    const squareShapeBtn = document.getElementById('welcome_card-yeaksa-employee-editor-squareShapeBtn');
                    const circleShapeBtn = document.getElementById('welcome_card-yeaksa-employee-editor-circleShapeBtn');
                    const resetProfileBtn = document.getElementById('welcome_card-yeaksa-employee-editor-resetProfileBtn');

                    if (changeProfileBtn && profileFile) {
                        changeProfileBtn.onclick = () => profileFile.click();
                        profileFile.onchange = (event) => {
                            const file = event.target.files[0];
                            if (file) {
                                if (!file.type.startsWith('image/')) {
                                    alert('Please select an image file.');
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    let profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                                    if (!profileElement) {
                                        this.initializeProfileImage();
                                        profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                                    }
                                    const img = profileElement.querySelector('img');
                                    if (img) {
                                        img.src = e.target.result;
                                        this.imgSrc = e.target.result;
                                    } else {
                                        console.error('Profile image element not found');
                                        alert('Failed to update profile image.');
                                    }
                                    profileElement.style.display = this.categoryToggles.welcome_cardprofileImage ? 'block' : 'none';
                                    profileElement.style.opacity = this.categoryToggles.welcome_cardprofileImage ? '1' : '0';
                                    this.debouncedSave();
                                };
                                reader.onerror = () => {
                                    console.error('Error reading profile image file');
                                    alert('Error reading image file.');
                                };
                                reader.readAsDataURL(file);
                            }
                        };
                    }

                    if (squareShapeBtn) {
                        squareShapeBtn.onclick = () => {
                            let profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                            if (!profileElement) {
                                this.initializeProfileImage();
                                profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                            }
                            profileElement.classList.add('square');
                            profileElement.style.display = this.categoryToggles.welcome_cardprofileImage ? 'block' : 'none';
                            profileElement.style.opacity = this.categoryToggles.welcome_cardprofileImage ? '1' : '0';
                            this.debouncedSave();
                        };
                    }

                    if (circleShapeBtn) {
                        circleShapeBtn.onclick = () => {
                            let profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                            if (!profileElement) {
                                this.initializeProfileImage();
                                profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                            }
                            profileElement.classList.remove('square');
                            profileElement.style.display = this.categoryToggles.welcome_cardprofileImage ? 'block' : 'none';
                            profileElement.style.opacity = this.categoryToggles.welcome_cardprofileImage ? '1' : '0';
                            this.debouncedSave();
                        };
                    }

                    if (resetProfileBtn) {
                        resetProfileBtn.onclick = () => {
                            const profileElement = document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage');
                            if (profileElement) {
                                this.removeResizeHandles(profileElement);
                                profileElement.remove();
                            }
                            if (this.categoryToggles.welcome_cardprofileImage) {
                                this.initializeProfileImage();
                            }
                            if (profileFile) profileFile.value = '';
                            this.debouncedSave();
                        };
                    }

                    const changeBackgroundBtn = document.getElementById('welcome_card-yeaksa-employee-editor-changeBackgroundBtn');
                    const backgroundFile = document.getElementById('welcome_card-yeaksa-employee-editor-backgroundFile');
                    const backgroundSelect = document.getElementById('welcome_card-yeaksa-employee-editor-backgroundSelect');
                    const resetBackgroundBtn = document.getElementById('welcome_card-yeaksa-employee-editor-resetBackgroundBtn');

                    if (changeBackgroundBtn && backgroundFile) {
                        changeBackgroundBtn.onclick = () => backgroundFile.click();
                        backgroundFile.onchange = (event) => {
                            const file = event.target.files[0];
                            if (file) {
                                if (!file.type.startsWith('image/')) {
                                    alert('Please selectಮಾಡಿ select an image file.');
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if (welcome_cardContainer) {
                                        welcome_cardContainer.style.backgroundImage = `url('${e.target.result}')`;
                                        welcome_cardContainer.style.backgroundSize = 'cover';
                                        welcome_cardContainer.style.backgroundPosition = 'center';
                                    }
                                    this.debouncedSave();
                                };
                                reader.onerror = () => {
                                    console.error('Error reading background image file');
                                    alert('Error reading background image file.');
                                };
                                reader.readAsDataURL(file);
                            }
                        };
                    }

                    if (backgroundSelect) {
                        backgroundSelect.onchange = () => {
                            const value = backgroundSelect.value;
                            if (value && welcome_cardContainer) {
                                welcome_cardContainer.style.backgroundImage = `url('${value}')`;
                                welcome_cardContainer.style.backgroundSize = 'cover';
                                welcome_cardContainer.style.backgroundPosition = 'center';
                            }
                            this.debouncedSave();
                        };
                    }

                    if (resetBackgroundBtn) {
                        resetBackgroundBtn.onclick = () => {
                            if (welcome_cardContainer) {
                                welcome_cardContainer.style.backgroundImage = `url('${defaultBackground}')`;
                                welcome_cardContainer.style.backgroundSize = 'cover';
                                welcome_cardContainer.style.backgroundPosition = 'center';
                            }
                            if (backgroundSelect) backgroundSelect.value = '';
                            if (backgroundFile) backgroundFile.value = '';
                            this.debouncedSave();
                        };
                    }

                    const textBtn = document.getElementById('welcome_card-yeaksa-employee-editor-textBtn');
                    const cancelTextBtn = document.getElementById('welcome_card-yeaksa-employee-editor-cancelTextBtn');
                    const textInput = document.getElementById('welcome_card-yeaksa-employee-editor-textInput');
                    const textColor = document.getElementById('welcome_card-yeaksa-employee-editor-textColor');
                    const fontSizeSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontSizeSelect');
                    const customFontSize = document.getElementById('welcome_card-yeaksa-employee-editor-customFontSize');
                    const fontStyleSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontStyleSelect');

                    if (fontSizeSelect) {
                        fontSizeSelect.onchange = () => {
                            if (fontSizeSelect.value === 'custom') {
                                customFontSize.style.display = 'block';
                                customFontSize.focus();
                            } else {
                                customFontSize.style.display = 'none';
                                customFontSize.value = '';
                            }
                        };
                    }

                    if (textBtn) {
                        textBtn.onclick = () => {
                            const text = textInput.value.trim();
                            if (!text) {
                                alert('Please enter text.');
                                return;
                            }
                            const color = textColor.value;
                            const fontFamily = fontStyleSelect.value;
                            let fontSize = fontSizeSelect.value === 'custom' ? (parseFloat(customFontSize.value) || 18) : parseFloat(fontSizeSelect.value);

                            if (this.selectedElement && this.selectedElement.classList.contains('welcome_card-yeaksa-employee-editor-text-element')) {
                                const span = this.selectedElement.querySelector('span');
                                if (span) {
                                    span.innerHTML = text;
                                }
                                this.selectedElement.style.color = color;
                                this.selectedElement.style.fontSize = `${fontSize}px`;
                                this.selectedElement.style.fontFamily = fontFamily;
                                this.resetTextInputs();
                                this.debouncedSave();
                            } else {
                                const textElement = document.createElement('div');
                                textElement.className = 'welcome_card-yeaksa-employee-editor-text-element';
                                textElement.id = this.generateUniqueId('text-element');
                                textElement.style.position = 'absolute';
                                textElement.style.left = '50px';
                                textElement.style.top = '50px';
                                textElement.style.color = color;
                                textElement.style.fontSize = `${fontSize}px`;
                                textElement.style.fontFamily = fontFamily;
                                textElement.style.zIndex = this.getNextZIndex();
                                textElement.style.cursor = 'move';
                                textElement.style.userSelect = 'none';
                                const span = document.createElement('span');
                                span.innerHTML = text;
                                span.style.display = 'inline';
                                textElement.appendChild(span);
                                textContainer.appendChild(textElement);
                                this.setupTextElement(textElement);
                                this.resetTextInputs();
                                this.debouncedSave();
                            }
                        };
                    }

                    if (cancelTextBtn) {
                        cancelTextBtn.onclick = () => this.resetTextInputs();
                    }

                    const addImageBtn = document.getElementById('welcome_card-yeaksa-employee-editor-addImageBtn');
                    const imageFile = document.getElementById('welcome_card-yeaksa-employee-editor-imageFile');
                    const changeImageBtn = document.getElementById('welcome_card-yeaksa-employee-editor-changeImageBtn');
                    const changeImageFile = document.getElementById('welcome_card-yeaksa-employee-editor-changeImageFile');
                    const imageSquareShapeBtn = document.getElementById('welcome_card-yeaksa-employee-editor-imageSquareShapeBtn');
                    const imageCircleShapeBtn = document.getElementById('welcome_card-yeaksa-employee-editor-imageCircleShapeBtn');

                    if (addImageBtn && imageFile) {
                        addImageBtn.onclick = () => imageFile.click();
                        imageFile.onchange = (event) => {
                            const file = event.target.files[0];
                            if (file) {
                                if (!file.type.startsWith('image/')) {
                                    alert('Please select an image file.');
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    const imageElement = document.createElement('div');
                                    imageElement.className = 'welcome_card-yeaksa-employee-editor-image-element';
                                    imageElement.id = this.generateUniqueId('image-element');
                                    imageElement.style.position = 'absolute';
                                    imageElement.style.left = '50px';
                                    imageElement.style.top = '50px';
                                    imageElement.style.width = '100px';
                                    imageElement.style.height = '100px';
                                    imageElement.style.zIndex = this.getNextZIndex();
                                    imageElement.style.cursor = 'move';
                                    imageElement.style.userSelect = 'none';
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.alt = 'Uploaded Image';
                                    img.style.width = '100%';
                                    img.style.height = '100%';
                                    img.style.objectFit = 'cover';
                                    imageElement.appendChild(img);
                                    imageContainer.appendChild(imageElement);
                                    this.setupImageElement(imageElement);
                                    this.lastAddedImage = imageElement;
                                    imageFile.value = '';
                                    this.debouncedSave();
                                };
                                reader.readAsDataURL(file);
                            }
                        };
                    }

                    if (changeImageBtn && changeImageFile) {
                        changeImageBtn.onclick = () => {
                            if (!this.lastAddedImage) {
                                alert('Please add an image first.');
                                return;
                            }
                            changeImageFile.click();
                        };
                        changeImageFile.onchange = (event) => {
                            const file = event.target.files[0];
                            if (file && this.lastAddedImage) {
                                if (!file.type.startsWith('image/')) {
                                    alert('Please select an image file.');
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    const img = this.lastAddedImage.querySelector('img');
                                    if (img) {
                                        img.src = e.target.result;
                                    }
                                    changeImageFile.value = '';
                                    this.debouncedSave();
                                };
                                reader.readAsDataURL(file);
                            }
                        };
                    }

                    if (imageSquareShapeBtn) {
                        imageSquareShapeBtn.onclick = () => {
                            if (this.lastAddedImage) {
                                this.lastAddedImage.classList.add('square');
                                this.debouncedSave();
                            }
                        };
                    }

                    if (imageCircleShapeBtn) {
                        imageCircleShapeBtn.onclick = () => {
                            if (this.lastAddedImage) {
                                this.lastAddedImage.classList.remove('square');
                                this.debouncedSave();
                            }
                        };
                    }

                    try {
                        const savedState = localStorage.getItem('yeaksaEmployeeWelcome_CardEditorState');
                        if (savedState) {
                            console.log('Loading position state from localStorage');
                            const state = JSON.parse(savedState);

                            state.textElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions[el.category]?.find(cfg => cfg.id === el.id) || {
                                    tag: 'div',
                                    class: `welcome_card-yeaksa-employee-editor-text-element welcome_card-yeaksa-employee-editor-category-${el.category}`,
                                    style: { fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif", color: '#000000' },
                                    text: 'Custom Text'
                                };
                                const textElement = document.createElement(config.tag);
                                textElement.className = config.class;
                                textElement.id = el.id || this.generateUniqueId('text-element');
                                textElement.style.position = 'absolute';
                                textElement.style.left = el.left || '50px';
                                textElement.style.top = el.top || '50px';
                                textElement.style.zIndex = el.zIndex || this.getNextZIndex();
                                textElement.style.transform = el.transform || 'none';
                                textElement.style.fontSize = config.style.fontSize;
                                textElement.style.fontFamily = config.style.fontFamily;
                                textElement.style.color = config.style.color;
                                textElement.dataset.category = el.category || '';
                                textElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                if (el.id === 'welcome_cardpersonalDetails-0' || el.id === 'welcome_cardpersonalDetails-1' || el.id === 'welcome_cardpersonalDetails-3') {
                                    textElement.style.whiteSpace = 'nowrap';
                                }
                                const span = document.createElement('span');
                                span.innerHTML = config.text || 'Custom Text';
                                span.style.display = 'inline';
                                textElement.appendChild(span);
                                textContainer.appendChild(textElement);
                                this.setupTextElement(textElement);
                            });

                            state.imageElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions[el.category]?.find(cfg => cfg.id === el.id) || {
                                    tag: 'div',
                                    class: `welcome_card-yeaksa-employee-editor-image-element welcome_card-yeaksa-employee-editor-category-${el.category}`,
                                    style: { width: '100px', height: '100px' },
                                    innerHTML: '<img src="' + this.imgSrc + '" alt="Added Image" style="display: block; width: 100%; height: 100%; object-fit: cover;">'
                                };
                                const imageElement = document.createElement(config.tag);
                                imageElement.className = config.class;
                                imageElement.id = el.id || this.generateUniqueId('image-element');
                                imageElement.style.position = 'absolute';
                                imageElement.style.left = el.left || '347px';
                                imageElement.style.top = el.top || '50px';
                                imageElement.style.width = config.style.width;
                                imageElement.style.height = config.style.height;
                                imageElement.style.zIndex = el.zIndex || this.getNextZIndex();
                                imageElement.style.transform = el.transform || 'none';
                                imageElement.dataset.category = el.category || '';
                                imageElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                imageElement.innerHTML = config.innerHTML;
                                imageContainer.appendChild(imageElement);
                                this.setupImageElement(imageElement);
                                if (el.id !== 'welcome_card-yeaksa-employee-editor-welcome_cardprofileImage') {
                                    this.lastAddedImage = imageElement;
                                }
                            });

                            state.qrElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions.welcome_cardqrCode?.find(cfg => cfg.id === el.id) || {
                                    tag: 'div',
                                    class: `welcome_card-yeaksa-employee-editor-qr-element welcome_card-yeaksa-employee-editor-category-${el.category}`,
                                    style: { width: '128px', height: '128px' }
                                };
                                const qrElement = document.createElement(config.tag);
                                qrElement.className = config.class;
                                qrElement.id = el.id || this.generateUniqueId('qr-element');
                                qrElement.style.position = 'absolute';
                                qrElement.style.left = el.left || '347px';
                                qrElement.style.top = el.top || '870px';
                                qrElement.style.width = config.style.width;
                                qrElement.style.height = config.style.height;
                                qrElement.style.zIndex = el.zIndex || this.getNextZIndex();
                                qrElement.style.transform = el.transform || 'none';
                                qrElement.dataset.category = el.category || '';
                                qrElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                qrContainer.appendChild(qrElement);
                                this.setupQRElement(qrElement);
                                const userLink = "{{ url('/employee/' . $user->id) }}";
                                const qrOptions = {
                                    text: userLink,
                                    margin: 4,
                                    width: parseFloat(config.style.width) || 128,
                                    height: parseFloat(config.style.height) || 128,
                                    quietZone: 10,
                                    colorDark: "#000000",
                                    colorLight: "#ffffff"
                                };
                                try {
                                    new QRCode(qrElement, qrOptions);
                                } catch (err) {
                                    console.error('Failed to generate QR code:', err);
                                    alert('Error generating QR code.');
                                }
                                const canvas = qrElement.querySelector('canvas');
                                if (canvas) {
                                    canvas.id = 'welcome_card-yeaksa-employee-editor-employee_qr_canvas';
                                    canvas.style.display = 'block';
                                }
                            });

                            this.updateElementVisibility();
                            console.log('Position state loaded successfully');
                        } else {
                            console.log('No saved state found, initializing defaults');
                            if (this.categoryToggles.welcome_cardprofileImage) this.initializeProfileImage();
                            if (this.categoryToggles.welcome_cardqrCode) this.initializeQRCode();
                            if (this.categoryToggles.welcome_cardpersonalDetails) this.initializeCategoryElements('welcome_cardpersonalDetails');
                            this.updateElementVisibility();
                        }
                        this.debugElementState();
                    } catch (err) {
                        console.error('Failed to load state:', err);
                        console.log('Falling back to default initialization');
                        if (this.categoryToggles.welcome_cardprofileImage) this.initializeProfileImage();
                        if (this.categoryToggles.welcome_cardqrCode) this.initializeQRCode();
                        if (this.categoryToggles.welcome_cardpersonalDetails) this.initializeCategoryElements('welcome_cardpersonalDetails');
                        this.updateElementVisibility();
                        this.debugElementState();
                    }
                },

                createElementFromConfig: function(config, container, setupFunction) {
                    const element = document.createElement(config.tag);
                    element.className = config.class;
                    element.id = config.id || this.generateUniqueId(config.class.match(/welcome_card-yeaksa-employee-editor-(\w+)/)?.[1] || 'element');
                    Object.assign(element.style, config.style);
                    element.style.position = 'absolute';
                    element.style.cursor = 'move';
                    element.style.userSelect = 'none';
                    if (config.text) {
                        const span = document.createElement('span');
                        span.innerHTML = config.text;
                        span.style.display = 'inline';
                        element.appendChild(span);
                    } else if (config.innerHTML) {
                        element.innerHTML = config.innerHTML;
                    }
                    element.dataset.category = config.class.match(/welcome_card-yeaksa-employee-editor-category-(\w+)/)?.[1] || '';
                    element.dataset.useTransform = config.style.transform && config.style.transform.includes('translateX(-50%)') ? 'true' : 'false';
                    container.appendChild(element);
                    setupFunction(element);
                    return element;
                },

                initializeProfileImage: function() {
                    if (document.getElementById('welcome_card-yeaksa-employee-editor-welcome_cardprofileImage')) return;
                    const profileElement = this.createElementFromConfig(this.defaultPositions.welcome_cardprofileImage, document.getElementById('welcome_card-yeaksa-employee-editor-imageContainer'), this.setupImageElement.bind(this));
                    profileElement.style.display = this.categoryToggles.welcome_cardprofileImage ? 'block' : 'none';
                    profileElement.style.opacity = this.categoryToggles.welcome_cardprofileImage ? '1' : '0';
                    this.debouncedSave();
                },

                initializeQRCode: function() {
                    if (document.getElementById('welcome_card-yeaksa-employee-editor-qrElement')) return;
                    const qrConfig = this.defaultPositions.welcome_cardqrCode[0];
                    const qrElement = this.createElementFromConfig(qrConfig, document.getElementById('welcome_card-yeaksa-employee-editor-qrContainer'), this.setupQRElement.bind(this));
                    const userLink = "{{ url('/employee/' . $user->id) }}";
                    const qrOptions = {
                        text: userLink,
                        margin: 4,
                        width: parseFloat(qrConfig.style.width) || 128,
                        height: parseFloat(qrConfig.style.height) || 128,
                        quietZone: 10,
                        colorDark: "#000000",
                        colorLight: "#ffffff"
                    };
                    try {
                        new QRCode(qrElement, qrOptions);
                    } catch (err) {
                        console.error('Failed to generate QR code:', err);
                        alert('Error generating QR code.');
                    }
                    const canvas = qrElement.querySelector('canvas');
                    if (canvas) {
                        canvas.id = 'welcome_card-yeaksa-employee-editor-employee_qr_canvas';
                        canvas.style.display = 'block';
                    }
                    qrElement.style.display = this.categoryToggles.welcome_cardqrCode ? 'block' : 'none';
                    qrElement.style.opacity = this.categoryToggles.welcome_cardqrCode ? '1' : '0';
                    this.debouncedSave();
                },

                initializeCategoryElements: function(category) {
                    if (!this.categoryToggles[category] || !this.defaultPositions[category]) return;
                    const textContainer = document.getElementById('welcome_card-yeaksa-employee-editor-textContainer');
                    this.defaultPositions[category].forEach(config => {
                        if (document.getElementById(config.id)) return;
                        this.createElementFromConfig(config, textContainer, this.setupTextElement.bind(this));
                    });
                    this.updateElementVisibility();
                    this.debouncedSave();
                },

                updateElementVisibility: function() {
                    document.querySelectorAll('.welcome_card-yeaksa-employee-editor-text-element, .welcome_card-yeaksa-employee-editor-image-element, .welcome_card-yeaksa-employee-editor-qr-element').forEach(el => {
                        const category = el.dataset.category;
                        if (category && this.categoryToggles[category] !== undefined) {
                            el.style.display = this.categoryToggles[category] ? 'block' : 'none';
                            el.style.opacity = this.categoryToggles[category] ? '1' : '0';
                        } else {
                            el.style.display = 'block';
                            el.style.opacity = '1';
                        }
                    });
                    this.debouncedSave();
                },

                setupTextElement: function(element) {
                    element.onmousedown = this.startDrag.bind(this);
                    element.onclick = (e) => this.selectElement(e, element);
                },

                setupImageElement: function(element) {
                    element.onmousedown = this.startDrag.bind(this);
                    element.onclick = (e) => this.selectElement(e, element);
                },

                setupQRElement: function(element) {
                    element.onmousedown = this.startDrag.bind(this);
                    element.onclick = (e) => this.selectElement(e, element);
                },

                addResizeHandles: function(element) {
                    if (!element.classList.contains('welcome_card-yeaksa-employee-editor-image-element') && !element.classList.contains('welcome_card-yeaksa-employee-editor-qr-element')) return;
                    const handles = [
                        { class: 'welcome_card-yeaksa-employee-editor-resize-handle top-left', cursor: 'nw-resize' },
                        { class: 'welcome_card-yeaksa-employee-editor-resize-handle top-right', cursor: 'ne-resize' },
                        { class: 'welcome_card-yeaksa-employee-editor-resize-handle bottom-left', cursor: 'sw-resize' },
                        { class: 'welcome_card-yeaksa-employee-editor-resize-handle bottom-right', cursor: 'se-resize' }
                    ];
                    handles.forEach(handle => {
                        const div = document.createElement('div');
                        div.className = handle.class;
                        div.style.cursor = handle.cursor;
                        div.onmousedown = (e) => this.startResize(e, element, handle.class.split(' ')[1]);
                        element.appendChild(div);
                    });
                },

                removeResizeHandles: function(element) {
                    if (!element) return;
                    const handles = element.querySelectorAll('.welcome_card-yeaksa-employee-editor-resize-handle');
                    handles.forEach(handle => handle.remove());
                },

                startDrag: function(e) {
                    if (e.target.classList.contains('welcome_card-yeaksa-employee-editor-resize-handle') || this.isResizing) return;
                    e.preventDefault();
                    this.isDragging = true;
                    this.selectElement(e, e.currentTarget);
                    this.startX = e.clientX;
                    this.startY = e.clientY;

                    const container = document.querySelector('.welcome_card-yeaksa-employee-editor-letter-page');
                    const { width, height, left: containerLeft, top: containerTop } = container.getBoundingClientRect();

                    const parsePos = (value, max) => {
                        if (value.includes('%')) {
                            return (parseFloat(value) / 100) * max;
                        }
                        return parseFloat(value) || 0;
                    };

                    this.startLeft = parsePos(this.selectedElement.style.left, width);
                    this.startTop = parsePos(this.selectedElement.style.top, height);
                    this.useTransform = this.selectedElement.dataset.useTransform === 'false';
                    this.originalTransform = this.selectedElement.style.transform || 'none';

                    if (this.useTransform) {
                        this.selectedElement.style.transform = 'none';
                        this.selectedElement.dataset.useTransform = 'false';
                    }

                    const rect = this.selectedElement.getBoundingClientRect();
                    this.mouseOffsetX = e.clientX - rect.left;
                    this.mouseOffsetY = e.clientY - rect.top;

                    document.addEventListener('mousemove', this.doDrag.bind(this));
                    document.addEventListener('mouseup', this.stopDrag.bind(this));
                },

                doDrag: function(e) {
                    if (!this.isDragging || !this.selectedElement) return;
                    requestAnimationFrame(() => {
                        const container = document.querySelector('.welcome_card-yeaksa-employee-editor-letter-page');
                        const { width, height, left: containerLeft, top: containerTop } = container.getBoundingClientRect();

                        let newLeft = e.clientX - this.mouseOffsetX - containerLeft;
                        let newTop = e.clientY - this.mouseOffsetY - containerTop;

                        newLeft = Math.max(0, Math.min(newLeft, width - this.selectedElement.offsetWidth));
                        newTop = Math.max(0, Math.min(newTop, height - this.selectedElement.offsetHeight));

                        this.selectedElement.style.left = `${newLeft}px`;
                        this.selectedElement.style.top = `${newTop}px`;
                        this.selectedElement.style.transform = 'none';
                        this.selectedElement.dataset.useTransform = 'false';

                        this.debouncedSave();
                    });
                },

                stopDrag: function() {
                    this.isDragging = false;
                    document.removeEventListener('mousemove', this.doDrag.bind(this));
                    document.removeEventListener('mouseup', this.stopDrag.bind(this));
                    if (this.selectedElement) {
                        this.selectedElement.dataset.useTransform = 'false';
                    }
                    this.debouncedSave();
                },

                startResize: function(e, element, handleType) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.isResizing = true;
                    this.selectedElement = element;
                    this.resizeHandleType = handleType;
                    this.startX = e.clientX;
                    this.startY = e.clientY;
                    this.startWidth = parseFloat(element.style.width) || element.offsetWidth;
                    this.startHeight = parseFloat(element.style.height) || element.offsetHeight;
                    this.startLeft = parseFloat(element.style.left) || 0;
                    this.startTop = parseFloat(element.style.top) || 0;
                    this.useTransform = element.dataset.useTransform === 'true';
                    this.originalTransform = element.style.transform || 'none';

                    document.addEventListener('mousemove', this.doResize.bind(this));
                    document.addEventListener('mouseup', this.stopResize.bind(this));
                },

                doResize: function(e) {
                    if (!this.isResizing || !this.selectedElement) return;
                    requestAnimationFrame(() => {
                        const container = document.querySelector('.welcome_card-yeaksa-employee-editor-letter-page');
                        const { width: containerWidth, height: containerHeight } = container.getBoundingClientRect();

                        const deltaX = e.clientX - this.startX;
                        const deltaY = e.clientY - this.startY;
                        let newWidth = this.startWidth;
                        let newHeight = this.startHeight;
                        let newLeft = this.startLeft;
                        let newTop = this.startTop;

                        const minSize = 20;
                        const maxSize = containerWidth;
                        switch (this.resizeHandleType) {
                            case 'top-left':
                                newWidth = Math.max(minSize, this.startWidth - deltaX);
                                newHeight = Math.max(minSize, this.startHeight - deltaY);
                                newLeft = Math.min(this.startLeft + deltaX, this.startLeft + this.startWidth - minSize);
                                newTop = Math.min(this.startTop + deltaY, this.startTop + this.startHeight - minSize);
                                break;
                            case 'top-right':
                                newWidth = Math.max(minSize, this.startWidth + deltaX);
                                newHeight = Math.max(minSize, this.startHeight - deltaY);
                                newTop = Math.min(this.startTop + deltaY, this.startTop + this.startHeight - minSize);
                                break;
                            case 'bottom-left':
                                newWidth = Math.max(minSize, this.startWidth - deltaX);
                                newHeight = Math.max(minSize, this.startHeight + deltaY);
                                newLeft = Math.min(this.startLeft + deltaX, this.startLeft + this.startWidth - minSize);
                                break;
                            case 'bottom-right':
                                newWidth = Math.max(minSize, this.startWidth + deltaX);
                                newHeight = Math.max(minSize, this.startHeight + deltaY);
                                break;
                        }

                        newWidth = Math.min(newWidth, containerWidth - newLeft);
                        newHeight = Math.min(newHeight, containerHeight - newTop);
                        newLeft = Math.max(0, Math.min(newLeft, containerWidth - newWidth));
                        newTop = Math.max(0, Math.min(newTop, containerHeight - newHeight));

                        if (this.selectedElement.classList.contains('welcome_card-yeaksa-employee-editor-image-element') || 
                            this.selectedElement.classList.contains('welcome_card-yeaksa-employee-editor-qr-element')) {
                            const aspectRatio = this.startWidth / this.startHeight;
                            newHeight = newWidth / aspectRatio;
                            if (newTop + newHeight > containerHeight) {
                                newHeight = containerHeight - newTop;
                                newWidth = newHeight * aspectRatio;
                            }
                        }

                        this.selectedElement.style.width = `${newWidth}px`;
                        this.selectedElement.style.height = `${newHeight}px`;
                        this.selectedElement.style.left = `${newLeft}px`;
                        this.selectedElement.style.top = `${newTop}px`;
                        this.selectedElement.style.transform = 'none';
                        this.selectedElement.dataset.useTransform = 'false';

                        this.debouncedSave();
                    });
                },

                stopResize: function() {
                    this.isResizing = false;
                    document.removeEventListener('mousemove', this.doResize.bind(this));
                    document.removeEventListener('mouseup', this.stopResize.bind(this));
                    this.debouncedSave();
                },

                selectElement: function(e, element) {
                    e.stopPropagation();
                    if (this.selectedElement !== element) {
                        this.resetSelection();
                        this.selectedElement = element;
                        this.selectedElement.classList.add('selected');
                        this.addResizeHandles(this.selectedElement);
                        const deleteBtn = document.getElementById('welcome_card-yeaksa-employee-editor-deleteElementBtn');
                        const bringForwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-bringForwardBtn');
                        const sendBackwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-sendBackwardBtn');
                        if (deleteBtn) deleteBtn.disabled = false;
                        if (bringForwardBtn) bringForwardBtn.disabled = false;
                        if (sendBackwardBtn) sendBackwardBtn.disabled = false;

                        const textInput = document.getElementById('welcome_card-yeaksa-employee-editor-textInput');
                        const textColor = document.getElementById('welcome_card-yeaksa-employee-editor-textColor');
                        const fontSizeSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontSizeSelect');
                        const customFontSize = document.getElementById('welcome_card-yeaksa-employee-editor-customFontSize');
                        const fontStyleSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontStyleSelect');

                        if (this.selectedElement.classList.contains('welcome_card-yeaksa-employee-editor-text-element')) {
                            const span = this.selectedElement.querySelector('span');
                            if (span && textInput) textInput.value = span.innerHTML;
                            if (textColor) textColor.value = this.selectedElement.style.color || '#000000';
                            const fontSize = parseFloat(this.selectedElement.style.fontSize) || 18;
                            if (fontSizeSelect) {
                                const options = Array.from(fontSizeSelect.options).map(opt => parseFloat(opt.value)).filter(val => !isNaN(val));
                                if (options.includes(fontSize)) {
                                    fontSizeSelect.value = fontSize.toString();
                                    customFontSize.style.display = 'none';
                                } else {
                                    fontSizeSelect.value = 'custom';
                                    customFontSize.style.display = 'block';
                                    customFontSize.value = fontSize;
                                }
                            }
                            if (fontStyleSelect) fontStyleSelect.value = this.selectedElement.style.fontFamily || "'Times New Roman', Moul, serif";
                        }
                    }
                },

                resetSelection: function() {
                    if (this.selectedElement) {
                        this.selectedElement.classList.remove('selected');
                        this.removeResizeHandles(this.selectedElement);
                        this.selectedElement = null;
                    }
                    const deleteBtn = document.getElementById('welcome_card-yeaksa-employee-editor-deleteElementBtn');
                    const bringForwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-bringForwardBtn');
                    const sendBackwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-sendBackwardBtn');
                    if (deleteBtn) deleteBtn.disabled = true;
                    if (bringForwardBtn) bringForwardBtn.disabled = true;
                    if (sendBackwardBtn) sendBackwardBtn.disabled = true;
                },

                getNextZIndex: function() {
                    const elements = document.querySelectorAll('.welcome_card-yeaksa-employee-editor-text-element, .welcome_card-yeaksa-employee-editor-image-element, .welcome_card-yeaksa-employee-editor-qr-element');
                    let maxZIndex = 1;
                    elements.forEach(el => {
                        const zIndex = parseInt(el.style.zIndex) || 1;
                        if (zIndex > maxZIndex) maxZIndex = zIndex;
                    });
                    return maxZIndex + 1;
                },

                resetTextInputs: function() {
                    const textInput = document.getElementById('welcome_card-yeaksa-employee-editor-textInput');
                    const textColor = document.getElementById('welcome_card-yeaksa-employee-editor-textColor');
                    const fontSizeSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontSizeSelect');
                    const customFontSize = document.getElementById('welcome_card-yeaksa-employee-editor-customFontSize');
                    const fontStyleSelect = document.getElementById('welcome_card-yeaksa-employee-editor-fontStyleSelect');
                    if (textInput) textInput.value = '';
                    if (textColor) textColor.value = '#000000';
                    if (fontSizeSelect) fontSizeSelect.value = '16';
                    if (customFontSize) {
                        customFontSize.value = '';
                        customFontSize.style.display = 'none';
                    }
                    if (fontStyleSelect) fontStyleSelect.value = "'Times New Roman', Moul, serif";
                    this.resetSelection();
                },

                saveState: function() {
                    const textElements = Array.from(document.querySelectorAll('.welcome_card-yeaksa-employee-editor-text-element')).map(el => ({
                        id: el.id || this.generateUniqueId('text-element'),
                        category: el.dataset.category || '',
                        left: el.style.left || '50px',
                        top: el.style.top || '50px',
                        zIndex: el.style.zIndex || '1',
                        transform: el.style.transform || 'none'
                    }));

                    const imageElements = Array.from(document.querySelectorAll('.welcome_card-yeaksa-employee-editor-image-element')).map(el => ({
                        id: el.id || this.generateUniqueId('image-element'),
                        category: el.dataset.category || '',
                        left: el.style.left || '347px',
                        top: el.style.top || '50px',
                        zIndex: el.style.zIndex || '1',
                        transform: el.style.transform || 'none'
                    }));

                    const qrElements = Array.from(document.querySelectorAll('.welcome_card-yeaksa-employee-editor-qr-element')).map(el => ({
                        id: el.id || this.generateUniqueId('qr-element'),
                        category: el.dataset.category || '',
                        left: el.style.left || '347px',
                        top: el.style.top || '870px',
                        zIndex: el.style.zIndex || '1',
                        transform: el.style.transform || 'none'
                    }));

                    const state = {
                        textElements,
                        imageElements,
                        qrElements
                    };

                    try {
                        localStorage.setItem('yeaksaEmployeeWelcome_CardEditorState', JSON.stringify(state));
                        console.log('State saved to localStorage (positions only)');
                    } catch (err) {
                        console.error('Failed to save state:', err);
                    }
                },

                debouncedSave: function() {
                    clearTimeout(this.saveTimeout);
                    this.saveTimeout = setTimeout(() => this.saveState(), 500);
                },

                debugElementState: function() {
                    const elements = document.querySelectorAll('.welcome_card-yeaksa-employee-editor-text-element, .welcome_card-yeaksa-employee-editor-image-element, .welcome_card-yeaksa-employee-editor-qr-element');
                    console.log(`Total elements: ${elements.length}`);
                    elements.forEach((el, index) => {
                        console.log(`Element ${index}:`, {
                            id: el.id || 'no-id',
                            class: el.className,
                            left: el.style.left,
                            top: el.style.top,
                            transform: el.style.transform,
                            zIndex: el.style.zIndex,
                            category: el.dataset.category,
                            useTransform: el.dataset.useTransform,
                            whiteSpace: el.style.whiteSpace
                        });
                    });
                }
            };

            welcome_cardYeaksaEmployeeEditor.initializeEditor();

            const deleteBtn = document.getElementById('welcome_card-yeaksa-employee-editor-deleteElementBtn');
            if (deleteBtn) {
                deleteBtn.onclick = () => {
                    if (welcome_cardYeaksaEmployeeEditor.selectedElement) {
                        welcome_cardYeaksaEmployeeEditor.removeResizeHandles(welcome_cardYeaksaEmployeeEditor.selectedElement);
                        welcome_cardYeaksaEmployeeEditor.selectedElement.remove();
                        welcome_cardYeaksaEmployeeEditor.selectedElement = null;
                        deleteBtn.disabled = true;
                        const bringForwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-bringForwardBtn');
                        const sendBackwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-sendBackwardBtn');
                        if (bringForwardBtn) bringForwardBtn.disabled = true;
                        if (sendBackwardBtn) sendBackwardBtn.disabled = true;
                        welcome_cardYeaksaEmployeeEditor.debouncedSave();
                    }
                };
            }

            const bringForwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-bringForwardBtn');
            if (bringForwardBtn) {
                bringForwardBtn.onclick = () => {
                    if (welcome_cardYeaksaEmployeeEditor.selectedElement) {
                        const currentZIndex = parseInt(welcome_cardYeaksaEmployeeEditor.selectedElement.style.zIndex) || 1;
                        welcome_cardYeaksaEmployeeEditor.selectedElement.style.zIndex = currentZIndex + 1;
                        welcome_cardYeaksaEmployeeEditor.debouncedSave();
                    }
                };
            }

            const sendBackwardBtn = document.getElementById('welcome_card-yeaksa-employee-editor-sendBackwardBtn');
            if (sendBackwardBtn) {
                sendBackwardBtn.onclick = () => {
                    if (welcome_cardYeaksaEmployeeEditor.selectedElement) {
                        const currentZIndex = parseInt(welcome_cardYeaksaEmployeeEditor.selectedElement.style.zIndex) || 1;
                        if (currentZIndex > 1) {
                            welcome_cardYeaksaEmployeeEditor.selectedElement.style.zIndex = currentZIndex - 1;
                        }
                        welcome_cardYeaksaEmployeeEditor.debouncedSave();
                    }
                };
            }
        })();
    </script>
</body>
</html>