<style>
    .moul-font {
        font-family: "Moul", serif;
        font-weight: 200;
        font-style: normal;
    }

    .note {
        font-size: 10px;
    }

    .logo {
        position: absolute;
        left: 20px;
        top: 20px;
        /* Adjust this value to move the logo higher */
        max-height: 120px;
        width: auto;
    }

    .address-details {
        text-align: left;
        margin-left: 200px;
        /* Adjust this value to align with the logo */
    }

    .table-invoice {
        border: 1px solid #000;
        /* Ensure overall table border */
    }

    .custom-checkbox-container {
        display: inline-flex;
        align-items: center;
    }

    .custom-checkbox {
        width: 15px;
        height: 15px;
        border: 2px solid #000;
        display: inline-block;
        margin-right: 0px;
        position: relative;
        cursor: pointer;
    }

    .signature-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .signature-section hr {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 15px;
    }

    .signature-text {
        margin-top: -10px;
        /* Moves text closer to the line */
    }

    .bold-text {
        font-size: 15px;
        font-weight: bold;
    }

    .header-container {
        position: relative;
        text-align: center;
        margin-top: -10px;
        /* Adjust this value to move the header higher */
    }
</style>


<div class="row" style="color: #000000 !important;">
    <div class="col-xs-12">
        <div class="header-container">
            <table class="table-auto w-full text-center width: 100%;">
                <tr>
                    @if (!empty($receipt_details->logo))
                        <td style="width: 20%;​ white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <img style="max-height: 80px; width: auto;" src="{{ $receipt_details->logo }}"
                                class="img img-responsive mx-auto">
                        </td>
                    @endif



                    <td style="width: 50%;​ white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">

                        <h1 class="moul-font ">
                            @if (!empty($receipt_details->display_name))
                                {{ $receipt_details->display_name }}
                            @endif
                        </h1>
                        <h3 class="text-center moul-font">

                            <p style="font-size: 22px">កិច្ចព្រមព្រៀងជួលរំលោះប្រចាំសប្តាហ៍</p>
                        </h3>
                        @if (!empty($receipt_details->invoice_no_prefix))
                            №: <strong>{{ $receipt_details->invoice_no }}</strong>
                        @endif
                        [ Date: <strong>{{ date('d-m-y', strtotime($receipt_details->invoice_date)) }}</strong> to
                        <strong>{{ date('d-m-y', strtotime($receipt_details->invoice_date . ' +' . $receipt_details->pay_term_number . ' days')) }}</strong>
                        ]
                        <span>{{ $receipt_details->pay_term_number }}</span> ថ្ងៃ
                    </td>
                    <td​ style="width: 30%;​ white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">




                        </td>

                </tr>
            </table>
        </div>
    </div>
</div>

</div>


