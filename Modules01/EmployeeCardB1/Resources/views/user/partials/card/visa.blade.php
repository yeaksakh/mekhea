<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khmer Survey Form</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --card-width: 85.5mm;
            --card-height: 120.6mm;
            --grid-gap: 3mm;
            --grid-padding: 5mm;
        }

        .star-filled {
            color: #facc15;
            position: relative;
            display: inline-block;
            width: 16px;
            height: 16px;
            margin: 0 1.2px;
        }
        
        .star-empty {
            color: #d1d5db;
            position: relative;
            display: inline-block;
            width: 16px;
            height: 16px;
            margin: 0 1.2px;
        }

        .total-star {
            margin: 0 1.2px;
        }
        
        @media print {
            body, html {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: white !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .star-filled::before {
                content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512' fill='%23facc15'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E") !important;
                width: 12.8px;
                height: 12.8px;
                display: inline-block;
            }
            
            .star-empty::before {
                content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512' fill='%23d1d5db'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E") !important;
                width: 12.8px;
                height: 12.8px;
                display: inline-block;
            }
            
            .total-star::before {
                content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512' fill='%23facc15'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E") !important;
                width: 12.8px;
                height: 12.8px;
                display: inline-block;
                vertical-align: text-bottom;
            }
            
            .bg-blue-900 { background-color: #1e3a8a !important; }
            .bg-gray-900 { background-color: #111827 !important; }
            .bg-gray-200 { background-color: #e5e7eb !important; }
            .text-white { color: #ffffff !important; }
            .text-yellow-200 { color: #fef08a !important; }
            .text-xl { font-size: 20px !important; }
            .text-[12px] { font-size: 12px !important; }
            .text-[10px] { font-size: 10px !important; }
            .mb-1 { margin-bottom: 4px !important; }
            .mb-4 { margin-bottom: 16px !important; }
            .p-3 { padding: 12px !important; }
            .pb-3 { padding-bottom: 12px !important; }
            
            /* Print-specific styles */
            .print-container .text-[12px] { font-size: 8px !important; }
            .print-container .text-[10px] { font-size: 9.4px !important; }
            .print-container .mb-1 { margin-bottom: 2px !important; }
            .print-container .mb-4 { margin-bottom: 8px !important; }
            .print-container .p-3 { padding: 5px !important; }
            .print-container .pb-3 { padding-bottom: 2px !important; }
            .print-container .fa-star { font-size: 12.8px !important; }
            .print-container .star-filled { margin: 0 1px !important; }
            .print-container .star-empty { margin: 0 1px !important; }
            .print-container .total-star { margin: 0 2px !important; }
        }
        
        body {
            font-family: 'Noto Sans Khmer', Arial, sans-serif;
        }

        .print-container {
            width: 306px;
        }
    </style>
</head>
<?php
// Get current month number
$currentMonth = date('n'); // 'n' gives month without leading zeros (1-12)

// Array of Khmer months
$khmerMonths = [
    1 => 'មករា',      // January
    2 => 'កុម្ភៈ',     // February  
    3 => 'មីនា',      // March
    4 => 'មេសា',      // April
    5 => 'ឧសភា',      // May
    6 => 'មិថុនា',     // June
    7 => 'កក្កដា',     // July
    8 => 'សីហា',      // August
    9 => 'កញ្ញា',     // September
    10 => 'តុលា',     // October
    11 => 'វិច្ឆិកា',   // November
    12 => 'ធ្នូ'       // December
];

// Get current month in Khmer
$currentKhmerMonth = $khmerMonths[$currentMonth];
?>
<body class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white rounded-xl overflow-hidden shadow-lg relative print-container">
        <div class="bg-blue-900 text-white text-center py-2 text-xl font-bold rounded-t-xl relative">
            វីសាប្រចាំខែ {{ $currentKhmerMonth }}
            <button onclick="printForm()" class="absolute top-2 right-2 bg-white text-blue-900 text-[12px] font-bold rounded hover:bg-gray-200 no-print w-[60px] h-[25px] flex items-center justify-center">
                Print
            </button>            
        </div>

        <div class="p-3 relative">
            <div class="absolute text-[144px] font-bold text-yellow-200 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 opacity-50">យក្សា</div>

            <!-- Table header -->
            <div class="flex mb-1">
                <div class="w-[25px] bg-gray-900 text-white text-[12px] flex items-center justify-center p-1">
                    ល.រ
                </div>
                <div class="flex-1 bg-gray-900 text-white text-[12px] p-1 mx-1">
                    សកម្មភាព
                </div>
                <div class="w-[100px] bg-gray-900 text-white text-[12px] flex items-center justify-center p-1">
                    ពិន្ទុ
                </div>
            </div>

            <!-- Dynamic rows -->
            <?php
            $financialIndex = 1;
            $total_stars = 0;
            ?>
            @foreach($appraisalData as $score)
            <?php
            $total_stars += $score->actual_value;
            ?>
            @if($score->competency_type === 'technical')
            <div class="flex items-center mb-1 bg-gray-200">
                <div class="w-[25px] bg-white text-center py-1 font-bold text-[12px]">{{ $financialIndex++ }}</div>
                <div class="flex-1 py-1 px-2 text-[12px] mx-1 relative z-20 flex items-center">
                    {{ $score->competency_name }}
                </div>
                <div class="w-[120px] flex justify-center p-1 items-center">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer flex items-center">
                                <input type="radio"
                                       name="row{{ $financialIndex - 1 }}"
                                       value="{{ $i }}"
                                       {{ $i <= $score->actual_value ? 'checked' : '' }}
                                       class="hidden">
                                <span class="text-xl flex items-center">
                                    <i class="fa-solid fa-star {{ $i <= $score->actual_value ? 'star-filled' : 'star-empty' }}" style="font-size:16px;"></i>
                                </span>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            @endif
            @endforeach

            <!-- Comment section -->
            <div class="p-3">
                <div class="flex justify-between mb-4">
                    <div class="w-2/5">
                        <div class="text-[12px] mb-1">ថ្ងៃខែឆ្នាំវាយតម្លៃ</div>
                        <div class="text-[12px]">{{ $appraisalData[0]->appraisal_month }}</div>
                    </div>
                    <div class="w-3/5 border-2 border-blue-500 border-dashed rounded-md p-3">
                        <div class="text-[12px]">សរុបផ្កាយ</div>
                        <div class="text-[12px]">{{ $total_stars }} <i class="fa-solid fa-star star-filled total-star" style="font-size:16px;"></i></div>
                    </div>
                </div>
            </div>

            <!-- Signature section -->
            <div class="flex justify-between mb-4">
                <div class="w-[45%]">
                    <div class="text-[12px]">ម្ចាស់ហាង</div>
                    <p class="text-[12px]">{{ $appraisalData[0]->contact_name }}</p>
                </div>
                <div class="w-[45%]">
                    <div class="text-[12px]">បុគ្គលិកក្រុមហ៊ុន</div>
                    <div class="w-full h-px bg-black mt-6"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-[10px] text-center text-gray-600 pb-3">
                សៀវភៅនេះជាទ្រព្យសម្បត្តិរបស់ក្រុមហ៊ុន ឌីហ្វក់សេស អាត ខូអិលធីឌី ករណីរកឃើញសៀវភៅនេះដែលមឹនមែនជាម្ចាស់សូមយកមករក្សាទុកនៅក្រុមហ៊ុនវិញ ឬ ទាក់ទង ទូរស័ព្ទលេខ : 087 538 907 វិបសាយ: www.yeaksa.com
            </div>
        </div>
    </div>

    <script>
        function printForm() {
            const visaCard = document.querySelector('.print-container > div.bg-blue-900').parentNode;

            const a4Container = document.createElement('div');
            a4Container.style.width = '210mm';
            a4Container.style.height = '297mm';
            a4Container.style.display = 'flex';
            a4Container.style.flexWrap = 'wrap';
            a4Container.style.gap = 'var(--grid-gap)';
            a4Container.style.padding = 'var(--grid-padding)';
            a4Container.style.margin = '0 auto';
            a4Container.style.background = 'white';
            a4Container.style.boxSizing = 'border-box';
            a4Container.style.justifyContent = 'center';
            a4Container.style.alignContent = 'center';

            // Customize these values to resize the card in the print output
            const cardWidthMM = 85.5; // Desired width in mm
            const cardHeightMM = 120.6; // Desired height in mm
            const originalWidthMM = 85.5; // Original card width
            const originalHeightMM = 120.6; // Original card height
            const scaleFactor = Math.min(cardWidthMM / originalWidthMM, cardHeightMM / originalHeightMM);
            const cardPaddingMM = 0.5; // Padding inside card in mm (adjustable)

            for (let i = 0; i < 4; i++) {
                const clone = visaCard.cloneNode(true);
                const clonedButton = clone.querySelector('button');
                if (clonedButton) clonedButton.remove();

                clone.style.width = `${cardWidthMM}mm`;
                clone.style.height = `${cardHeightMM}mm`;
                clone.style.boxSizing = 'border-box';
                clone.style.background = 'white';
                clone.style.boxShadow = 'none';
                clone.style.border = '1px solid black';

                // Scale child container widths
                const childContainers = clone.querySelectorAll('.w-\\[25px\\], .w-\\[100px\\], .w-\\[120px\\], .w-2\\/5, .w-3\\/5, .w-\\[45\\%\\]');
                childContainers.forEach(container => {
                    const originalWidth = parseFloat(container.style.width || getComputedStyle(container).width);
                    if (originalWidth) {
                        container.style.width = `${originalWidth * scaleFactor}px`;
                    }
                });

                a4Container.appendChild(clone);
            }

            const printStyle = document.createElement('style');
            printStyle.type = 'text/css';
            printStyle.innerHTML = `
                @page {
                    size: A4 portrait;
                    margin: 0;
                }
                @media print {
                    html, body {
                        width: 210mm;
                        height: 297mm;
                        margin: 0;
                        padding: 0;
                        background: white !important;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    .bg-gray-100 {
                        background: none !important;
                    }
                    .print-container {
                        width: ${cardWidthMM}mm !important;
                        height: ${cardHeightMM}mm !important;
                        padding: ${cardPaddingMM * scaleFactor}mm !important;
                        margin: 0 !important;
                        box-shadow: none !important;
                        border: 1px solid black !important;
                        box-sizing: border-box !important;
                    }
                    .a4-container {
                        width: 210mm !important;
                        height: 297mm !important;
                        display: flex !important;
                        flex-wrap: wrap !important;
                        gap: var(--grid-gap) !important;
                        padding: var(--grid-padding) !important;
                        margin: 0 auto !important;
                        box-sizing: border-box !important;
                        justify-content: center !important;
                        align-content: center !important;
                    }
                    .print-container .text-xl { font-size: ${14 * scaleFactor}px !important; }
                    .print-container .text-[12px] { font-size: ${8 * scaleFactor}px !important; }
                    .print-container .text-[10px] { font-size: ${9.4 * scaleFactor}px !important; }
                    .print-container .mb-1 { margin-bottom: ${2 * scaleFactor}px !important; }
                    .print-container .mb-4 { margin-bottom: ${8 * scaleFactor}px !important; }
                    .print-container .p-3 { padding: ${5 * scaleFactor}px !important; }
                    .print-container .pb-3 { padding-bottom: ${2 * scaleFactor}px !important; }
                    .print-container .fa-star { font-size: ${12.8 * scaleFactor}px !important; }
                    .print-container .star-filled { 
                        width: ${16 * scaleFactor}px !important; 
                        height: ${16 * scaleFactor}px !important; 
                        margin: 0 ${1 * scaleFactor}px !important; 
                    }
                    .print-container .star-empty { 
                        width: ${16 * scaleFactor}px !important; 
                        height: ${16 * scaleFactor}px !important; 
                        margin: 0 ${1 * scaleFactor}px !important; 
                    }
                    .print-container .total-star { 
                        width: ${16 * scaleFactor}px !important; 
                        height: ${16 * scaleFactor}px !important; 
                        margin: 0 ${2 * scaleFactor}px !important; 
                    }
                    .star-filled::before, .star-empty::before, .total-star::before {
                        width: ${12.8 * scaleFactor}px !important;
                        height: ${12.8 * scaleFactor}px !important;
                        transform: scale(${scaleFactor}) !important;
                        display: inline-block;
                    }
                    .print-container .mx-1 { margin-left: ${4 * scaleFactor}px !important; margin-right: ${4 * scaleFactor}px !important; }
                    .print-container .px-2 { padding-left: ${8 * scaleFactor}px !important; padding-right: ${8 * scaleFactor}px !important; }
                    .print-container .py-1 { padding-top: ${4 * scaleFactor}px !important; padding-bottom: ${4 * scaleFactor}px !important; }
                }
            `;
            a4Container.appendChild(printStyle);

            document.body.style.display = 'none';
            a4Container.classList.add('a4-container');
            document.documentElement.appendChild(a4Container);

            window.print();

            a4Container.remove();
            document.body.style.display = '';
        }

        window.addEventListener('afterprint', () => {
            const a4Container = document.querySelector('.a4-container');
            if (a4Container) {
                a4Container.remove();
                document.body.style.display = '';
            }
        });
    </script>
</body>
</html>