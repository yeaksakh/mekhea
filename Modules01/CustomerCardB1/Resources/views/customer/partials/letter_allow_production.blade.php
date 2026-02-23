<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>លិខិតអនុញ្ញាតផលិតកម្ម</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap');
        
        /* Wrapper and print area (modeled after franchise contract) */
        .jester_letter_allow_production_wrapper {
            margin: 0 auto;
            max-width: 21cm;
            width: 100%;
            overflow-x: hidden;
            word-break: break-word;
            text-align: center;
            background-color: #f5f5f5;
        }
        .jester_letter_allow_production_toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .jester_letter_allow_production_print_button {
            margin: 0.5rem auto;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .jester_letter_allow_production_print_button:hover {
            background-color: #2563eb;
        }
        .jester_letter_allow_production_print_container {
            width: 210mm;
            min-height: 297mm;
            background-color: #ffffff;
            padding: 20mm;
            margin: 0 auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12), 0 6px 6px rgba(0, 0, 0, 0.10);
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Khmer', sans-serif;
            background-color: #f5f5f5;
            padding: 0;
            margin: 0;
            line-height: 1.6;
        }
        
        .jester_letter_allow_production_document {
            width: 100%;
            min-height: calc(297mm - 40mm);
        }
        
        @media print {
            body * { visibility: hidden; }
            .jester_letter_allow_production_wrapper,
            .jester_letter_allow_production_wrapper #jester_letter_allow_production_area,
            .jester_letter_allow_production_wrapper #jester_letter_allow_production_area * {
                visibility: visible;
            }
            .jester_letter_allow_production_wrapper {
                margin: 0 auto;
                padding: 0;
                text-align: center;
                background: transparent;
            }
            .jester_letter_allow_production_wrapper #jester_letter_allow_production_area {
                position: static;
                width: 100%;
                max-width: 21cm;
                margin: 0 auto;
                padding: 1.5cm;
                text-align: left;
            }
            .jester_letter_allow_production_toolbar, .jester_letter_allow_production_print_button { display: none; }
            .jester_letter_allow_production_print_container {
                width: 210mm;
                min-height: 297mm;
                padding: 20mm;
                margin: 0;
                box-shadow: none;
                border: none;
            }
            @page { size: A4; margin: 0; }
        }
        
        .jester_letter_allow_production_header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
            text-decoration: underline;
        }
        
        .jester_letter_allow_production_form_section {
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .jester_letter_allow_production_inline_field {
            display: inline-block;
            border-bottom: 1px solid black;
            min-width: 150px;
            height: 20px;
            margin: 0 3px;
        }
        
        .jester_letter_allow_production_long_field {
            display: inline-block;
            border-bottom: 1px solid black;
            min-width: 400px;
            height: 20px;
            margin: 0 3px;
        }
        
        .jester_letter_allow_production_very_long_field {
            display: inline-block;
            border-bottom: 1px solid black;
            min-width: 450px;
            height: 20px;
            margin: 0 3px;
        }
        
        .jester_letter_allow_production_short_field {
            display: inline-block;
            border-bottom: 1px solid black;
            min-width: 100px;
            height: 20px;
            margin: 0 3px;
        }
        
        .jester_letter_allow_production_content_paragraph {
            line-height: 1.8;
            margin: 20px 0;
            text-align: justify;
        }
        
        .jester_letter_allow_production_numbered_list {
            margin: 20px 0;
        }
        
        .jester_letter_allow_production_numbered_item {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .jester_letter_allow_production_number {
            margin-right: 10px;
            min-width: 20px;
        }
        
        .jester_letter_allow_production_item_line {
            flex: 1;
            border-bottom: 1px solid black;
            height: 20px;
        }
        
        .jester_letter_allow_production_company_name {
            font-weight: bold;
        }
        
        .jester_letter_allow_production_signature_section {
            margin-top: 40px;
        }
        
        .jester_letter_allow_production_signature_area {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
        }
        
        .jester_letter_allow_production_signature_block {
            text-align: center;
        }
        
        .jester_letter_allow_production_signature_line {
            border-bottom: 1px solid black;
            width: 275px;
            height: 20px;
            margin: 10px 0;
        }
        
        .jester_letter_allow_production_date_location {
            text-align: left;
        }
        
        .jester_letter_allow_production_date_line {
            display: inline-block;
            border-bottom: 1px solid black;
            width: 125px;
            height: 20px;
            margin: 0 5px;
        }
        
        .jester_letter_allow_production_right_signature {
            text-align: center;
        }
        
        .jester_letter_allow_production_field_group {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="jester_letter_allow_production_wrapper">
        <div class="jester_letter_allow_production_toolbar">
            <button onclick="jesterLetterAllowProductionPrint()" class="jester_letter_allow_production_print_button" aria-label="Print letter">
                Print
            </button>
        </div>
        <div class="jester_letter_allow_production_print_container" id="jester_letter_allow_production_area">
            <div class="jester_letter_allow_production_document">
                <div class="jester_letter_allow_production_header">
                    លិខិតអនុញ្ញាតផលិតកម្ម
                </div>

                <div class="jester_letter_allow_production_form_section">
                    ក្រុមហ៊ុន <div class="jester_letter_allow_production_very_long_field"></div> តំណាងដោយ លោក.ស្រី <div class="jester_letter_allow_production_long_field"></div> អត្តសញ្ញាណប័ណ្ណលេខ <div class="jester_letter_allow_production_inline_field"></div> ផុតសុពលភាពថ្ងៃទី<div class="jester_letter_allow_production_short_field"></div> ទីតាំងចេញប័ណ្ណ<div class="jester_letter_allow_production_inline_field"></div>
                </div>

                <div class="jester_letter_allow_production_content_paragraph">
                    បានផ្ដល់ការអនុញ្ញាតឱ្យ ក្រុមហ៊ុន <span class="jester_letter_allow_production_company_name">ឌឹហ្វក់សេស អាត ខូអិលធីឌី</span> ផលិតនូវផលិតផល មានដូចជា ៖
                </div>

                <div class="jester_letter_allow_production_numbered_list">
                    <div class="jester_letter_allow_production_numbered_item">
                        <span class="jester_letter_allow_production_number">១.</span>
                        <div class="jester_letter_allow_production_item_line"></div>
                    </div>
                    <div class="jester_letter_allow_production_numbered_item">
                        <span class="jester_letter_allow_production_number">២.</span>
                        <div class="jester_letter_allow_production_item_line"></div>
                    </div>
                    <div class="jester_letter_allow_production_numbered_item">
                        <span class="jester_letter_allow_production_number">៣.</span>
                        <div class="jester_letter_allow_production_item_line"></div>
                    </div>
                    <div class="jester_letter_allow_production_numbered_item">
                        <span class="jester_letter_allow_production_number">៤.</span>
                        <div class="jester_letter_allow_production_item_line"></div>
                    </div>
                    <div class="jester_letter_allow_production_numbered_item">
                        <span class="jester_letter_allow_production_number">៥.</span>
                        <div class="jester_letter_allow_production_item_line"></div>
                    </div>
                </div>

                <div class="jester_letter_allow_production_content_paragraph">
                    ផលិតផលនេះបានចុះបញ្ជីពាណិជ្ជកម្មនៅ ប្រទេសកម្ពុជា និងនឹងត្រូវដឹកជញ្ជូនចេញពី ក្រុមហ៊ុន <span class="jester_letter_allow_production_company_name">ឌឹហ្វក់សេសអាត ខូអិលធីឌី</span> ទាំងអស់ បន្ទាប់ពីផលិតរួចរាល់ ។ ក្រុមហ៊ុន <span class="jester_letter_allow_production_company_name">ឌឹហ្វក់សេសអាត ខូអិលធីឌី</span> ជាអ្នកផលិតធានាថា ផលិតផលនេះ នឹងមិនត្រូវ ចែកចាយ នៅក្នុងទីផ្សារក្នុងនាមជារបស់ខ្លួននោះទេ ។<br> ម៉ាក ទាំងនេះ គឺជាកម្មសិទ្ធិរបស់ក្រុមហ៊ុន<div class="jester_letter_allow_production_long_field" style="margin-left: 5px;"></div>
                </div>

                <div class="jester_letter_allow_production_content_paragraph">
                    ប្រសិនបើមានជម្លោះផ្នែកកម្មសិទ្ធិបញ្ញាអំពីស្លាកសញ្ញា ក្រុមហ៊ុននឹងទទួលខុសត្រូវលើចំណាយ និង ភារកិច្ច ទាំង អស់ ដែលកើតឡើង។
                </div>

                <div class="jester_letter_allow_production_signature_section">
                    <div class="jester_letter_allow_production_signature_area" style="justify-content: flex-end;">
                        <div class="jester_letter_allow_production_right_signature">
                            <div style="margin-bottom: 15px;">ធ្វើនៅ<div class="jester_letter_allow_production_date_line"></div>ថ្ងៃទី<div class="jester_letter_allow_production_date_line"></div></div>
                            <div style="margin-bottom: 15px; text-align: right; margin-right: 5px;">ហត្ថលេខា និងត្រា</div>
                            <div class="jester_letter_allow_production_signature_line" style="margin-left: 40px; min-width: 275px;"></div>
                            <div style="margin-bottom: 10px;">ឈ្មោះ៖<div class="jester_letter_allow_production_inline_field" style="margin-left: 10px; min-width: 240px;"></div></div>
                            <div>តួនាទី៖<div class="jester_letter_allow_production_inline_field" style="margin-left: 10px; min-width: 240px;"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function jesterLetterAllowProductionPrint() {
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            var area = document.getElementById('jester_letter_allow_production_area').cloneNode(true);

            var html = `
                <!DOCTYPE html>
                <html lang="km">
                <head>
                    <meta charset="UTF-8">
                    <title>Print Letter Allow Production</title>
                    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
                    <style>
                        * { box-sizing: border-box; }
                        body { margin: 0; padding: 0; font-family: 'Noto Sans Khmer', sans-serif; background: #fff; }
                        .jester_letter_allow_production_print_container {
                            width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm;
                            background-color: #ffffff; overflow: hidden; text-align: left;
                        }
                        .jester_letter_allow_production_document {
                            width: 100%; min-height: calc(297mm - 40mm);
                        }
                        .jester_letter_allow_production_header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 30px; text-decoration: underline; }
                        .jester_letter_allow_production_form_section { margin-bottom: 20px; line-height: 1.8; }
                        .jester_letter_allow_production_inline_field { display: inline-block; border-bottom: 1px solid black; min-width: 150px; height: 20px; margin: 0 3px; }
                        .jester_letter_allow_production_long_field { display: inline-block; border-bottom: 1px solid black; min-width: 400px; height: 20px; margin: 0 3px; }
                        .jester_letter_allow_production_very_long_field { display: inline-block; border-bottom: 1px solid black; min-width: 450px; height: 20px; margin: 0 3px; }
                        .jester_letter_allow_production_short_field { display: inline-block; border-bottom: 1px solid black; min-width: 100px; height: 20px; margin: 0 3px; }
                        .jester_letter_allow_production_content_paragraph { line-height: 1.8; margin: 20px 0; text-align: justify; }
                        .jester_letter_allow_production_numbered_list { margin: 20px 0; }
                        .jester_letter_allow_production_numbered_item { display: flex; margin-bottom: 10px; align-items: center; }
                        .jester_letter_allow_production_number { margin-right: 10px; min-width: 20px; }
                        .jester_letter_allow_production_item_line { flex: 1; border-bottom: 1px solid black; height: 20px; }
                        .jester_letter_allow_production_company_name { font-weight: bold; }
                        .jester_letter_allow_production_signature_section { margin-top: 40px; }
                        .jester_letter_allow_production_signature_area { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 30px; }
                        .jester_letter_allow_production_signature_block { text-align: center; }
                        .jester_letter_allow_production_signature_line { border-bottom: 1px solid black; width: 275px; height: 20px; margin: 10px 0; }
                        .jester_letter_allow_production_date_location { text-align: left; }
                        .jester_letter_allow_production_date_line { display: inline-block; border-bottom: 1px solid black; width: 125px; height: 20px; margin: 0 5px; }
                        .jester_letter_allow_production_right_signature { text-align: center; }
                        .jester_letter_allow_production_field_group { margin: 15px 0; }
                        @page { size: A4; margin: 0; }
                    </style>
                </head>
                <body>
                    <div id="jester_letter_allow_production_print_content"></div>
                </body>
                </html>
            `;

            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();

            var printContent = doc.getElementById('jester_letter_allow_production_print_content');
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