<div class="letter_worked-container" style="display: flex; width: 100%; min-height: 100vh; margin: 0; padding: 0; box-sizing: border-box; gap: 20px;">
    <!-- Left Side (30%) - Light Blue Background -->
    <div class="left-side" style="width: 30%; background-color: #e6f3ff; padding: 10px; box-sizing: border-box; position: relative; border: 1px solid gray; border-radius: 10px;">
        <div style="text-align: center;">
            <h2 style="margin: 0 0 10px 0; color: #333;">EDIT</h2><hr>
            
            <!-- Collapsible Text Input Container -->
            <div class="text-input-container" style="margin-bottom: 15px;">
                <div class="text-input-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span>Add Text</span>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="text-input-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label for="textInput" style="display: block; margin-bottom: 5px;">Text Content:</label>
                        <input type="text" id="textInput" placeholder="Enter your text here" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                    </div>
                    <div style="margin: 15px 0; display: flex; align-items: center;">
                        <label for="colorInput" style="margin-right: 10px;">Color:</label>
                        <input type="color" id="colorInput" value="#000000" style="width: 40px; height: 40px;">
                    </div>
                    <div style="margin: 15px 0;">
                        <label for="sizeInput" style="display: block; margin-bottom: 5px;">Size (px):</label>
                        <input type="range" id="sizeInput" min="10" max="72" value="20" style="width: 100%;">
                        <span id="sizeValue" style="display: inline-block; margin-top: 5px;">20px</span>
                    </div>
                    <div style="margin: 15px 0;">
                        <label for="fontInput" style="display: block; margin-bottom: 5px;">Font:</label>
                        <select id="fontInput" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="Arial">Arial</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Verdana">Verdana</option>
                        </select>
                    </div>
                    <button id="submitBtn" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Add Text to Image</button>
                </div>
            </div>

            <!-- Collapsible Background Image Container -->
            <div class="background-input-container" style="margin-bottom: 15px;">
                <div class="background-input-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span>Change Background</span>
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

            <!-- Collapsible More Image Container -->
            <div class="more-image-container" style="margin-bottom: 15px;">
                <div class="more-image-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span>More Image</span>
                    <span class="arrow" style="transition: transform 0.3s;">▼</span>
                </div>
                <div class="more-image-content" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <div style="margin: 15px 0;">
                        <label for="imageUpload" style="display: block; margin-bottom: 5px;">Upload Image (Logo/Button/Sticker):</label>
                        <input type="file" id="imageUpload" accept="image/*" style="width: 100%; padding: 8px;">
                    </div>
                    <button id="addImageBtn" style="width: 100%; padding: 10px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Add Image</button>
                </div>
            </div>

            <!-- Collapsible Insert Text Container -->
            <div class="insert-text-container" style="margin-bottom: 15px;">
                <div class="insert-text-header" style="padding: 10px; background-color: #d4e6ff; border-radius: 5px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <span>Insert Text</span>
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
                            <option value="Arial">Arial</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Verdana">Verdana</option>
                        </select>
                    </div>
                    <button id="insertTextBtn" style="width: 100%; padding: 10px; background-color: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 15px 0; transition: background-color 0.2s;">Insert Text</button>
                </div>
            </div>

            <!-- Edit Controls -->
            <div class="edit-controls" style="margin: 15px 0; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                <button id="deleteBtn" style="padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-trash"></i> Delete</button>
                <button id="editBtn" style="padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-edit"></i> Edit</button>
                <button id="resizeBtn" style="padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-expand"></i> Resize</button>
                <button id="fontSizeBtn" style="padding: 10px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-text-height"></i> Font Size</button>
                <button id="fontColorBtn" style="padding: 10px; background-color: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-palette"></i> Font Color</button>
                <button id="fontStyleBtn" style="padding: 10px; background-color: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px;"><i class="fas fa-font"></i> Font Style</button>
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
        </div>
    </div>
</div>

