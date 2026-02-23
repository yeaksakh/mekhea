<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --arimako-pro-text-dark: #333333;
        --arimako-pro-text-light: #ffffff;
        --arimako-pro-pink: #e50064;
        --arimako-pro-border-color: #eeeeee;
    }

    .arimako-pro-body {
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .arimako-pro-container {
        max-width: 420px;
        margin: auto;
        background-color: #ffffff;
        min-height: 100vh;
        position: relative;
    }

    .arimako-pro-header {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://media.istockphoto.com/id/485371557/photo/twilight-at-spirit-island.jpg?s=612x612&w=0&k=20&c=FSGliJ4EKFP70Yjpzso0HfRR4WwflC6GKfl4F3Hj7fk=');
        background-size: cover;
        background-position: center;
        color: var(--arimako-pro-text-light);
        padding: 40px 20px 20px;
        text-align: center;
        position: relative;
    }

    .arimako-pro-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .arimako-pro-logo-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333333;
        font-weight: bold;
    }

    .arimako-pro-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .arimako-pro-content-area {
        background-color: #ffffff;
        border-top-left-radius: 25px;
        border-top-right-radius: 25px;
        padding: 25px 15px;
        padding-bottom: 90px; /* Space for sticky footer */
        margin-top: -10px;
        position: relative;
        z-index: 1;
    }

    .arimako-pro-section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--arimako-pro-text-dark);
        margin-bottom: 15px;
        padding-left: 5px;
    }

    .arimako-pro-card, .arimako-pro-faq-item-container {
        margin-bottom: 15px;
    }
    
    .arimako-pro-card-header {
        background-color: #ffffff;
        border: 1px solid var(--arimako-pro-border-color);
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        color: var(--arimako-pro-text-dark);
    }

    .arimako-pro-card-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .arimako-pro-card-icon {
        font-size: 20px;
        width: 30px;
        text-align: center;
        color: var(--arimako-pro-pink);
    }

    .arimako-pro-card-text {
        font-weight: 500;
        font-size: 15px;
    }

    .arimako-pro-dropdown-icon {
        font-size: 16px;
        transition: transform 0.3s ease;
    }

    .arimako-pro-dropdown-icon.active {
        transform: rotate(180deg);
    }

    .arimako-pro-collapsible-content {
        display: none;
        padding: 15px;
        background-color: #ffffff;
        border: 1px solid var(--arimako-pro-border-color);
        border-top: none;
        margin-top: -10px;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        color: var(--arimako-pro-text-dark);
    }

    .arimako-pro-promo-banner {
        background-color: #f8f9fa;
        border: 1px solid var(--arimako-pro-border-color);
        border-radius: 12px;
        padding: 15px;
        margin: 25px 0;
        display: flex;
        align-items: center;
        gap: 15px;
        color: var(--arimako-pro-text-dark);
        font-weight: 500;
    }

    .arimako-pro-promo-icon {
        font-size: 20px;
        color: var(--arimako-pro-pink);
    }

    .arimako-pro-faq-section {
        background-color: #fff;
        padding: 25px 0;
    }

    .arimako-pro-faq-item-container .arimako-pro-card-header {
        background-color: #fff;
    }

    .arimako-pro-faq-item-container .arimako-pro-collapsible-content {
        background-color: #fff;
        border-top: none;
        padding-top: 0;
    }

    .arimako-pro-see-all-faqs {
        text-align: center;
        margin: 25px 0 15px;
    }

    .arimako-pro-see-all-faqs a {
        color: var(--arimako-pro-pink);
        font-weight: 600;
        text-decoration: none;
        font-size: 15px;
    }

    .arimako-pro-footer-note {
        text-align: center;
        font-size: 12px;
        color: #888;
        padding: 0 20px;
    }

    .arimako-pro-footer-note a {
        color: var(--arimako-pro-text-dark);
        font-weight: 500;
        text-decoration: underline;
    }

    .arimako-pro-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        padding: 15px 20px 20px;
        border-top: 1px solid var(--arimako-pro-border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 420px;
        margin: auto;
        z-index: 10;
    }

    .arimako-pro-price-info .starting-from {
        font-size: 12px;
        color: #555;
    }

    .arimako-pro-price-info .price {
        font-size: 20px;
        font-weight: 700;
        color: var(--arimako-pro-text-dark);
    }

    .arimako-pro-select-plan-btn {
        background-color: var(--arimako-pro-pink);
        color: var(--arimako-pro-text-light);
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="arimako-pro-body">
    <div class="arimako-pro-container">
        <div class="arimako-pro-header">
            <div class="arimako-pro-logo">
                <div class="arimako-pro-logo-icon">A</div>
                <span>Arimako<b>pro</b></span>
            </div>
            <h1 class="tw-text-white">Save like a PRO!</h1>
        </div>

        <div class="arimako-pro-content-area">
            <h2 class="arimako-pro-section-title">Monthly perks</h2>
            <div class="arimako-pro-card">
                <div class="arimako-pro-card-header">
                    <div class="arimako-pro-card-info">
                        <i class="fas fa-motorcycle arimako-pro-card-icon"></i>
                        <span class="arimako-pro-card-text">Unlimited Free Delivery</span>
                    </div>
                    <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                </div>
                <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
            </div>
            <div class="arimako-pro-card">
                <div class="arimako-pro-card-header">
                    <div class="arimako-pro-card-info">
                        <i class="fas fa-tags arimako-pro-card-icon"></i>
                        <span class="arimako-pro-card-text">Up to 60% Off on restaurants</span>
                    </div>
                    <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                </div>
                <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
            </div>

            <h2 class="arimako-pro-section-title">Surprise perks</h2>
            <div class="arimako-pro-card">
                <div class="arimako-pro-card-header">
                    <div class="arimako-pro-card-info">
                        <i class="fas fa-shopping-bag arimako-pro-card-icon"></i>
                        <span class="arimako-pro-card-text">10% off pickup</span>
                    </div>
                    <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                </div>
                <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
            </div>

            <div class="arimako-pro-promo-banner">
                <i class="fas fa-gift arimako-pro-promo-icon"></i>
                <span>Use code <b>FREEPRO</b> to get Arimakopro FREE for 1 month!</span>
            </div>

            <div class="arimako-pro-faq-section">
                <h2 class="arimako-pro-section-title" style="font-size: 20px; font-weight: 700; padding-left: 0;">Frequently Asked Questions</h2>
                <div class="arimako-pro-faq-item-container">
                    <div class="arimako-pro-card-header">
                        <span class="arimako-pro-card-text">What is Arimakopro?</span>
                        <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                    </div>
                    <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
                </div>
                <div class="arimako-pro-faq-item-container">
                    <div class="arimako-pro-card-header">
                        <span class="arimako-pro-card-text">How do I use my Arimakopro perks?</span>
                        <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                    </div>
                    <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
                </div>
                <div class="arimako-pro-faq-item-container">
                    <div class="arimako-pro-card-header">
                        <span class="arimako-pro-card-text">When can I enjoy my Arimakopro perks?</span>
                        <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                    </div>
                    <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
                </div>
                <div class="arimako-pro-faq-item-container">
                    <div class="arimako-pro-card-header">
                        <span class="arimako-pro-card-text">What are surprise perks?</span>
                        <i class="fas fa-chevron-down arimako-pro-dropdown-icon"></i>
                    </div>
                    <div class="arimako-pro-collapsible-content"><p>hello world</p></div>
                </div>
                
                <div class="arimako-pro-see-all-faqs">
                    <a href="#">See all FAQs</a>
                </div>
            </div>

            <div class="arimako-pro-footer-note">
                Subscription automatically renews each month. Check out the <a href="#">terms</a>.
            </div>
        </div>

        <div class="arimako-pro-sticky-footer">
            <div class="arimako-pro-price-info">
                <div class="starting-from">Starting from</div>
                <div class="price">$0.66 / mo.</div>
            </div>
            <button class="arimako-pro-select-plan-btn">Select plan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accordions = document.querySelectorAll('.arimako-pro-card-header');

        accordions.forEach(accordion => {
            accordion.addEventListener('click', function () {
                const icon = this.querySelector('.arimako-pro-dropdown-icon');
                if (icon) {
                    icon.classList.toggle('active');
                }

                const content = this.nextElementSibling;
                if (content && content.classList.contains('arimako-pro-collapsible-content')) {
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                    } else {
                        // Close other open accordions within the same section
                        const parent = this.closest('.arimako-pro-content-area, .arimako-pro-faq-section');
                        if (parent) {
                            parent.querySelectorAll('.arimako-pro-collapsible-content').forEach(item => {
                                if (item !== content) {
                                    item.style.display = 'none';
                                    const otherIcon = item.previousElementSibling.querySelector('.arimako-pro-dropdown-icon');
                                    if (otherIcon) otherIcon.classList.remove('active');
                                }
                            });
                        }
                        content.style.display = 'block';
                    }
                }
            });
        });
    });
</script>