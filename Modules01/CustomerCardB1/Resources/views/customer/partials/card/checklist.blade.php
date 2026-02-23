@php
    // Note: This logic is added to fetch business details.
    $business_id = session()->get('user.business_id');
    $business = \App\Business::find($business_id);
    $logo_url = $business && $business->logo ? url('uploads/business_logos/' . $business->logo) : null;
    $business_name = $business ? $business->name : 'Business Name';
    $business_location = optional(
        \App\BusinessLocation::where('business_id', $business_id)->first(),
    )->name;
    $print_by = Auth::user()->user_full_name;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Check List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container-btn-print"
        style="display: flex; justify-content: flex-end; align-items: center; padding-right: 5%;">
        <button class="print-check-list btn btn-success" onclick="printContainer()">Print</button>
    </div>
    <!-- Project Dropdown -->
    @if ($project_id->isNotEmpty())
        <div class="dropdown-container">
            <label for="project_id" class="dropdown-label">ជ្រើសរើស Project:</label>
            <select name="project_id" id="project_id" class="custom-select">
                <option value="Please Select" selected disabled>Please Select</option>
                @foreach ($project_id as $project)
                    <option value="{{ $project->id }}" data-id="{{ $project->id }}">
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <p>អតិថិជននេះមិនមាន Project ទេ។</p>
    @endif

    <!-- Custom CSS for Dropdown and Table -->
    <style>
        .dropdown-container {
            margin-bottom: 20px;
            max-width: 400px;
        }

        .dropdown-label {
            display: block;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .custom-select {
            width: 100%;
            padding: 12px 16px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .custom-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .custom-select:hover {
            border-color: #999;
        }

        .custom-select option {
            padding: 10px;
            font-size: 16px;
        }
    </style>

    <div class="container mt-5" style="width: 90%; padding-bottom: 50px;" id="printableArea1">
        <header class="jester_customer_card_b1_header">
            <div class="jester_customer_card_b1_header-left">
                @if ($logo_url)
                    <img src="{{ $logo_url }}" class="jester_customer_card_b1_business-logo"
                        alt="{{ $business_name }} Logo">
                @endif
                <div>
                    <div class="jester_customer_card_b1_business-name">{{ $business_name }}</div>
                    <div class="jester_customer_card_b1_business-location">{{ $business_location }}</div>
                    <div class="jester_customer_card_b1_page-number" id="jester_customer_card_b1_page-display">Page 1
                    </div>
                </div>
            </div>
            <div class="jester_customer_card_b1_header-right">
                <div class="jester_customer_card_b1_name"><span class="jester_customer_card_b1_bold-name" id="project-name-header">Please Select a
                        Project</span></div>
                {{-- <div class="jester_customer_card_b1_project-name">
                    <span class="jester_customer_card_b1_bold-name" id="project-name-header">Please Select a
                        Project</span>
                </div> --}}
                <div class="jester_customer_card_b1_date-range">{{ __('Printed by') }}: <span
                        class="jester_customer_card_b1_bold-name">{{ $print_by }}</span></div>
                <div class="jester_customer_card_b1_date-range">{{ __('Printed on') }}:
                    {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}</div>
            </div>
        </header>
        <header class="check-list-header tw-mb-4">
            <h1 class="title h1 text-center"
                style="font-family: 'Times New Roman', Times, serif; font-size: 36px; font-weight: bolder; text-decoration-line: underline;">
                <span id="project-name"></span></h1>
        </header>
        <main class="check-list-body">
            <div style="display: flex; justify-content: space-between;">
                <div style="flex: 1; padding-right: 10px;">
                    <p class="h4 text-left">
                        <strong>@lang('contact.prefix'):</strong>
                        {{ implode(
                            ' ',
                            array_filter(
                                [$contact->prefix, $contact->first_name, $contact->middle_name, $contact->last_name],
                                fn($value) => !is_null($value) && $value !== '',
                            ),
                        ) ?:
                            '-' }}
                    </p>
                    <p class="h4 text-left">
                        <strong>@lang('business.business_name'):</strong>{{ $contact->supplier_business_name ?? '-' }}
                    </p>
                    <p class="h4 text-left">
                        <strong>@lang('business.address'):</strong>
                        {{ implode(
                            ', ',
                            array_filter(
                                [
                                    $contact->address_line_1,
                                    $contact->address_line_2,
                                    $contact->city,
                                    $contact->state,
                                    $contact->country,
                                    $contact->zip_code,
                                ],
                                fn($value) => !is_null($value) && $value !== '',
                            ),
                        ) ?:
                            '-' }}
                    </p>
                </div>
                <div style="flex: 1; padding-left: 10px; text-align: right;">
                    <p class="h4 text-right">
                        <strong>@lang('contact.mobile'):</strong> {{ $contact->mobile ?? '-' }}
                    </p>
                    <p class="h4 text-right">
                        <strong>@lang('contact.register_date'):</strong>{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}
                    </p>
                    <p class="h4 text-right">
                        <strong>@lang('contact.study_date'):</strong>
                        {{ $contact->study_date ? \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') : '-' }}
                    </p>
                </div>
            </div>
            @include('customercardb1::customer.partials.project_task')
        </main>
        <footer class="check-list-footer" style="margin-top: 20px;">
            <div class="footer" align="right">
                <p style="margin-bottom: 10px;">ថ្ងៃទី..................ខែ.................ឆ្នាំ2025</p>
                <p>ហត្ថលេខានិងឈ្មោះបុគ្គលិក</p>
            </div>
        </footer>
    </div>

    <!-- JavaScript for Dynamic Filtering -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        /**
         * Initialize project task datatable with project selection integration
         */
        $(document).ready(function() {
            // Ensure jQuery is loaded
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded');
                return;
            }

            // Initialize the datatable
            if (typeof project_task_datatable === 'undefined') {
                project_task_datatable = $('#project_task_table').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    lengthChange: false,
                    paging: false,
                    info: false,
                    ajax: {
                        url: '/customercardb1/project-task',
                        data: function(d) {
                            d.project_id = $('#project_id').val();
                            d.user_id = $('#assigned_to_filter').val();
                            d.status = $('#status_filter').val();
                            d.due_date = $('#due_date_filter').val();
                            d.priority = $('#priority_filter').val();
                            d.task_view = 'list_view';
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTable AJAX error:', error, thrown);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    columnDefs: [{
                        targets: [0, 3, 8],
                        orderable: false,
                        searchable: false,
                    }, ],
                    aaSorting: [
                        [0, 'asc']
                    ],
                    columns: [{
                            data: 'action',
                            name: 'action',
                            visible: false
                        },
                        {
                            data: null,
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'subject',
                            name: 'subject',
                            searchable: false
                        },
                        {
                            data: 'members',
                            name: 'members',
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'priority',
                            name: 'priority',
                            searchable: false
                        },
                        {
                            data: 'start_date',
                            name: 'start_date',
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'due_date',
                            name: 'due_date',
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            searchable: false
                        },
                        {
                            data: 'createdBy',
                            name: 'createdBy',
                            searchable: false,
                            defaultContent: '-'
                        }
                    ]
                });
            }

            // Store projects for reference
            var projects = @json($project_id->pluck('name', 'id'));

            // Handle project selection change
            $('#project_id').on('change', function() {
                var selectedId = $(this).val();
                if (selectedId === 'Please Select' || !selectedId) {
                    $('#project-name-header').text('Please Select a Project');
                    project_task_datatable.clear().draw();
                    return;
                }

                var selectedName = projects[selectedId] || 'Please Select a Project';
                $('#project-name-header').text(selectedName);
                $('#project-name').text("{{ __('project::lang.project') }} ៖ " + selectedName);

                // Reload the datatable with the new project_id
                if (typeof project_task_datatable !== 'undefined') {
                    project_task_datatable.ajax.reload(null, false);
                } else {
                    console.error('DataTable not initialized');
                }
            });

            // Trigger change on page load to show tasks for the first project
            if ($('#project_id option').length > 1) {
                $('#project_id option:eq(1)').prop('selected', true);
                $('#project_id').trigger('change');
            }
        });
    </script>

    <script>
        function printContainer() {
            // Create a hidden iframe
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            // Get the printable content by cloning the DOM element
            var printableArea = document.getElementById("printableArea1");
            var printContents = printableArea.cloneNode(true);

            // Check if content is empty
            if (!printContents.innerHTML.trim() || printContents.querySelectorAll(
                    '*:not(.table):not(.table-responsive)').length === 0) {
                document.body.removeChild(iframe);
                return;
            }

            // Build the HTML for the iframe
            var html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Print Check List</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Moul&display=swap');
                        body:empty {
                            display: none !important;
                        }
                        body {
                            margin: 0 !important;
                            padding: 0 !important;
                            font-family: 'Times New Roman', Times, serif;
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                        }
                        .container {
                            box-sizing: border-box !important;
                            background-color: white !important;
                        }
                        .container:empty,
                        .container > *:empty:not(.table):not(.table-responsive) {
                            display: none !important;
                        }
                        h1.title,
                        h4.h4.text-center {
                            text-align: center !important;
                        }
                        h1.title.h2 {
                            margin-top: 20mm !important;
                        }
                        .khmer-moul-font, h1.title.h2.text-center {
                            font-family: 'Moul', serif !important;
                        }
                        .table {
                            width: 100% !important;
                            border-collapse: collapse !important;
                            margin: 0 auto !important;
                        }
                        .table-bordered th,
                        .table-bordered td {
                            border: 1px solid black !important;
                            padding: 5px !important;
                            box-sizing: border-box !important;
                            text-align: center !important;
                            font-size: 11px !important;
                        }
                        input[type="checkbox"] {
                            -webkit-appearance: checkbox !important;
                            -moz-appearance: checkbox !important;
                            appearance: checkbox !important;
                            width: 15px !important;
                            height: 15px !important;
                            margin: 0 auto !important;
                        }
                        a {
                            text-decoration: none !important;
                            color: inherit !important;
                        }
                        .label {
                            display: inline-block !important;
                            padding: 2px 4px !important;
                            border-radius: 4px !important;
                        }
                        .bg-green { background-color: #28a745 !important; color: white !important; }
                        .bg-red { background-color: #dc3545 !important; color: white !important; }
                        .bg-yellow { background-color: #ffc107 !important; color: black !important; }
                        .bg-info { background-color: #17a2b8 !important; color: white !important; }
                        
                        /* Header styles for iframe content */
                        .jester_customer_card_b1_header {
                            margin-bottom: 20px !important;
                            display: flex !important;
                            flex-wrap: wrap !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                            background-color: #f8f9fa !important;
                            padding: 15px !important;
                            border-radius: 4px !important;
                            position: relative !important;
                        }

                        .jester_customer_card_b1_header-left {
                            display: flex !important;
                            align-items: center !important;
                            flex: 1 !important;
                            z-index: 1 !important;
                        }

                        .jester_customer_card_b1_header-right {
                            flex: 1 !important;
                            text-align: right !important;
                            z-index: 1 !important;
                        }

                        .jester_customer_card_b1_business-logo {
                            max-height: 50px !important;
                            max-width: 50px !important;
                            margin-right: 15px !important;
                        }

                        .jester_customer_card_b1_business-name {
                            font-size: 20px !important;
                            font-weight: 600 !important;
                        }

                        .jester_customer_card_b1_business-location {
                            font-size: 14px !important;
                            color: #666 !important;
                            margin-top: 2px !important;
                        }

                        .jester_customer_card_b1_page-number {
                            font-size: 12px !important;
                            color: #666 !important;
                            margin-top: 2px !important;
                        }

                        .jester_customer_card_b1_name {
                            font-size: 22px !important;
                            font-weight: 600 !important;
                            margin-bottom: 5px !important;
                        }

                        .jester_customer_card_b1_date-range {
                            font-size: 14px !important;
                            margin-top: 5px !important;
                        }

                        .jester_customer_card_b1_bold-name {
                            font-weight: 400 !important;
                        }
                        
                        @media print {
                            .label {
                                background-color: transparent !important;
                                color: black !important; /* Ensure text is readable */
                            }
                            html, body {
                                margin: 0 !important; 
                                padding: 0 !important;
                            }
                            /* Landscape print */
                            @page :landscape {
                                size: A4 landscape !important;
                                margin: 10mm !important;
                            }
                            .container {
                                width: 277mm !important;
                                min-height: 190mm !important;
                            }
                            .table th:nth-child(1),
                            .table td:nth-child(1) { width: 10%; }
                            .table th:nth-child(2),
                            .table td:nth-child(2) { width: 5%; }
                            .table th:nth-child(3),
                            .table td:nth-child(3) { width: 25%; }
                            .table th:nth-child(4),
                            .table td:nth-child(4) { width: 15%; }
                            .table th:nth-child(5),
                            .table td:nth-child(5) { width: 10%; }
                            .table th:nth-child(6),
                            .table td:nth-child(6) { width: 10%; }
                            .table th:nth-child(7),
                            .table td:nth-child(7) { width: 10%; }
                            .table th:nth-child(8),
                            .table td:nth-child(8) { width: 10%; }
                            .table th:nth-child(9),
                            .table td:nth-child(9) { width: 15%; }
                            
                            /* Print-specific header styles */
                            .jester_customer_card_b1_header {
                                margin-bottom: 16px !important;
                                display: flex !important;
                                justify-content: space-between !important;
                                align-items: center !important;
                                background-color: #f8f9fa !important;
                                padding: 12px !important;
                                position: relative !important;
                                border-radius: 0 !important;
                                -webkit-print-color-adjust: exact !important;
                                print-color-adjust: exact !important;
                                page-break-after: avoid !important;
                                width: 100% !important;
                            }

                            .jester_customer_card_b1_header-left {
                                display: flex !important;
                                align-items: center !important;
                                justify-content: flex-start !important;
                                text-align: left !important;
                                z-index: 1 !important;
                                flex: 1 !important;
                                overflow: hidden !important;
                                min-height: 40px !important;
                                padding: 2px 0 !important;
                            }

                            .jester_customer_card_b1_header-left > div {
                                text-align: left !important;
                            }

                            .jester_customer_card_b1_header-right {
                                flex: 1 !important;
                                text-align: right !important;
                                z-index: 1 !important;
                            }

                            .jester_customer_card_b1_business-logo {
                                max-height: 40px !important;
                                max-width: 40px !important;
                                margin-right: 12px !important;
                                width: 35px !important;
                                height: 35px !important;
                                margin-top: 2px !important;
                                margin-bottom: 2px !important;
                                -webkit-print-color-adjust: exact !important;
                                print-color-adjust: exact !important;
                                flex-shrink: 0 !important;
                                object-fit: contain !important;
                                object-position: center !important;
                                display: block !important;
                                page-break-inside: avoid !important;
                            }

                            .jester_customer_card_b1_business-name {
                                font-size: 12.8px !important;
                                font-weight: 400 !important;
                                color: #000 !important;
                            }

                            .jester_customer_card_b1_business-location {
                                font-size: 8.8px !important;
                                color: #000 !important;
                                text-align: left !important;
                                margin-top: 2px !important;
                            }

                            .jester_customer_card_b1_page-number {
                                font-size: 10px !important;
                                color: #000 !important;
                                text-align: left !important;
                                margin-top: 2px !important;
                                display: block !important;
                            }

                            .jester_customer_card_b1_name {
                                font-size: 12.8px !important;
                                font-weight: 400 !important;
                                margin-bottom: 5px !important;
                                margin-right: 20px !important;
                                color: #000 !important;
                                text-align: right !important;
                            }

                            .jester_customer_card_b1_date-range {
                                font-size: 8.8px !important;
                                margin-top: 4px !important;
                                color: #000 !important;
                                margin-right: 20px !important;
                                text-align: right !important;
                            }

                            .jester_customer_card_b1_bold-name {
                                font-weight: 400 !important;
                                color: #000 !important;
                            }
                        }
                        /* Portrait print */
                        @media print and (orientation: portrait) {
                            @page {
                                margin: 10mm !important;
                            }
                            .container {
                                width: 190mm !important;
                                min-height: 277mm !important;
                            }
                            .table th:nth-child(1),
                            .table td:nth-child(1) { width: 5%; }
                            .table th:nth-child(2),
                            .table td:nth-child(2) { width: 10%; }
                            .table th:nth-child(3),
                            .table td:nth-child(3) { width: 20%; }
                            .table th:nth-child(4),
                            .table td:nth-child(4) { width: 10%; }
                            .table th:nth-child(5),
                            .table td:nth-child(5) { width: 10%; }
                            .table th:nth-child(6),
                            .table td:nth-child(6) { width: 10%; }
                            .table th:nth-child(7),
                            .table td:nth-child(7) { width: 10%; }
                            .table th:nth-child(8),
                            .table td:nth-child(8) { width: 10%; }
                            .table th:nth-child(9),
                            .table td:nth-child(9) { width: 10%; }
                            .table-bordered th,
                            .table-bordered td {
                                font-size: 10px !important;
                                padding: 3px !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container"></div>
                </body>
                </html>
            `;

            // Write to the iframe's document
            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();

            // Append the cloned content to the iframe's container
            doc.querySelector('.container').appendChild(printContents);

            // Wait for resources to load before printing
            iframe.contentWindow.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                // Remove the iframe after printing
                iframe.contentWindow.onafterprint = function() {
                    document.body.removeChild(iframe);
                };
            };
        }
    </script>
</body>

</html>

<style>
    .jester_customer_card_b1_header {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        position: relative;
    }

    .jester_customer_card_b1_header-left {
        display: flex;
        align-items: center;
        flex: 1;
        z-index: 1;
    }

    .jester_customer_card_b1_header-right {
        flex: 1;
        text-align: right;
        z-index: 1;
    }

    .jester_customer_card_b1_business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }

    .jester_customer_card_b1_business-name {
        font-size: 20px;
        font-weight: 600;
    }

    .jester_customer_card_b1_business-location {
        font-size: 14px;
        color: #666;
        margin-top: 2px;
        text-align: left;
    }

    .jester_customer_card_b1_page-number {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
        text-align: left;
    }

    .jester_customer_card_b1_name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .jester_customer_card_b1_date-range {
        font-size: 14px;
        margin-top: 5px;
    }

    .jester_customer_card_b1_bold-name {
        font-weight: 400;
    }

    @media print {
        #printableArea1 {
            padding: 0rem !important;
        }

        #printableArea1>div {
            padding: 0.25rem !important;
        }

        @page {
            margin: 10mm;

            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #000;
            }
        }

        body {
            counter-reset: page;
        }

        a {
            text-decoration: none;
            color: #000;
        }

        .jester_customer_card_b1_header {
            margin-bottom: 16px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            background-color: #f8f9fa !important;
            padding: 12px !important;
            position: relative !important;
            border-radius: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-after: avoid !important;
            width: 100% !important;
        }

        .jester_customer_card_b1_header-left {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            text-align: left !important;
            z-index: 1 !important;
            flex: 1 !important;
            overflow: hidden !important;
            min-height: 40px !important;
            padding: 2px 0 !important;
        }

        .jester_customer_card_b1_header-left>div {
            text-align: left !important;
        }

        .jester_customer_card_b1_header-right {
            flex: 1 !important;
            text-align: right !important;
            z-index: 1 !important;
        }

        .jester_customer_card_b1_business-logo {
            max-height: 40px !important;
            max-width: 40px !important;
            margin-right: 12px !important;
            width: 35px !important;
            height: 35px !important;
            margin-top: 2px !important;
            margin-bottom: 2px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            flex-shrink: 0 !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            page-break-inside: avoid !important;
        }

        .jester_customer_card_b1_business-name {
            font-size: 12.8px !important;
            font-weight: 400 !important;
            color: #000 !important;
        }

        .jester_customer_card_b1_business-location {
            font-size: 8.8px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
        }

        .jester_customer_card_b1_page-number {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            margin-top: 2px !important;
            display: block !important;
        }

        .jester_customer_card_b1_name {
            font-size: 12.8px !important;
            font-weight: 400 !important;
            margin-bottom: 5px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .jester_customer_card_b1_date-range {
            font-size: 8.8px !important;
            margin-top: 4px !important;
            color: #000 !important;
            text-align: right !important;
        }

        .jester_customer_card_b1_bold-name {
            font-weight: 400 !important;
            color: #000 !important;
        }

        .no-screen {
            display: block !important;
        }
    }
</style>

<script>
    function updatePageCounter() {
        const pageHeight = 1056;
        const headerHeight = document.querySelector('.jester_customer_card_b1_header') ? document.querySelector(
            '.jester_customer_card_b1_header').offsetHeight : 0;
        const contentHeight = document.body.scrollHeight;
        const estimatedPages = Math.ceil(contentHeight / pageHeight);

        const pageDisplay = document.getElementById('jester_customer_card_b1_page-display');
        if (pageDisplay) {
            pageDisplay.textContent = estimatedPages > 1 ? 'Page 1 of ' + estimatedPages : 'Page 1';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Update page counter
        setTimeout(function() {
            updatePageCounter();
        }, 100);
    });

    // Update page counter on window resize
    window.addEventListener('resize', function() {
        updatePageCounter();
    });
</script>