$(document).ready(function() {
    // --- DATE FILTER LOGIC ---
    const dateFilter = {
        $toggle: $('.date-filter-toggle'),
        $menu: $('.date-filter-menu'),
        $overlay: $('.modal-overlay'),
        $customStartDate: $('#custom-start-date'),
        $customEndDate: $('#custom-end-date'),
        $applyButton: $('.date-filter-menu .apply-button'),
        $startDateInput: $('#start_date'),
        $endDateInput: $('#end_date'),
        $dateRangeDisplay: $('#date-range-display'),

        init: function() {
            if (!this.$toggle.length) return;
            this.bindEvents();
            this.setDefaultDates();
        },

        bindEvents: function() {
            this.$toggle.on('click', () => this.toggleMenu());
            this.$overlay.on('click', () => this.closeMenu());
            
            $('.date-filter-menu .preset-option').on('click', (e) => {
                this.applyPresetRange($(e.currentTarget).data('range'));
            });

            this.$applyButton.on('click', () => this.applyCustomRange());

            // Initialize Datepickers
            this.$customStartDate.datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            }).on('changeDate', (e) => {
                this.$customEndDate.datepicker('setStartDate', e.date);
                this.validateCustomRange();
            });

            this.$customEndDate.datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            }).on('changeDate', (e) => {
                this.$customStartDate.datepicker('setEndDate', e.date);
                this.validateCustomRange();
            });
        },

        toggleMenu: function() {
            this.$menu.toggleClass('active');
            this.$overlay.toggleClass('active');
            this.$toggle.toggleClass('active');
        },

        closeMenu: function() {
            this.$menu.removeClass('active');
            this.$overlay.removeClass('active');
            this.$toggle.removeClass('active');
        },

        setDefaultDates: function() {
            const today = moment();
            this.updateDateRange(today.clone().startOf('month'), today.clone().endOf('month'), 'This Month');
        },

        updateDateRange: function(startDate, endDate, filterText) {
            const diffDays = endDate.diff(startDate, 'days') + 1;
            const daysText = diffDays > 0 ? `(${diffDays}d)` : '';

            this.$startDateInput.val(startDate.format('YYYY-MM-DD'));
            this.$endDateInput.val(endDate.format('YYYY-MM-DD'));

            this.$toggle.find('.toggle-text').text(filterText);
            this.$dateRangeDisplay.text(`Date: ${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')} ${daysText}`);

            loadData();
        },

        applyPresetRange: function(range) {
            const now = moment();
            let startDate, endDate, filterText;

            switch (range) {
                case 'today':
                    startDate = now.clone().startOf('day');
                    endDate = now.clone().endOf('day');
                    filterText = 'Today';
                    break;
                case 'yesterday':
                    startDate = now.clone().subtract(1, 'day').startOf('day');
                    endDate = now.clone().subtract(1, 'day').endOf('day');
                    filterText = 'Yesterday';
                    break;
                case 'this-week':
                    startDate = now.clone().startOf('isoWeek');
                    endDate = now.clone().endOf('isoWeek');
                    filterText = 'This Week';
                    break;
                case 'this-month':
                    startDate = now.clone().startOf('month');
                    endDate = now.clone().endOf('month');
                    filterText = 'This Month';
                    break;
                case 'last-month':
                    startDate = now.clone().subtract(1, 'month').startOf('month');
                    endDate = now.clone().subtract(1, 'month').endOf('month');
                    filterText = 'Last Month';
                    break;
                case 'this-year':
                    startDate = now.clone().startOf('year');
                    endDate = now.clone().endOf('year');
                    filterText = 'This Year';
                    break;
                case 'last-year':
                    startDate = now.clone().subtract(1, 'year').startOf('year');
                    endDate = now.clone().subtract(1, 'year').endOf('year');
                    filterText = 'Last Year';
                    break;
            }

            if (startDate && endDate) {
                this.updateDateRange(startDate, endDate, filterText);
                this.closeMenu();
            }
        },

        validateCustomRange: function() {
            const startDate = this.$customStartDate.datepicker('getDate');
            const endDate = this.$customEndDate.datepicker('getDate');
            this.$applyButton.prop('disabled', !startDate || !endDate);
        },

        applyCustomRange: function() {
            const startDate = this.$customStartDate.datepicker('getDate');
            const endDate = this.$customEndDate.datepicker('getDate');
            
            if (startDate && endDate) {
                this.updateDateRange(
                    moment(startDate),
                    moment(endDate),
                    'Custom Range'
                );
                this.closeMenu();
            }
        }
    };

    // --- ROW FILTER LOGIC ---
    const rowFilter = {
        $toggle: $('.row-filter-toggle'),
        $menu: $('.row-filter-menu'),
        $overlay: $('.modal-overlay'),
        $customRows: $('#custom-rows'),
        $applyButton: $('.custom-rows .apply-button'),
        $rowsInput: $('#rows_per_page'),

        init: function() {
            if (!this.$toggle.length) return;
            this.bindEvents();
            this.setDefaultRows();
        },

        bindEvents: function() {
            this.$toggle.on('click', () => this.toggleMenu());
            this.$overlay.on('click', () => this.closeMenu());
            
            $('.row-filter-menu .preset-option').on('click', (e) => {
                this.applyPresetRows($(e.currentTarget).data('rows'));
            });

            this.$customRows.on('input', () => this.validateCustomRows());
            this.$applyButton.on('click', () => this.applyCustomRows());
        },

        toggleMenu: function() {
            this.$menu.toggleClass('active');
            this.$overlay.toggleClass('active');
            this.$toggle.toggleClass('active');
        },

        closeMenu: function() {
            this.$menu.removeClass('active');
            this.$overlay.removeClass('active');
            this.$toggle.removeClass('active');
        },

        setDefaultRows: function() {
            this.updateRowCount(10, '10 Rows');
        },

        updateRowCount: function(count, filterText) {
            this.$rowsInput.val(count);
            this.$toggle.find('.toggle-text').text(filterText);
            loadData();
        },

        applyPresetRows: function(rows) {
            this.updateRowCount(rows, rows + ' Rows');
            this.closeMenu();
        },

        validateCustomRows: function() {
            const value = this.$customRows.val();
            const isValid = value && value >= 1 && value <= 1000;
            this.$applyButton.prop('disabled', !isValid);
        },

        applyCustomRows: function() {
            const rows = parseInt(this.$customRows.val(), 10);
            if (rows >= 1 && rows <= 1000) {
                this.updateRowCount(rows, rows + ' Rows');
                this.closeMenu();
            }
        }
    };

    // Initialize filters
    dateFilter.init();
    rowFilter.init();

    // --- DATA LOADING ---
    function loadData() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const rowsPerPage = $('#rows_per_page').val();
        const sellerCards = $('#seller-cards');

        if (!startDate || !endDate) {
            console.error("Date range is not set");
            sellerCards.html('<div class="error">Please select a date range</div>');
            return;
        }

        sellerCards.html('<div class="loading">Loading...</div>');

        $.ajax({
            url: window.reportAjaxUrl,
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                rows_per_page: rowsPerPage
            },
            success: function(response) {
                if (response.data && Array.isArray(response.data)) {
                    if (response.data.length === 0) {
                        sellerCards.html('<div class="no-data">No data available for selected date range</div>');
                        return;
                    }
                    sellerCards.html(window.renderReportData(response.data));
                } else {
                    console.error('Invalid response format:', response);
                    sellerCards.html('<div class="error">Error: Invalid data format received</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                sellerCards.html('<div class="error">Error loading data</div>');
            }
        });
    }

    // --- SHARE FUNCTIONALITY ---
    window.showShareMenu = function() {
        $('.share-menu, .share-overlay').addClass('active');
    };

    window.hideShareMenu = function() {
        $('.share-menu, .share-overlay').removeClass('active');
    };

    window.shareVia = function(platform) {
        const url = window.location.href;
        const title = document.title;
        const text = 'Check out this report!';

        let shareUrl;
        switch(platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;
            case 'whatsapp':
                shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
                break;
            case 'email':
                shareUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(text + '\n\n' + url)}`;
                break;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank');
        }
        hideShareMenu();
    };

    window.copyLink = function() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!');
        }).catch(() => {
            const textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('Link copied to clipboard!');
        });
        hideShareMenu();
    };

    function showToast(message) {
        const toast = $('.toast');
        toast.find('span').text(message);
        toast.addClass('active');
        setTimeout(() => {
            toast.removeClass('active');
        }, 2000);
    }
});