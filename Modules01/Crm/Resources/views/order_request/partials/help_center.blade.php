<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_help_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_help_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_help_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_help_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_help_search {
        position: relative;
        margin-bottom: 25px;
    }

    .jester_ecommerce_help_input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        font-size: 16px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .jester_ecommerce_help_input:focus {
        border-color: #3498db;
    }

    .jester_ecommerce_help_search_btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 20px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .jester_ecommerce_help_search_btn:hover {
        background-color: #2980b9;
    }

    .jester_ecommerce_help_categories {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }

    .jester_ecommerce_help_category {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .jester_ecommerce_help_category:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .jester_ecommerce_help_category i {
        font-size: 30px;
        color: #3498db;
        margin-bottom: 10px;
    }

    .jester_ecommerce_help_category h3 {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .jester_ecommerce_help_category p {
        font-size: 12px;
        color: #7f8c8d;
    }

    .jester_ecommerce_faqs {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_faqs h3 {
        font-size: 20px;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jester_ecommerce_faqs h3 i {
        color: #3498db;
    }

    .jester_ecommerce_faq_item {
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 15px;
    }

    .jester_ecommerce_faq_item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .jester_ecommerce_faq_question {
        padding: 15px 0;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #2c3e50;
        transition: color 0.3s ease;
    }

    .jester_ecommerce_faq_question:hover {
        color: #3498db;
    }

    .jester_ecommerce_faq_answer {
        padding: 0 0 15px;
        color: #7f8c8d;
        line-height: 1.6;
        display: none;
    }

    .jester_ecommerce_faq_item.active .jester_ecommerce_faq_answer {
        display: block;
    }

    .jester_ecommerce_faq_item.active .jester_ecommerce_faq_question i {
        transform: rotate(180deg);
    }

    .jester_ecommerce_faq_question i {
        transition: transform 0.3s ease;
        color: #bdc3c7;
    }

    .jester_ecommerce_contact-section {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_contact-section h3 {
        font-size: 20px;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jester_ecommerce_contact-section h3 i {
        color: #3498db;
    }

    .jester_ecommerce_contact-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .jester_ecommerce_contact-option {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .jester_ecommerce_contact-option:hover {
        background-color: #e9ecef;
        transform: translateY(-3px);
    }

    .jester_ecommerce_contact-option i {
        font-size: 24px;
        color: #3498db;
        margin-bottom: 10px;
    }

    .jester_ecommerce_contact-option h4 {
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .jester_ecommerce_contact-option p {
        font-size: 12px;
        color: #7f8c8d;
    }

    .jester_ecommerce_articles {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_articles h3 {
        font-size: 20px;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jester_ecommerce_articles h3 i {
        color: #3498db;
    }

    .jester_ecommerce_article-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .jester_ecommerce_article-item:last-child {
        border-bottom: none;
    }

    .jester_ecommerce_article-item:hover {
        padding-left: 10px;
    }

    .jester_ecommerce_article-icon {
        width: 40px;
        height: 40px;
        background-color: #e8f8f5;
        color: #2ecc71;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
        flex-shrink: 0;
    }

    .jester_ecommerce_article-content h4 {
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 3px;
    }

    .jester_ecommerce_article-content p {
        font-size: 12px;
        color: #7f8c8d;
    }
</style>

<div class="jester_ecommerce_help_container no-scrollbar">
    <div class="jester_ecommerce_help_header">
        <h1>Help Center</h1>
        <p>How can we help you today?</p>
    </div>

    <div class="jester_ecommerce_help_search">
        <input type="text" class="jester_ecommerce_help_input" placeholder="Search for help...">
        <button class="jester_ecommerce_help_search_btn">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div class="jester_ecommerce_help_categories">
        <div class="jester_ecommerce_help_category" onclick="openCategory('account')">
            <i class="fas fa-user-circle"></i>
            <h3>Account</h3>
            <p>Login, registration & settings</p>
        </div>
        <div class="jester_ecommerce_help_category" onclick="openCategory('orders')">
            <i class="fas fa-shopping-bag"></i>
            <h3>Orders</h3>
            <p>Track & manage orders</p>
        </div>
        <div class="jester_ecommerce_help_category" onclick="openCategory('payments')">
            <i class="fas fa-credit-card"></i>
            <h3>Payments</h3>
            <p>Payment methods & refunds</p>
        </div>
        <div class="jester_ecommerce_help_category" onclick="openCategory('technical')">
            <i class="fas fa-cog"></i>
            <h3>Technical</h3>
            <p>App issues & troubleshooting</p>
        </div>
    </div>

    <div class="jester_ecommerce_faqs">
        <h3><i class="fas fa-question-circle"></i> Frequently Asked Questions</h3>
        
        <div class="jester_ecommerce_faq_item">
            <div class="jester_ecommerce_faq_question" onclick="toggleFAQ(this)">
                How do I reset my password?
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="jester_ecommerce_faq_answer">
                To reset your password, go to the login page and click on "Forgot Password". Enter your email address and we'll send you a link to reset your password.
            </div>
        </div>

        <div class="jester_ecommerce_faq_item">
            <div class="jester_ecommerce_faq_question" onclick="toggleFAQ(this)">
                How long does shipping take?
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="jester_ecommerce_faq_answer">
                Standard shipping takes 3-5 business days. Express shipping is available for an additional fee and delivers within 1-2 business days.
            </div>
        </div>

        <div class="jester_ecommerce_faq_item">
            <div class="jester_ecommerce_faq_question" onclick="toggleFAQ(this)">
                What is your return policy?
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="jester_ecommerce_faq_answer">
                We offer a 30-day return policy for most items. Items must be in original condition with tags attached. Some restrictions apply for certain products.
            </div>
        </div>

        <div class="jester_ecommerce_faq_item">
            <div class="jester_ecommerce_faq_question" onclick="toggleFAQ(this)">
                How do I track my order?
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="jester_ecommerce_faq_answer">
                Once your order ships, you'll receive a tracking number via email. You can also track your order in the "My Orders" section of your account.
            </div>
        </div>
    </div>

    <div class="jester_ecommerce_contact-section">
        <h3><i class="fas fa-headset"></i> Contact Support</h3>
        <div class="jester_ecommerce_contact-options">
            <div class="jester_ecommerce_contact-option" onclick="contactSupport('chat')">
                <i class="fas fa-comments"></i>
                <h4>Live Chat</h4>
                <p>Available 24/7</p>
            </div>
            <div class="jester_ecommerce_contact-option" onclick="contactSupport('email')">
                <i class="fas fa-envelope"></i>
                <h4>Email</h4>
                <p>Response within 24h</p>
            </div>
            <div class="jester_ecommerce_contact-option" onclick="contactSupport('phone')">
                <i class="fas fa-phone"></i>
                <h4>Phone</h4>
                <p>Mon-Fri 9AM-6PM</p>
            </div>
            <div class="jester_ecommerce_contact-option" onclick="contactSupport('community')">
                <i class="fas fa-users"></i>
                <h4>Community</h4>
                <p>Ask the community</p>
            </div>
        </div>
    </div>

    <div class="jester_ecommerce_articles">
        <h3><i class="fas fa-book"></i> Popular Articles</h3>
        
        <div class="jester_ecommerce_article-item" onclick="openArticle('getting-started')">
            <div class="jester_ecommerce_article-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="jester_ecommerce_article-content">
                <h4>Getting Started Guide</h4>
                <p>Learn the basics of using our platform</p>
            </div>
        </div>

        <div class="jester_ecommerce_article-item" onclick="openArticle('payment-methods')">
            <div class="jester_ecommerce_article-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="jester_ecommerce_article-content">
                <h4>Accepted Payment Methods</h4>
                <p>See all payment options available</p>
            </div>
        </div>

        <div class="jester_ecommerce_article-item" onclick="openArticle('shipping-info')">
            <div class="jester_ecommerce_article-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="jester_ecommerce_article-content">
                <h4>Shipping Information</h4>
                <p>Everything about shipping & delivery</p>
            </div>
        </div>

        <div class="jester_ecommerce_article-item" onclick="openArticle('account-security')">
            <div class="jester_ecommerce_article-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="jester_ecommerce_article-content">
                <h4>Account Security</h4>
                <p>Keep your account safe & secure</p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFAQ(element) {
        const faqItem = element.parentElement;
        faqItem.classList.toggle('active');
    }

    function openCategory(category) {
        showNotification(`Opening ${category} help category...`);
    }

    function contactSupport(method) {
        showNotification(`Opening ${method} support...`);
    }

    function openArticle(articleId) {
        showNotification(`Opening article: ${articleId}`);
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