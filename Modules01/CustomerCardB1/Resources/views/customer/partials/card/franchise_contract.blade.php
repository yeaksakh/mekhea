@php
$registerDate = $contact->register_date ?? '2023-01-01';
$khmerMonths = [
    1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា',
    7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'
];
$date = Carbon::parse($registerDate);
$day = $date->day;
$month = $khmerMonths[$date->month];
$year = $date->year;
@endphp

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
        .fc_unique_wrapper {
            margin: 0 auto;
            max-width: 21cm;
            width: 100%;
            overflow-x: hidden;
            word-break: break-all;
            text-align: center;
        }
        .fc_unique_wrapper .font-moul {
            font-family: 'Moul', sans-serif;
        }
        .fc_unique_wrapper .print-container {
            background-color: #ffffff;
            padding: 1.5cm;
            margin: 0.5rem auto;
            max-width: 21cm;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .fc_unique_wrapper .container {
            margin: 0 auto;
            max-width: 100%;
        }
        .fc_unique_wrapper .text-center {
            text-align: center;
        }
        .fc_unique_wrapper .mb-2 {
            margin-bottom: 0.5rem;
        }
        .fc_unique_wrapper .mb-4 {
            margin-bottom: 1rem;
        }
        .fc_unique_wrapper .my-2 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .fc_unique_wrapper .my-4 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .fc_unique_wrapper .my-8 {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .fc_unique_wrapper .mt-2 {
            margin-top: 0.5rem;
        }
        .fc_unique_wrapper .mt-4 {
            margin-top: 1rem;
        }
        .fc_unique_wrapper .mt-20 {
            margin-top: 5rem;
        }
        .fc_unique_wrapper .pb-2 {
            padding-bottom: 0.5rem;
        }
        .fc_unique_wrapper .pl-5 {
            padding-left: 1.25rem;
            text-align: left;
        }
        .fc_unique_wrapper .font-bold {
            font-weight: 700;
        }
        .fc_unique_wrapper .text-lg {
            font-size: 1.125rem;
        }
        .fc_unique_wrapper .text-sm {
            font-size: 0.875rem;
        }
        .fc_unique_wrapper .text-xl {
            font-size: 1.25rem;
        }
        .fc_unique_wrapper .w-33 {
            width: 33.333333%;
            margin-left: auto;
            margin-right: auto;
        }
        .fc_unique_wrapper .w-42 {
            width: 41.666667%;
            margin-left: auto;
            margin-right: auto;
        }
        .fc_unique_wrapper .border-b {
            border-bottom: 1px solid #000000;
        }
        .fc_unique_wrapper .flex {
            display: flex;
            justify-content: center;
        }
        .fc_unique_wrapper .justify-between {
            justify-content: space-between;
        }
        .fc_unique_wrapper .print-button {
            margin: 0.5rem auto;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            display: block;
        }
        .fc_unique_wrapper .print-button:hover {
            background-color: #2563eb;
        }
        .fc_unique_wrapper .signature-section,
        .fc_unique_wrapper .witness-section {
            margin-left: auto;
            margin-right: auto;
            max-width: 21cm;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .fc_unique_wrapper,
            .fc_unique_wrapper #fc_unique_area,
            .fc_unique_wrapper #fc_unique_area * {
                visibility: visible;
            }
            .fc_unique_wrapper {
                margin: 0 auto;
                padding: 0;
                text-align: center;
            }
            .fc_unique_wrapper #fc_unique_area {
                position: static;
                width: 100%;
                max-width: 21cm;
                margin: 0 auto;
                padding: 1.5cm;
                text-align: center;
            }
            .fc_unique_wrapper .print-button {
                display: none;
            }
            .fc_unique_wrapper .pl-5 {
                text-align: left;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
<body>
    <div class="fc_unique_wrapper">
        <button onclick="fcUniquePrintContract()" class="print-button">Print</button>
        <div class="print-container" id="fc_unique_area">
            <div class="text-center mb-4">
                <div class="font-moul text-lg">ព្រះរាជាណាចក្រកម្ពុជា</div>
                <div class="font-moul text-sm">ជាតិ សាសនា ព្រះមហាក្សត្រ</div>
                <div class="w-33 border-b my-4"></div>
            </div>

            <div class="container">
                <div class="text-center font-bold text-xl mb-2">កិច្ចសន្យាប្រើប្រាស់ផ្លាកយឺហោបោកអ៊ុត</div>
                <div class="text-center mb-4">Franchise Agreement</div>

                <div class="text-center mb-4">យោងកិច្ចសន្យាលេខ៖ {{ $contact->contact_id ?? '........................' }}</div>

                <div class="mb-2">
                    កិច្ចសន្យានេះធ្វើឡើងនៅថ្ងៃទី {{ $day ?? '...' }} ខែ {{ $month ?? '...' }} ឆ្នាំ {{ $year ?? '...' }} នៅរាជធានីភ្នំពេញ
                </div>

                <div class="my-2">
                    <div class="font-bold">រវាង</div>
                    <div class="my-2">
                        ក្រុមហ៊ុន <span class="font-bold">The Foxest Art Co.,Ltd.</span> តំណាងដោយ លោកស្រី ឡុង ស៊ីចាន់ បន្ទាប់មកហៅថា ភាគី "ក" តំណាងស្របច្បាប់ផ្លាកយីហោបោកអ៊ុត យក្សា (Yeaksa) និង ហេម៉ា (HEMA)
                    </div>
                    <div class="font-bold mt-2">និង</div>
                    <div class="my-2">
                        {{ $contact->name ?? '........................' }} កាន់អត្តសញ្ញាណប័ណ្ណលេខ {{ $contact->id_proof_name ?? '........................' }} បន្ទាប់មកហៅថា ភាគី "ខ"
                    </div>
                </div>

                <div class="my-2">
                    ភាគីទាំងពីរបានព្រមព្រៀងគ្នាទាំងស្រុងលើលក្ខខណ្ឌ និងប្រការដូចបានចែងខាងក្រោម៖
                </div>

                <div class="font-bold my-2">គោលបំណង</div>
                <div class="my-2">
                    កិច្ចសន្យានេះមានគោលបំណងផ្តល់ភាពស្របច្បាប់នៃកិច្ចសន្យាក្នុងការប្រើប្រាស់ផ្លាកយីហោបោកអ៊ុត យក្សា (Yeaksa) និង ហេម៉ា (HEMA) រវាងភាគីទាំងពីរ។
                </div>

                <div class="font-bold my-2">ស្លាកយីហោ</div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១</span> ៖ ភាគី”ក” ផ្តល់សិទ្ធិ និងអនុញ្ញាតអោយ ភាគី”ខ”  ប្រើប្រាស់ស្លាកយីហោ​ យក្សា (Yeaksa) រឺ ហេម៉ា (HEMA) ក្នុងការតុបតែងហាងបោកអ៊ុត ឬហាងលក់ទំនិញ ក្នុងគោលបំណងផ្សព្វផ្សាយលក់ទំនិញរបស់ខ្លួន។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី២</span> ៖ ភាគី”ខ” ត្រូវពិភាក្សា និងយល់ព្រមពី ភាគី​”ក” ជាមុនសិន ក្នុងការ ប្រើ ប្រាស់ស្លាកយីហោ​ យក្សា (Yeaksa) រឺ ហេម៉ា (HEMA)  ក្នុងការតុបតែង ឬកែប្រែ ( Decoration and Renovation) ហាងបោកអ៊ុត​ ឬហាងលក់ទំនិញ ដើម្បីធានាអោយបានត្រឹមត្រូវតាមស្តង់ដាររចនាបទស្លាកយីហោ។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៣</span> ៖ ភាគី”ខ”ត្រូវបង់សួយសារអាករ(Royality)ប្រចាំឆ្នាំក្នុងការប្រើប្រាស់ស្លាកយីហោដែរបានចែងក្នុងប្រការទី១ ទៅអោយភាគី”ក”ដូចខាងក្រោម​ ៖​
                                                                <div style="text-indent: 20px">
                                                                    <div>៣.១សេវាប្រឹក្សាយោបល់សម្រាប់អាជីវកម្មបោកអ៊ុតថ្មីនិងសួយសារអារករដែរបានប្រើផ្លាកយីហោថ្មីដំបូងខាលើ ចំនួនទឹកប្រាក់1000ដុល្លារ។​</div>
                                                                    <div>
                                                                        ៣.២ បង់សួយសារអាករ (Royality) ទឹកប្រាក់ចំនួន200$ ប្រចាំឆ្នាំ ឬ 20$ប្រចាំខែទៅអោយភាគី
                                                                        ”ក” ការទូទាត់ប្រចាំនឹងត្រូវបង់រៀលរាល់ដើមខែនៃឆ្នាំបន្ទាប់។
                                                                    </div>
                                                                </div>
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៤</span> ៖ ភាគី”ខ” អស់សិទ្ធិដោយស្វ័យប្រវត្តិក្នុងការប្រើប្រាស់ ស្លាកយីហោ ខាងលើនេះក្នុងករណីកិច្ចសន្យា នេះអស់សុពលភាព។ ភាពស្របច្បាប់ និងពន្ឋអាករ។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៥</span> ៖ ភាគី”ក” មិនទទួលខុសត្រូវរាល់សកម្មភាពអាជីវកម្មជាមួយអតិថិជនរបស់ ភាគី”ខ” ឬភាគីទី3 និង ភាពស្របច្បាប់របស់ ភាគី”ខ” ទេ។ រាល់អាជ្ញាប័ណ្ណប្រកបអាជីវកម្មជាបន្ទុករបស់ ភាគី ”ខ” ដោយ ស្របតាមបទដ្ឋាន គោលការណ៍ច្បាប់សហគ្រាស និងច្បាប់ពន្ធដារនៃព្រះរាជាណាចក្រកម្ពុជា។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៦</span> ៖ រាល់ពន្ឋអាកររួមទាំងស្លាកយីហោផ្ទាំងផ្សាយពាណិជ្ជកម្មនិង​​​សេវាសាធារណៈផ្សេងៗក្នុងតំបន់ទាក់ទងនឹង សកម្មភាពអាជីវកម្មរបស់ភាគី”ខ”ជាបន្ទុក និងការទទួលខុសត្រូវរបស់ ភាគី ”ខ”។ 
                </div>
                <div class="font-bold my-2">ការប្រើប្រាស់ ចែកចាយលក់ ផលិតផល និងផ្សាយពាណិជ្ចកម្ម</div>     
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៧</span> ៖ ភាគី”ខ” មានសិទ្ធិផ្សព្វផ្សាយផលិតផល និងស្លាកយីហោ ​យក្សា (Yeaksa) រឺ ហេម៉ា (HEMA) ក្នុងគោលបំណងជំរុញ​ការលក់ទៅអតិថិជនគោលដៅ និងអាជីវកម្មបោកអ៊ុតរបស់ខ្លួន។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៨</span> ៖ ភាគី”ខ”មិនអនុញ្ញាតប្រើប្រាស់ផលិតផលនិងស្លាកយីហោផ្សេងៗក្នុងហាងបោកអ៊ុតដែរមានផ្លាកយីហោ ខាងលើដោយមិនមានការអនុញ្ញាតជាមុន។ក្នុងករណីមានការរកឃើញថាមានការប្រើប្រាស់ផលិតផលរីផ្លាកយីហោផ្សេងភាគី "ក" នឹងធ្វើការដកហូតរឺបញ្ចាប់កិច្ចសន្យាផ្លាកយឺហោនេះដោយមានការជួនដំណឹងជា លាយលក្ខណ៍អក្សរ។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី៩</span> ៖ ភាគី”ខ” មានសិទ្ធិដាក់តាំង លក់ និងចែកចាយបន្ត ផលិតផល ​យក្សា (Yeaksa) រឺ ហេម៉ា (HEMA)ទៅ អោយអតិថិជន និង ទីផ្សារគោលដៅរបស់ខ្លូន។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១០</span> ៖ ភាគី”ខ” មានសិទ្ធិក្នុងការកំណត់តម្លៃលក់ទីផ្សារបន្តសមរម្យ (Markup Price) ទៅអោយអតិថិជន ផ្ទាល់ ប៉ុន្តែមិនអាចលក់អោយក្រោមតម្លៃទិញចូលពី ភាគី”ក” ​ជាដាច់ខាត។ ក្នុងករណីពិសេសណា មួយត្រូវមានការពិភាក្សា និង​យល់ព្រមពី ភាគី”ក” ជាមុនសិន។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១១</span> ៖ ទំនិញដែលបានទិញពី ភាគី ”ក” មិនអាចប្តូរជាសាច់ប្រាក់វិញបាន ទេ ក្នុងករណីមានការប្តូរ ឬបញ្ជូនទំនិញត្រលប់មកវិញត្រូវមានការយល់ព្រមពី ភាគី”ក” ជាមុនសិន។                   
                </div>
                <div class="font-bold my-2">ប្រពន្ឋ័គ្រប់គ្រងហាងបោកអ៊ុត ( Laundry Payment System)</div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១២</span> ៖ ភាគី”ខ”តម្រូវអោយប្រើប្រាស់ប្រព័ន្ឋទូទាត់ និងគ្រប់គ្រងហាងបោកអ៊ុតរបស់ក្រុមហ៊ុន។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៣</span> ៖ ភាគី”ខ” ប្រព័ន្ឋគ្រប់គ្រងហាងនេះនឹងតម្រូវអោយសាកល្បងប្រើប្រាស់រយះពេល1ខែដោយមិនគិតថ្លៃ និង គិតចាប់ពីខែទី2 ក្នុងមួយខែ៩ដុល្លារ រឺ 70ដុល្លារក្នុង1ឆ្នាំសម្រាប់ការថែទាំប្រព័ន្ធ។
                </div>
                <div class="font-bold my-2">ដោះស្រាយវិវាទ</div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៤</span> ៖ រាល់ពេលមានជម្លោះ ឬវិវាទរវាង ភាគី”ក” និង ភាគី”ខ” ការអនុវត្តន៍កិច្ចសន្យានេះ ត្រូវដោះស្រាយ	ដោយឈរលើគោលការណ៍សន្តិវិធី ដោយភាគីទាំងពីរមានសិទ្ធិក្នុងការជ្រើសរើសអ្នកសម្របសម្រួល កណ្តាល ឬអាជ្ញាធរពាក់ព័ន្ធ ដើម្បី ដោះស្រាយវិវាទនេះបើសិនមានការចាំបាច់។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៥</span> ៖ ក្នុងករណីភាគីទាំងពីរមិនអាចសម្របសម្រួលគ្នាបាន ភាគីទាំងពីរយល់ព្រមដោះស្រាយតាមផ្លូវច្បាប់ ឬប្រព័ន្ធតុលាការនៃព្រះរាជាណាចក្រកម្ពុជា។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៦</span> ៖ រាល់ពេលមានជម្លោះ ឬវិវាទ រវាង ភាគី”ក” និងភាគី”ខ”​ ការអនុវត្តន៍កិច្ចសន្យានេះ ដែលមានបញ្ហា ឬជម្លោះនៃគូភាគីទាំងពីរ ដែលបានដោះស្រាយតាមផ្លូវច្បាប់ ឬតាមតុលាការ ភាគីអ្នកចាញ់ក្តីត្រូវ ទទួលរ៉ាប់រងចំណាយទាំងអស់ លើថ្លៃសេវាដែលខាតបង់ ជម្ងឺចិត្ត និងរាល់ចំណាយផ្សេងៗដែលពាក់ ព័ន្ធរឿងក្តីនេះរួមទាំងតម្លៃមេធាវី ទៅភាគីឈ្នះក្ដីវិញគ្រប់ចំនួន។
                </div>
                <div class="font-bold my-2">សុពលភាព សិទ្ឋិ និងកាតព្វកិច្ច</div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៧</span> ៖ ភាគីទាំងពីរមានសិទ្ធិកែប្រែ ឬអាចបញ្ចប់កិច្ចសន្យានេះជាលាយលក្ខណ៍អក្សរ ដោយមានការយល់ ព្រមនិងជូនដំណឹងជាមុនយ៉ាងតិច60​ថ្ងៃ។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៨</span> ៖ ភាគី”ក” មានសិទ្ធិដោយឯកច្ឆន្ទក្នុងការបញ្ចប់កិច្ចសន្យានេះក្នុងករណី ភាគី”ខ” ឈប់ទិញ ផលិតផលពី រីបង់សួយសារអាករទៅអោយ ភាគី”ក” ក្នុងប្រការទី3 ក្នុងរយៈពេល 3ខែ ជាប់គ្នា។
                </div>
                <div class="pl-5 my-2">
                    <span class="font-bold">ប្រការទី១៩</span> ៖ ភាគីទាំងពីរយល់ព្រម និងទទួលយកទាំងស្រុងនូវខ្លឹមសារនៃកិច្ចសន្យាដោយមិនមានការបង្ខិតបង្ខំ ឡើយ ហើយកិច្ចសន្យានេះមានសុពលភាព 1ឆ្នាំម្តង គិតចាប់ពីថ្ងៃចុះហត្ថលេខា ឬផ្តិតស្នាមមេដៃ តទៅ។
                </div>

                <div class="my-2">
                    កិច្ចសន្យានេះធ្វើឡើងជា 2ច្បាប់ដើម សម្រាប់ភាគីទាំងពីររក្សាទុក​។
                </div>

                <div class="signature-section flex justify-between mt-4">
                    <div class="w-42 text-center">
                        <div class="font-bold">ហត្ថលេខា និង ត្រាក្រុមហ៊ុន</div>
                        <div class="border-b" style="margin-top: 8.5rem"></div>
                        <div class="my-8">តំណាងក្រុមហ៊ុន ឡុង ស៊ីចាន់</div>
                    </div>
                    <div class="w-42 text-center">
                        <div class="font-bold">ភាគី "ខ" ស្នាមមេដៃ និង ឈ្មោះ</div>
                        <div class="border-b" style="margin-top: 8.5rem"></div>
                        <div class="my-8">{{ $contact->name ?? '........................' }}</div>
                    </div>
                </div>

                <div class="witness-section flex justify-between">
                    <div class="w-42 text-center" style="margin-top: -15px">
                        {{-- <div>សាក្សី</div> --}}
                        {{-- <div class="border-b"></div> --}}
                    </div>
                    <div class="w-42 text-center" style="margin-top: -15px">
                        {{-- <div>សាក្សី</div> --}}
                        {{-- <div class="border-b my-8"></div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function fcUniquePrintContract() {
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        var area = document.getElementById("fc_unique_area").cloneNode(true);

        var html = `
            <!DOCTYPE html>
            <html lang="km">
            <head>
                <meta charset="UTF-8">
                <title>Print Franchise Contract</title>
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
                    .print-container {
                        max-width: 21cm;
                        width: 100%;
                        margin: 0 auto;
                        padding: 1.5cm;
                        background-color: #ffffff;
                        overflow-x: hidden;
                        word-break: break-all;
                        text-align: center;
                        zoom: 96%;
                    }
                    .container {
                        margin: 0 auto;
                        max-width: 100%;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .mb-2 {
                        margin-bottom: 0.5rem;
                    }
                    .mb-4 {
                        margin-bottom: 1rem;
                    }
                    .my-2 {
                        margin-top: 0.5rem;
                        margin-bottom: 0.5rem;
                    }
                    .my-4 {
                        margin-top: 0.5rem;
                        margin-bottom: 0.5rem;
                    }
                    .my-8 {
                        margin-top: 2rem;
                        margin-bottom: 2rem;
                    }
                    .mt-2 {
                        margin-top: 0.5rem;
                    }
                    .mt-4 {
                        margin-top: 1rem;
                    }
                    .mt-20 {
                        margin-top: 5rem;
                    }
                    .pb-2 {
                        padding-bottom: 0.5rem;
                    }
                    .pl-5 {
                        padding-left: 1.25rem;
                        text-align: left;
                    }
                    .font-bold {
                        font-weight: 700;
                    }
                    .text-lg {
                        font-size: 1.125rem;
                    }
                    .text-sm {
                        font-size: 0.875rem;
                    }
                    .text-xl {
                        font-size: 1.25rem;
                    }
                    .w-33 {
                        width: 33.333333%;
                        margin-left: auto;
                        margin-right: auto;
                    }
                    .w-42 {
                        width: 41.666667%;
                        margin-left: auto;
                        margin-right: auto;
                    }
                    .border-b {
                        border-bottom: 1px solid #000000;
                    }
                    .flex {
                        display: flex;
                        justify-content: center;
                    }
                    .justify-between {
                        justify-content: space-between;
                    }
                    .signature-section,
                    .witness-section {
                        margin-left: auto;
                        margin-right: auto;
                        max-width: 21cm;
                    }
                    @page {
                        size: A4;
                        margin-top: 45px;
                        margin-inline: -20px;
                        margin-bottom: 2.5cm;
                    }
                    @page:first {
                        margin-top: -15px;
                    }
                </style>
            </head>
            <body>
                <div id="fc_unique_print_content"></div>
            </body>
            </html>
        `;

        var doc = iframe.contentWindow.document;
        doc.open();
        doc.write(html);
        doc.close();

        var printContent = doc.getElementById('fc_unique_print_content');
        printContent.appendChild(area);

        setTimeout(function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            document.body.removeChild(iframe);
        }, 500);
    }
    </script>
</body>