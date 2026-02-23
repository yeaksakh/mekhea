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

    <!-- Custom CSS for Dropdown -->
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
        <header class="check-list-header tw-mb-4">
            <h1 class="title h1 text-center"
                style="font-family: 'Times New Roman', Times, serif; font-size: 36px; font-weight: bolder; text-decoration-line: underline;">
                {{ __('project::lang.project') }} ៖ <span id="project-name">Please Select a Project</span></h1>
        </header>
        <main class="check-list-body">
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('contact.prefix'):</strong> 
                {{ implode(' ', array_filter([
                    $contact->prefix,
                    $contact->first_name,
                    $contact->middle_name,
                    $contact->last_name
                ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
            </h4>
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('contact.mobile'):</strong> {{ $contact->mobile ?? '-' }}                
            </h4>
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('business.business_name'):</strong>{{ $contact->supplier_business_name ?? '-' }}
            </h4>
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('business.address'):</strong> 
                {{ implode(', ', array_filter([
                    $contact->address_line_1,
                    $contact->address_line_2,
                    $contact->city,
                    $contact->state,
                    $contact->country,
                    $contact->zip_code
                ], fn($value) => !is_null($value) && $value !== '')) ?: '-' }}
            </h4>
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('contact.register_date'):</strong>{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') : '-' }}
            </h4>
            <h4 class="h4 text-left" style="margin: 15px 0;">
                <strong>@lang('contact.study_date'):</strong> {{ $contact->study_date ? \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') : '-' }}
            </h4>

            @include('customercardb1::customer.partials.project_task')
        </main>
        <footer class="check-list-footer" style="margin-top: 20px;">
            <div class="footer" align="right">
                <h4 style="margin-bottom: 10px;">ថ្ងៃទី..................ខែ.................ឆ្នាំ2025</h4>
                <h4>ហត្ថលេខានិងឈ្មោះបុគ្គលិក</h4>
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
                url: '/project/project-task',
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
            columnDefs: [
                {
                    targets: [0, 3, 8, 9],
                    orderable: false,
                    searchable: false,
                },
            ],
            aaSorting: [[6, 'asc']],
            columns: [
                { data: 'action', name: 'action', visible: false },
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'subject', name: 'subject', searchable: false },
                { data: 'members', name: 'members', searchable: false, defaultContent: '-' },
                { data: 'priority', name: 'priority', searchable: false },
                { data: 'start_date', name: 'start_date', searchable: false, defaultContent: '-' },
                { data: 'due_date', name: 'due_date', searchable: false, defaultContent: '-' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'createdBy', name: 'createdBy', searchable: false, defaultContent: '-' },
                { data: 'description', name: 'description', searchable: false, defaultContent: '-' }
            ]
        });
    }
    
    // Store projects for reference
    var projects = @json($project_id->pluck('name', 'id'));
    
    // Handle project selection change
    $('#project_id').on('change', function() {
        var selectedId = $(this).val();
        console.log('Selected project_id:', selectedId); // Debug log
        if (selectedId === 'Please Select' || !selectedId) {
            $('#project-name').text('Please Select a Project');
            project_task_datatable.clear().draw();
            return;
        }
        
        var selectedName = projects[selectedId] || 'Please Select a Project';
        $('#project-name').text(selectedName);
        
        // Reload the datatable with the new project_id
        if (typeof project_task_datatable !== 'undefined') {
            console.log('Reloading DataTable with project_id:', selectedId);
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
                        body {
                            margin: 0 !important;
                            padding: 0 !important;
                            font-family: 'Times New Roman', Times, serif;
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                            min-height: 190mm !important; /* A4 landscape height minus margins */
                        }
                        .container {
                            width: 277mm !important; /* A4 landscape width minus margins */
                            box-sizing: border-box !important;
                            background-color: white !important;
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
                        .table-bordered td, .table-bordered th {
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
                        @media print {
                            .label {
                                background-color: transparent !important;
                                color: black !important; /* Ensure text is readable */
                            }
                        }
                        @page {
                            size: A4 landscape !important;
                            margin: 10mm !important;
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