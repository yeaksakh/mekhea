<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_invite_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_invite_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_invite_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_invite_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_invite_card {
        background-color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        text-align: center;
    }

    .jester_ecommerce_invite_title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .jester_ecommerce_invite_code {
        font-size: 24px;
        font-weight: bold;
        color: #3498db;
        margin: 20px 0;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 10px;
        border: 2px dashed #3498db;
        letter-spacing: 2px;
    }

    .jester_ecommerce_copy_btn {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .jester_ecommerce_copy_btn:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    .jester_ecommerce_share_buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 25px;
    }

    .jester_ecommerce_share_btn {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .jester_ecommerce_share_btn:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .jester_ecommerce_facebook { background-color: #3b5998; }
    .jester_ecommerce_twitter { background-color: #1da1f2; }
    .jester_ecommerce_whatsapp { background-color: #25d366; }
    .jester_ecommerce_instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); }

    .jester_ecommerce_benefits {
        margin-top: 30px;
    }

    .jester_ecommerce_benefits h3 {
        font-size: 20px;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }

    .jester_ecommerce_benefit_item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_benefit_item i {
        color: #2ecc71;
        font-size: 20px;
        margin-right: 15px;
    }

    .jester_ecommerce_benefit_text {
        font-size: 16px;
        color: #2c3e50;
    }

    .jester_ecommerce_invite_stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 30px;
    }

    .jester_ecommerce_invite_stat {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_invite_stat_number {
        font-size: 24px;
        font-weight: bold;
        color: #3498db;
        margin-bottom: 5px;
    }

    .jester_ecommerce_invite_stat_label {
        font-size: 14px;
        color: #7f8c8d;
    }
</style>

<div class="jester_ecommerce_invite_container no-scrollbar">
    <div class="jester_ecommerce_invite_header">
        <h1>Invite Friends</h1>
        <p>Share the love and earn rewards together</p>
    </div>

    <div class="jester_ecommerce_invite_card">
        <h3 class="jester_ecommerce_invite_title">Your Unique Invite Code</h3>
        <div class="jester_ecommerce_invite_code">ARIMAKO2024</div>
        <button class="jester_ecommerce_copy_btn" onclick="copyInviteCode()">
            <i class="fas fa-copy"></i> Copy Code
        </button>
        
        <div class="jester_ecommerce_share_buttons">
            <button class="jester_ecommerce_share_btn jester_ecommerce_facebook" onclick="shareOnFacebook()">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="jester_ecommerce_share_btn jester_ecommerce_twitter" onclick="shareOnTwitter()">
                <i class="fab fa-twitter"></i>
            </button>
            <button class="jester_ecommerce_share_btn jester_ecommerce_whatsapp" onclick="shareOnWhatsApp()">
                <i class="fab fa-whatsapp"></i>
            </button>
            <button class="jester_ecommerce_share_btn jester_ecommerce_instagram" onclick="shareOnInstagram()">
                <i class="fab fa-instagram"></i>
            </button>
        </div>
    </div>

    <div class="jester_ecommerce_benefits">
        <h3>Invite Benefits</h3>
        <div class="jester_ecommerce_benefit_item">
            <i class="fas fa-gift"></i>
            <span class="jester_ecommerce_benefit_text">Get $10 credit for each friend who signs up</span>
        </div>
        <div class="jester_ecommerce_benefit_item">
            <i class="fas fa-percentage"></i>
            <span class="jester_ecommerce_benefit_text">Your friend gets 20% off their first purchase</span>
        </div>
        <div class="jester_ecommerce_benefit_item">
            <i class="fas fa-crown"></i>
            <span class="jester_ecommerce_benefit_text">Unlock exclusive rewards after 5 invites</span>
        </div>
        <div class="jester_ecommerce_benefit_item">
            <i class="fas fa-infinity"></i>
            <span class="jester_ecommerce_benefit_text">No limit on how many friends you can invite</span>
        </div>
    </div>

    <div class="jester_ecommerce_invite_stats">
        <div class="jester_ecommerce_invite_stat">
            <div class="jester_ecommerce_invite_stat_number">12</div>
            <div class="jester_ecommerce_invite_stat_label">Friends Invited</div>
        </div>
        <div class="jester_ecommerce_invite_stat">
            <div class="jester_ecommerce_invite_stat_number">$120</div>
            <div class="jester_ecommerce_invite_stat_label">Total Earned</div>
        </div>
    </div>
</div>

<script>
    function copyInviteCode() {
        const code = document.querySelector('.jester_ecommerce_invite_code').textContent;
        navigator.clipboard.writeText(code).then(() => {
            showNotification('Code copied to clipboard!');
        });
    }

    function shareOnFacebook() {
        showNotification('Opening Facebook share...');
    }

    function shareOnTwitter() {
        showNotification('Opening Twitter share...');
    }

    function shareOnWhatsApp() {
        showNotification('Opening WhatsApp share...');
    }

    function shareOnInstagram() {
        showNotification('Opening Instagram share...');
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