<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passport Book - Page 2</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Siemreap&display=swap" rel="stylesheet">
    <style>
        :root {
            --row-gap: 20px;
            --column-gap: 0px;
            --grid-padding: 10px;
            --card-margin: 0px;
            --card-padding: 3px;
            --card-width: 380px;
            --card-height: 560px;
        }

        .pspt_wrapper * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
            outline: none;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            list-style: none;
        }

        .pspt_wrapper {
            font-family: 'Moul', 'Arial', sans-serif;
            background-color: #e8ecef;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 0;
            overflow-x: hidden;
        }

        .pspt_container {
            max-width: 1700px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            padding: 20px;
        }

        .pspt_downloadBtn {
            width: 250px;
            padding: 12px;
            background-color: #D4A017;
            color: #fff;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .pspt_downloadBtn:hover {
            background-color: #b58914;
            transform: translateY(-2px);
        }

        .pspt_displayArea {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 5px;
        }

        .pspt_grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-gap: var(--row-gap) var(--column-gap);
            padding: var(--grid-padding);
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: calc(2 * var(--card-width) + var(--column-gap) + 2 * var(--grid-padding));
        }

        .pspt_card {
            width: 100%;
            aspect-ratio: 380 / 560;
            margin: 0;
            padding: var(--card-padding);
            border-radius: 8px;
            border: 0.5px solid #1C2526;
            position: relative;
            overflow: hidden;
            font-family: 'Battambang', Tahoma, Geneva, Verdana, sans-serif;
        }

        .passport-panel {
            position: relative;
            overflow: hidden;
        }

        .passport-blue {
            background-color: #2a3286;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .passport-title {
            font-size: 36px;
            color: #f9d71c;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .passport-khmer-title {
            font-size: 54px;
            color: #f9d71c;
            font-weight: bold;
            margin-bottom: 60px;
        }

        .signature-area {
            margin-top: auto;
            text-align: center;
            width: 100%;
        }

        .signature-text {
            font-size: 14px;
            color: white;
            margin-bottom: 5px;
        }

        .color-strip {
            display: flex;
            width: 170px;
            height: 34px;
            margin: 10px auto;
        }

        .blue-strip {
            background-color: #2a3286;
            border: 1px solid white;
            flex: 1;
        }

        .yellow-strip {
            background-color: #f9d71c;
            flex: 1;
        }

        .passport-white {
            background-color: white;
            padding: 20px;
            overflow-y: auto;
        }

        .subheader-text {
            color: #333;
            font-size: 8px;
        }

        .section-title {
            color: #2a3286;
            font-size: 16px;
            margin: 15px 0 5px 0;
        }

        .bullet-list {
            list-style-type: none;
            margin-left: 20px;
        }

        .bullet-list li {
            position: relative;
            padding-left: 15px;
            margin-bottom: 5px;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .bullet-list li:before {
            content: "-";
            position: absolute;
            left: 0;
            color: #2a3286;
        }

        .passport-qr {
            background-color: #2a3286;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .qr-container {
            margin-top: auto;
            width: 170px;
            height: 170px;
            background-color: white;
            padding: 10px;
            margin-bottom: 15px;
        }

        .qr-code {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-text {
            text-align: center;
            font-size: 11px;
            margin-bottom: auto;
            color: white;
        }

        .passport-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .page-number {
            color: #ff0000;
            font-size: 14px;
            text-align: right;
            margin-bottom: 10px;
        }

        .signature-box {
            width: 130px;
            min-height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0 auto;
        }

        .verify-text {
            text-align: center;
            font-size: 11px;
        }

        .passport-logo-container {
            margin-top: auto;
            text-align: center;
        }

        .passport-logo-text {
            font-size: 28px;
            font-weight: bold;
            color: #999;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .khmer-subtitle {
            color: #666;
            font-size: 16px;
            margin-top: 5px;
        }

        .english-subtitle {
            color: #888;
            font-size: 14px;
            margin-top: 2px;
        }

        .passport-info-columns {
            display: flex;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .passport-info-left, .passport-info-right {
            width: 48%;
            font-size: 10px;
            color: #333;
        }

        .info-title {
            font-weight: bold;
            color: #2a3286;
            margin-bottom: 5px;
        }

        .info-items {
            margin-bottom: 10px;
        }

        .passport-footer-text {
            font-size: 9px;
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="pspt_wrapper">
        <div class="pspt_container">
            <button id="pspt_downloadBtn" class="pspt_downloadBtn">បោះពុម្ភ (Print)</button>

            <div class="pspt_displayArea">
                <!-- Second Grid (Cards 5-8) -->
                <div id="pspt_cardsContainer2" class="pspt_grid">

                    <div class="pspt_card passport-panel passport-qr" style="background-image: url('/docs/images/1.png'); background-position: center; background-size: cover; background-repeat: no-repeat;">
                        {{-- <div class="color-strip" style="margin-top: auto;">
                            <div class="blue-strip"></div>
                            <div class="yellow-strip"></div>
                        </div>
                        <div class="qr-container">
                            <img src="/docs/images/certificate_of_yeaksa_member.png" class="qr-code" alt="QR Code">
                        </div>
                        <div class="qr-text">
                            <div class="verify-text">អ្នកមានត្រូវមាន៣របរ: របរប្រចាំថ្ងៃ ប្រចាំខែ ប្រចាំឆ្នាំ</div>
                            <div class="verify-text" style="padding-top: 10px; border-top: 0.5px solid black;">
                                សៀវភៅនេះជាទ្រព្យសម្បត្តិរបស់ក្រុមហ៊ុន ឌឹហ្វក់សេស អាត ខូអិលធីឌី
                                ករណីរកឃើញសៀវភៅនេះដែលមិនមែនជាម្ចាស់សូមយកមករក្សាទុកនៅក្រុមហ៊ុនវិញ ឬ ទាក់ទង
                                ទូរស័ព្ទលេខ : 098 538 907 វិបសាយ: www.yeaksa.com
                            </div>
                        </div> --}}
                    </div>
                    
                    <!-- Card 5 -->
                    <div class="pspt_card passport-panel passport-blue" style="background-image: url('/docs/images/2.png'); background-position: center; background-size: cover; background-repeat: no-repeat;">
                        {{-- <div class="passport-title">PASSPORT</div>
                        <div class="passport-khmer-title">យក្សា</div>
                        <div class="signature-area">
                            <div class="signature-text">ហត្ថលេខា របស់ អ្នកកាន់</div>
                            <div class="color-strip">
                                <div class="blue-strip"></div>
                                <div class="yellow-strip"></div>
                            </div>
                        </div> --}}
                    </div>
                    <!-- Card 6 -->
                    
                    <!-- Card 7 -->
                    
                    <!-- Card 8 -->
                    <div class="pspt_card passport-panel passport-info" style="transform: rotate(180deg); background-image: url('/docs/images/4.png'); background-position: center; background-size: cover; background-repeat: no-repeat;">
                        
                        <div class="verify-text" style="color: red; margin: 12px auto 8px;">
                            ពិន្ទុប្រចាំឆ្នាំ
                        </div>
                        <div class="signature-box"></div>
                        <div class="verify-text" style="margin: 20px auto 10px; color: black;">
                            ការប្រើប្រាស់
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; padding: 4px 0; color: black;">
                            ពិន្ទុ * ផ្កាយ1&ZeroWidthSpace;=1ពិន្ទុ, ផ្កាយ2=2ពិន្ទុ, ផ្កាយ3&ZeroWidthSpace;=3ពិន្ទុ, ផ្កាយ4&ZeroWidthSpace;=4ពិន្ទុ, ផ្កាយ5&ZeroWidthSpace;=5ពិន្ទុ <br>
                            រង្វាន់ * រង្វាន់គឺអាស្រ័យទៅតាមពិន្ទុរបស់លោកអ្នកប្រចាំឆ្នាំ តាមលក្ខ័ណ្ឌក្រុមហ៊ុន
                        </div>
                        <div style="margin: 45px 0;"></div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; margin-top: 15px;">
                            
                            <table cellpadding="5" cellspacing="0" style="font-size: 12.5px; color: #2a3286;" width="380">
                                <tbody><tr>
                                  <td>យក្សាស្នាដៃរបស់កូនខ្មែរ</td>
                                  <td>យើងត្រូវតែរួមគ្នាថែរក្សា</td>
                                </tr>
                                <tr>
                                  <td>អោយគង់វង្សតទៅនៅលើលោកា</td>
                                  <td>ក្នុងរដ្ឋានៃដែនកម្ពុជា ។</td>
                                </tr>
                                <tr>
                                  <td>ត្រូវក្លាហានចេញមុខតតាំងប្រយុទ្ធ</td>
                                  <td>រួមសុខទុក្ខរីករាយជាមួយយក្សា</td>
                                </tr>
                                <tr>
                                  <td>ជួយស្ត្រី រឹងប៉ឹងនៅក្នុងគ្រួសារ</td>
                                  <td>ក្នុងមាគ៌ាសេដ្ឋកិច្ចដ៏រឹងមាំ ។</td>
                                </tr>
                                <tr>
                                  <td>រៀនសូត្រលត់ដំកុំអោយរួញរា</td>
                                  <td>យើងនាំគ្នាប្រឹងប្រែងជាប្រចាំ</td>
                                </tr>
                                <tr>
                                  <td>ទាំងក្រៅក្នុងជ្រោមជ្រែងអោយបានខ្លាំង</td>
                                  <td>កុំដេកចាំសំណាងពីលើមេឃ ។</td>
                                </tr>
                            </tbody></table>                              
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; color: black;">
                            <strong>ក្រុមហ៊ុន ឌឹហ្វក់សេស អាត ខូអិលធីឌី</strong> ជាក្រុមហ៊ុនគ្រប់គ្រង ចែកចាយ ផលិតផល 
                            នាំចេញ នាំចូល  ហ្វ្រែនឆាយគ្រប់ប្រភេទ។ យើងជាអ្នកផលិតផ្តាច់មុខនូវផលិតផល
                            យក្សា ដែលជាអ្នកជំនាញខាងសំអាត និង បោកអ៊ុត ផងដែរ ។
                        </div>
                        <div class="verify-text" style="border-top: 0.5px solid black; padding: 5px 0; color: black;">
                            សៀវភៅនេះជាទ្រព្យសម្បត្តិរបស់ក្រុមហ៊ុន ឌឹហ្វក់សេស អាត ខូអិលធីឌី
                            ករណីរកឃើញសៀវភៅនេះដែលមិនមែនជាម្ចាស់សូមយកមករក្សាទុកនៅក្រុមហ៊ុនវិញ ឬ ទាក់ទង
                            ទូរស័ព្ទលេខ : 098 538 907 វិបសាយ: www.yeaksa.com
                        </div>
                        
                    </div>

                    <div class="pspt_card passport-panel passport-white" style="transform: rotate(180deg); padding: 8px!important;">
                        <div style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0;">ចក្ខុវិស័យ</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">ធ្វើអោយគ្រួសារមានសុភមង្គលតាមរយៈអាជីវកម្មបោកអ៊ុតដែលប្រើប្រាស់ផលិតផល</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">ខ្មែរ តម្លៃក្នុងស្រុក គុណភាពអន្តរជាតិ</div>
                        <div style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0;">បេសកកម្ម</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">លើកកម្ពស់ម៉ាកយីហោ និង ផលិតផល ក្នុងស្រុក រួមចំណែកកសាងសេដ្ឋកិច្ចគ្រួសារ</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">គ្រប់ផ្ទះបានប្រើប្រាស់ផលិតផលយក្សាដែលមានគុណភាពល្អបំផុត</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">ភូមិមួយមានហាងបោកអ៊ុតមួយ</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">ផ្តល់ការបណ្តុះបណ្តាលជំនាញបោកអ៊ុតស្តង់ដាដល់ស្ត្រីតាមសហគមន៍</div>
                        <div style="text-align: left; color: black; margin-left: 20px; font-size: 12px;">គ្រប់ហាងបោកសម្លៀកបំពាក់ទាំងអស់ប្រើប្រាស់ផលិតផលយក្សា</div>
                        <div style="text-align: left; color: #2a3286; font-size: 15px; font-family: Moul; padding: 5px 0;">ហេតុអ្វីបានជាចូលរួមសមាជិក?</div>
                        <ul class="bullet-list" style="text-align: left; font-size: 12px; color: black;">
                            <li style="text-align: left; font-size: 12px; color: black;">សម្រេចក្តីស្រមៃរបស់ខ្លួនដែលចង់ក្លាយជាសហគ្រិន</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ក្លាយជាម្ចាស់អាជីវកម្មយក្សាបោកអ៊ុតដ៏ល្បីល្បាញពេញពិភពលោក</li>
                            <li style="text-align: left; font-size: 12px; color: black;">មានទីប្រឹក្សាផ្ទាល់រហូតអស់មួយជីវិតអំពីអាជីវកម្មបោកអ៊ុត</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានការបង្ហាត់បង្រៀនបច្ចេកទេសថ្មីៗពេញលេញ</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ក្លាយជាភ្នាក់ងារហ្វ្រែនឆាយ និង តំណាងលក់ផលិតផល</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានវិញ្ញាបនបត្របញ្ជាក់សាខាពេញច្បាប់របស់យក្សា</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានប្រព័ន្ធគ្រប់គ្រងហិរញ្ញវត្ថុក្នុងហាង</li>
                            <li style="text-align: left; font-size: 12px; color: black;">មានអតិថិជនស្រាប់ក្នុងដៃដោយសារតែភាពល្បីល្បាញ</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ជួយក្នុងការផ្សព្វផ្សាយបន្ថែមពីក្រុមហ៊ុន</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ក្លាយខ្លួនជាអ្នកលក់ផលិតផលក្រុមហ៊ុនភ្លាមៗ</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ទទួលបានសិទ្ធិពិសេសក្នុងការទិញលក់ផលិតផល</li>
                            <li style="text-align: left; font-size: 12px; color: black;">បង្កើតទំនាក់ទំនងថ្មីៗ និងពង្រីកបណ្តាញសាធារណៈ</li>
                            <li style="text-align: left; font-size: 12px; color: black;">ចែករំលែកចំណេះដឹង និងបទពិសោធន៍ជាមួយសមាជិកដទៃទៀត</li>
                            <li style="text-align: left; font-size: 12px; color: black;">រួមចំណែកដល់សហគមន៍ដើម្បីសេដ្ឋកិច្ចគ្រួសារ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent multiple event listener bindings
        const initializeEventListeners = () => {
            const downloadBtn = document.getElementById('pspt_downloadBtn');
            const cardsContainer = document.getElementById('pspt_cardsContainer2');

            // Remove existing listeners to prevent duplicates
            downloadBtn.removeEventListener('click', generatePNG);

            async function captureGrid(container) {
                return await html2canvas(container, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    width: container.offsetWidth,
                    height: container.offsetHeight,
                    x: 0,
                    y: 0
                });
            }

            async function generatePNG() {
                downloadBtn.textContent = 'កំពុងបង្កើត PNG...';
                downloadBtn.disabled = true;

                try {
                    const a4Canvas = document.createElement('canvas');
                    a4Canvas.width = 794 * 2;
                    a4Canvas.height = 1123 * 2;
                    const ctx = a4Canvas.getContext('2d');

                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, a4Canvas.width, a4Canvas.height);

                    const gridCanvas = await captureGrid(cardsContainer);
                    const gridWidth = gridCanvas.width;
                    const gridHeight = gridCanvas.height;
                    const scale = Math.min((a4Canvas.width - 80) / gridWidth, (a4Canvas.height - 80) / gridHeight);
                    const scaledWidth = gridWidth * scale;
                    const scaledHeight = gridHeight * scale;
                    const offsetX = (a4Canvas.width - scaledWidth) / 2;
                    const offsetY = (a4Canvas.height - scaledHeight) / 2;

                    ctx.drawImage(gridCanvas, offsetX, offsetY, scaledWidth, scaledHeight);

                    const link = document.createElement('a');
                    link.download = 'passport_page2.png';
                    link.href = a4Canvas.toDataURL('image/png');
                    link.click();
                } catch (error) {
                    console.error('Error generating PNG:', error);
                    alert('បរាជ័យក្នុងការបង្កើត PNG។ សូមព្យាយាមម្តងទៀត។');
                } finally {
                    downloadBtn.textContent = 'បោះពុម្ភ (Print)';
                    downloadBtn.disabled = false;
                }
            }

            // Add event listener
            downloadBtn.addEventListener('click', generatePNG);
        };

        // Initialize event listeners only once when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', initializeEventListeners);
    </script>
</body>
</html>