<style>
    div.no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .jester_ecommerce_rewards_container {
        max-width: 414px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .jester_ecommerce_rewards_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .jester_ecommerce_rewards_header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .jester_ecommerce_rewards_header p {
        color: #7f8c8d;
        font-size: 16px;
    }

    .jester_ecommerce_points_balance {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        text-align: center;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .jester_ecommerce_points-label {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .jester_ecommerce_points-value {
        font-size: 48px;
        font-weight: bold;
        margin: 10px 0;
    }

    .jester_ecommerce_points-info {
        font-size: 14px;
        opacity: 0.8;
    }

    .jester_ecommerce_rewards-section {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_section-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jester_ecommerce_section-title i {
        color: #667eea;
    }

    .jester_ecommerce_rewards-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .jester_ecommerce_reward-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .jester_ecommerce_reward-item:hover {
        background-color: #e9ecef;
        transform: translateX(5px);
    }

    .jester_ecommerce_reward-icon {
        width: 50px;
        height: 50px;
        background-color: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
        flex-shrink: 0;
    }

    .jester_ecommerce_reward-content {
        flex: 1;
    }

    .jester_ecommerce_reward-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .jester_ecommerce_reward-description {
        font-size: 14px;
        color: #7f8c8d;
    }

    .jester_ecommerce_reward-points {
        font-weight: bold;
        color: #667eea;
        font-size: 18px;
        margin-left: 10px;
    }

    .jester_ecommerce_activity-section {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .jester_ecommerce_activity-item:last-child {
        border-bottom: none;
    }

    .jester_ecommerce_activity-icon {
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

    .jester_ecommerce_activity-content {
        flex: 1;
    }

    .jester_ecommerce_activity-title {
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 3px;
    }

    .jester_ecommerce_activity-time {
        font-size: 12px;
        color: #7f8c8d;
    }

    .jester_ecommerce_activity-points {
        font-weight: bold;
        color: #2ecc71;
        font-size: 16px;
    }

    .jester_ecommerce_tiers-section {
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .jester_ecommerce_tiers {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        position: relative;
    }

    .jester_ecommerce_tiers::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 30px;
        right: 30px;
        height: 4px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .jester_ecommerce_tiers::after {
        content: '';
        position: absolute;
        top: 25px;
        left: 30px;
        width: 40%;
        height: 4px;
        background-color: #667eea;
        z-index: 2;
    }

    .jester_ecommerce-tier {
        text-align: center;
        position: relative;
        z-index: 3;
    }

    .jester_ecommerce-tier-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 20px;
        background-color: white;
        border: 4px solid #e9ecef;
        color: #bdc3c7;
    }

    .jester_ecommerce-tier.active .jester_ecommerce-tier-icon {
        border-color: #667eea;
        color: #667eea;
    }

    .jester_ecommerce-tier.completed .jester_ecommerce-tier-icon {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
    }

    .jester_ecommerce-tier-name {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
    }

    .jester_ecommerce-tier-points {
        font-size: 12px;
        color: #7f8c8d;
    }

    .jester_ecommerce_reward-btn {
        width: 100%;
        padding: 15px;
        background-color: #667eea;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .jester_ecommerce_reward-btn:hover {
        background-color: #5a6fd8;
        transform: translateY(-2px);
    }
</style>

<div class="jester_ecommerce_rewards_container no-scrollbar">
    <div class="jester_ecommerce_rewards_header">
        <h1>Arimako Rewards</h1>
        <p>Earn points and unlock exclusive rewards</p>
    </div>

    <div class="jester_ecommerce_points_balance">
        <div class="jester_ecommerce-points-label">Your Points Balance</div>
        <div class="jester_ecommerce_points-value">2,450</div>
        <div class="jester_ecommerce-points-info">Next reward at 3,000 points</div>
    </div>

    <div class="jester_ecommerce_rewards-section">
        <h3 class="jester_ecommerce_section-title">
            <i class="fas fa-gift"></i>
            Available Rewards
        </h3>
        <div class="jester_ecommerce_rewards-list">
            <div class="jester_ecommerce_reward-item" onclick="redeemReward('discount10')">
                <div class="jester_ecommerce_reward-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="jester_ecommerce_reward-content">
                    <div class="jester_ecommerce_reward-title">10% Discount</div>
                    <div class="jester_ecommerce_reward-description">Get 10% off on your next purchase</div>
                </div>
                <div class="jester_ecommerce_reward-points">500 pts</div>
            </div>

            <div class="jester_ecommerce_reward-item" onclick="redeemReward('freeship')">
                <div class="jester_ecommerce_reward-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="jester_ecommerce_reward-content">
                    <div class="jester_ecommerce_reward-title">Free Shipping</div>
                    <div class="jester_ecommerce_reward-description">Free shipping on your next order</div>
                </div>
                <div class="jester_ecommerce_reward-points">300 pts</div>
            </div>

            <div class="jester_ecommerce_reward-item" onclick="redeemReward('giftcard')">
                <div class="jester_ecommerce_reward-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="jester_ecommerce_reward-content">
                    <div class="jester_ecommerce_reward-title">$25 Gift Card</div>
                    <div class="jester_ecommerce_reward-description">Gift card for any purchase</div>
                </div>
                <div class="jester_ecommerce_reward-points">2,500 pts</div>
            </div>

            <div class="jester_ecommerce_reward-item" onclick="redeemReward('vip')">
                <div class="jester_ecommerce_reward-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="jester_ecommerce_reward-content">
                    <div class="jester_ecommerce_reward-title">VIP Status</div>
                    <div class="jester_ecommerce_reward-description">1 month of VIP membership</div>
                </div>
                <div class="jester_ecommerce_reward-points">3,000 pts</div>
            </div>
        </div>
    </div>

    <div class="jester_ecommerce_activity-section">
        <h3 class="jester_ecommerce_section-title">
            <i class="fas fa-history"></i>
            Recent Activity
        </h3>
        <div class="jester_ecommerce_activity-item">
            <div class="jester_ecommerce_activity-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="jester_ecommerce_activity-content">
                <div class="jester_ecommerce_activity-title">Purchase completed</div>
                <div class="jester_ecommerce_activity-time">2 hours ago</div>
            </div>
            <div class="jester_ecommerce_activity-points">+50</div>
        </div>

        <div class="jester_ecommerce_activity-item">
            <div class="jester_ecommerce_activity-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="jester_ecommerce_activity-content">
                <div class="jester_ecommerce_activity-title">Friend joined</div>
                <div class="jester_ecommerce_activity-time">1 day ago</div>
            </div>
            <div class="jester_ecommerce_activity-points">+100</div>
        </div>

        <div class="jester_ecommerce_activity-item">
            <div class="jester_ecommerce_activity-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="jester_ecommerce_activity-content">
                <div class="jester_ecommerce_activity-title">Product review</div>
                <div class="jester_ecommerce_activity-time">3 days ago</div>
            </div>
            <div class="jester_ecommerce_activity-points">+25</div>
        </div>
    </div>

    <div class="jester_ecommerce_tiers-section">
        <h3 class="jester_ecommerce_section-title">
            <i class="fas fa-trophy"></i>
            Reward Tiers
        </h3>
        <div class="jester_ecommerce_tiers">
            <div class="jester_ecommerce-tier completed">
                <div class="jester_ecommerce-tier-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="jester_ecommerce-tier-name">Bronze</div>
                <div class="jester_ecommerce-tier-points">0 pts</div>
            </div>
            <div class="jester_ecommerce-tier active">
                <div class="jester_ecommerce-tier-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="jester_ecommerce-tier-name">Silver</div>
                <div class="jester_ecommerce-tier-points">1,000 pts</div>
            </div>
            <div class="jester_ecommerce-tier">
                <div class="jester_ecommerce-tier-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="jester_ecommerce-tier-name">Gold</div>
                <div class="jester_ecommerce-tier-points">5,000 pts</div>
            </div>
            <div class="jester_ecommerce-tier">
                <div class="jester_ecommerce-tier-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="jester_ecommerce-tier-name">Platinum</div>
                <div class="jester_ecommerce-tier-points">10,000 pts</div>
            </div>
        </div>
        <button class="jester_ecommerce_reward-btn" onclick="viewAllRewards()">View All Rewards</button>
    </div>
</div>

<script>
    function redeemReward(rewardId) {
        showNotification(`Redeeming reward...`);
    }

    function viewAllRewards() {
        showNotification('Loading all rewards...');
    }

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #667eea;
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