<!-- Rest of the template remains the same -->
<div class="row">
    <div class="col-xs-5">
        <p>
            @if (!empty($receipt_details->customer_info))
                អតិថិជន / Customer: {!! $receipt_details->customer_info !!} <br />
            @endif
        </p>
        <p>
            ចុះថ្ងៃទី៖ {!! date('d-m-y', strtotime($receipt_details->invoice_date)) !!}
        </p>
        <p>
            អ្នកទទួលខុសត្រូវ៖​ {{ $receipt_details->commission_agent }}
        </p>
    </div>
    <div class="col-xs-7 text-right">
        <table class="table table-bordered table-invoice" style="font-size: 12px;">
            <thead>
                <tr>
                    <th style="text-align: center;"> ល.រ </th>
                    <th style="text-align: center;">ផលិតផល</th>
                    <th style="text-align: center;">បរិមាណ</th>
                    <th style="text-align: center;">ថ្លៃសរុប</th>
                    <th style="text-align: center;">បង់មុន</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalLineTotal = 0; // Initialize a variable to store the sum of line totals
                @endphp

                @forelse($receipt_details->lines as $index => $line)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $line['name'] }}</td>
                        <td>{{ $line['quantity'] }} {{ $line['units'] }}</td>
                        <td>{{ $line['line_total'] }}</td>
                        <td>
                            @if ($index === 0)
                                <!-- Display total_paid only in the first row -->
                                {{ $receipt_details->total_paid }}
                            @endif
                        </td>
                    </tr>
                    @php
                        $totalLineTotal += (float) $line['line_total']; // Add each line_total to the sum
                    @endphp
                @empty
                    <tr>
                        <td colspan="5">No items found</td>
                    </tr>
                @endforelse

                <!-- Add a new row to display the total line_total and total_paid -->
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>{{ $totalLineTotal }}</strong></td>
                    <td><strong>{{ $receipt_details->total_paid }}</strong></td>
                </tr>
            </tbody>

            @php
                // Calculate the start and end dates
                $startDate = \Carbon\Carbon::parse($receipt_details->invoice_date);
                $endDate = \Carbon\Carbon::parse($receipt_details->invoice_date)->addDays(
                    $receipt_details->pay_term_number,
                );

                // Calculate the total amount to be paid
                $totalAmount = (float) preg_replace('/[^0-9.]+/', '', $receipt_details->total_due);

                // Calculate the number of weeks
                $numberOfWeeks = $startDate->diffInWeeks($endDate);

                // Ensure $numberOfWeeks is not zero to avoid divide-by-zero error
                if ($numberOfWeeks == 0) {
                    $numberOfWeeks = 1; // Default to 1 week to avoid division by zero
                }

                // Calculate the weekly payment amount
                $weeklyAmount = $totalAmount / $numberOfWeeks;
                $weeklyAmount += $totalAmount / 100;
            @endphp

        </table>




        @if ($receipt_details->pay_term_number == 0)
            <script>
                alert("Pay term is 0. No payment details to display.");
            </script>
        @else
            <table class="table table-bordered table-invoice" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th style="text-align: center;">ប្រាក់នៅសល់</th>
                        {{-- <th style="text-align: center;">អត្រាការ</th> --}}
                        <th style="text-align: center;">ចំនួនដង</th>
                        {{-- <th style="text-align: center;">ការប្រាក់</th> --}}
                        <th style="text-align: center;">បង់១ដង</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <!-- Remaining Amount -->
                        <td>{{ $receipt_details->total_due }}</td>

                        <!-- Interest Rate (1% of Remaining Amount) -->
                        {{-- <td>{{ $receipt_details->sell_custom_field_1_value ?? 1 }}%</td> --}}

                        <!-- Number of Instalments -->
                        <td>{{ $receipt_details->pay_term_number / 7 }}</td>

                        <!-- Total Interest -->
                        {{-- <td>
                            ${{ number_format(
                                ((float) preg_replace('/[^0-9.]+/', '', $receipt_details->total_due) / 100) *
                                    ($receipt_details->pay_term_number / 7),
                            ) }}
                        </td> --}}

                        <!-- Total Amount to Pay -->
                        <td>

                            {{ number_format(round($weeklyAmount, 2), 2) }}


                        </td>

                    </tr>

                </tbody>
            </table>
        @endif
    </div>
</div>


