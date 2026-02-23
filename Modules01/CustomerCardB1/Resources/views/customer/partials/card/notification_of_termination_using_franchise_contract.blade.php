<!DOCTYPE html>
<html lang="km">
@php

    // Set the locale and timezone if needed
    date_default_timezone_set('Asia/Phnom_Penh');

    // Get the current date
    $noticeDay = date('d');
    $noticeMonthNumber = date('n'); // 1-12
    $noticeYear = date('Y');

    // Khmer month names
    $khmerMonths = [
        1 => 'មករា',
        2 => 'កម្ភៈ',
        3 => 'មីនា',
        4 => 'មេសា',
        5 => 'ឧសភា',
        6 => 'មិថុនា',
        7 => 'កក្កដា',
        8 => 'សីហា',
        9 => 'កញ្ញា',
        10 => 'តុលា',
        11 => 'វិច្ឆិកា',
        12 => 'ធ្នូ'
    ];

    $noticeMonth = $khmerMonths[$noticeMonthNumber];

    $registerDate = $contact->register_date ?? '2023-01-01';
    $khmerMonths = [
        1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា',
        7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'
    ];
    $date = Carbon::parse($registerDate);
    $dayReg = $date->day;
    $monthReg = $khmerMonths[$date->month];
    $yearReg = $date->year;

    $expiredDate = $contact->expired_date ?? '-';
    $khmerMonthsExp = [
        1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា',
        7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'
    ];
    if ($expiredDate !== '-') {
        $dateExp = Carbon::parse($expiredDate);
        $dayExp = $dateExp->day;
        $monthExp = $khmerMonthsExp[$dateExp->month];
        $yearExp = $dateExp->year;
    } else {
        $dayExp = '-';
        $monthExp = '-';
        $yearExp = '-';
    }
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>សេចក្តីជូនដំណឹងស្តីអំពីការបញ្ចប់ការប្រើប្រាស់ស្លាកយីហោ</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: 'Battambang', sans-serif;
        }
        .notif_term_wrapper {
            margin: 0 auto;
            max-width: 21cm;
            width: 100%;
            overflow-x: hidden;
            word-break: break-all;
            text-align: center;
        }
        .notif_term_wrapper .font-moul {
            font-family: 'Moul', sans-serif;
        }
        .notif_term_wrapper .notif_term_print_container {
            background-color: #ffffff;
            padding: 5px;
            margin: 0 auto;
            max-width: 21cm;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .notif_term_wrapper .notif_term_container {
            margin: 0 auto;
            max-width: 100%;
        }
        .notif_term_wrapper .notif_term_text_center {
            text-align: center;
        }
        .notif_term_wrapper .notif_term_mb_2 {
            margin-bottom: 0.5rem;
        }
        .notif_term_wrapper .notif_term_mb_4 {
            margin-bottom: 1rem;
        }
        .notif_term_wrapper .notif_term_my_2 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .notif_term_wrapper .notif_term_my_4 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .notif_term_wrapper .notif_term_mt_2 {
            margin-top: 0.5rem;
        }
        .notif_term_wrapper .notif_term_mt_4 {
            margin-top: 1rem;
        }
        .notif_term_wrapper .notif_term_mt_20 {
            margin-top: 5rem;
        }
        .notif_term_wrapper .notif_term_pb_2 {
            padding-bottom: 0.5rem;
        }
        .notif_term_wrapper .notif_term_pl_5 {
            padding-left: 1.25rem;
            text-align: left;
        }
        .notif_term_wrapper .notif_term_font_bold {
            font-weight: 700;
        }
        .notif_term_wrapper .notif_term_text_lg {
            font-size: 1.125rem;
        }
        .notif_term_wrapper .notif_term_text_sm {
            font-size: 0.875rem;
        }
        .notif_term_wrapper .notif_term_text_xl {
            font-size: 1.25rem;
        }
        .notif_term_wrapper .notif_term_w_33 {
            width: 33.333333%;
            margin-left: auto;
        }
        .notif_term_wrapper .notif_term_border_b {
            border-bottom: 1px solid #000000;
        }
        .notif_term_wrapper .notif_term_flex {
            display: flex;
            justify-content: center;
        }
        .notif_term_wrapper .notif_term_justify_between {
            justify-content: space-between;
        }
        .notif_term_wrapper .notif_term_print_button {
            margin: 0.5rem auto;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            display: block;
        }
        .notif_term_wrapper .notif_term_print_button:hover {
            background-color: #2563eb;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .notif_term_wrapper,
            .notif_term_wrapper #notif_term_area,
            .notif_term_wrapper #notif_term_area * {
                visibility: visible;
            }
            .notif_term_wrapper {
                margin: 0 auto;
                padding: 0;
                text-align: center;
            }
            .notif_term_wrapper #notif_term_area {
                position: static;
                width: 100%;
                max-width: 21cm;
                margin: 0 auto;
                padding: 1.5cm;
                text-align: center;
            }
            .notif_term_wrapper .notif_term_print_button {
                display: none;
            }
            .notif_term_wrapper .notif_term_pl_5 {
                text-align: left;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="notif_term_wrapper">
        <button onclick="notifTermPrintNotice()" class="notif_term_print_button">Print</button>
        <div class="notif_term_print_container" id="notif_term_area">
            <div class="notif_term_text_center notif_term_mb_4">
                <div class="font-moul notif_term_text_lg">ព្រះរាជាណាចក្រកម្ពុជា</div>
                <div class="font-moul notif_term_text_sm">ជាតិ សាសនា ព្រះមហាក្សត្រ</div>
                <div class="notif_term_w_33 notif_term_border_b notif_term_my_4" style="margin-right: auto;"></div>
            </div>

            <div class="notif_term_container">
                <div class="notif_term_text_center notif_term_font_bold notif_term_text_xl notif_term_mb_2">សេចក្តីជូនដំណឹង</div>
                <div class="notif_term_text_center notif_term_mb_4">ស្តីអំពីការបញ្ចប់ការប្រើប្រាស់ស្លាកយឺហោ</div>

                <div class="notif_term_mb_2">
                    យោងកិច្ចសន្យាលេខ៖ {{ $contact->contact_id ?? '........................' }}
                </div>
{{-- Ask before can do --}}
                <div class="notif_term_my_2">
                    រវាង ក្រុមហ៊ុន <span class="notif_term_font_bold">The Foxest Art Co.,Ltd.</span> តំណាងដោយលោកស្រី ឡុង ស៊ីចាន់ ជាតំណាងស្របច្បាប់ទៅលើស្លាកយីហោ បោកអ៊ុតយក្សា (YEAKSA) និង ហេម៉ា (HEMA) និង លោក/លោកស្រី {{ $contact->name ?? '........................' }} ម្ចាស់ហាងបោកអ៊ុត ស្លាកយីហោ (YEAKSA) បានចុះកិច្ចព្រមព្រៀងក្នុងការប្រើប្រាស់នៅថ្ងៃទី {{ $dayReg }} ខែ {{ $monthReg }} ឆ្នាំ {{ $yearReg }} រហូតដល់ថ្ងៃទី {{ $dayExp ?? '...' }} ខែ {{ $monthExp ?? '...' }} ឆ្នាំ {{ $yearExp ?? '...' }} ដែលមានទីតាំងនៅ {{ $contact->city ? $contact->city : '................' }} {{ $contact->state ?? '................' }} {{ $contact->country ?? '................' }} {{ $contact->address_line_1 ?? '................' }} {{ $contact->address_line_2 ?? '................' }}
                </div>

                <div class="notif_term_my_2">
                    ក្រុមហ៊ុន <span class="notif_term_font_bold">The Foxest Art Co.,Ltd.</span> សូមជូនដំណឹងដល់ម្ចាស់អាជីវកម្មខាងលើ ជ្រាបថាក្រុមហ៊ុនសម្រេចបញ្ចប់កិច្ចសន្យាជាស្ថាពរ និងមិនអនុញ្ញាតអោយបន្តប្រើប្រាស់ស្លាកយឺហោបោកអ៊ុតខាងលើបន្តទៀតទេដោយមានមូលហេតុ និងលក្ខខណ្ឌដូចខាងក្រោម។
                </div>

                <div class="notif_term_pl_5 notif_term_my_2">
                    <span class="notif_term_font_bold">១.</span> មូលហេតុម្ចាស់ហាងបោកអ៊ុតមិនបានបំពេញលក្ខខណ្ឌទៅតាមកិច្ចសន្យា<span style="color: red;">ប្រការទី​ ២</span> ដូចដែលបានកំណត់ក្នុងកិច្ចព្រមព្រៀងការប្រើប្រាស់ស្លាកយីហោ។
                </div>
                <div class="notif_term_pl_5 notif_term_my_2">
                    <span class="notif_term_font_bold">២.</span> ម្ចាស់ហាងបោកអ៊ុតត្រូវទំលាក់ ឬលុបស្លាកយីហោ ឈ្មោះ {{ $contact->supplier_business_name ?? '........................' }} ឱ្យបានមុនថ្ងៃទី {{ $dayExp ?? '.........' }} ខែ {{ $monthExp ?? '.........' }} ឆ្នាំ {{ $yearExp ?? '.........' }}។ ករណីរកឃើញនៅបន្តក្នុងការប្រើប្រាស់ឈ្មោះស្លាកយីហោនោះ ភាគីអ្នកប្រើនឹងត្រូវរងចាំការចាត់វិធានការតាមផ្លូវច្បាប់។
                </div>
                <div class="notif_term_pl_5 notif_term_my_2">
                    <span class="notif_term_font_bold">៣.</span> ម្ចាស់ហាងបោកអ៊ុតត្រូវធានាបានថាមិនមានការប្រើប្រាស់ស្លាកយីហោបន្តទៀតក្រោយបញ្ចប់នេះឡើយ។
                </div>

                <div class="notif_term_my_2">
                    អាស្រ័យដូចបានជូនដំណឹងខាងលើ សូមលោក-លោកស្រីជាម្ចាស់ហាងអភ័យទោស​ និងចូលរូមសហការណ័ដោយរីករាយក្នុងការអនុវត្តន័សេចក្តីជូនដំណឹងនេះ ។ ក្រុមហ៊ុន <span class="notif_term_font_bold">The Foxest Art Co.,Ltd.</span> រីករាយ និងមានឆន្ទះបើកទ្វារងចាំទទួលការពិភាក្សា និងស្វាគមន័ ក្នុងករណីសូមលោក-លោកស្រីជាម្ចាស់ហាង ចង់បន្តការប្រើប្រាស់ស្លាកយីហោ និងផលិតផលរបស់ក្រុមហ៊ុននៅថ្ងៃអនាគត។
                </div>

                <div style="text-align: right;">
                    <div class="notif_term_my_2" style="margin-right: 30px;">
                        ភ្នំពេញ ថ្ងៃទី {{ $noticeDay ?? '...' }} ខែ {{ $noticeMonth ?? '...' }} ឆ្នាំ {{ $noticeYear ?? '2025' }}
                    </div>
                    <div class="notif_term_mt_4" style="margin-right: 20px;">
                        <div class="notif_term_font_bold" style="padding-right: 65px">នាយិកាក្រុមហ៊ុន</div>
                        <div class="notif_term_border_b notif_term_w_33" style="margin-top: 8.5rem; margin-bottom: 1rem;"></div>
                        <div style="padding-right: 50px">លោកស្រី ឡុង ស៊ីចាន់</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function notifTermPrintNotice() {
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        var area = document.getElementById("notif_term_area").cloneNode(true);

        var html = `
            <!DOCTYPE html>
            <html lang="km">
            <head>
                <meta charset="UTF-8">
                <title>Print Termination Notice</title>
                <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
                <style>
                    * {
                        box-sizing: border-box;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        font-family: 'Battambang', sans-serif;
                    }
                    .font-moul {
                        font-family: 'Moul', sans-serif;
                    }
                    .notif_term_print_container {
                        max-width: 21cm;
                        width: 100%;
                        margin: 0 auto;
                        padding: 0.5cm;
                        background-color: #ffffff;
                        overflow-x: hidden;
                        word-break: break-all;
                        text-align: center;
                    }
                    .notif_term_container {
                        margin: 0 auto;
                        max-width: 100%;
                    }
                    .notif_term_text_center {
                        text-align: center;
                    }
                    .notif_term_mb_2 {
                        margin-bottom: 0.5rem;
                    }
                    .notif_term_mb_4 {
                        margin-bottom: 1rem;
                    }
                    .notif_term_my_2 {
                        margin-top: 0.5rem;
                        margin-bottom: 0.5rem;
                    }
                    .notif_term_my_4 {
                        margin-top: 0.5rem;
                        margin-bottom: 0.5rem;
                    }
                    .notif_term_mt_2 {
                        margin-top: 0.5rem;
                    }
                    .notif_term_mt_4 {
                        margin-top: 1rem;
                    }
                    .notif_term_mt_20 {
                        margin-top: 5rem;
                    }
                    .notif_term_pb_2 {
                        padding-bottom: 0.5rem;
                    }
                    .notif_term_pl_5 {
                        padding-left: 1.25rem;
                        text-align: left;
                    }
                    .notif_term_font_bold {
                        font-weight: 700;
                    }
                    .notif_term_text_lg {
                        font-size: 1.125rem;
                    }
                    .notif_term_text_sm {
                        font-size: 0.875rem;
                    }
                    .notif_term_text_xl {
                        font-size: 1.25rem;
                    }
                    .notif_term_w_33 {
                        width: 33.333333%;
                        margin-left: auto;
                    }
                    .notif_term_border_b {
                        border-bottom: 1px solid #000000;
                    }
                    .notif_term_flex {
                        display: flex;
                        justify-content: center;
                    }
                    .notif_term_justify_between {
                        justify-content: space-between;
                    }
                    @page {
                        size: A4;
                        margin-top: 10px;
                        margin-inline: -10px;
                        margin-bottom: 2.5cm;
                    }
                </style>
            </head>
            <body>
                <div id="notif_term_print_content"></div>
            </body>
            </html>
        `;

        var doc = iframe.contentWindow.document;
        doc.open();
        doc.write(html);
        doc.close();

        var printContent = doc.getElementById('notif_term_print_content');
        printContent.appendChild(area);

        setTimeout(function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            document.body.removeChild(iframe);
        }, 500);
    }
    </script>
</body>
</html>