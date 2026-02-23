<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_arimako_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_business_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_business_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_business_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_business_card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .jester_ecommerce_business_card h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .jester_ecommerce_business_card p {
        margin-bottom: 20px;
        opacity: 0.9;
        line-height: 1.6;
    }

    .jester_ecommerce_btn {
        display: inline-block;
        padding: 12px 25px;
        background-color: white;
        color: #667eea;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .jester_ecommerce_btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .jester_ecommerce_features {
        margin-top: 30px;
    }

    .jester_ecommerce_feature_item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding: 20px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .jester_ecommerce_feature_item:hover {
        transform: translateY(-5px);
    }

    .jester_ecommerce_feature_icon {
        width: 60px;
        height: 60px;
        background-color: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 24px;
        flex-shrink: 0;
    }

    .jester_ecommerce_feature_content h4 {
        font-size: 18px;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .jester_ecommerce_feature_content p {
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.5;
    }

    .jester_ecommerce_stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 30px;
    }

    .jester_ecommerce_stat_item {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_stat_number {
        font-size: 28px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 5px;
    }

    .jester_ecommerce_stat_label {
        font-size: 14px;
        color: #7f8c8d;
    }
</style>

<div class="jester_ecommerce_arimako_container no-scrollbar">
    <div class="jester_ecommerce_business_header">
        <h1>Arimako for Business</h1>
        <p>Empower your business with our comprehensive solutions</p>
    </div>

    <div class="jester_ecommerce_business_card">
        <h3>Grow Your Business with Us</h3>
        <p>Join thousands of businesses that trust our platform to reach more customers and increase sales. Get started today and unlock your business potential.</p>
        <button class="jester_ecommerce_btn">Get Started Now</button>
    </div>

    <div class="jester_ecommerce_features">
        <div class="jester_ecommerce_feature_item">
            <div class="jester_ecommerce_feature_icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="jester_ecommerce_feature_content">
                <h4>Advanced Analytics</h4>
                <p>Track your business performance with detailed reports and insights to make data-driven decisions.</p>
            </div>
        </div>

        <div class="jester_ecommerce_feature_item">
            <div class="jester_ecommerce_feature_icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="jester_ecommerce_feature_content">
                <h4>Customer Management</h4>
                <p>Build lasting relationships with your customers through our powerful CRM tools.</p>
            </div>
        </div>

        <div class="jester_ecommerce_feature_item">
            <div class="jester_ecommerce_feature_icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="jester_ecommerce_feature_content">
                <h4>E-commerce Integration</h4>
                <p>Seamlessly integrate with popular e-commerce platforms to streamline your operations.</p>
            </div>
        </div>
    </div>

    <div class="jester_ecommerce_stats">
        <div class="jester_ecommerce_stat_item">
            <div class="jester_ecommerce_stat_number">10K+</div>
            <div class="jester_ecommerce_stat_label">Businesses</div>
        </div>
        <div class="jester_ecommerce_stat_item">
            <div class="jester_ecommerce_stat_number">50M+</div>
            <div class="jester_ecommerce_stat_label">Customers</div>
        </div>
        <div class="jester_ecommerce_stat_item">
            <div class="jester_ecommerce_stat_number">$2B+</div>
            <div class="jester_ecommerce_stat_label">Revenue</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const getStartedBtn = document.querySelector('.jester_ecommerce_btn');
        getStartedBtn.addEventListener('click', function() {
            alert('Redirecting to business registration...');
        });
    });
</script>