<div class="col-xs-12 text-left">
    <p><strong style="text-decoration: underline;">សេចក្តីបញ្ជាក់</strong><small>
            យើងខ្ញុំ(អ្នកខ្ចី)និង អ្នកធានា(បើមាន)សូមបញ្ជាក់ថា
            រាល់ពត៌មានដែលផ្តល់អោយនៅក្នុងកិច្ចព្រមព្រៀងបង់រំលោះនេះគឺជាពត៌មានពិតនិងត្រឹមត្រូវ
            ហើយរាល់ឯកសារទាំងអស់ដែលបានតំកល់ទុកនៅក្នុងកិច្ចព្រមព្រៀងនេះគឺជាការសំរេចចិត្តរបស់យើងខ្ញុំដោយពុំមានជនណាមកបង្ខិតបង្ខំយើងខ្ញុំឡើយ។
            ក្នុងករណីអ្នកខ្ចីមានបំណងគេចវេស(ទាក់ទងមិនបាន ឬ ព្យាយាមពន្យាពេលនៃការបង់រំលោះទៅអោយភាគីអ្នកអោយខ្ចីនោះ
            អ្នកអោយខ្ចីមានសិទ្ធគ្រប់គ្រាន់ក្នុងការបង្ហោះពត៌មានរបស់ខ្លួន(អ្នកខ្ចី)តាមបណ្តាញទំនាក់ទំនងសង្គមនានា
            ដើម្បីអោយសាមីខ្លួនចូលមកដោះស្រាយបំណុលរបស់ខ្លួនទៅអោយភាគីអ្នកអោយខ្ចី
            ហើយភាគីអ្នកខ្ចីពុំមានសិទ្ធប្តឹងបកពីបទផ្សេងៗមកលើភាគីអ្នកអោយខ្ចីឡើយ។
            ក្នុងករណីដោះស្រាយអស់លទ្ធភាពហើយនោះ
            ភាគីអ្នកខ្ចីនៅមានបំណងគេចវេសមិនព្រមសងប្រាក់ដែលបានខ្ចីទៀតនោះភាគីអ្នកធានា(បើមាន)ត្រូវចេញមខមកដោះស្រាយរាល់បំណុលរបស់ភាគីអ្នកខ្ចី
            ត្រូវបានធ្លាក់ជាបន្ទុករបស់អ្នកធានាដោយគ្មានលក្ខខណ្ឌ។
            អ្នកធានា(បើមាន)យល់ព្រមផ្តិតមេដៃនៅក្នុងកិច្ចព្រមព្រៀងនេះដោយមានបញ្ជាក់ពីថ្ងៃខែឆ្នាំនៅខាងក្រោមនេះ។
            ដើម្បីជាសក្ខីភាព អ្នកធានា(បើមាន)យល់ព្រមផ្តិតមេដៃនៅក្នុងកិច្ចព្រមព្រៀងនេះដោយមានបញ្ជាក់ពីថ្ងៃខែឆ្នាំ ខាងក្រោម។
        </small> </p>
</div>


</div>
<div class="col-xs-12 text-left">
    <p><strong style="text-decoration: underline;">លក្ខខ័ណ្ឌ</strong></p>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; vertical-align: top;">
                <small>
                    <p>1. យល់ព្រមប្រើប្រាស់សាប៊ូឬផលិតផលក្រុមហ៊ុន</p>
                    <p>2. យល់ព្រមបំពេញឯកសារជាមួយក្រុមហ៊ុន</p>
                    <p>3. រាល់ការបង់ប្រាក់តាម QR ABA តែមួយមុខគត់</p>
                    <p>4. មានទីតាំងនៅសៀមរាប ឬ ភ្នំពេញ</p>
                </small>
            </td>
            <td style="padding: 8px; vertical-align: top; border-left: 1px solid #000;">
                <small>
                    <p>5. មានទីតាំងហាងពិតប្រាកដ</p>
                    {{-- <p>6. ចូលរួមបង់ប្រាក់មុន ​២០ភាគរយឡើង</p> --}}
                    <p>6. បង់ប្រាក់រៀងរាល់សប្តាហ៍ ឬ ខែ</p>
                    {{-- <p>8. អត្រាការប្រាក់ 1ភាគរយ ក្នុង 1សប្តាហ៍</p> --}}
                    <p><strong>7. ចំនួនត្រូវបង់ប្រចាំ​​
                            {{ number_format(round($weeklyAmount, 2), 2) }}
                        </strong> </p>
                </small>
            </td>
        </tr>
    </table>

</div>

</div>

<table class="table table-bordered table-invoice" style="font-size: 12px;">
    <thead>
        <tr>
            <th style="text-align: center;">ខែ</th>
            <th style="text-align: center;">សប្តាហ៍ 1</th>
            <th style="text-align: center;">សប្តាហ៍ 2</th>
            <th style="text-align: center;">សប្តាហ៍ 3</th>
            <th style="text-align: center;">សប្តាហ៍ 4</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentDate = $startDate->copy()->addDays(7);
            $monthCounter = 1; 
        @endphp

        @while ($currentDate->lt($endDate))
            <tr>
                <td style="text-align: center;">{{ $monthCounter }}</td>
                @for ($week = 1; $week <= 4; $week++)
                    <td style="text-align: center;">
                        @if ($currentDate->lte($endDate)) <!-- Changed from lt to lte -->
                            {{ $currentDate->format('d-m-Y') }}<br>
                            @php
                                $currentDate->addWeek();
                            @endphp
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            @php
                $monthCounter++;
            @endphp
        @endwhile
    </tbody>
</table>


<div class="table-responsive signature-section">
    <table class="table table-borderless text-center">
        <tr>
            <td style="width: 33%; vertical-align: top;">
                <br /><br /><br />
                <hr />
                <div class="signature-text">
                    {!! $receipt_details->customer_name !!}
                </div>
            </td>
            <td style="width: 33%; vertical-align: top;">
                <br /><br /><br />
                <hr />
                <div class="signature-text">
                    {{-- {{ $receipt_details->display_name }} --}}
                    អ្នកទទួលខុសត្រូវ
                </div>
            </td>
            <td style="width: 33%; vertical-align: top;">
                <br /><br /><br />
                <hr />
                <div class="signature-text">
                    ប្រធានផ្នែកបង់រំលោះ
                </div>
            </td>
        </tr>
    </table>
</div>



</div>