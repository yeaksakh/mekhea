<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Yeaksa Member Editor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&family=Arial&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/easyqrcodejs@4.5.0/dist/easy.qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .certificate-of-yeaksa-member-editor {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }
        .certificate-of-yeaksa-member-editor-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            gap: 5px;
        }
        .certificate-of-yeaksa-member-editor-left-side {
            width: 30%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid gray;
            border-radius: 10px;
            overflow-y: auto;
        }
        .certificate-of-yeaksa-member-editor-right-side {
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
        .certificate-of-yeaksa-member-editor-display-area {
            position: relative;
            width: 794px;
            height: 1123px;
            overflow: hidden;
            background-color: transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 0;
            padding: 0;
        }
        .certificate-of-yeaksa-member-editor-letter-page {
            position: absolute;
            top: 0;
            left: 0;
            width: 794px;
            height: 1123px;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        .certificate-of-yeaksa-member-editor #certificate-of-yeaksa-member-editor-textContainer, 
        .certificate-of-yeaksa-member-editor #certificate-of-yeaksa-member-editor-imageContainer, 
        .certificate-of-yeaksa-member-editor #certificate-of-yeaksa-member-editor-qrContainer {
            position: absolute;
            top: 0;
            left: 0;
            width: 794px;
            height: 1123px;
            pointer-events: none;
        }
        .certificate-of-yeaksa-member-editor-text-element,
        .certificate-of-yeaksa-member-editor-image-element,
        .certificate-of-yeaksa-member-editor-qr-element {
            position: absolute;
            cursor: move;
            pointer-events: auto;
            user-select: none;
            font-size: 18px;
            color: #000000;
            font-family: 'Times New Roman', Moul, serif !important;
        }
        .certificate-of-yeaksa-member-editor-text-element.selected,
        .certificate-of-yeaksa-member-editor-image-element.selected,
        .certificate-of-yeaksa-member-editor-qr-element.selected {
            border: 1px solid cyan;
        }
        .certificate-of-yeaksa-member-editor-image-element {
            width: 100px;
            height: 100px;
        }
        .certificate-of-yeaksa-member-editor-image-element img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .certificate-of-yeaksa-member-editor-image-element.square img {
            border-radius: 0;
        }
        .certificate-of-yeaksa-member-editor-image-element:not(.square) img {
            border-radius: 50%;
        }
        .certificate-of-yeaksa-member-editor-qr-element {
            width: 128px;
            height: 128px;
        }
        .certificate-of-yeaksa-member-editor-qr-element canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .certificate-of-yeaksa-member-editor-resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: cyan;
            cursor: pointer;
            pointer-events: auto;
        }
        .certificate-of-yeaksa-member-editor-resize-handle.top-left {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }
        .certificate-of-yeaksa-member-editor-resize-handle.top-right {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }
        .certificate-of-yeaksa-member-editor-resize-handle.bottom-left {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }
        .certificate-of-yeaksa-member-editor-resize-handle.bottom-right {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }
        .certificate-of-yeaksa-member-editor-element-hidden {
            display: none;
            opacity: 0;
        }
        .certificate-of-yeaksa-member-editor-left-side h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #333;
        }
        .certificate-of-yeaksa-member-editor-left-side hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }
        .certificate-of-yeaksa-member-editor-change-profile-container,
        .certificate-of-yeaksa-member-editor-background-input-container,
        .certificate-of-yeaksa-member-editor-insert-text-container,
        .certificate-of-yeaksa-member-editor-add-image-container,
        .certificate-of-yeaksa-member-editor-element-control-container,
        .certificate-of-yeaksa-member-editor-toggle-elements-container {
            margin-bottom: 15px;
        }
        .certificate-of-yeaksa-member-editor-change-profile-header,
        .certificate-of-yeaksa-member-editor-background-input-header,
        .certificate-of-yeaksa-member-editor-insert-text-header,
        .certificate-of-yeaksa-member-editor-add-image-header,
        .certificate-of-yeaksa-member-editor-element-control-header,
        .certificate-of-yeaksa-member-editor-toggle-elements-header {
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
        .certificate-of-yeaksa-member-editor-change-profile-header:hover,
        .certificate-of-yeaksa-member-editor-background-input-header:hover,
        .certificate-of-yeaksa-member-editor-insert-text-header:hover,
        .certificate-of-yeaksa-member-editor-add-image-header:hover,
        .certificate-of-yeaksa-member-editor-element-control-header:hover,
        .certificate-of-yeaksa-member-editor-toggle-elements-header:hover {
            background-color: #c8d8ff;
        }
        .certificate-of-yeaksa-member-editor-change-profile-content,
        .certificate-of-yeaksa-member-editor-background-input-content,
        .certificate-of-yeaksa-member-editor-insert-text-content,
        .certificate-of-yeaksa-member-editor-add-image-content,
        .certificate-of-yeaksa-member-editor-element-control-content,
        .certificate-of-yeaksa-member-editor-toggle-elements-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }
        .certificate-of-yeaksa-member-editor-change-profile-container.active .certificate-of-yeaksa-member-editor-change-profile-content,
        .certificate-of-yeaksa-member-editor-background-input-container.active .certificate-of-yeaksa-member-editor-background-input-content,
        .certificate-of-yeaksa-member-editor-insert-text-container.active .certificate-of-yeaksa-member-editor-insert-text-content,
        .certificate-of-yeaksa-member-editor-add-image-container.active .certificate-of-yeaksa-member-editor-add-image-content,
        .certificate-of-yeaksa-member-editor-element-control-container.active .certificate-of-yeaksa-member-editor-element-control-content,
        .certificate-of-yeaksa-member-editor-toggle-elements-container.active .certificate-of-yeaksa-member-editor-toggle-elements-content {
            max-height: 500px;
            padding: 10px;
        }
        .certificate-of-yeaksa-member-editor-change-profile-container.active .certificate-of-yeaksa-member-editor-arrow,
        .certificate-of-yeaksa-member-editor-background-input-container.active .certificate-of-yeaksa-member-editor-arrow,
        .certificate-of-yeaksa-member-editor-insert-text-container.active .certificate-of-yeaksa-member-editor-arrow,
        .certificate-of-yeaksa-member-editor-add-image-container.active .certificate-of-yeaksa-member-editor-arrow,
        .certificate-of-yeaksa-member-editor-element-control-container.active .certificate-of-yeaksa-member-editor-arrow,
        .certificate-of-yeaksa-member-editor-toggle-elements-container.active .certificate-of-yeaksa-member-editor-arrow {
            transform: rotate(180deg);
        }
        .certificate-of-yeaksa-member-editor-arrow {
            transition: transform 0.3s;
            font-size: 14px;
        }
        .certificate-of-yeaksa-member-editor select, 
        .certificate-of-yeaksa-member-editor input[type="checkbox"], 
        .certificate-of-yeaksa-member-editor input[type="text"], 
        .certificate-of-yeaksa-member-editor input[type="color"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .certificate-of-yeaksa-member-editor input[type="text"]:focus {
            border-color: #17a2b8;
            outline: none;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }
        .certificate-of-yeaksa-member-editor input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        .certificate-of-yeaksa-member-editor input[type="file"] {
            display: none;
        }
        .certificate-of-yeaksa-member-editor-btn {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .certificate-of-yeaksa-member-editor-btn-primary {
            background-color: #17a2b8;
            color: white;
        }
        .certificate-of-yeaksa-member-editor-btn-primary:hover {
            background-color: #138496;
        }
        .certificate-of-yeaksa-member-editor-btn-reset {
            background-color: #dc3545;
            color: white;
        }
        .certificate-of-yeaksa-member-editor-btn-reset:hover {
            background-color: #c82333;
        }
        .certificate-of-yeaksa-member-editor-btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .certificate-of-yeaksa-member-editor-btn-secondary:hover {
            background-color: #5a6268;
        }
        .certificate-of-yeaksa-member-editor-hidden {
            display: none;
        }
        @media print {
            .certificate-of-yeaksa-member-editor {
                display: none;
            }
        }
        @media (max-width: 1200px) {
            .certificate-of-yeaksa-member-editor-right-side {
                min-width: auto;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-of-yeaksa-member-editor">
        <div class="certificate-of-yeaksa-member-editor-container">
            <div class="certificate-of-yeaksa-member-editor-left-side">
                <h2>កែសម្រួល</h2>
                <hr>
                <!-- Profile Image Controls -->
                <div class="certificate-of-yeaksa-member-editor-change-profile-container">
                    <div class="certificate-of-yeaksa-member-editor-change-profile-header">
                        <i class="fas fa-user"></i>
                        <span>រូបភាពប្រវត្តិរូប</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-change-profile-content">
                        <button id="certificate-of-yeaksa-member-editor-changeProfileBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">ផ្លាស់ប្តូររូបភាព</button>
                        <input type="file" id="certificate-of-yeaksa-member-editor-profileFile" accept="image/*">
                        <button id="certificate-of-yeaksa-member-editor-squareShapeBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary">រាងការ៉េ</button>
                        <button id="certificate-of-yeaksa-member-editor-circleShapeBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary">រាងមូល</button>
                        <button id="certificate-of-yeaksa-member-editor-resetProfileBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset">កំណត់រូបភាពឡើងវិញ</button>
                    </div>
                </div>
                <!-- Background Controls -->
                <div class="certificate-of-yeaksa-member-editor-background-input-container">
                    <div class="certificate-of-yeaksa-member-editor-background-input-header">
                        <i class="fas fa-image"></i>
                        <span>រូបភាពផ្ទៃខាងក្រោយ</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-background-input-content">
                        <select id="certificate-of-yeaksa-member-editor-backgroundSelect">
                            <option value="">ជ្រើសរើសផ្ទៃខាងក្រោយ</option>
                            <option value="https://via.placeholder.com/794x1123?text=YeaksaEmployeeCertificate">លិខិតបញ្ជាក់និយោជិតយក្សា</option>
                            <option value="https://via.placeholder.com/794x1123?text=TrainingCertificate">វិញ្ញាបនបត្របញ្ជាក់ការសិក្សា</option>
                        </select>
                        <button id="certificate-of-yeaksa-member-editor-changeBackgroundBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">ផ្លាស់ប្តូរផ្ទៃខាងក្រោយ</button>
                        <input type="file" id="certificate-of-yeaksa-member-editor-backgroundFile" accept="image/*">
                        <button id="certificate-of-yeaksa-member-editor-resetBackgroundBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset">កំណត់ផ្ទៃខាងក្រោយឡើងវិញ</button>
                    </div>
                </div>
                <!-- Text Input Controls -->
                <div class="certificate-of-yeaksa-member-editor-insert-text-container">
                    <div class="certificate-of-yeaksa-member-editor-insert-text-header">
                        <i class="fas fa-font"></i>
                        <span>បញ្ចូលអត្ថបទ</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-insert-text-content">
                        <input type="text" id="certificate-of-yeaksa-member-editor-textInput" placeholder="បញ្ចូលអត្ថបទ">
                        <input type="color" id="certificate-of-yeaksa-member-editor-textColor" value="#000000">
                        <select id="certificate-of-yeaksa-member-editor-fontSizeSelect">
                            <option value="12">12px</option>
                            <option value="16" selected>16px</option>
                            <option value="24">24px</option>
                            <option value="32">32px</option>
                            <option value="36">36px</option>
                            <option value="custom">ផ្ទាល់ខ្លួន</option>
                        </select>
                        <input type="number" id="certificate-of-yeaksa-member-editor-customFontSize" class="certificate-of-yeaksa-member-editor-hidden" placeholder="ទំហំផ្ទាល់ខ្លួន (px)">
                        <select id="certificate-of-yeaksa-member-editor-fontStyleSelect">
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Moul', serif !important">Moul</option>
                            <option value="'Arial', sans-serif">Arial</option>
                            <option value="'Courier New', monospace">Courier New</option>
                        </select>
                        <button id="certificate-of-yeaksa-member-editor-textBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">បញ្ចូលអត្ថបទ</button>
                        <button id="certificate-of-yeaksa-member-editor-cancelTextBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset">បោះបង់</button>
                    </div>
                </div>
                <!-- Image Upload Controls -->
                <div class="certificate-of-yeaksa-member-editor-add-image-container">
                    <div class="certificate-of-yeaksa-member-editor-add-image-header">
                        <i class="fas fa-image"></i>
                        <span>បញ្ចូលរូបភាព</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-add-image-content">
                        <button id="certificate-of-yeaksa-member-editor-addImageBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">បញ្ចូលរូបភាព</button>
                        <input type="file" id="certificate-of-yeaksa-member-editor-imageFile" accept="image/*">
                        <button id="certificate-of-yeaksa-member-editor-changeImageBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">ផ្លាស់ប្តូររូបភាព</button>
                        <input type="file" id="certificate-of-yeaksa-member-editor-changeImageFile" accept="image/*">
                        <button id="certificate-of-yeaksa-member-editor-imageSquareShapeBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary">រាងការ៉េ</button>
                        <button id="certificate-of-yeaksa-member-editor-imageCircleShapeBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary">រាងមូល</button>
                    </div>
                </div>
                <!-- Element Control -->
                <div class="certificate-of-yeaksa-member-editor-element-control-container">
                    <div class="certificate-of-yeaksa-member-editor-element-control-header">
                        <i class="fas fa-cog"></i>
                        <span>គ្រប់គ្រងធាតុ</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-element-control-content">
                        <button id="certificate-of-yeaksa-member-editor-deleteElementBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset" disabled>លុបធាតុ</button>
                        <button id="certificate-of-yeaksa-member-editor-bringForwardBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary" disabled>នាំមកមុខ</button>
                        <button id="certificate-of-yeaksa-member-editor-sendBackwardBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-secondary" disabled>បញ្ជូនទៅក្រោយ</button>
                    </div>
                </div>
                <!-- Toggle Elements -->
                <div class="certificate-of-yeaksa-member-editor-toggle-elements-container">
                    <div class="certificate-of-yeaksa-member-editor-toggle-elements-header">
                        <i class="fas fa-toggle-on"></i>
                        <span>ប្តូរព័ត៌មាននិយោជិត</span>
                        <span class="certificate-of-yeaksa-member-editor-arrow">▼</span>
                    </div>
                    <div class="certificate-of-yeaksa-member-editor-toggle-elements-content">
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-togglePersonalDetails" data-category="certificateMemberpersonalDetails" checked> ព័ត៌មានផ្ទាល់ខ្លួន</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleContactDetails" data-category="contactDetails"> ព័ត៌មានទំនាក់ទំនង</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleBusinessMetrics" data-category="businessMetrics"> ព័ត៌មានការងារ</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleCustomerPreferences" data-category="customerPreferences"> ចំណូលចិត្ត</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleAccountActivity" data-category="accountActivity"> សកម្មភាពគណនី</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleQRCode" data-category="qrCode" checked> កូដ QR</label>
                        <label><input type="checkbox" id="certificate-of-yeaksa-member-editor-toggleProfileImage" data-category="profileImage" checked> រូបភាពប្រវត្តិរូប</label>
                        <button id="certificate-of-yeaksa-member-editor-selectAllBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">ជ្រើសរើសទាំងអស់</button>
                        <button id="certificate-of-yeaksa-member-editor-deselectAllBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset">លុបជម្រើសទាំងអស់</button>
                    </div>
                </div>
                <!-- Reset and Download -->
                <button id="certificate-of-yeaksa-member-editor-resetAllBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-reset">កំណត់ឡើងវិញទាំងអស់</button>
                <button id="certificate-of-yeaksa-member-editor-downloadBtn" class="certificate-of-yeaksa-member-editor-btn certificate-of-yeaksa-member-editor-btn-primary">ទាញយក (PNG)</button>
            </div>
            <div class="certificate-of-yeaksa-member-editor-right-side">
                <div class="certificate-of-yeaksa-member-editor-display-area">
                    <div class="certificate-of-yeaksa-member-editor-letter-page" style="background-image: url('https://via.placeholder.com/794x1123?text=YeaksaEmployeeCertificate');">
                        <div id="certificate-of-yeaksa-member-editor-textContainer"></div>
                        <div id="certificate-of-yeaksa-member-editor-imageContainer"></div>
                        <div id="certificate-of-yeaksa-member-editor-qrContainer"></div>
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
    <p class="certificate-of-yeaksa-member-editor">ធ្វើនៅ ភ្នំពេញ ថ្ងៃទី ២២ ខែ ឧសភា ឆ្នាំ ២០២៥</p>
    <p class="certificate-of-yeaksa-member-editor">Date 22 May 2025</p>
    <script>
        (function() {
            const YeaksaMemberEditor = {
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
                imgSrc: "{{ $contact->media->display_url ?? 'https://ulm.webstudio.co.zw/themes/adminlte/img/user.png' }}",
                categoryToggles: {
                    certificateMemberpersonalDetails: true,
                    contactDetails: false,
                    businessMetrics: false,
                    customerPreferences: false,
                    accountActivity: false,
                    qrCode: true,
                    profileImage: true
                },
                defaultPositions: {
                    profileImage: {
                        tag: 'div',
                        class: 'certificate-of-yeaksa-member-editor-image-element certificate-of-yeaksa-member-editor-category-profileImage',
                        style: { left: '50%', top: '430px', width: '170px', height: '170px', zIndex: '1', transform: 'translateX(-50%)' },
                        id: 'certificate-of-yeaksa-member-editor-profileImage',
                        innerHTML: '<img src="{{ $contact->media->display_url ?? "https://ulm.webstudio.co.zw/themes/adminlte/img/user.png" }}" alt="Profile Image" style="display: block; opacity: 1; width: 100%; height: 100%; object-fit: cover;">'
                    },
                    certificateMemberpersonalDetails: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '162px', top: '269px', fontSize: '36px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "លិខិតបញ្ជាក់សមាជិកយក្សា", id: 'certificateMemberpersonalDetails-0' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '190px', top: '330px', fontSize: '24px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "<b>CERTIFICATE OF YEAKSA MEMBER</b>", id: 'certificateMemberpersonalDetails-1' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '235px', top: '377px', fontSize: '24px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none' }, text: "សូមបញ្ជាក់ថាសិក្ខាកាមឈ្មោះ", id: 'certificateMemberpersonalDetails-2' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '50%', top: '615px', fontSize: '36px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'translateX(-50%)', whiteSpace: 'nowrap' }, text: "ឈ្មោះ: <?php echo htmlspecialchars(implode(' ', array_filter([$contact->prefix, $contact->first_name, $contact->middle_name, $contact->last_name], fn($value) => !is_null($value) && $value !== '')) ?: '-'); ?>", id: 'certificateMemberpersonalDetails-3' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '170px', top: '680px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "សាខា: <?php echo htmlspecialchars($contact->supplier_business_name ?? '-'); ?>", id: 'certificateMemberpersonalDetails-4' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '170px', top: '725px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "<?php echo htmlspecialchars(implode(', ', array_filter([$contact->address_line_1, $contact->address_line_2, $contact->city, $contact->state, $contact->country, $contact->zip_code], fn($value) => !is_null($value) && $value !== '')) ?: '-'); ?>", id: 'certificateMemberpersonalDetails-5' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '170px', top: '780px', fontSize: '12px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "@lang('customercardb1::lang.register_date'): <?php echo htmlspecialchars($contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-'); ?>", id: 'certificateMemberpersonalDetails-6' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '465px', top: '780px', fontSize: '12px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none', whiteSpace: 'nowrap' }, text: "@lang('customercardb1::lang.expired_at'): <?php echo htmlspecialchars($contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') : '-'); ?>", id: 'certificateMemberpersonalDetails-7' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '50%', top: '815px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "<?php echo htmlspecialchars($englishDate); ?>", id: 'certificateMemberpersonalDetails-8' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '50%', top: '872px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "ប្រធាន", id: 'certificateMemberpersonalDetails-11' },
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-certificateMemberpersonalDetails', style: { left: '50%', top: '966px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'translateX(-50%)' }, text: "________________", id: 'certificateMemberpersonalDetails-12' },
                    ],
                    contactDetails: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-contactDetails', style: { left: '300px', top: '200px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none' }, text: "Email: <?php echo htmlspecialchars($user->email ?? '-'); ?>", id: 'contactDetails-0' }
                    ],
                    businessMetrics: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-businessMetrics', style: { left: '550px', top: '200px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none' }, text: "DOB: <?php echo htmlspecialchars($user->dob ? \Carbon\Carbon::parse($user->dob)->format('d-m-Y') : '-'); ?>", id: 'businessMetrics-0' }
                    ],
                    customerPreferences: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-customerPreferences', style: { left: '50px', top: '400px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none' }, text: "Type: -1", id: 'customerPreferences-0' }
                    ],
                    accountActivity: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-text-element certificate-of-yeaksa-member-editor-category-accountActivity', style: { left: '300px', top: '400px', fontSize: '18px', fontFamily: "'Times New Roman', Moul, serif !important", color: '#000000', zIndex: '1', transform: 'none' }, text: "User ID: <?php echo htmlspecialchars($user->user_id ?? '-'); ?>", id: 'accountActivity-0' }
                    ],
                    qrCode: [
                        { tag: 'div', class: 'certificate-of-yeaksa-member-editor-qr-element certificate-of-yeaksa-member-editor-category-qrCode', style: { left: '610px', top: '870px', width: '128px', height: '128px', zIndex: '1', transform: 'none' }, id: 'certificate-of-yeaksa-member-editor-qrElement', innerHTML: '' }
                    ]
                },
    
                generateUniqueId: function(prefix) {
                    const timestamp = Date.now();
                    const random = Math.floor(Math.random() * 1000);
                    return `${prefix}-${timestamp}-${random}`;
                },
    
                initializeEditor: function() {
                    console.log('Initializing Yeaksa Employee Editor');
                    const certificateContainer = document.querySelector('.certificate-of-yeaksa-member-editor-letter-page');
                    const defaultBackground = '/docs/images/certificate_of_yeaksa.png';
                    if (certificateContainer) {
                        certificateContainer.style.backgroundImage = `url('${defaultBackground}')`;
                        certificateContainer.style.backgroundSize = 'cover';
                        certificateContainer.style.backgroundPosition = 'center';
                        certificateContainer.onclick = (e) => {
                            if (e.target === certificateContainer) {
                                this.resetSelection();
                            }
                        };
                    } else {
                        console.error('Certificate container not found');
                    }
    
                    const containers = document.querySelectorAll(
                        '.certificate-of-yeaksa-member-editor-change-profile-container, ' +
                        '.certificate-of-yeaksa-member-editor-background-input-container, ' +
                        '.certificate-of-yeaksa-member-editor-insert-text-container, ' +
                        '.certificate-of-yeaksa-member-editor-add-image-container, ' +
                        '.certificate-of-yeaksa-member-editor-element-control-container, ' +
                        '.certificate-of-yeaksa-member-editor-toggle-elements-container'
                    );
    
                    containers.forEach(container => {
                        const header = container.querySelector(
                            '.certificate-of-yeaksa-member-editor-change-profile-header, ' +
                            '.certificate-of-yeaksa-member-editor-background-input-header, ' +
                            '.certificate-of-yeaksa-member-editor-insert-text-header, ' +
                            '.certificate-of-yeaksa-member-editor-add-image-header, ' +
                            '.certificate-of-yeaksa-member-editor-element-control-header, ' +
                            '.certificate-of-yeaksa-member-editor-toggle-elements-header'
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
    
                    const textContainer = document.getElementById('certificate-of-yeaksa-member-editor-textContainer');
                    const imageContainer = document.getElementById('certificate-of-yeaksa-member-editor-imageContainer');
                    const qrContainer = document.getElementById('certificate-of-yeaksa-member-editor-qrContainer');
                    if (!textContainer || !imageContainer || !qrContainer) {
                        console.error('One or more containers (text, image, qr) not found');
                        return;
                    }
    
                    const toggles = [
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-togglePersonalDetails'), category: 'certificateMemberpersonalDetails' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleContactDetails'), category: 'contactDetails' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleBusinessMetrics'), category: 'businessMetrics' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleCustomerPreferences'), category: 'customerPreferences' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleAccountActivity'), category: 'accountActivity' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleQRCode'), category: 'qrCode' },
                        { element: document.getElementById('certificate-of-yeaksa-member-editor-toggleProfileImage'), category: 'profileImage' }
                    ];
    
                    toggles.forEach(({ element, category }) => {
                        if (!element) return;
                        element.onchange = () => {
                            this.categoryToggles[category] = element.checked;
                            if (category === 'qrCode') {
                                const qrElement = document.getElementById('certificate-of-yeaksa-member-editor-qrElement');
                                if (element.checked && !qrElement) {
                                    this.initializeQRCode();
                                } else if (!element.checked && qrElement) {
                                    this.removeResizeHandles(qrElement);
                                    qrElement.remove();
                                }
                            } else if (category === 'profileImage') {
                                const profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                                if (element.checked && !profileElement) {
                                    this.initializeProfileImage();
                                } else if (!element.checked && profileElement) {
                                    this.removeResizeHandles(profileElement);
                                    profileElement.remove();
                                }
                            } else {
                                textContainer.querySelectorAll(`.certificate-of-yeaksa-member-editor-category-${category}`).forEach(el => {
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
    
                    const selectAllBtn = document.getElementById('certificate-of-yeaksa-member-editor-selectAllBtn');
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
                                if (category === 'profileImage') {
                                    this.initializeProfileImage();
                                } else if (category === 'qrCode') {
                                    this.initializeQRCode();
                                } else if (this.categoryToggles[category]) {
                                    this.initializeCategoryElements(category);
                                }
                            });
                            this.updateElementVisibility();
                        };
                    }
    
                    const deselectAllBtn = document.getElementById('certificate-of-yeaksa-member-editor-deselectAllBtn');
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
    
                    const resetAllBtn = document.getElementById('certificate-of-yeaksa-member-editor-resetAllBtn');
                    if (resetAllBtn) {
                        resetAllBtn.onclick = () => {
                            textContainer.innerHTML = '';
                            imageContainer.innerHTML = '';
                            qrContainer.innerHTML = '';
                            this.lastAddedImage = null;
                            this.resetSelection();
                            if (certificateContainer) {
                                certificateContainer.style.backgroundImage = `url('${defaultBackground}')`;
                                certificateContainer.style.backgroundSize = 'cover';
                                certificateContainer.style.backgroundPosition = 'center';
                            }
                            const backgroundSelect = document.getElementById('certificate-of-yeaksa-member-editor-backgroundSelect');
                            const backgroundFile = document.getElementById('certificate-of-yeaksa-member-editor-backgroundFile');
                            const profileFile = document.getElementById('certificate-of-yeaksa-member-editor-profileFile');
                            if (backgroundSelect) backgroundSelect.value = '';
                            if (backgroundFile) backgroundFile.value = '';
                            if (profileFile) profileFile.value = '';
                            this.categoryToggles = {
                                certificateMemberpersonalDetails: true,
                                contactDetails: false,
                                businessMetrics: false,
                                customerPreferences: false,
                                accountActivity: false,
                                qrCode: true,
                                profileImage: true
                            };
                            toggles.forEach(({ element, category }) => {
                                if (!element) return;
                                element.checked = this.categoryToggles[category];
                            });
                            if (this.categoryToggles.profileImage) this.initializeProfileImage();
                            if (this.categoryToggles.qrCode) this.initializeQRCode();
                            if (this.categoryToggles.certificateMemberpersonalDetails) this.initializeCategoryElements('certificateMemberpersonalDetails');
                            this.updateElementVisibility();
                            try {
                                localStorage.removeItem('yeaksaMemberCertificateEditorState');
                                console.log('localStorage cleared');
                            } catch (err) {
                                console.error('Failed to clear localStorage:', err);
                            }
                            this.debouncedSave();
                        };
                    }
    
                    const downloadBtn = document.getElementById('certificate-of-yeaksa-member-editor-downloadBtn');
                    if (downloadBtn) {
                        downloadBtn.onclick = () => {
                            const displayArea = document.querySelector('.certificate-of-yeaksa-member-editor-display-area');
                            const clone = displayArea.cloneNode(true);
                            clone.classList.add('certificate-of-yeaksa-member-editor-download-clone');
                            document.body.appendChild(clone);
    
                            clone.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));
                            clone.querySelectorAll('.certificate-of-yeaksa-member-editor-resize-handle').forEach(handle => handle.remove());
    
                            const container = clone.querySelector('.certificate-of-yeaksa-member-editor-letter-page');
                            const containerWidth = 794; // Fixed width for A4 at 96 DPI
                            const containerHeight = 1123; // Fixed height for A4 at 96 DPI
    
                            clone.querySelectorAll('.certificate-of-yeaksa-member-editor-text-element, .certificate-of-yeaksa-member-editor-image-element, .certificate-of-yeaksa-member-editor-qr-element').forEach(el => {
                                const isTextElement = el.classList.contains('certificate-of-yeaksa-member-editor-text-element');
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
                                    el.style.fontFamily = el.style.fontFamily || "'Times New Roman', Moul, serif !important";
                                    el.style.fontSize = `${fontSize}px`;
                                    el.style.color = el.style.color || '#000000';
                                    el.style.lineHeight = '1.2';
    
                                    if (el.id === 'certificateMemberpersonalDetails-0' || el.id === 'certificateMemberpersonalDetails-1' || el.id === 'certificateMemberpersonalDetails-3' || el.id === 'certificateMemberpersonalDetails-4' || el.id === 'certificateMemberpersonalDetails-5' || el.id === 'certificateMemberpersonalDetails-6' || el.id === 'certificateMemberpersonalDetails-7') {
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
                                    link.download = 'certificate.png';
                                    link.href = canvas.toDataURL('image/png');
                                    link.click();
                                    document.body.removeChild(clone);
                                }).catch(err => {
                                    console.error('Failed to generate certificate:', err);
                                    alert('Failed to generate certificate. Please try again.');
                                    document.body.removeChild(clone);
                                });
                            } catch (err) {
                                console.error('Failed to initiate download:', err);
                                alert('Failed to initiate certificate download.');
                                document.body.removeChild(clone);
                            }
                        };
                    }
    
                    const changeProfileBtn = document.getElementById('certificate-of-yeaksa-member-editor-changeProfileBtn');
                    const profileFile = document.getElementById('certificate-of-yeaksa-member-editor-profileFile');
                    const squareShapeBtn = document.getElementById('certificate-of-yeaksa-member-editor-squareShapeBtn');
                    const circleShapeBtn = document.getElementById('certificate-of-yeaksa-member-editor-circleShapeBtn');
                    const resetProfileBtn = document.getElementById('certificate-of-yeaksa-member-editor-resetProfileBtn');
    
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
                                    let profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                                    if (!profileElement) {
                                        this.initializeProfileImage();
                                        profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                                    }
                                    const img = profileElement.querySelector('img');
                                    if (img) {
                                        img.src = e.target.result;
                                        this.imgSrc = e.target.result;
                                    } else {
                                        console.error('Profile image element not found');
                                        alert('Failed to update profile image.');
                                    }
                                    profileElement.style.display = this.categoryToggles.profileImage ? 'block' : 'none';
                                    profileElement.style.opacity = this.categoryToggles.profileImage ? '1' : '0';
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
                            let profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                            if (!profileElement) {
                                this.initializeProfileImage();
                                profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                            }
                            profileElement.classList.add('square');
                            profileElement.style.display = this.categoryToggles.profileImage ? 'block' : 'none';
                            profileElement.style.opacity = this.categoryToggles.profileImage ? '1' : '0';
                            this.debouncedSave();
                        };
                    }
    
                    if (circleShapeBtn) {
                        circleShapeBtn.onclick = () => {
                            let profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                            if (!profileElement) {
                                this.initializeProfileImage();
                                profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                            }
                            profileElement.classList.remove('square');
                            profileElement.style.display = this.categoryToggles.profileImage ? 'block' : 'none';
                            profileElement.style.opacity = this.categoryToggles.profileImage ? '1' : '0';
                            this.debouncedSave();
                        };
                    }
    
                    if (resetProfileBtn) {
                        resetProfileBtn.onclick = () => {
                            const profileElement = document.getElementById('certificate-of-yeaksa-member-editor-profileImage');
                            if (profileElement) {
                                this.removeResizeHandles(profileElement);
                                profileElement.remove();
                            }
                            if (this.categoryToggles.profileImage) {
                                this.initializeProfileImage();
                            }
                            if (profileFile) profileFile.value = '';
                            this.debouncedSave();
                        };
                    }
    
                    const changeBackgroundBtn = document.getElementById('certificate-of-yeaksa-member-editor-changeBackgroundBtn');
                    const backgroundFile = document.getElementById('certificate-of-yeaksa-member-editor-backgroundFile');
                    const backgroundSelect = document.getElementById('certificate-of-yeaksa-member-editor-backgroundSelect');
                    const resetBackgroundBtn = document.getElementById('certificate-of-yeaksa-member-editor-resetBackgroundBtn');
    
                    if (changeBackgroundBtn && backgroundFile) {
                        changeBackgroundBtn.onclick = () => backgroundFile.click();
                        backgroundFile.onchange = (event) => {
                            const file = event.target.files[0];
                            if (file) {
                                if (!file.type.startsWith('image/')) {
                                    alert('Please select an image file.');
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if (certificateContainer) {
                                        certificateContainer.style.backgroundImage = `url('${e.target.result}')`;
                                        certificateContainer.style.backgroundSize = 'cover';
                                        certificateContainer.style.backgroundPosition = 'center';
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
                            if (value && certificateContainer) {
                                certificateContainer.style.backgroundImage = `url('${value}')`;
                                certificateContainer.style.backgroundSize = 'cover';
                                certificateContainer.style.backgroundPosition = 'center';
                            }
                            this.debouncedSave();
                        };
                    }
    
                    if (resetBackgroundBtn) {
                        resetBackgroundBtn.onclick = () => {
                            if (certificateContainer) {
                                certificateContainer.style.backgroundImage = `url('${defaultBackground}')`;
                                certificateContainer.style.backgroundSize = 'cover';
                                certificateContainer.style.backgroundPosition = 'center';
                            }
                            if (backgroundSelect) backgroundSelect.value = '';
                            if (backgroundFile) backgroundFile.value = '';
                            this.debouncedSave();
                        };
                    }
    
                    const textBtn = document.getElementById('certificate-of-yeaksa-member-editor-textBtn');
                    const cancelTextBtn = document.getElementById('certificate-of-yeaksa-member-editor-cancelTextBtn');
                    const textInput = document.getElementById('certificate-of-yeaksa-member-editor-textInput');
                    const textColor = document.getElementById('certificate-of-yeaksa-member-editor-textColor');
                    const fontSizeSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontSizeSelect');
                    const customFontSize = document.getElementById('certificate-of-yeaksa-member-editor-customFontSize');
                    const fontStyleSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontStyleSelect');
    
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
    
                            if (this.selectedElement && this.selectedElement.classList.contains('certificate-of-yeaksa-member-editor-text-element')) {
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
                                textElement.className = 'certificate-of-yeaksa-member-editor-text-element';
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
    
                    const addImageBtn = document.getElementById('certificate-of-yeaksa-member-editor-addImageBtn');
                    const imageFile = document.getElementById('certificate-of-yeaksa-member-editor-imageFile');
                    const changeImageBtn = document.getElementById('certificate-of-yeaksa-member-editor-changeImageBtn');
                    const changeImageFile = document.getElementById('certificate-of-yeaksa-member-editor-changeImageFile');
                    const imageSquareShapeBtn = document.getElementById('certificate-of-yeaksa-member-editor-imageSquareShapeBtn');
                    const imageCircleShapeBtn = document.getElementById('certificate-of-yeaksa-member-editor-imageCircleShapeBtn');
    
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
                                    imageElement.className = 'certificate-of-yeaksa-member-editor-image-element';
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
                        const savedState = localStorage.getItem('yeaksaMemberCertificateEditorState');
                        if (savedState) {
                            console.log('Loading position state from localStorage');
                            const state = JSON.parse(savedState);
    
                            state.textElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions[el.category]?.find(cfg => cfg.id === el.id);
                                if (!config) return; // Skip if no matching default config
                                const textElement = document.createElement(config.tag);
                                textElement.className = config.class;
                                textElement.id = el.id || this.generateUniqueId('text-element');
                                textElement.style.position = 'absolute';
                                textElement.style.left = el.left || config.style.left || '50px';
                                textElement.style.top = el.top || config.style.top || '50px';
                                textElement.style.zIndex = el.zIndex || config.style.zIndex || this.getNextZIndex();
                                textElement.style.transform = el.transform || config.style.transform || 'none';
                                textElement.style.fontSize = config.style.fontSize || '18px';
                                textElement.style.fontFamily = config.style.fontFamily || "'Times New Roman', Moul, serif !important";
                                textElement.style.color = config.style.color || '#000000';
                                textElement.style.whiteSpace = config.style.whiteSpace || 'normal';
                                textElement.dataset.category = el.category || '';
                                textElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                const span = document.createElement('span');
                                span.innerHTML = config.text || '';
                                span.style.display = 'inline';
                                textElement.appendChild(span);
                                textContainer.appendChild(textElement);
                                this.setupTextElement(textElement);
                            });
    
                            state.imageElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions[el.category]?.find(cfg => cfg.id === el.id);
                                if (!config) return; // Skip if no matching default config
                                const imageElement = document.createElement(config.tag);
                                imageElement.className = config.class;
                                imageElement.id = el.id || this.generateUniqueId('image-element');
                                imageElement.style.position = 'absolute';
                                imageElement.style.left = el.left || config.style.left || '347px';
                                imageElement.style.top = el.top || config.style.top || '50px';
                                imageElement.style.width = config.style.width || '100px';
                                imageElement.style.height = config.style.height || '100px';
                                imageElement.style.zIndex = el.zIndex || config.style.zIndex || this.getNextZIndex();
                                imageElement.style.transform = el.transform || config.style.transform || 'none';
                                imageElement.dataset.category = el.category || '';
                                imageElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                imageElement.innerHTML = config.innerHTML;
                                imageContainer.appendChild(imageElement);
                                this.setupImageElement(imageElement);
                                if (el.id !== 'certificate-of-yeaksa-member-editor-profileImage') {
                                    this.lastAddedImage = imageElement;
                                }
                            });
    
                            state.qrElements.forEach(el => {
                                if (document.getElementById(el.id)) return;
                                const config = this.defaultPositions.qrCode?.find(cfg => cfg.id === el.id);
                                if (!config) return; // Skip if no matching default config
                                const qrElement = document.createElement(config.tag);
                                qrElement.className = config.class;
                                qrElement.id = el.id || this.generateUniqueId('qr-element');
                                qrElement.style.position = 'absolute';
                                qrElement.style.left = el.left || config.style.left || '347px';
                                qrElement.style.top = el.top || config.style.top || '870px';
                                qrElement.style.width = config.style.width || '128px';
                                qrElement.style.height = config.style.height || '128px';
                                qrElement.style.zIndex = el.zIndex || config.style.zIndex || this.getNextZIndex();
                                qrElement.style.transform = el.transform || config.style.transform || 'none';
                                qrElement.dataset.category = el.category || '';
                                qrElement.dataset.useTransform = el.transform && el.transform.includes('translateX(-50%)') ? 'true' : 'false';
                                qrContainer.appendChild(qrElement);
                                this.setupQRElement(qrElement);
                                const customerLink = "https://arimako.com/business/<?php echo htmlspecialchars($contact->business_id); ?>/customer/<?php echo htmlspecialchars($contact->id); ?>";
                                const qrOptions = {
                                    text: customerLink,
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
                                    canvas.id = 'certificate-of-yeaksa-member-editor-customer_qr_canvas';
                                    canvas.style.display = 'block';
                                }
                            });
    
                            this.updateElementVisibility();
                            console.log('Position state loaded successfully');
                        } else {
                            console.log('No saved state found, initializing defaults');
                            this.initializeDefaultElements();
                        }
                        this.debugElementState();
                    } catch (err) {
                        console.error('Failed to load state:', err);
                        console.log('Falling back to default initialization');
                        this.initializeDefaultElements();
                        this.debugElementState();
                    }
                },
    
                initializeDefaultElements: function() {
                    if (this.categoryToggles.profileImage) this.initializeProfileImage();
                    if (this.categoryToggles.qrCode) this.initializeQRCode();
                    if (this.categoryToggles.certificateMemberpersonalDetails) this.initializeCategoryElements('certificateMemberpersonalDetails');
                    this.updateElementVisibility();
                    this.debouncedSave();
                },
    
                createElementFromConfig: function(config, container, setupFunction) {
                    const element = document.createElement(config.tag);
                    element.className = config.class;
                    element.id = config.id || this.generateUniqueId(config.class.match(/certificate-of-yeaksa-member-editor-(\w+)/)?.[1] || 'element');
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
                    element.dataset.category = config.class.match(/certificate-of-yeaksa-member-editor-category-(\w+)/)?.[1] || '';
                    element.dataset.useTransform = config.style.transform && config.style.transform.includes('translateX(-50%)') ? 'true' : 'false';
                    container.appendChild(element);
                    setupFunction(element);
                    return element;
                },
    
                initializeProfileImage: function() {
                    if (document.getElementById('certificate-of-yeaksa-member-editor-profileImage')) return;
                    const profileElement = this.createElementFromConfig(this.defaultPositions.profileImage, document.getElementById('certificate-of-yeaksa-member-editor-imageContainer'), this.setupImageElement.bind(this));
                    profileElement.style.display = this.categoryToggles.profileImage ? 'block' : 'none';
                    profileElement.style.opacity = this.categoryToggles.profileImage ? '1' : '0';
                    this.debouncedSave();
                },
    
                initializeQRCode: function() {
                    if (document.getElementById('certificate-of-yeaksa-member-editor-qrElement')) return;
                    const qrConfig = this.defaultPositions.qrCode[0];
                    const qrElement = this.createElementFromConfig(qrConfig, document.getElementById('certificate-of-yeaksa-member-editor-qrContainer'), this.setupQRElement.bind(this));
                    const customerLink = "https://arimako.com/business/<?php echo htmlspecialchars($contact->business_id); ?>/customer/<?php echo htmlspecialchars($contact->id); ?>";
                    const qrOptions = {
                        text: customerLink,
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
                        canvas.id = 'certificate-of-yeaksa-member-editor-customer_qr_canvas';
                        canvas.style.display = 'block';
                    }
                    qrElement.style.display = this.categoryToggles.qrCode ? 'block' : 'none';
                    qrElement.style.opacity = this.categoryToggles.qrCode ? '1' : '0';
                    this.debouncedSave();
                },
    
                initializeCategoryElements: function(category) {
                    if (!this.categoryToggles[category] || !this.defaultPositions[category]) return;
                    const textContainer = document.getElementById('certificate-of-yeaksa-member-editor-textContainer');
                    this.defaultPositions[category].forEach(config => {
                        if (document.getElementById(config.id)) return;
                        this.createElementFromConfig(config, textContainer, this.setupTextElement.bind(this));
                    });
                    this.updateElementVisibility();
                    this.debouncedSave();
                },
    
                updateElementVisibility: function() {
                    document.querySelectorAll('.certificate-of-yeaksa-member-editor-text-element, .certificate-of-yeaksa-member-editor-image-element, .certificate-of-yeaksa-member-editor-qr-element').forEach(el => {
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
                    if (!element.classList.contains('certificate-of-yeaksa-member-editor-image-element') && !element.classList.contains('certificate-of-yeaksa-member-editor-qr-element')) return;
                    const handles = [
                        { class: 'certificate-of-yeaksa-member-editor-resize-handle top-left', cursor: 'nw-resize' },
                        { class: 'certificate-of-yeaksa-member-editor-resize-handle top-right', cursor: 'ne-resize' },
                        { class: 'certificate-of-yeaksa-member-editor-resize-handle bottom-left', cursor: 'sw-resize' },
                        { class: 'certificate-of-yeaksa-member-editor-resize-handle bottom-right', cursor: 'se-resize' }
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
                    const handles = element.querySelectorAll('.certificate-of-yeaksa-member-editor-resize-handle');
                    handles.forEach(handle => handle.remove());
                },
    
                startDrag: function(e) {
                    if (e.target.classList.contains('certificate-of-yeaksa-member-editor-resize-handle') || this.isResizing) return;
                    e.preventDefault();
                    this.isDragging = true;
                    this.selectElement(e, e.currentTarget);
                    this.startX = e.clientX;
                    this.startY = e.clientY;
    
                    const container = document.querySelector('.certificate-of-yeaksa-member-editor-letter-page');
                    const { width, height, left: containerLeft, top: containerTop } = container.getBoundingClientRect();
    
                    const parsePos = (value, max) => {
                        if (value.includes('%')) {
                            return (parseFloat(value) / 100) * max;
                        }
                        return parseFloat(value) || 0;
                    };
    
                    this.startLeft = parsePos(this.selectedElement.style.left, width);
                    this.startTop = parsePos(this.selectedElement.style.top, height);
                    this.useTransform = this.selectedElement.dataset.useTransform === 'true';
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
                        const container = document.querySelector('.certificate-of-yeaksa-member-editor-letter-page');
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
                        const container = document.querySelector('.certificate-of-yeaksa-member-editor-letter-page');
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
    
                        if (this.selectedElement.classList.contains('certificate-of-yeaksa-member-editor-image-element') || 
                            this.selectedElement.classList.contains('certificate-of-yeaksa-member-editor-qr-element')) {
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
                        const deleteBtn = document.getElementById('certificate-of-yeaksa-member-editor-deleteElementBtn');
                        const bringForwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-bringForwardBtn');
                        const sendBackwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-sendBackwardBtn');
                        if (deleteBtn) deleteBtn.disabled = false;
                        if (bringForwardBtn) bringForwardBtn.disabled = false;
                        if (sendBackwardBtn) sendBackwardBtn.disabled = false;
    
                        const textInput = document.getElementById('certificate-of-yeaksa-member-editor-textInput');
                        const textColor = document.getElementById('certificate-of-yeaksa-member-editor-textColor');
                        const fontSizeSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontSizeSelect');
                        const customFontSize = document.getElementById('certificate-of-yeaksa-member-editor-customFontSize');
                        const fontStyleSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontStyleSelect');
    
                        if (this.selectedElement.classList.contains('certificate-of-yeaksa-member-editor-text-element')) {
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
                            if (fontStyleSelect) fontStyleSelect.value = this.selectedElement.style.fontFamily || "'Times New Roman', Moul, serif !important";
                        }
                    }
                },
    
                resetSelection: function() {
                    if (this.selectedElement) {
                        this.selectedElement.classList.remove('selected');
                        this.removeResizeHandles(this.selectedElement);
                        this.selectedElement = null;
                    }
                    const deleteBtn = document.getElementById('certificate-of-yeaksa-member-editor-deleteElementBtn');
                    const bringForwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-bringForwardBtn');
                    const sendBackwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-sendBackwardBtn');
                    if (deleteBtn) deleteBtn.disabled = true;
                    if (bringForwardBtn) bringForwardBtn.disabled = true;
                    if (sendBackwardBtn) sendBackwardBtn.disabled = true;
                },
    
                getNextZIndex: function() {
                    const elements = document.querySelectorAll('.certificate-of-yeaksa-member-editor-text-element, .certificate-of-yeaksa-member-editor-image-element, .certificate-of-yeaksa-member-editor-qr-element');
                    let maxZIndex = 1;
                    elements.forEach(el => {
                        const zIndex = parseInt(el.style.zIndex) || 1;
                        if (zIndex > maxZIndex) maxZIndex = zIndex;
                    });
                    return maxZIndex + 1;
                },
    
                resetTextInputs: function() {
                    const textInput = document.getElementById('certificate-of-yeaksa-member-editor-textInput');
                    const textColor = document.getElementById('certificate-of-yeaksa-member-editor-textColor');
                    const fontSizeSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontSizeSelect');
                    const customFontSize = document.getElementById('certificate-of-yeaksa-member-editor-customFontSize');
                    const fontStyleSelect = document.getElementById('certificate-of-yeaksa-member-editor-fontStyleSelect');
                    if (textInput) textInput.value = '';
                    if (textColor) textColor.value = '#000000';
                    if (fontSizeSelect) fontSizeSelect.value = '16';
                    if (customFontSize) {
                        customFontSize.value = '';
                        customFontSize.style.display = 'none';
                    }
                    if (fontStyleSelect) fontStyleSelect.value = "'Times New Roman', Moul, serif !important";
                    this.resetSelection();
                },
    
                saveState: function() {
                    const textElements = Array.from(document.querySelectorAll('.certificate-of-yeaksa-member-editor-text-element')).map(el => ({
                        id: el.id || this.generateUniqueId('text-element'),
                        category: el.dataset.category || '',
                        left: el.style.left || '50px',
                        top: el.style.top || '50px',
                        zIndex: el.style.zIndex || '1',
                        transform: el.style.transform || 'none'
                    }));
    
                    const imageElements = Array.from(document.querySelectorAll('.certificate-of-yeaksa-member-editor-image-element')).map(el => ({
                        id: el.id || this.generateUniqueId('image-element'),
                        category: el.dataset.category || '',
                        left: el.style.left || '347px',
                        top: el.style.top || '50px',
                        zIndex: el.style.zIndex || '1',
                        transform: el.style.transform || 'none'
                    }));
    
                    const qrElements = Array.from(document.querySelectorAll('.certificate-of-yeaksa-member-editor-qr-element')).map(el => ({
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
                        localStorage.setItem('yeaksaMemberCertificateEditorState', JSON.stringify(state));
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
                    const elements = document.querySelectorAll('.certificate-of-yeaksa-member-editor-text-element, .certificate-of-yeaksa-member-editor-image-element, .certificate-of-yeaksa-member-editor-qr-element');
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
    
            YeaksaMemberEditor.initializeEditor();
    
            const deleteBtn = document.getElementById('certificate-of-yeaksa-member-editor-deleteElementBtn');
            if (deleteBtn) {
                deleteBtn.onclick = () => {
                    if (YeaksaMemberEditor.selectedElement) {
                        YeaksaMemberEditor.removeResizeHandles(YeaksaMemberEditor.selectedElement);
                        YeaksaMemberEditor.selectedElement.remove();
                        YeaksaMemberEditor.selectedElement = null;
                        deleteBtn.disabled = true;
                        const bringForwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-bringForwardBtn');
                        const sendBackwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-sendBackwardBtn');
                        if (bringForwardBtn) bringForwardBtn.disabled = true;
                        if (sendBackwardBtn) sendBackwardBtn.disabled = true;
                        YeaksaMemberEditor.debouncedSave();
                    }
                };
            }
    
            const bringForwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-bringForwardBtn');
            if (bringForwardBtn) {
                bringForwardBtn.onclick = () => {
                    if (YeaksaMemberEditor.selectedElement) {
                        const currentZIndex = parseInt(YeaksaMemberEditor.selectedElement.style.zIndex) || 1;
                        YeaksaMemberEditor.selectedElement.style.zIndex = currentZIndex + 1;
                        YeaksaMemberEditor.debouncedSave();
                    }
                };
            }
    
            const sendBackwardBtn = document.getElementById('certificate-of-yeaksa-member-editor-sendBackwardBtn');
            if (sendBackwardBtn) {
                sendBackwardBtn.onclick = () => {
                    if (YeaksaMemberEditor.selectedElement) {
                        const currentZIndex = parseInt(YeaksaMemberEditor.selectedElement.style.zIndex) || 1;
                        if (currentZIndex > 1) {
                            YeaksaMemberEditor.selectedElement.style.zIndex = currentZIndex - 1;
                        }
                        YeaksaMemberEditor.debouncedSave();
                    }
                };
            }
        })();
    </script>
</body>
</html>