<div class="modal-dialog modal-lg" id="yourModalId" role="document">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title text-center" style="color: #024cd7;">@lang('kpi::lang.view_appraisal')</h4>
            <button type="button" class="close btn-dan" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

 <!-- Modal Body -->
        <div class="modal-body" id="printableArea">
            <div class="text-center mb-4">
                <div id="dial_chart_container" style="width: 400px; height: 220px; margin: 0 auto;"></div>
            </div>
            <hr>
            
<div class="text-center mb-4 ">
    <p><strong>@lang('kpi::lang.title'):</strong> {{ $appraisalData[0]->indicator_title ?? 'N/A' }}</p>
    <p><strong>@lang('kpi::lang.department'):</strong> {{ $appraisalData[0]->department_name ?? 'N/A' }}</p>
    <p><strong>@lang('kpi::lang.designation'):</strong> {{ $appraisalData[0]->designation_name ?? 'N/A' }}</p>
    <p><strong>@lang('kpi::lang.employee'):</strong> {{ $appraisalData[0]->employee_username ?? 'Not Available' }}</p>
</div>


  <style>
        .blue-border {
            border: 2px solid blue;  /* Creates a 2px solid blue border */
            padding: 10px;            /* Adds padding inside the box */
            border-radius: 5px;       /* Optional: Adds rounded corners */
        }
    </style>
    
    

            <div class="card mb-4 blue-border">
                <div class="card-header text-center" style="color: #0056b3;">
                    @lang('kpi::lang.total')
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('kpi::lang.expect_value')</th>
                            <th>@lang('kpi::lang.expect_score')</th>
                            <th>@lang('kpi::lang.actual_value')</th>
                            <th>@lang('kpi::lang.actual_score')</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ number_format($appraisalData->sum('expect_value'), 2) ?? 'N/A' }}</td>
                            <td>{{ number_format($appraisalData->sum('expect_score'), 2) ?? 'N/A' }}</td>
                            <td>{{ $appraisalData->sum('actual_value') ?? 'N/A' }}</td>
                            <td>{{ number_format($appraisalData->sum('actual_score'), 2) ?? 'N/A' }}</td>
                            <td>
                                {{ number_format($appraisalData->avg(function ($score) {
                                    // Ensure 'expect_value' and 'actual_value' are numeric before performing division
                                    $expectValue = is_numeric($score->expect_value) ? $score->expect_value : 0;
                                    $actualValue = is_numeric($score->actual_value) ? $score->actual_value : 0;

                                    // Avoid division by zero, and calculate the percentage if valid
                                    if ($expectValue > 0) {
                                        return ($actualValue * 100) / $expectValue;
                                    }
                                    return 0; // Return 0 if invalid or division by zero
                                }), 2) ?? 'N/A' }}%

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <div class="card-header text-center" style="color: #0056b3;">
                    @lang('kpi::lang.financial')
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="text-align: center;">#</th>
                            <th style="width: 20%;">@lang('kpi::lang.indicator')</th>
                            <th>@lang('kpi::lang.expect_value')</th>
                            <th>@lang('kpi::lang.expect_score')</th>
                            <th>@lang('kpi::lang.actual_value_money')</th>
                            <th>@lang('kpi::lang.actual_score')</th>
                            <th>%</th>
                            <th>@lang('kpi::lang.note')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $behaviorIndex = 1; @endphp
                        @foreach($appraisalData as $score)
                            @if($score->competency_type === 'technical')
                                <tr>
                                    <td style="text-align: center;">{{ $behaviorIndex++ }}</td>
                                    <td style="width: 20%;">{{ $score->competency_name ?? 'N/A' }}</td>
                                    <td>{{ number_format($score->expect_value, 2) ?? 'N/A' }}</td>
                                    <td>{{ number_format($score->expect_score, 2) ?? 'N/A' }}</td>
                                    <td>{{ $score->actual_value ?? 'N/A' }}</td>
                                    <td>
                                       {{ number_format($score->actual_score, 2) ?? 'N/A'}}
                                    </td>
                                    <td>
                                        {{ number_format(($score->actual_value * 100) / $score->expect_value) }}%
                                    </td>
                                    <td>{{ $score->note ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card">
                <div class="card-header text-center" style="color: #0056b3;">
                     @lang('kpi::lang.non_financial')
                </div>
                <table class="table table-bordered">
                    <thead class="table-light">
                         <tr>
                            <th style="text-align: center;">#</th>
                            <th style="width: 20%;">@lang('kpi::lang.indicator')</th>
                            <th>@lang('kpi::lang.expect_value')</th>
                            <th>@lang('kpi::lang.expect_score')</th>
                            <th>@lang('kpi::lang.actual_value')</th>
                            <th>@lang('kpi::lang.actual_score')</th>
                            <th>%</th>
                            <th>@lang('kpi::lang.note')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $behaviorIndex = 1; @endphp
                        @foreach($appraisalData as $score)
                            @if($score->competency_type === 'behavioral')
                                <tr>
                                    <td style="text-align: center;">{{ $behaviorIndex++ }}</td>
                                    <td style="width: 20%;">{{ $score->competency_name ?? 'N/A' }}</td>
                                    <td>{{ number_format($score->expect_value, 2) ?? 'N/A' }}</td>
                                    <td>{{ number_format($score->expect_score, 2) ?? 'N/A' }}</td>
                                    <td>{{ $score->actual_value ?? 'N/A' }}</td>
                                    <td>
                                       {{ number_format($score->actual_score, 2) ?? 'N/A'}}
                                    </td>
                                    <td>
                                        {{ number_format(($score->actual_value * 100) / $score->expect_value) }}%
                                    </td>
                                    <td>{{ $score->note ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printDiv('printableArea')">Print</button>
        </div>
    </div>
</div>

<!-- JavaScript Print Function -->
<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    $('.modal').modal('hide');

    // Redirect to KPI appraisal list page after printing
    window.location.href = '/kpi/appraisal-list'; // Update this URL based on your routing
}
</script>


<script>
    function drawDialChart() {
        // Calculate the total score, making sure to handle non-numeric values gracefully
        const totalScore = {{ number_format($appraisalData->avg(function ($score) {
            // Ensure the actual_value and expect_value are numeric and check for division by zero
            $expectValue = is_numeric($score->expect_value) ? $score->expect_value : 0;
            $actualValue = is_numeric($score->actual_value) ? $score->actual_value : 0;

            if ($expectValue !== 0 && $actualValue !== 0) {
                return ($actualValue * 100) / $expectValue;
            }
            return 0; // Return 0 if data is invalid or division by zero occurs
        }), 2) ?? 0 }};
  // Fallback to 0 if the sum result is not numeric
        
        console.log(totalScore); // Log to check the calculated totalScore

        // Cap the totalScore to 200 for the dial pointer
        const cappedScore = Math.min(totalScore, 200);

        // Validate that the capped score is a valid number
        const validScore = isNaN(cappedScore) ? 0 : cappedScore;

        // Calculate angle for the dial pointer
        const maxScore = 200; // The highest value on the dial (200%)
        const minScore = 0;   // The lowest value on the dial (0%)
        const angle = Math.PI * (validScore - minScore) / (maxScore - minScore);

        // Dial coordinates and pointer calculations
        const centerX = 200; // Center of the dial
        const centerY = 200; // Center of the dial
        const radius = 140;  // Radius of the arc

        const pointerX = centerX + radius * Math.cos(Math.PI - angle); // Flip angle for correct positioning
        const pointerY = centerY - radius * Math.sin(Math.PI - angle); // Account for SVG coordinate system

        // Dial chart container
        const container = document.getElementById('dial_chart_container');
        container.innerHTML = ''; // Clear existing chart content

        // SVG to represent the dial chart
        const svg = `
            <svg width="400" height="220" viewBox="0 0 400 220" xmlns="http://www.w3.org/2000/svg">
                <!-- Background Arc -->
                <path d="M40,205 A163,163 0 0,1 360,204" fill="none" stroke="#d3d3d3" stroke-width="20"></path>
                
                <!-- Red (Low KPI) -->
                <path d="M20,200 A180,180 0 0,1 50,131" fill="none" stroke="#f44336" stroke-width="20"></path>
                <text x="60" y="190" font-size="14" text-anchor="start" fill="#f44336">@lang('kpi::lang.risk')</text>

                <!-- Orange (Warning) -->
                <path d="M51,129 A180,180 0 0,1 200,53" fill="none" stroke="#FE9900" stroke-width="20"></path>
                <text x="90" y="140" font-size="14" text-anchor="start" fill="#FE9900">@lang('kpi::lang.poor')</text>

                <!-- Cyan (Normal) -->
                <path d="M201,53 A180,180 0 0,1 209,53" fill="none" stroke="#14C5D5" stroke-width="20"></path>
                <text x="200" y="100" font-size="14" text-anchor="middle" fill="#14C5D5">@lang('kpi::lang.everage')</text>

                <!-- Green (Good KPI) -->
                <path d="M210,53 A180,180 0 0,1 349,130" fill="none" stroke="#4caf50" stroke-width="20"></path>
                <text x="300" y="140" font-size="14" text-anchor="end" fill="#4caf50">@lang('kpi::lang.good')</text>

                <!-- Dark Green (Excellent KPI) -->
                <path d="M350,131 A180,180 0 0,1 380,200" fill="none" stroke="#116A38" stroke-width="20"></path>
                <text x="340" y="190" font-size="14" text-anchor="end" fill="#116A38">@lang('kpi::lang.excellent')</text>

                <!-- Pointer (Current Score) -->
                <line x1="200" y1="200" 
                      x2="${pointerX}" 
                      y2="${pointerY}" 
                      stroke="#000" stroke-width="4" />

                <!-- Center Circle -->
                <circle cx="200" cy="200" r="7" fill="#000"></circle>

                <!-- Score Text -->
                <text x="200" y="180" font-size="20" text-anchor="middle" fill="#000">
                    ${totalScore}%
                </text>
            </svg>
        `;

        container.innerHTML = svg;
    }

    // Reinitialize chart when modal is shown
    $('#yourModalId').on('shown.bs.modal', function () {
        drawDialChart();
    });

    // Initial chart load
    drawDialChart();
</script>