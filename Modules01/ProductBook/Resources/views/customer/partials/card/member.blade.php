<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Editor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Battambang&display=swap" rel="stylesheet">
</head>
<body>
<div class="letter_worked-container" style="display: flex; width: 100%; min-height: 100vh; margin: 0; padding: 0; box-sizing: border-box; gap: 20px;">
    <!-- Left Side (30%) - Light Blue Background -->
    <div class="left-side" style="width: 30%; background-color: #e6f3ff; padding: 10px; box-sizing: border-box; position: relative; border: 1px solid gray; border-radius: 10px;">
        <div style="text-align: center;">
            <h2 style="margin: 0 0 10px 0; color: #333;">EDIT</h2><hr>
            
            <!-- Collapsible Background Image Container -->
            <div class="background-input-container" style="margin-bottom: 15px;">
                <div class="background-input-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <i class="fas fa-image"></i>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="background-input-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label for="backgroundSelect" style="display: block; margin-bottom: 5px;">Select Background:</label>
                        <select id="backgroundSelect" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="/docs/images/customer_member.png">Default Image</option>
                            <option value="https://via.placeholder.com/800x600?text=Background+1">Background 1</option>
                            <option value="https://via.placeholder.com/800x600/FF0000/FFFFFF?text=Background+2">Background 2</option>
                            <option value="https://via.placeholder.com/800x600/00FF00/FFFFFF?text=Background+3">Background 3</option>
                        </select>
                    </div>
                    <button id="changeBackgroundBtn" style="width: 100%; padding: 10px; background-color: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Apply Background</button>
                </div>
            </div>

            <!-- Collapsible Insert Text Container -->
            <div class="insert-text-container" style="margin-bottom: 15px;">
                <div class="insert-text-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <i class="fas fa-font"></i>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="insert-text-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label for="insertTextInput" style="display: block; margin-bottom: 5px;">Text Content:</label>
                        <input type="text" id="insertTextInput" placeholder="Enter text to insert" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                    </div>
                    <div style="margin: 15px 0; display: flex; align-items: center;">
                        <label for="insertColorInput" style="margin-right: 10px;">Color:</label>
                        <input type="color" id="insertColorInput" value="#000000" style="width: 40px; height: 40px;">
                    </div>
                    <div style="margin: 15px 0;">
                        <label for="insertSizeInput" style="display: block; margin-bottom: 5px;">Size (px):</label>
                        <input type="range" id="insertSizeInput" min="10" max="72" value="20" style="width: 100%;">
                        <span id="insertSizeValue" style="display: inline-block; margin-top: 5px;">20px</span>
                    </div>
                    <div style="margin: 15px 0;">
                        <label for="insertFontInput" style="display: block; margin-bottom: 5px;">Font:</label>
                        <select id="insertFontInput" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Khmer OS Battambang">Khmer OS Battambang</option>
                        </select>
                    </div>
                    <button id="insertTextBtn" style="width: 100%; padding: 10px; background-color: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Insert Text</button>
                </div>
            </div>

            <!-- Collapsible Image and Icons Container -->
            <div class="image-icons-container" style="margin-bottom: 15px;">
                <div class="image-icons-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <i class="fas fa-icons"></i>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="image-icons-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label style="display: block; margin-bottom: 5px;">Select Font Awesome Icon:</label>
                        <div id="iconGrid" style="display: grid; grid-template-columns: repeat(10, 1fr); gap: 5px; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <button id="addIconBtn" style="width: 100%; padding: 10px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Add Icon</button>
                    <div style="margin: 15px 0;">
                        <label for="imageUpload" style="display: block; margin-bottom: 5px;">Upload Image:</label>
                        <input type="file" id="imageUpload" accept="image/*" style="width: 100%; padding: 8px;">
                    </div>
                    <button id="addImageBtn" style="width: 100%; padding: 10px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Add Image</button>
                </div>
            </div>

            <!-- Collapsible Sticker and Frame Container -->
            <div class="sticker-frame-container" style="margin-bottom: 15px;">
                <div class="sticker-frame-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <i class="fas fa-stamp"></i>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="sticker-frame-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label for="stickerSelect" style="display: block; margin-bottom: 5px;">Select Sticker:</label>
                        <select id="stickerSelect" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="https://via.placeholder.com/100?text=Sticker1">Sticker 1</option>
                            <option value="https://via.placeholder.com/100?text=Sticker2">Sticker 2</option>
                            <option value="https://via.placeholder.com/100?text=Sticker3">Sticker 3</option>
                        </select>
                    </div>
                    <button id="addStickerBtn" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Add Sticker</button>
                    <div style="margin: 15px 0;">
                        <label for="frameSelect" style="display: block; margin-bottom: 5px;">Select Frame:</label>
                        <select id="frameSelect" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="">No Frame</option>
                            <option value="https://via.placeholder.com/800x600/000000/FFFFFF?text=Frame1">Frame 1</option>
                            <option value="https://via.placeholder.com/800x600/000000/FFFFFF?text=Frame2">Frame 2</option>
                        </select>
                    </div>
                    <button id="applyFrameBtn" style="width: 100%; padding: 10px; background-color: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Apply Frame</button>
                </div>
            </div>

            <!-- Collapsible Edit Controls Container -->
            <div class="edit-controls-container" style="margin-bottom: 15px;">
                <div class="edit-controls-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <i class="fas fa-edit"></i>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="edit-controls-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div class="edit-controls" style="margin: 15px 0; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                        <div style="width: 100%;">
                            <label for="fontSizeSlider" style="display: block; margin-bottom: 5px;">Font Size (px):</label>
                            <input type="range" id="fontSizeSlider" min="12" max="100" value="20" style="width: 100%;">
                            <span id="fontSizeSliderValue" style="display: inline-block; margin-top: 5px;">20px</span>
                        </div>
                        <div style="width: 100%;">
                            <label for="fontStyleSelect" style="display: block; margin-bottom: 5px;">Font Style:</label>
                            <select id="fontStyleSelect" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Tahoma">Tahoma</option>
                                <option value="Khmer OS Battambang">Khmer OS Battambang</option>
                            </select>
                        </div>
                        <div style="width: 100%;">
                            <label for="resizeSlider" style="display: block; margin-bottom: 5px;">Image/Icon Width (px):</label>
                            <input type="range" id="resizeSlider" min="50" max="300" value="100" style="width: 100%;">
                            <span id="resizeSliderValue" style="display: inline-block; margin-top: 5px;">100px</span>
                        </div>
                        <button id="fontColorBtn" style="padding: 10px; background-color: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-palette"></i> Font Color</button>
                    </div>
                </div>
            </div>
            
            <!-- Download Button -->
            <a id="downloadLink" href="#" download="edited_customer_member.png">
                <button style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Download Image</button>
            </a>
        </div>
    </div>

    <!-- Right Side (70%) -->
    <div class="right-side" style="width: 70%; padding: 10px; margin: 0; position: relative; box-sizing: border-box; border: 1px solid gray; border-radius: 10px;">
        <div style="position: relative; width: 100%; height: 100vh; overflow: hidden;">
            <img id="backgroundImage" src="/docs/images/customer_member.png" style="width: 100%; height: 100%; object-fit: contain; display: block;">
            <div id="textContainer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>
            <div id="frameContainer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>
        </div>
    </div>
