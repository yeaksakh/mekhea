<!-- Job Description Section -->
   <div class="row no-print" id="job-description-print" style="margin-bottom: 20px; page-break-inside: avoid;">
    <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">JOB
        DESCRIPTION
        <button onclick="printJobDescription()" class="btn btn-secondary no-print">
            <i class="fas fa-print"></i> Print
        </button>
    </h2>

    <div class="cv-header" style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin-bottom: 5px; color: #333; font-size: 28px;">{{ $user->first_name }} {{ $user->last_name }}
        </h1>
        <p><strong>@lang('user.name_in_khmer'):</strong> <span
                style="<?php echo isIncomplete($user->name_in_khmer) ? 'color: red;' : ''; ?>">{{ $user->name_in_khmer ?? __('user.no_data') }}</span>
        </p>
    </div>
    
    <p style="line-height: 1.6; <?php echo isIncomplete($user->job_description) ? 'color: red;' : ''; ?>">
        {{ $user->job_description ?? 'A dedicated professional with diverse skills and experience. Committed to excellence and continuous improvement in my field.' }}
    </p>
</div>

<script>
      function printJobDescription() {
        var printContents = document.getElementById('job-description-print').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>


<style>
    @media print {

        body,
        html {
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            line-height: 1.2 !important;
        }

        .no-print {
            display: none !important;
        }

        .cv-container {
            display: flex !important;
            width: 100% !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            line-height: 1.2 !important;
        }

        .left-side,
        .right-side {
            padding: 15px !important;
        }

        .left-side {
            width: 30% !important;
            background: #f5f5f5 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-inside: avoid !important;
            float: left !important;
        }

        .right-side {
            width: 70% !important;
            page-break-inside: avoid !important;
            float: left !important;
        }

        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        div {
            margin-top: 0.3em !important;
            margin-bottom: 0.3em !important;
            line-height: 1.2 !important;
        }

        h2 {
            border-bottom: 1px solid #333 !important;
            padding-bottom: 1px !important;
            margin-bottom: 8px !important;
            font-size: 12px !important;
        }

        .cv-section,
        .experience-item {
            page-break-inside: avoid !important;
            margin-bottom: 10px !important;
        }

        * {
            box-sizing: border-box !important;
        }
    }

    .completion-button-container {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
    }

    @media print {
        .completion-button-container {
            display: none !important;
        }
    }

    .progress {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        transition: width 0.6s ease;
        background-color: #28a745 !important;
        /* Green color for completed portion */
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .progress-bar span {
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
</style>