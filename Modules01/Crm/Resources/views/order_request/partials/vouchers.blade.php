<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_vouchers_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_vouchers_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_vouchers_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_vouchers_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_voucher_tabs {
        display: flex;
        margin-bottom: 25px;
        background-color: white;
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_voucher_tab {
        flex: 1;
        padding: 12px;
        text-align: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .jester_ecommerce_voucher_tab.active {
        background-color: #3498db;
        color: white;
    }

    .jester_ecommerce_voucher_card {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .jester_ecommerce_voucher_card:hover {
        transform: translateY(-5px);
    }

    .jester_ecommerce_voucher_card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 8px;
        height: 100%;
        background-color: #3498db;
    }

    .jester_ecommerce_voucher_card.expired::before {
        background-color: #95a5a6;
    }

    .jester_ecommerce_voucher_card.used::before {
        background-color: #e74c3c;
    }

    .jester_ecommerce_voucher_header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .jester_ecommerce_voucher_title {
        font-weight: 600;
        font-size: 18px;
        color: #2c3e50;
    }

    .jester_ecommerce_voucher_discount {
        font-size: 22px;
        font-weight: bold;
        color: #e74c3c;
    }

    .jester_ecommerce_voucher_details {
        margin-bottom: 15px;
    }

    .jester_ecommerce_voucher_code {
        font-family: monospace;
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin: 10px 0;
        display: inline-block;
        font-size: 16px;
        font-weight: 600;
        color: #3498db;
        letter-spacing: 1px;
    }

    .jester_ecommerce_voucher_description {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .jester_ecommerce_voucher_expiry {
        font-size: 14px;
        color: #e74c3c;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .jester_ecommerce_voucher_status {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 10px;
    }

    .jester_ecommerce_status_active {
        background-color: #e8f8f5;
        color: #2ecc71;
    }

    .jester_ecommerce_status_used {
        background-color: #fdeaea;
        color: #e74c3c;
    }

    .jester_ecommerce_status_expired {
        background-color: #f4f4f4;
        color: #95a5a6;
    }

    .jester_ecommerce_voucher_actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .jester_ecommerce_voucher_btn {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .jester_ecommerce_btn_primary {
        background-color: #3498db;
        color: white;
    }

    .jester_ecommerce_btn_primary:hover {
        background-color: #2980b9;
    }

    .jester_ecommerce_btn_secondary {
        background-color: #ecf0f1;
        color: #7f8c8d;
    }

    .jester_ecommerce_btn_secondary:hover {
        background-color: #bdc3c7;
    }

    .jester_ecommerce_empty_state {
        text-align: center;
        padding: 40px 20px;
        color: #7f8c8d;
    }

    .jester_ecommerce_empty_state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #bdc3c7;
    }

    .jester_ecommerce_empty_state h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #2c3e50;
    }
</style>

<div class="jester_ecommerce_vouchers_container no-scrollbar">
    <div class="jester_ecommerce_vouchers_header">
        <h1>My Vouchers</h1>
        <p>Manage your discount vouchers and special offers</p>
    </div>

    <div class="jester_ecommerce_voucher_tabs">
        <div class="jester_ecommerce_voucher_tab active" onclick="showVoucherTab('active')">Active</div>
        <div class="jester_ecommerce_voucher_tab" onclick="showVoucherTab('used')">Used</div>
        <div class="jester_ecommerce_voucher_tab" onclick="showVoucherTab('expired')">Expired</div>
    </div>

    <div id="activeVouchers">
        <div class="jester_ecommerce_voucher_card">
            <div class="jester_ecommerce_voucher_header">
                <div class="jester_ecommerce_voucher_title">Summer Sale</div>
                <div class="jester_ecommerce_voucher_discount">25% OFF</div>
            </div>
            <div class="jester_ecommerce_voucher_details">
                <div class="jester_ecommerce_voucher_code">SUMMER25</div>
                <div class="jester_ecommerce_voucher_description">Get 25% off on all summer collection items. Minimum purchase of $50 required.</div>
                <div class="jester_ecommerce_voucher_expiry">
                    <i class="fas fa-clock"></i> Expires in 5 days
                </div>
            </div>
            <div class="jester_ecommerce_voucher_status jester_ecommerce_status_active">Active</div>
            <div class="jester_ecommerce_voucher_actions">
                <button class="jester_ecommerce_voucher_btn jester_ecommerce_btn_primary" onclick="useVoucher('SUMMER25')">Use Now</button>
                <button class="jester_ecommerce_voucher_btn jester_ecommerce_btn_secondary" onclick="copyVoucherCode('SUMMER25')">Copy</button>
            </div>
        </div>

        <div class="jester_ecommerce_voucher_card">
            <div class="jester_ecommerce_voucher_header">
                <div class="jester_ecommerce_voucher_title">Welcome Bonus</div>
                <div class="jester_ecommerce_voucher_discount">$10 OFF</div>
            </div>
            <div class="jester_ecommerce_voucher_details">
                <div class="jester_ecommerce_voucher_code">WELCOME10</div>
                <div class="jester_ecommerce_voucher_description">Get $10 off on your first order. No minimum purchase required.</div>
                <div class="jester_ecommerce_voucher_expiry">
                    <i class="fas fa-clock"></i> Expires in 12 days
                </div>
            </div>
            <div class="jester_ecommerce_voucher_status jester_ecommerce_status_active">Active</div>
            <div class="jester_ecommerce_voucher_actions">
                <button class="jester_ecommerce_voucher_btn jester_ecommerce_btn_primary" onclick="useVoucher('WELCOME10')">Use Now</button>
                <button class="jester_ecommerce_voucher_btn jester_ecommerce_btn_secondary" onclick="copyVoucherCode('WELCOME10')">Copy</button>
            </div>
        </div>
    </div>

    <div id="usedVouchers" style="display: none;">
        <div class="jester_ecommerce_voucher_card used">
            <div class="jester_ecommerce_voucher_header">
                <div class="jester_ecommerce_voucher_title">Flash Sale</div>
                <div class="jester_ecommerce_voucher_discount">30% OFF</div>
            </div>
            <div class="jester_ecommerce_voucher_details">
                <div class="jester_ecommerce_voucher_code">FLASH30</div>
                <div class="jester_ecommerce_voucher_description">Flash sale discount on selected items.</div>
            </div>
            <div class="jester_ecommerce_voucher_status jester_ecommerce_status_used">Used</div>
        </div>
    </div>

    <div id="expiredVouchers" style="display: none;">
        <div class="jester_ecommerce_voucher_card expired">
            <div class="jester_ecommerce_voucher_header">
                <div class="jester_ecommerce_voucher_title">New Year Special</div>
                <div class="jester_ecommerce_voucher_discount">20% OFF</div>
            </div>
            <div class="jester_ecommerce_voucher_details">
                <div class="jester_ecommerce_voucher_code">NEWYEAR20</div>
                <div class="jester_ecommerce_voucher_description">New year special discount on all products.</div>
            </div>
            <div class="jester_ecommerce_voucher_status jester_ecommerce_status_expired">Expired</div>
        </div>
    </div>
</div>

<script>
    function showVoucherTab(tab) {
        // Update tab active state
        document.querySelectorAll('.jester_ecommerce_voucher_tab').forEach(t => {
            t.classList.remove('active');
        });
        event.target.classList.add('active');

        // Show corresponding vouchers
        document.getElementById('activeVouchers').style.display = tab === 'active' ? 'block' : 'none';
        document.getElementById('usedVouchers').style.display = tab === 'used' ? 'block' : 'none';
        document.getElementById('expiredVouchers').style.display = tab === 'expired' ? 'block' : 'none';
    }

    function useVoucher(code) {
        showNotification(`Applying voucher ${code}...`);
    }

    function copyVoucherCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            showNotification('Voucher code copied to clipboard!');
        });
    }

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2ecc71;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            z-index: 1000;
            animation: slideDown 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideUp 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // Add CSS animation for notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            from { transform: translateX(-50%) translateY(-100%); }
            to { transform: translateX(-50%) translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateX(-50%) translateY(0); }
            to { transform: translateX(-50%) translateY(-100%); }
        }
    `;
    document.head.appendChild(style);
</script>