</div>

<style>
    .background-input-container.active .background-input-content,
    .insert-text-container.active .insert-text-content,
    .image-icons-container.active .image-icons-content,
    .sticker-frame-container.active .sticker-frame-content,
    .edit-controls-container.active .edit-controls-content {
        max-height: 500px !important;
        padding: 10px;
    }
    .background-input-container.active .arrow,
    .insert-text-container.active .arrow,
    .image-icons-container.active .arrow,
    .sticker-frame-container.active .arrow,
    .edit-controls-container.active .arrow {
        transform: rotate(180deg);
    }
    .draggable-text, .draggable-icon, .draggable-image, .draggable-sticker {
        position: absolute;
        cursor: move;
        user-select: none;
        pointer-events: auto;
    }
    .draggable-text.selected, .draggable-icon.selected, .draggable-image.selected, .draggable-sticker.selected {
        border: 2px dashed #ff00ff;
    }
    .draggable-icon, .draggable-image, .draggable-sticker {
        resize: both;
        overflow: auto;
        max-width: 300px;
        max-height: 300px;
        min-width: 50px;
        min-height: 50px;
    }
    .draggable-icon i {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: inherit;
    }
    .edit-controls button:disabled,
    .edit-controls input:disabled,
    .edit-controls select:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .frame-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
    }
    .icon-grid-item {
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    .icon-grid-item:hover {
        background-color: #f0f0f0;
    }
    .icon-grid-item i {
        font-size: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadLink = document.getElementById('downloadLink');
        const textContainer = document.getElementById('textContainer');
        const frameContainer = document.getElementById('frameContainer');
        const backgroundImage = document.getElementById('backgroundImage');
        const backgroundInputContainer = document.querySelector('.background-input-container');
        const backgroundInputHeader = document.querySelector('.background-input-header');
        const backgroundSelect = document.getElementById('backgroundSelect');
        const changeBackgroundBtn = document.getElementById('changeBackgroundBtn');
        const insertTextContainer = document.querySelector('.insert-text-container');
        const insertTextHeader = document.querySelector('.insert-text-header');
        const insertTextInput = document.getElementById('insertTextInput');
        const insertColorInput = document.getElementById('insertColorInput');
        const insertSizeInput = document.getElementById('insertSizeInput');
        const insertSizeValue = document.getElementById('insertSizeValue');
        const insertFontInput = document.getElementById('insertFontInput');
        const insertTextBtn = document.getElementById('insertTextBtn');
        const imageIconsContainer = document.querySelector('.image-icons-container');
        const imageIconsHeader = document.querySelector('.image-icons-header');
        const iconGrid = document.getElementById('iconGrid');
        const addIconBtn = document.getElementById('addIconBtn');
        const imageUpload = document.getElementById('imageUpload');
        const addImageBtn = document.getElementById('addImageBtn');
        const stickerFrameContainer = document.querySelector('.sticker-frame-container');
        const stickerFrameHeader = document.querySelector('.sticker-frame-header');
        const stickerSelect = document.getElementById('stickerSelect');
        const addStickerBtn = document.getElementById('addStickerBtn');
        const frameSelect = document.getElementById('frameSelect');
        const applyFrameBtn = document.getElementById('applyFrameBtn');
        const editControlsContainer = document.querySelector('.edit-controls-container');
        const editControlsHeader = document.querySelector('.edit-controls-header');
        const fontSizeSlider = document.getElementById('fontSizeSlider');
        const fontSizeSliderValue = document.getElementById('fontSizeSliderValue');
        const fontStyleSelect = document.getElementById('fontStyleSelect');
        const fontColorBtn = document.getElementById('fontColorBtn');
        const resizeSlider = document.getElementById('resizeSlider');
        const resizeSliderValue = document.getElementById('resizeSliderValue');

        let isDragging = false;
        let offsetX, offsetY;
        let currentElement = null;
        let zIndexCounter = 10;
        let selectedIconClass = null;

        // List of 200 Font Awesome icons
        const iconClasses = [
            'fas fa-star', 'fas fa-heart', 'fas fa-smile', 'fas fa-camera', 'fas fa-music', 'fas fa-tree', 'fas fa-cloud', 'fas fa-sun', 'fas fa-moon', 'fas fa-bolt',
            'fas fa-car', 'fas fa-plane', 'fas fa-rocket', 'fas fa-ship', 'fas fa-bicycle', 'fas fa-bus', 'fas fa-train', 'fas fa-taxi', 'fas fa-truck', 'fas fa-ambulance',
            'fas fa-home', 'fas fa-building', 'fas fa-school', 'fas fa-hospital', 'fas fa-church', 'fas fa-mosque', 'fas fa-synagogue', 'fas fa-landmark', 'fas fa-city', 'fas fa-warehouse',
            'fas fa-dog', 'fas fa-cat', 'fas fa-fish', 'fas fa-horse', 'fas fa-crow', 'fas fa-spider', 'fas fa-bug', 'fas fa-dove', 'fas fa-dragon', 'fas fa-hippo',
            'fas fa-apple-alt', 'fas fa-carrot', 'fas fa-cheese', 'fas fa-egg', 'fas fa-bread-slice', 'fas fa-pizza-slice', 'fas fa-hamburger', 'fas fa-hotdog', 'fas fa-ice-cream', 'fas fa-cookie',
            'fas fa-book', 'fas fa-pen', 'fas fa-pencil-alt', 'fas fa-eraser', 'fas fa-ruler', 'fas fa-compass', 'fas fa-globe', 'fas fa-map', 'fas fa-atlas', 'fas fa-book-open',
            'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-desktop', 'fas fa-tablet-alt', 'fas fa-headphones', 'fas fa-microphone', 'fas fa-camera-retro', 'fas fa-tv', 'fas fa-gamepad', 'fas fa-keyboard',
            'fas fa-gift', 'fas fa-trophy', 'fas fa-medal', 'fas fa-crown', 'fas fa-ring', 'fas fa-gem', 'fas fa-star-of-life', 'fas fa-peace', 'fas fa-yin-yang', 'fas fa-infinity',
            'fas fa-leaf', 'fas fa-flower', 'fas fa-seedling', 'fas fa-tree', 'fas fa-mountain', 'fas fa-water', 'fas fa-fire', 'fas fa-wind', 'fas fa-snowflake', 'fas fa-cloud-rain',
            'fas fa-lock', 'fas fa-unlock', 'fas fa-key', 'fas fa-shield-alt', 'fas fa-door-open', 'fas fa-door-closed', 'fas fa-bell', 'fas fa-exclamation', 'fas fa-question', 'fas fa-info',
            'fas fa-eye', 'fas fa-eye-slash', 'fas fa-handshake', 'fas fa-hand-holding-heart', 'fas fa-hand-peace', 'fas fa-hand-point-up', 'fas fa-hand-point-down', 'fas fa-hand-point-left', 'fas fa-hand-point-right', 'fas fa-thumbs-up',
            'fas fa-user', 'fas fa-users', 'fas fa-user-plus', 'fas fa-user-minus', 'fas fa-user-circle', 'fas fa-user-tie', 'fas fa-user-astronaut', 'fas fa-user-ninja', 'fas fa-user-secret', 'fas fa-user-md',
            'fas fa-heartbeat', 'fas fa-stethoscope', 'fas fa-syringe', 'fas fa-pills', 'fas fa-band-aid', 'fas fa-thermometer', 'fas fa-dna', 'fas fa-crutch', 'fas fa-wheelchair', 'fas fa-ambulance',
            'fas fa-coffee', 'fas fa-wine-glass', 'fas fa-beer', 'fas fa-cocktail', 'fas fa-glass-martini', 'fas fa-mug-hot', 'fas fa-wine-bottle', 'fas fa-utensils', 'fas fa-blender', 'fas fa-birthday-cake',
            'fas fa-futbol', 'fas fa-basketball-ball', 'fas fa-football-ball', 'fas fa-volleyball-ball', 'fas fa-baseball-ball', 'fas fa-golf-ball', 'fas fa-hockey-puck', 'fas fa-table-tennis', 'fas fa-bowling-ball', 'fas fa-dumbbell',
            'fas fa-paint-brush', 'fas fa-palette', 'fas fa-theater-masks', 'fas fa-music', 'fas fa-guitar', 'fas fa-drum', 'fas fa-piano', 'fas fa-violin', 'fas fa-camera', 'fas fa-video',
            'fas fa-anchor', 'fas fa-life-ring', 'fas fa-umbrella', 'fas fa-parachute-box', 'fas fa-suitcase', 'fas fa-briefcase', 'fas fa-backpack', 'fas fa-luggage-cart', 'fas fa-ticket-alt', 'fas fa-passport',
            'fas fa-lightbulb', 'fas fa-battery-full', 'fas fa-plug', 'fas fa-solar-panel', 'fas fa-wifi', 'fas fa-satellite', 'fas fa-robot', 'fas fa-microchip', 'fas fa-cogs', 'fas fa-tools'
        ];

        // Populate icon grid
        iconClasses.forEach(iconClass => {
            const iconItem = document.createElement('div');
            iconItem.className = 'icon-grid-item';
            iconItem.innerHTML = `<i class="${iconClass}"></i>`;
            iconItem.addEventListener('click', function() {
                selectedIconClass = iconClass;
                document.querySelectorAll('.icon-grid-item').forEach(item => item.style.backgroundColor = '');
                iconItem.style.backgroundColor = '#d4e6ff';
            });
            iconGrid.appendChild(iconItem);
        });

        backgroundInputHeader.addEventListener('click', function() {
            backgroundInputContainer.classList.toggle('active');
        });

        insertTextHeader.addEventListener('click', function() {
            insertTextContainer.classList.toggle('active');
        });

        imageIconsHeader.addEventListener('click', function() {
            imageIconsContainer.classList.toggle('active');
        });

        stickerFrameHeader.addEventListener('click', function() {
            stickerFrameContainer.classList.toggle('active');
        });

        editControlsHeader.addEventListener('click', function() {
            editControlsContainer.classList.toggle('active');
        });

        insertSizeInput.addEventListener('input', function() {
            insertSizeValue.textContent = this.value + 'px';
        });

        fontSizeSlider.addEventListener('input', function() {
            fontSizeSliderValue.textContent = this.value + 'px';
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                currentElement.style.fontSize = `${this.value}px`;
                updateDownloadLink();
            }
        });

        fontStyleSelect.addEventListener('change', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                currentElement.style.fontFamily = this.value;
                updateDownloadLink();
            }
        });

        resizeSlider.addEventListener('input', function() {
            resizeSliderValue.textContent = this.value + 'px';
            if (currentElement && (currentElement.classList.contains('draggable-icon') || currentElement.classList.contains('draggable-image') || currentElement.classList.contains('draggable-sticker'))) {
                const aspectRatio = parseFloat(currentElement.dataset.aspectRatio) || 1;
                const newWidth = parseFloat(this.value);
                const newHeight = newWidth / aspectRatio;
                currentElement.style.width = `${newWidth}px`;
                currentElement.style.height = `${newHeight}px`;
                if (currentElement.classList.contains('draggable-icon')) {
                    currentElement.style.fontSize = `${newWidth}px`;
                }
                updateDownloadLink();
            }
        });

        backgroundImage.onload = function() {
            updateDownloadLink();
        };

        if (backgroundImage.complete) {
            backgroundImage.onload();
        }

        insertTextBtn.addEventListener('click', function() {
            addText(insertTextInput, insertColorInput, insertSizeInput, insertFontInput);
            insertTextInput.value = '';
        });

        function addText(input, color, size, font) {
            const text = input.value.trim();
            if (!text) return;

            const textElement = document.createElement('div');
            textElement.className = 'draggable-text';
            textElement.textContent = text;
            textElement.style.color = color.value;
            textElement.style.fontSize = size.value + 'px';
            textElement.style.fontFamily = font.value;
            textElement.style.position = 'absolute';
            textElement.style.left = '50px';
            textElement.style.top = '50px';
            textElement.style.zIndex = zIndexCounter++;

            textElement.addEventListener('mousedown', startDrag);
            textElement.addEventListener('click', selectElement);

            textContainer.appendChild(textElement);
            updateDownloadLink();
        }

        changeBackgroundBtn.addEventListener('click', function() {
            const newBackground = backgroundSelect.value;
            backgroundImage.src = newBackground;
            backgroundImage.onload = function() {
                updateDownloadLink();
            };
        });

        addIconBtn.addEventListener('click', function() {
            if (selectedIconClass) {
                addIconElement(selectedIconClass);
            }
        });

        function addIconElement(iconClass) {
            const iconElement = document.createElement('div');
            iconElement.className = 'draggable-icon';
            iconElement.style.position = 'absolute';
            iconElement.style.left = '50px';
            iconElement.style.top = '50px';
            iconElement.style.zIndex = zIndexCounter++;
            iconElement.style.width = '50px';
            iconElement.style.height = '50px';
            iconElement.style.color = '#000000';
            iconElement.style.fontSize = '50px';
            iconElement.dataset.aspectRatio = '1'; // Icons are square by default

            const icon = document.createElement('i');
            icon.className = iconClass;
            iconElement.appendChild(icon);

            iconElement.addEventListener('mousedown', startDrag);
            iconElement.addEventListener('click', selectElement);
            iconElement.addEventListener('resize', updateDownloadLink);

            textContainer.appendChild(iconElement);
            updateDownloadLink();
        }

        addImageBtn.addEventListener('click', function() {
            const file = imageUpload.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    const aspectRatio = img.width / img.height;
                    const imgElement = document.createElement('div');
                    imgElement.className = 'draggable-image';
                    imgElement.style.position = 'absolute';
                    imgElement.style.left = '50px';
                    imgElement.style.top = '50px';
                    imgElement.style.zIndex = zIndexCounter++;
                    imgElement.style.width = '100px';
                    imgElement.style.height = `${100 / aspectRatio}px`;
                    imgElement.dataset.aspectRatio = aspectRatio;

                    const imgTag = document.createElement('img');
                    imgTag.src = e.target.result;
                    imgTag.style.width = '100%';
                    imgTag.style.height = '100%';
                    imgTag.style.objectFit = 'contain';

                    imgElement.appendChild(imgTag);
                    imgElement.addEventListener('mousedown', startDrag);
                    imgElement.addEventListener('click', selectElement);
                    imgElement.addEventListener('resize', updateDownloadLink);

                    textContainer.appendChild(imgElement);
                    updateDownloadLink();
                    imageUpload.value = '';
                };
            };
            reader.readAsDataURL(file);
        });

        addStickerBtn.addEventListener('click', function() {
            const stickerSrc = stickerSelect.value;
            addImageElement(stickerSrc, 'draggable-sticker');
        });

        function addImageElement(src, className) {
            const img = new Image();
            img.src = src;
            img.onload = function() {
                const aspectRatio = img.width / img.height;
                const imgElement = document.createElement('div');
                imgElement.className = className;
                imgElement.style.position = 'absolute';
                imgElement.style.left = '50px';
                imgElement.style.top = '50px';
                imgElement.style.zIndex = zIndexCounter++;
                imgElement.style.width = '100px';
                imgElement.style.height = `${100 / aspectRatio}px`;
                imgElement.dataset.aspectRatio = aspectRatio;

                const imgTag = document.createElement('img');
                imgTag.src = src;
                imgTag.style.width = '100%';
                imgTag.style.height = '100%';
                imgTag.style.objectFit = 'contain';

                imgElement.appendChild(imgTag);
                imgElement.addEventListener('mousedown', startDrag);
                imgElement.addEventListener('click', selectElement);
                imgElement.addEventListener('resize', updateDownloadLink);

                textContainer.appendChild(imgElement);
                updateDownloadLink();
            };
        }

        applyFrameBtn.addEventListener('click', function() {
            const frameSrc = frameSelect.value;
            frameContainer.innerHTML = ''; // Clear existing frame
            if (frameSrc) {
                const frameElement = document.createElement('img');
                frameElement.className = 'frame-image';
                frameElement.src = frameSrc;
                frameContainer.appendChild(frameElement);
            }
            updateDownloadLink();
        });

        fontColorBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                const colorPicker = document.createElement('input');
                colorPicker.type = 'color';
                colorPicker.value = currentElement.style.color || '#000000';
                colorPicker.addEventListener('change', function() {
                    currentElement.style.color = this.value;
                    updateDownloadLink();
                });
                colorPicker.click();
            }
        });

        function selectElement(e) {
            e.stopPropagation();
            if (currentElement) {
                currentElement.classList.remove('selected');
            }
            currentElement = e.target.closest('.draggable-text') || e.target.closest('.draggable-icon') || e.target.closest('.draggable-image') || e.target.closest('.draggable-sticker');
            if (currentElement) {
                currentElement.classList.add('selected');
                updateButtonStates();
                if (currentElement.classList.contains('draggable-text')) {
                    fontSizeSlider.value = parseFloat(currentElement.style.fontSize) || 20;
                    fontSizeSliderValue.textContent = fontSizeSlider.value + 'px';
                    fontStyleSelect.value = currentElement.style.fontFamily.replace(/"/g, '') || 'Times New Roman';
                } else if (currentElement.classList.contains('draggable-icon') || currentElement.classList.contains('draggable-image') || currentElement.classList.contains('draggable-sticker')) {
                    resizeSlider.value = parseFloat(currentElement.style.width) || 100;
                    resizeSliderValue.textContent = resizeSlider.value + 'px';
                }
            } else {
                updateButtonStates();
            }
        }

        function startDrag(e) {
            currentElement = e.target.closest('.draggable-text') || e.target.closest('.draggable-icon') || e.target.closest('.draggable-image') || e.target.closest('.draggable-sticker');
            if (!currentElement) return;

            isDragging = true;
            const rect = currentElement.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;

            currentElement.style.zIndex = zIndexCounter++;
            currentElement.classList.add('selected');
            updateButtonStates();

            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);

            e.preventDefault();
        }

        function drag(e) {
            if (!isDragging || !currentElement) return;

            const containerRect = textContainer.getBoundingClientRect();
            let x = e.clientX - containerRect.left - offsetX;
            let y = e.clientY - containerRect.top - offsetY;

            const elementWidth = currentElement.offsetWidth;
            const elementHeight = currentElement.offsetHeight;

            x = Math.max(0, Math.min(x, containerRect.width - elementWidth));
            y = Math.max(0, Math.min(y, containerRect.height - elementHeight));

            currentElement.style.left = x + 'px';
            currentElement.style.top = y + 'px';
        }

        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
            updateDownloadLink();
        }

        function updateButtonStates() {
            const isText = currentElement && currentElement.classList.contains('draggable-text');
            const isImage = currentElement && (currentElement.classList.contains('draggable-icon') || currentElement.classList.contains('draggable-image') || currentElement.classList.contains('draggable-sticker'));

            fontSizeSlider.disabled = !isText;
            fontStyleSelect.disabled = !isText;
            fontColorBtn.disabled = !isText;
            resizeSlider.disabled = !isImage;
        }

        async function updateDownloadLink() {
            const canvas = document.createElement('canvas');
            const img = backgroundImage;

            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const elements = textContainer.querySelectorAll('.draggable-text, .draggable-icon, .draggable-image, .draggable-sticker');
            for (const element of elements) {
                const containerRect = textContainer.getBoundingClientRect();
                const x = (parseFloat(element.style.left) / containerRect.width) * canvas.width;
                const y = (parseFloat(element.style.top) / containerRect.height) * canvas.height;

                if (element.classList.contains('draggable-text')) {
                    const style = window.getComputedStyle(element);
                    const color = style.color;
                    const fontSize = parseFloat(style.fontSize) * (img.naturalWidth / containerRect.width);
                    const fontFamily = style.fontFamily.replace(/"/g, '');
                    const text = element.textContent;

                    ctx.font = `${fontSize}px ${fontFamily}`;
                    ctx.fillStyle = color;
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'top';
                    ctx.fillText(text, x, y);
                } else if (element.classList.contains('draggable-icon')) {
                    const icon = element.querySelector('i');
                    const iconClass = icon.className;
                    const width = parseFloat(element.style.width) * (img.naturalWidth / containerRect.width);
                    const aspectRatio = parseFloat(element.dataset.aspectRatio) || 1;
                    const height = width / aspectRatio;
                    const color = element.style.color || '#000000';

                    // Convert Font Awesome icon to SVG
                    const svgData = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="${width}" height="${height}">
                        <foreignObject width="100%" height="100%">
                            <i class="${iconClass}" style="color: ${color}; font-size: ${width}px;"></i>
                        </foreignObject>
                    </svg>`;
                    const imgElement = new Image();
                    imgElement.src = 'data:image/svg+xml;base64,' + btoa(svgData);
                    await new Promise(resolve => { imgElement.onload = resolve; });
                    ctx.drawImage(imgElement, x, y, width, height);
                } else if (element.classList.contains('draggable-image') || element.classList.contains('draggable-sticker')) {
                    const imgElement = element.querySelector('img');
                    const width = (element.offsetWidth / containerRect.width) * canvas.width;
                    const aspectRatio = parseFloat(element.dataset.aspectRatio) || 1;
                    const height = width / aspectRatio;
                    ctx.drawImage(imgElement, x, y, width, height);
                }
            }

            const frameElement = frameContainer.querySelector('.frame-image');
            if (frameElement) {
                ctx.drawImage(frameElement, 0, 0, canvas.width, canvas.height);
            }

            downloadLink.href = canvas.toDataURL('image/png');
        }

        // Initial button state
        updateButtonStates();
    });
</script>
</body>
</html>