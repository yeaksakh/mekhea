

<div id="unitCalcModal" class="unit-calc-modal">
    <div class="unit-calc-modal-content">
        <span class="unit-calc-close" onclick="closeCalculator()">&times;</span>
        <h2>Unit Calculator</h2>

        <div class="unit-calc-form-group">
            <label>Quantity:</label>
            <input type="number" id="quantity" value="100" step="0.01" style="background-color: white">
        </div>

        <div class="unit-calc-form-row">
            <div class="unit-calc-form-group" style="flex: 1; margin-right: 10px;">
                <label>1 Unit = How many:</label>
                <input type="number" id="unitSize" value="750" step="0.01" style="background-color: white; width: 100%;">
            </div>
            
            <div class="unit-calc-form-group" style="flex: 1;">
                <label>Unit Type:</label>
                <select id="unitType" style="background-color: white; width: 100%;">
                    <option value="ml">ml</option>
                    <option value="g">g</option>
                    <option value="kg">kg</option>
                    <option value="l">l</option>
                </select>
            </div>
        </div>

        <div class="unit-calc-form-group">
            <label>Convert To:</label>
            <select id="toUnit" style="background-color: white; width: 100%;">
                <option value="ton">ton</option>
                <option value="kg">kg</option>
                <option value="g">g</option>
                <option value="l">l</option>
                <option value="ml">ml</option>
            </select>
        </div>

        <button onclick="convert()">Calculate</button>

        <div id="result" class="unit-calc-result" style="display: none;"></div>
        <button class="unit-calc-copy-btn" onclick="copyResult()" style="display: none;">Copy Number</button>
    </div>
</div>

<div id="unitCalcAlert" class="unit-calc-alert"></div>

<style>
    .unit-calc-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .unit-calc-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999; /* Higher z-index to avoid conflicts */
    }

    .unit-calc-modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .unit-calc-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
    }

    .unit-calc-form-group {
        margin-bottom: 15px;
    }
    
    .unit-calc-form-row {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .unit-calc-form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .unit-calc-form-group input,
    .unit-calc-form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .unit-calc-modal button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 5px;
    }

    .unit-calc-result {
        margin-top: 15px;
        padding: 10px;
        background-color: #e8f5e8;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
    }

    .unit-calc-copy-btn {
        background-color: #28a745;
        margin-top: 10px;
    }

    /* Custom Alert Styles */
    .unit-calc-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: #28a745;
        color: white;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 10000; /* Even higher z-index for alerts */
        display: none;
        animation: unit-calc-slideIn 0.3s, unit-calc-fadeOut 0.5s 2.5s;
    }

    @keyframes unit-calc-slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes unit-calc-fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }
</style>

<script>
    // Conversion factors to tons
    const unitFactors = {
        'ton': 1,
        'kg': 0.001,
        'g': 0.000001,
        'l': 0.001,
        'ml': 0.000001
    };

    function openCalculator() {
        document.getElementById('unitCalcModal').style.display = 'block';
    }

    function closeCalculator() {
        document.getElementById('unitCalcModal').style.display = 'none';
    }

    function convert() {
        const quantity = parseFloat(document.getElementById('quantity').value);
        const unitSize = parseFloat(document.getElementById('unitSize').value);
        const unitType = document.getElementById('unitType').value;
        const toUnit = document.getElementById('toUnit').value;

        if (isNaN(quantity) || isNaN(unitSize) || !unitType || !toUnit) {
            showUnitCalcAlert('Please fill in all fields', 'error');
            return;
        }

        // Calculate total amount in unitType
        const totalInUnitType = quantity * unitSize;

        // Convert to tons first
        const totalInTons = totalInUnitType * unitFactors[unitType];

        // Convert to target unit
        const result = totalInTons / unitFactors[toUnit];

        const resultText = `${result.toFixed(6)} ${toUnit}`;
        document.getElementById('result').textContent = resultText;
        document.getElementById('result').style.display = 'block';
        document.querySelector('.unit-calc-copy-btn').style.display = 'block';
    }

    function copyResult() {
        const resultText = document.getElementById('result').textContent;
        const numberOnly = resultText.split(' ')[0]; // Extract only the number part

        // Create a temporary textarea to copy from
        const tempInput = document.createElement('input');
        tempInput.value = numberOnly;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        showUnitCalcAlert('Number copied: ' + numberOnly);

        // Update main button text
        document.getElementById('mainButton').textContent = numberOnly;
    }

    function showUnitCalcAlert(message, type = 'success') {
        const alertDiv = document.getElementById('unitCalcAlert');
        alertDiv.textContent = message;
        alertDiv.style.display = 'block';

        // Change color based on type
        if (type === 'error') {
            alertDiv.style.backgroundColor = '#dc3545';
        } else {
            alertDiv.style.backgroundColor = '#28a745';
        }

        // Hide after 3 seconds
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('unitCalcModal');
        if (event.target === modal) {
            closeCalculator();
        }
    }
</script>