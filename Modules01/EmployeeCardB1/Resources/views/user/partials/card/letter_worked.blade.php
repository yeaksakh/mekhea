<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambodian Work Certificate</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang&family=Moul&display=swap" rel="stylesheet">
    <style>
        /* Scope all styles to a unique container to avoid conflicts */
        .khmercert_container {
            font-family: "Khmer OS", "Khmer OS System", Arial, sans-serif;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            background-color: white;
            box-sizing: border-box;
            position: relative;
            line-height: 1; /* Reset inherited line-height */
        }

        /* Reset margins to avoid interference */
        .khmercert_container * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Print button */
        .khmercert_container .khmercert_print-button {
            position: absolute;
            top: 10mm;
            right: 10mm;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            z-index: 100;
        }

        .khmercert_container .khmercert_print-button:hover {
            background-color: #45a049;
        }

        /* Print styles */
        @page {
            size: A4;
            margin: 0;
            margin-top: 45px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .khmercert_container,
            .khmercert_container #khmercert_documentContent,
            .khmercert_container #khmercert_documentContent * {
                visibility: visible;
            }
            .khmercert_container {
                margin: 0;
                padding: 20mm;
                width: 210mm;
                height: 297mm;
                position: static;
            }
            .khmercert_container .khmercert_print-button {
                display: none;
            }
        }

        /* Header */
        .khmercert_container .khmercert_header {
            text-align: center;
            margin-bottom: 0.8rem;
        }

        .khmercert_container .khmercert_header p {
            font-family: "Moul", sans-serif;
            font-size: 18pt;
            margin-bottom: 0.2rem;
        }

        /* Title */
        .khmercert_container .khmercert_title {
            text-align: center;
            font-size: 16pt;
            margin: 0.8rem 0;
            text-decoration: underline;
            font-weight: bold;
        }

        /* Form sections */
        .khmercert_container .khmercert_form-section {
            margin-bottom: 0.8rem;
        }

        .khmercert_container .khmercert_form-section p {
            font-size: 12pt;
            line-height: 1.3; /* Increased from 1.2 for slightly larger line height */
            text-align: justify;
            text-indent: 20px;
        }

        .khmercert_container .khmercert_form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }

        .khmercert_container .khmercert_form-label {
            min-width: 120px;
        }

        .khmercert_container .khmercert_id-field {
            display: flex;
            align-items: center;
            margin: 0.3rem 0;
        }

        .khmercert_container .khmercert_id-boxes {
            display: flex;
            margin: 0 0.5rem;
        }

        .khmercert_container .khmercert_id-box {
            width: 30px;
            height: 30px;
            border: 1px solid #000;
            margin-right: 5px;
            display: inline-block;
            vertical-align: middle;
        }

        .khmercert_container .khmercert_id-box-span {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .khmercert_container .khmercert_id-box-group {
            display: inline-flex;
            align-items: center;
            white-space: nowrap; /* Prevent line breaks within group */
        }

        .khmercert_container .khmercert_id-box-group .khmercert_id-box-span {
            margin-right: 2px; /* Small gap between boxes in group */
        }

        .khmercert_container .khmercert_id-box-group .khmercert_id-box-span:last-child {
            margin-right: 0; /* No margin on last box in group */
        }

        .khmercert_container .khmercert_id-separator {
            padding: 0 8px; /* Equal padding on both sides for balanced spacing */
            vertical-align: middle;
            display: inline-block;
        }

        .khmercert_container .khmercert_id-wrapper {
            display: inline-flex;
            align-items: center;
            white-space: nowrap; /* Keep all elements inline */
            margin-right: 5px; /* Equal spacing around wrapper */
            margin-left: 5px;
        }

        .khmercert_container .khmercert_contact-field {
            display: flex;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }

        .khmercert_container .khmercert_contact-label {
            min-width: 150px;
        }

        .khmercert_container .khmercert_contact-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-width: 200px;
        }

        .khmercert_container .khmercert_section-title {
            text-align: center;
            font-size: 14pt;
            margin: 0.8rem 0 0.3rem;
            font-weight: bold;
            text-decoration: underline;
        }

        .khmercert_container .khmercert_list-section p {
            font-size: 12pt;
            line-height: 1.3; /* Increased from 1.2 for slightly larger line height */
            text-align: justify;
            padding-left: 20px;
        }

        .khmercert_container .khmercert_note {
            margin-top: 0.8rem;
            text-align: justify;
            font-size: 10pt;
            line-height: 1.2;
        }

        .khmercert_container .khmercert_signature {
            margin-top: 1rem;
            text-align: right;
        }

        .khmercert_container .khmercert_signature-date {
            margin-bottom: 0.3rem;
            text-align: right;
        }

        .khmercert_container .khmercert_signature-title {
            font-weight: bold;
            margin-top: 1rem;
            text-align: right;
            padding-right: 2rem;
        }

        .khmercert_container #khmercert_printFrame {
            display: none;
        }

        /* Ensure consistent box styling in print */
        @media print {
            .khmercert_id-box-span {
                display: inline-block;
                width: 30px;
                height: 30px;
                border: 1px solid #000;
                vertical-align: middle;
            }
            .khmercert_id-box-group {
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
            }
            .khmercert_id-box-group .khmercert_id-box-span {
                margin-right: 2px;
            }
            .khmercert_id-box-group .khmercert_id-box-span:last-child {
                margin-right: 0;
            }
            .khmercert_id-separator {
                padding: 0 8px; /* Equal padding in print */
                vertical-align: middle;
                display: inline-block;
            }
            .khmercert_id-wrapper {
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
                margin-right: 5px;
                margin-left: 5px;
            }
            .khmercert_form-section p {
                font-size: 12pt;
                line-height: 1.3; /* Match normal view */
                text-align: justify;
                text-indent: 20px;
            }
            .khmercert_list-section p {
                font-size: 12pt;
                line-height: 1.3; /* Match normal view */
                text-align: justify;
                padding-left: 20px;
            }
        }
    </style>
    <script>
        (function() {
            // Check for namespace conflicts
            if (window.KhmerCert) {
                console.warn('KhmerCert namespace already exists. Potential conflict detected.');
            }

            window.KhmerCert = window.KhmerCert || {};

            KhmerCert.printCertificate = function() {
                try {
                    // Create iframe
                    var iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);

                    // Clone content
                    var area = document.getElementById('khmercert_documentContent').cloneNode(true);

                    // Define print HTML
                    var html = `
                        <!DOCTYPE html>
                        <html lang="km">
                        <head>
                            <meta charset="UTF-8">
                            <title>Print Work Certificate</title>
                            <link href="https://fonts.googleapis.com/css2?family=Battambang&family=Moul&display=swap" rel="stylesheet">
                            <style>
                                * {
                                    box-sizing: border-box;
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                    font-family: "Khmer OS", "Khmer OS System", Arial, sans-serif;
                                }
                                .khmercert_documentContent {
                                    max-width: 21cm;
                                    width: 100%;
                                    margin: 0 auto;
                                    padding: 20mm;
                                    background-color: white;
                                    overflow-x: hidden;
                                    word-break: break-all;
                                    text-align: center;
                                }
                                .khmercert_header {
                                    text-align: center;
                                    margin-bottom: 0.8rem;
                                    line-height: 1;
                                }
                                .khmercert_header p {
                                    font-family: "Moul", sans-serif;
                                    font-size: 14pt;
                                }
                                .khmercert_title {
                                    text-align: center;
                                    font-size: 12pt;
                                    font-family: "Moul", sans-serif;
                                    margin: 0.8rem 0;
                                    text-decoration: underline;
                                }
                                .khmercert_form-section {
                                    margin-bottom: 0.8rem;
                                }
                                .khmercert_form-section p {
                                    font-size: 12pt;
                                    line-height: 2;
                                    font-family: "Battambang", sans-serif;
                                    text-align: justify;
                                    text-indent: 20px;
                                }
                                .khmercert_form-row {
                                    display: flex;
                                    flex-wrap: wrap;
                                    margin-bottom: 0.3rem;
                                    line-height: 1.2;
                                }
                                .khmercert_form-label {
                                    min-width: 120px;
                                }
                                .khmercert_id-field {
                                    display: flex;
                                    align-items: center;
                                    margin: 0.3rem 0;
                                }
                                .khmercert_id-boxes {
                                    display: flex;
                                    margin: 0 0.5rem;
                                }
                                .khmercert_id-box {
                                    border: 1px solid #000;
                                    margin-right: 5px;
                                    display: inline-block;
                                }
                                .khmercert_id-box-span {
                                    display: inline-block;
                                    text-align: center;
                                    padding-right: 40px;
                                    width: 30px;
                                    border: 1px solid #000;
                                }
                                .khmercert_id-box-group {
                                    display: inline-flex;
                                    align-items: center;
                                    white-space: nowrap;
                                }
                                .khmercert_id-box-group .khmercert_id-box-span {
                                    margin-right: 2px;
                                    display: inline-block;
                                    text-align: center;
                                    padding-right: 40px;
                                    width: 30px;
                                }
                                .khmercert_id-box-group .khmercert_id-box-span:last-child {
                                    margin-right: 0;
                                }
                                .khmercert_id-separator {
                                    padding: 0 8px;
                                    vertical-align: middle;
                                    display: inline-block;
                                }
                                .khmercert_id-wrapper {
                                    display: inline-flex;
                                    align-items: center;
                                    white-space: nowrap;
                                    margin-right: 5px;
                                    margin-left: 5px;
                                }
                                .khmercert_contact-field {
                                    display: flex;
                                    margin-bottom: 0.2rem;
                                    line-height: 1.2;
                                }
                                .khmercert_contact-label {
                                    min-width: 150px;
                                }
                                .khmercert_contact-value {
                                    flex: 1;
                                    border-bottom: 1px dotted #000;
                                    min-width: 200px;
                                }
                                .khmercert_section-title {
                                    text-align: center;
                                    font-size: 12pt;
                                    font-family: "Moul", sans-serif;
                                    margin: 0.8rem 0 0.3rem;
                                    text-decoration: underline;
                                }
                                .khmercert_list-section p {
                                    font-size: 12pt;
                                    line-height: 2;
                                    font-family: "Battambang", sans-serif;
                                    text-align: justify;
                                    padding-left: 20px;
                                }
                                .khmercert_note {
                                    margin-top: 0.8rem;
                                    text-align: justify;
                                    font-size: 12pt;
                                    font-family: "Battambang", sans-serif;
                                    line-height: 1.2;
                                    padding-left: 20px;
                                }
                                .khmercert_signature {
                                    margin-top: 1rem;
                                    text-align: right;
                                }
                                .khmercert_signature-date {
                                    margin-bottom: 0.3rem;
                                    text-align: right;
                                }
                                .khmercert_signature-title {
                                    margin-top: 1rem;
                                    font-family: "Moul", sans-serif;
                                    padding-right: 1rem;
                                    text-align: right;
                                }
                                @page {
                                    size: A4;
                                    margin: 20;
                                    margin-top: 45px;
                                }
                            </style>
                        </head>
                        <body>
                            <div id="khmercert_print_content"></div>
                        </body>
                        </html>
                    `;

                    // Write to iframe
                    var doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(html);
                    doc.close();

                    // Append cloned content
                    var printContent = doc.getElementById('khmercert_print_content');
                    printContent.appendChild(area);

                    // Print and remove iframe
                    setTimeout(function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        document.body.removeChild(iframe);
                    }, 500);
                } catch (e) {
                    console.error('Print error:', e);
                }
            };
        })();
    </script>
