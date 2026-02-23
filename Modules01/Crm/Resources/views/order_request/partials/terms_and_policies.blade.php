<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_terms_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_terms_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_terms_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_terms_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_terms_nav {
        display: flex;
        overflow-x: auto;
        padding: 10px 0;
        margin-bottom: 25px;
        scrollbar-width: none;
        background-color: white;
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_terms_nav::-webkit-scrollbar {
        display: none;
    }

    .jester_ecommerce_terms_nav_item {
        padding: 10px 15px;
        background-color: #f0f0f0;
        border-radius: 20px;
        margin-right: 10px;
        white-space: nowrap;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .jester_ecommerce_terms_nav_item.active {
        background-color: #3498db;
        color: white;
    }

    .jester_ecommerce_terms_content {
        background-color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .jester_ecommerce_terms_section {
        margin-bottom: 30px;
    }

    .jester_ecommerce_terms_section:last-child {
        margin-bottom: 0;
    }

    .jester_ecommerce_terms_section h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jester_ecommerce_terms-section h3 i {
        color: #3498db;
        font-size: 20px;
    }

    .jester_ecommerce_terms-section p {
        color: #555;
        margin-bottom: 15px;
        line-height: 1.7;
        font-size: 14px;
    }

    .jester_ecommerce_terms-list {
        padding-left: 20px;
        margin-bottom: 15px;
    }

    .jester_ecommerce_terms-list li {
        margin-bottom: 10px;
        color: #555;
        line-height: 1.6;
        font-size: 14px;
    }

    .jester_ecommerce_terms-highlight {
        background-color: #e8f8f5;
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #2ecc71;
        margin: 20px 0;
    }

    .jester_ecommerce_terms-highlight p {
        margin-bottom: 0;
        color: #27ae60;
        font-weight: 500;
    }

    .jester_ecommerce_terms-warning {
        background-color: #fdeaea;
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #e74c3c;
        margin: 20px 0;
    }

    .jester_ecommerce_terms-warning p {
        margin-bottom: 0;
        color: #c0392b;
        font-weight: 500;
    }

    .jester_ecommerce_terms-date {
        text-align: right;
        color: #7f8c8d;
        font-size: 12px;
        margin-top: 20px;
        font-style: italic;
    }

    .jester_ecommerce_accept-section {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        text-align: center;
    }

    .jester_ecommerce_accept-btn {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .jester_ecommerce_accept-btn:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    .jester_ecommerce_decline-btn {
        background-color: transparent;
        color: #7f8c8d;
        border: 1px solid #bdc3c7;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-left: 10px;
    }

    .jester_ecommerce_decline-btn:hover {
        background-color: #ecf0f1;
    }

    .jester_ecommerce_terms-checkbox {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .jester_ecommerce_terms-label {
        font-size: 14px;
        color: #555;
    }
</style>

<div class="jester_ecommerce_terms_container no-scrollbar">
    <div class="jester_ecommerce_terms_header">
        <h1>Terms & Policies</h1>
        <p>Please read our terms and policies carefully</p>
    </div>

    <div class="jester_ecommerce_terms_nav">
        <div class="jester_ecommerce_terms_nav_item active" onclick="showTermsSection('terms')">Terms of Service</div>
        <div class="jester_ecommerce_terms_nav_item" onclick="showTermsSection('privacy')">Privacy Policy</div>
        <div class="jester_ecommerce_terms_nav_item" onclick="showTermsSection('cookies')">Cookie Policy</div>
        <div class="jester_ecommerce_terms_nav_item" onclick="showTermsSection('refund')">Refund Policy</div>
    </div>

    <div id="termsContent" class="jester_ecommerce_terms_content">
        <div class="jester_ecommerce_terms-section">
            <h3><i class="fas fa-file-contract"></i> Terms of Service</h3>
            <p>Welcome to our platform. By using our services, you agree to be bound by these Terms of Service.</p>
            
            <div class="jester_ecommerce_terms-highlight">
                <p>Last updated: January 1, 2024</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>1. Acceptance of Terms</h3>
                <p>By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>2. Use License</h3>
                <p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>3. Disclaimer</h3>
                <p>The materials on our website are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>4. Limitations</h3>
                <p>In no event shall our company or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>5. Accuracy of Materials</h3>
                <p>The materials appearing on our website could include technical, typographical, or photographic errors. We do not warrant that any of the materials on its website are accurate, complete, or current.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>6. Links</h3>
                <p>We have not reviewed all of the sites linked to our website and are not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by us of the site. Use of any such linked website is at the user's own risk.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>7. Modifications</h3>
                <p>We may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>
            </div>

            <div class="jester_ecommerce_terms-warning">
                <p>Failure to comply with these terms may result in termination of your account and legal action.</p>
            </div>
        </div>

        <div class="jester_ecommerce_terms-date">Effective Date: January 1, 2024</div>
    </div>

    <div id="privacyContent" class="jester_ecommerce_terms_content" style="display: none;">
        <div class="jester_ecommerce_terms-section">
            <h3><i class="fas fa-shield-alt"></i> Privacy Policy</h3>
            <p>Your privacy is important to us. This privacy policy explains what personal information we collect and how we use it.</p>
            
            <div class="jester_ecommerce_terms-highlight">
                <p>Last updated: January 1, 2024</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>1. Information We Collect</h3>
                <p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support.</p>
                <ul class="jester_ecommerce_terms-list">
                    <li>Name and contact information</li>
                    <li>Payment information</li>
                    <li>Device and usage information</li>
                    <li>Location data</li>
                </ul>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>2. How We Use Your Information</h3>
                <p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>3. Information Sharing</h3>
                <p>We do not sell your personal information. We only share information with third parties as described in this policy.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>4. Data Security</h3>
                <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>5. Your Rights</h3>
                <p>You have the right to access, update, or delete your personal information. You can also object to or restrict the processing of your information.</p>
            </div>
        </div>

        <div class="jester_ecommerce_terms-date">Effective Date: January 1, 2024</div>
    </div>

    <div id="cookiesContent" class="jester_ecommerce_terms_content" style="display: none;">
        <div class="jester_ecommerce_terms-section">
            <h3><i class="fas fa-cookie-bite"></i> Cookie Policy</h3>
            <p>This cookie policy explains what cookies are and how we use them on our website.</p>
            
            <div class="jester_ecommerce_terms-highlight">
                <p>Last updated: January 1, 2024</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>1. What Are Cookies</h3>
                <p>Cookies are small text files that are placed on your computer or mobile device when you visit a website. They allow the website to recognize your device and remember certain information about your visit.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>2. Types of Cookies We Use</h3>
                <ul class="jester_ecommerce_terms-list">
                    <li><strong>Essential cookies:</strong> Necessary for the website to function properly</li>
                    <li><strong>Performance cookies:</strong> Collect information about how you use our website</li>
                    <li><strong>Functionality cookies:</strong> Remember your preferences and choices</li>
                    <li><strong>Targeting cookies:</strong> Used to deliver advertising relevant to you</li>
                </ul>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>3. Managing Cookies</h3>
                <p>You can manage your cookie preferences through your browser settings. Most browsers allow you to refuse cookies or delete cookies from your device.</p>
            </div>
        </div>

        <div class="jester_ecommerce_terms-date">Effective Date: January 1, 2024</div>
    </div>

    <div id="refundContent" class="jester_ecommerce_terms-content" style="display: none;">
        <div class="jester_ecommerce_terms-section">
            <h3><i class="fas fa-undo"></i> Refund Policy</h3>
            <p>We want you to be completely satisfied with your purchase. If you're not happy with your purchase, we're here to help.</p>
            
            <div class="jester_ecommerce_terms-highlight">
                <p>Last updated: January 1, 2024</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>1. Eligibility for Refunds</h3>
                <p>You may be eligible for a refund if:</p>
                <ul class="jester_ecommerce_terms-list">
                    <li>You request a refund within 30 days of purchase</li>
                    <li>The item is in its original condition</li>
                    <li>You provide proof of purchase</li>
                </ul>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>2. Refund Process</h3>
                <p>To request a refund, please contact our support team with your order number and reason for the refund request. We will process your request within 5-7 business days.</p>
            </div>

            <div class="jester_ecommerce_terms-section">
                <h3>3. Non-Refundable Items</h3>
                <p>Certain items are not eligible for refunds, including:</p>
                <ul class="jester_ecommerce_terms-list">
                    <li>Digital downloads that have been accessed</li>
                    <li>Customized or personalized products</li>
                    <li>Perishable goods</li>
                </ul>
            </div>

            <div class="jester_ecommerce_terms-warning">
                <p>Refunds will be issued to the original payment method used for the purchase.</p>
            </div>
        </div>

        <div class="jester_ecommerce_terms-date">Effective Date: January 1, 2024</div>
    </div>

    <div class="jester_ecommerce_accept-section">
        <label>
            <input type="checkbox" class="jester_ecommerce_terms-checkbox" id="termsCheckbox">
            <span class="jester_ecommerce_terms-label">I have read and agree to the Terms of Service and Privacy Policy</span>
        </label>
        <div>
            <button class="jester_ecommerce_accept-btn" onclick="acceptTerms()">Accept & Continue</button>
            <button class="jester_ecommerce_decline-btn" onclick="declineTerms()">Decline</button>
        </div>
    </div>
</div>

<script>
    function showTermsSection(section) {
        // Update navigation active state
        document.querySelectorAll('.jester_ecommerce_terms_nav_item').forEach(item => {
            item.classList.remove('active');
        });
        event.target.classList.add('active');

        // Show corresponding content
        document.getElementById('termsContent').style.display = section === 'terms' ? 'block' : 'none';
        document.getElementById('privacyContent').style.display = section === 'privacy' ? 'block' : 'none';
        document.getElementById('cookiesContent').style.display = section === 'cookies' ? 'block' : 'none';
        document.getElementById('refundContent').style.display = section === 'refund' ? 'block' : 'none';
    }

    function acceptTerms() {
        const checkbox = document.getElementById('termsCheckbox');
        if (!checkbox.checked) {
            showNotification('Please accept the terms and conditions');
            return;
        }
        showNotification('Terms accepted successfully!');
    }

    function declineTerms() {
        showNotification('Terms declined. Redirecting...');
    }

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            z-index: 1000;
            animation: slideDown 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideUp 0.3s ease;
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