<style>
    .text-input-container.active .text-input-content,
    .background-input-container.active .background-input-content,
    .more-image-container.active .more-image-content,
    .insert-text-container.active .insert-text-content {
        max-height: 500px !important;
        padding: 10px;
    }
    .text-input-container.active .arrow,
    .background-input-container.active .arrow,
    .more-image-container.active .arrow,
    .insert-text-container.active .arrow {
        transform: rotate(180deg);
    }
    .draggable-text, .draggable-image {
        position: absolute;
        cursor: move;
        user-select: none;
        pointer-events: auto;
    }
    .draggable-text.selected, .draggable-image.selected {
        border: 2px dashed #ff00ff;
    }
    .draggable-image {
        resize: both;
        overflow: auto;
        max-width: 300px;
        max-height: 300px;
        min-width: 50px;
        min-height: 50px;
    }
    .edit-controls button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textInput = document.getElementById('textInput');
        const colorInput = document.getElementById('colorInput');
        const sizeInput = document.getElementById('sizeInput');
        const sizeValue = document.getElementById('sizeValue');
        const fontInput = document.getElementById('fontInput');
        const submitBtn = document.getElementById('submitBtn');
        const downloadLink = document.getElementById('downloadLink');
        const textContainer = document.getElementById('textContainer');
        const backgroundImage = document.getElementById('backgroundImage');
        const textInputContainer = document.querySelector('.text-input-container');
        const textInputHeader = document.querySelector('.text-input-header');
        const backgroundInputContainer = document.querySelector('.background-input-container');
        const backgroundInputHeader = document.querySelector('.background-input-header');
        const backgroundSelect = document.getElementById('backgroundSelect');
        const changeBackgroundBtn = document.getElementById('changeBackgroundBtn');
        const moreImageContainer = document.querySelector('.more-image-container');
        const moreImageHeader = document.querySelector('.more-image-header');
        const imageUpload = document.getElementById('imageUpload');
        const addImageBtn = document.getElementById('addImageBtn');
        const insertTextContainer = document.querySelector('.insert-text-container');
        const insertTextHeader = document.querySelector('.insert-text-header');
        const insertTextInput = document.getElementById('insertTextInput');
        const insertColorInput = document.getElementById('insertColorInput');
        const insertSizeInput = document.getElementById('insertSizeInput');
        const insertSizeValue = document.getElementById('insertSizeValue');
        const insertFontInput = document.getElementById('insertFontInput');
        const insertTextBtn = document.getElementById('insertTextBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const editBtn = document.getElementById('editBtn');
        const resizeBtn = document.getElementById('resizeBtn');
        const fontSizeBtn = document.getElementById('fontSizeBtn');
        const fontColorBtn = document.getElementById('fontColorBtn');
        const fontStyleBtn = document.getElementById('fontStyleBtn');
        
        let isDragging = false;
        let offsetX, offsetY;
        let currentElement = null;
        let zIndexCounter = 10;
        
        textInputHeader.addEventListener('click', function() {
            textInputContainer.classList.toggle('active');
        });

        backgroundInputHeader.addEventListener('click', function() {
            backgroundInputContainer.classList.toggle('active');
        });

        moreImageHeader.addEventListener('click', function() {
            moreImageContainer.classList.toggle('active');
        });

        insertTextHeader.addEventListener('click', function() {
            insertTextContainer.classList.toggle('active');
        });

        sizeInput.addEventListener('input', function() {
            sizeValue.textContent = this.value + 'px';
        });

        insertSizeInput.addEventListener('input', function() {
            insertSizeValue.textContent = this.value + 'px';
        });

        backgroundImage.onload = function() {
            updateDownloadLink();
        };

        if (backgroundImage.complete) {
            backgroundImage.onload();
        }

        submitBtn.addEventListener('click', function() {
            addText(textInput, colorInput, sizeInput, fontInput);
            textInput.value = '';
        });

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

        addImageBtn.addEventListener('click', function() {
            const file = imageUpload.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const imgElement = document.createElement('div');
                imgElement.className = 'draggable-image';
                imgElement.style.position = 'absolute';
                imgElement.style.left = '50px';
                imgElement.style.top = '50px';
                imgElement.style.zIndex = zIndexCounter++;
                imgElement.style.width = '100px';
                imgElement.style.height = '100px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';

                imgElement.appendChild(img);
                imgElement.addEventListener('mousedown', startDrag);
                imgElement.addEventListener('click', selectElement);
                imgElement.addEventListener('resize', updateDownloadLink);

                textContainer.appendChild(imgElement);
                updateDownloadLink();
                imageUpload.value = '';
            };
            reader.readAsDataURL(file);
        });

        deleteBtn.addEventListener('click', function() {
            if (currentElement) {
                currentElement.remove();
                currentElement = null;
                updateDownloadLink();
            }
        });

        editBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                const newText = prompt('Edit text:', currentElement.textContent);
                if (newText) {
                    currentElement.textContent = newText;
                    updateDownloadLink();
                }
            }
        });

        resizeBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-image')) {
                const newWidth = prompt('New width (px):', currentElement.style.width.replace('px', ''));
                const newHeight = prompt('New height (px):', currentElement.style.height.replace('px', ''));
                if (newWidth && newHeight) {
                    currentElement.style.width = `${Math.min(300, Math.max(50, newWidth))}px`;
                    currentElement.style.height = `${Math.min(300, Math.max(50, newHeight))}px`;
                    updateDownloadLink();
                }
            }
        });

        fontSizeBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                const newSize = prompt('New font size (10-72px):', currentElement.style.fontSize.replace('px', ''));
                if (newSize) {
                    currentElement.style.fontSize = `${Math.min(72, Math.max(10, newSize))}px`;
                    updateDownloadLink();
                }
            }
        });

        fontColorBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                const newColor = prompt('New color (hex):', currentElement.style.color);
                if (newColor) {
                    currentElement.style.color = newColor;
                    updateDownloadLink();
                }
            }
        });

        fontStyleBtn.addEventListener('click', function() {
            if (currentElement && currentElement.classList.contains('draggable-text')) {
                const newFont = prompt('New font (Arial, Times New Roman, Courier New, Georgia, Verdana):', currentElement.style.fontFamily);
                if (newFont) {
                    currentElement.style.fontFamily = newFont;
                    updateDownloadLink();
                }
            }
        });

        function selectElement(e) {
            e.stopPropagation();
            if (currentElement) {
                currentElement.classList.remove('selected');
            }
            currentElement = e.target.closest('.draggable-text') || e.target.closest('.draggable-image');
            if (currentElement) {
                currentElement.classList.add('selected');
                updateButtonStates();
            } else {
                updateButtonStates();
            }
        }

        function startDrag(e) {
            currentElement = e.target.closest('.draggable-text') || e.target.closest('.draggable-image');
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
            const isImage = currentElement && currentElement.classList.contains('draggable-image');
            
            deleteBtn.disabled = !currentElement;
            editBtn.disabled = !isText;
            resizeBtn.disabled = !isImage;
            fontSizeBtn.disabled = !isText;
            fontColorBtn.disabled = !isText;
            fontStyleBtn.disabled = !isText;
        }

        function updateDownloadLink() {
            const canvas = document.createElement('canvas');
            const img = backgroundImage;

            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const elements = textContainer.querySelectorAll('.draggable-text, .draggable-image');
            elements.forEach(element => {
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
                } else if (element.classList.contains('draggable-image')) {
                    const imgElement = element.querySelector('img');
                    const width = (element.offsetWidth / containerRect.width) * canvas.width;
                    const height = (element.offsetHeight / containerRect.height) * canvas.height;
                    ctx.drawImage(imgElement, x, y, width, height);
                }
            });

            downloadLink.href = canvas.toDataURL('image/png');
        }

        // Initial button state
        updateButtonStates();
    });
</script>