</head>
<body>
    <div class="khmercert_container">
        <button class="khmercert_print-button" onclick="KhmerCert.printCertificate()" aria-label="Print the Cambodian work certificate form">បោះពុម្ព / Print</button>
        <div id="khmercert_documentContent">
            <div class="khmercert_header">
                <p>ព្រះរាជាណាចក្រកម្ពុជា</p>
                <p>ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
                <div style="text-align: center; margin: 0.3rem 0;">
                    <hr style="width: 30%; margin: 0 auto;">
                </div>
            </div>

            <div class="khmercert_title">
                លិខិតបញ្ជាក់ការងារ
            </div>

            <div class="khmercert_form-section">
                <p>
                    ខ្ញុំបាទ/នាងខ្ញុំឈ្មោះ ឡុង ស៊ីចាន់ មានតួនាទីជា អ្នកគ្រប់គ្រង ធ្វើការនៅក្នុងសហគ្រាស គ្រឹះស្ថាន ឌឹ ហ្វក់សេស អាត ឯ.ក
                    លេខអត្តសញ្ញាណសហគ្រាសក្នុង ប.ស.ស. ៖ 
                    <span class="khmercert_id-wrapper">
                        <span class="khmercert_id-box-group">
                            <span class="khmercert_id-box-span">1</span>
                            <span class="khmercert_id-box-span">0</span>
                            <span class="khmercert_id-box-span">2</span>
                            <span class="khmercert_id-box-span">6</span>
                            <span class="khmercert_id-box-span">3</span>
                            <span class="khmercert_id-box-span">7</span>
                            <span class="khmercert_id-box-span">5</span>
                        </span>
                        <span class="khmercert_id-separator">-</span>
                        <span class="khmercert_id-box-span">ថ</span>
                    </span>
                    ដែលមានអាសយដ្ខានៈ អគារ/ 
                    ផ្ទះលេខ 09K ផ្លូវលេខ 1800 ភូមិ ទួលពពែ 
                    ឃុំ/សង្កាត់ ទួលសង្កែ ក្រុង/ស្រុក/ខណ្ឌ ឬស្សីកែវ 
                    ខេត្ត/រាជធានី ភ្នំពេញ លេខទូរសារ (Fax) ..................... លេខទូរស័ព្ទលើតុ (Desk Telephone) 098 538 907 
                    លេខទូរស័ព្ទដៃ (Hand Phone) ..................... 
                    សារអេឡិចត្រូនិក (E-mail) foxest2023@gmail.com ។
                </p>
            </div>

            <div class="khmercert_section-title">
                សូមបញ្ជាក់ថា
            </div>

            <div class="khmercert_list-section">
                <p>
                    - ឈ្មោះ: {{ $user->user_full_name ?? '................' }} ភេទ @if (!empty($user->gender)) @lang('lang_v1.' . $user->gender) @endif 
                    សញ្ជាតិ {{ $user->nation ?? 'ខ្មែរ' }} ថ្ងៃខែឆ្នាំកំណើត @if (!empty($user->dob)) {{ @format_date($user->dob) }} @endif 
                    កាន់អត្តសញ្ញាណប័ណ្ណលេខ {{ $user->id_proof_name ?? '................' }} លេខទូរស័ព្ទដៃ (Hand Phone) {{ $user->contact_number ?? '................' }} <br>
                    - @lang('lang_v1.permanent_address'): {{ $user->permanent_address ?? 'អគារ/ផ្ទះលេខ ................ ផ្លូវលេខ ................ ភូមិ ................ឃុំ/សង្កាត់ ................ ក្រុង/ស្រុក/ខណ្ឌ ................ ខេត្ត/រាជធានី ................' }}  
                    <br>
                    - @lang('lang_v1.current_address'): {{ $user->current_address ?? 'អគារ/ផ្ទះលេខ ................ ផ្លូវលេខ ................ ភូមិ ................ឃុំ/សង្កាត់ ................ ក្រុង/ស្រុក/ខណ្ឌ ................ ខេត្ត/រាជធានី ................' }}  
                    <br>
                    - ពិតជាបានបម្រើការងារនៅសហគ្រាស គ្រឹះស្ថានរបស់ខ្ញុំបាទ/នាងខ្ញុំ តាំងពីថ្ងៃទី {{ $user->member_date ? collect(str_split(\Carbon\Carbon::parse($user->member_date)->format('d')))->map(fn($d) => ['០','១','២','៣','៤','៥','៦','៧','៨','៩'][$d])->implode('') : '...' }} 
                    ខែ {{ $user->member_date ? ['មករា','កុម្ភៈ','មីនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ'][\Carbon\Carbon::parse($user->member_date)->month - 1] : '...' }} 
                    ឆ្នាំ {{ $user->member_date ? collect(str_split(\Carbon\Carbon::parse($user->member_date)->year))->map(fn($d) => ['០','១','២','៣','៤','៥','៦','៧','៨','៩'][$d])->implode('') : '...' }} 
                    រហូតដល់បច្ចុប្បន្ន នៅផ្នែក {{ $user_designation->name ?? '................'}} ពិតប្រាកដមែន។
                </p>
            </div>

            <div class="khmercert_note">
                ខ្ញុំបាទ/នាងខ្ញុំចេញលិខិតបញ្ជាក់នេះឱ្យសាមីខ្លួនយកទៅប្រើប្រាស់តាមការងារណាដែលអាចប្រើបាន។
            </div>

            <div class="khmercert_signature">
                <div class="khmercert_signature-date">
                    <span class="khmercert_current-place"><script>document.currentScript.parentElement.textContent = 'ភ្នំពេញ';</script></span>
                    <span>ថ្ងៃទី</span>
                    <span class="khmercert_current-day"><script>document.currentScript.parentElement.textContent = new Date('2025-05-15T16:18:00+07:00').getDate().toString().padStart(2, '0').split('').map(d => ['០','១','២','៣','៤','៥','៦','៧','៨','៩'][parseInt(d)]).join('');</script></span>
                    <span>ខែ</span>
                    <span class="khmercert_current-month"><script>document.currentScript.parentElement.textContent = ['មករា','កុម្ភៈ','មីនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ'][new Date('2025-05-15T16:18:00+07:00').getMonth()];</script></span>
                    <span>ឆ្នាំ</span>
                    <span class="khmercert_current-year"><script>document.currentScript.parentElement.textContent = new Date('2025-05-15T16:18:00+07:00').getFullYear().toString().split('').map(d => ['០','១','២','៣','៤','៥','៦','៧','៨','៩'][parseInt(d)]).join('');</script></span>
                </div>
                <div class="khmercert_signature-title">
                    ហត្ថលេខា និងត្រានិយោជក
                </div>
            </div>
        </div>
    </div>
</body>
</html>