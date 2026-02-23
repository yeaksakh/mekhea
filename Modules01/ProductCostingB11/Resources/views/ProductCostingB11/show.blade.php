<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="printArea">
        <div class="modal-header no-print">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Details</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                          <div class="col-md-12 d-flex justify-content-center align-items-center">
    <div class="form-group  text-center">
        <p for="product_1" class="mr-2">Products:
         <i id="product_1" class="form-control-static mb-0">{{ $productcostingb11->product_1 }}</i>
        </p>
    </div>
    

                    <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                      
                        <div class="card-body d-flex justify-content-center">
@if(is_object($productcostingb11) && $productcostingb11->productcost)
<table class="table table-hover text-center" id="quantity-table" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>សមាសភាគថ្លៃដើមសរុប</th>
                                        <th>បរិមាណនាំចូល ឬ ផលិតចេញ</th>
                                        <th>ថ្លៃដើមក្នុង1ឯកតា</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total_value = $productcostingb11->productcost->sum(function ($value) {
                                    return $value->value ? floatval($value->value) : 0;
                                    });

                                    $total_qty = $productcostingb11->productcost->sum(function ($qty) {
                                    return $qty->qty ? floatval($qty->qty) : 0;
                                    });

                                    $cost_per_unit = $total_qty != 0 ? $total_value / $total_qty : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ number_format($total_value, 2) }}$</td>
                                        <td>{{ $total_qty }}</td>
                                        <td>
                                            @if (floor($cost_per_unit) == $cost_per_unit)
                                            {{ number_format($cost_per_unit, 0) }}$
                                            @else
                                            {{ number_format($cost_per_unit, 2) }}$
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @else
                            <p>No product costing data available.</p>
                            @endif
                        </div>
                    </div>

                <div class="col-md-12">
                    <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                        <div class="card-body">
                            <canvas id="myChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                

                <div class="col-md-6">
                    <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                        <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">សមាសភាគថ្លៃដើម</h3>
                        <div class="card-body">
                            <table class="table table-hover" id="cost-table" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>#</th>
                                        <th style="width: 50%;">ឈ្មោះត្រូវចំណាយ</th>
                                        <th style="width: 50%;">ថ្លៃដើម</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costs  as $key =>$cost)
                                    @if ($cost->value > 0)
                                    <tr>
                                        <td>{{$key +1 }}</td>
                                        <td>{{ $cost->name }}</td>
                                        <td>{{ $cost->value }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                        <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">បរិមាណនាំចូល ឬ ផលិត</h3>
                        <div class="card-body">
                            <table class="table table-hover" id="quantity-table" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>#</th>
                                        <th style="width: 50%;">@lang('productcostingb11::lang.value')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quantities  as $key => $quantity)
                                    @if ($quantity->qty > 0)
                                    <tr>
                                        <td>{{$key +1 }}</td>
                                        <td>{{ $quantity->qty }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer no-print">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" aria-label="Print" onclick="printModalContent();">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var costData = @json($costs);
    var quantityData = @json($quantities);

    var labels = costData.map(function(cost) {
        return cost.name;
    });
    var costValues = costData.map(function(cost) {
        return cost.value;
    });

    var ctx = document.getElementById('myChart').getContext('2d');

    function getRandomColor(opacity) {
        var r = Math.floor(Math.random() * 256);
        var g = Math.floor(Math.random() * 256);
        var b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    var backgroundColors = costValues.map(() => getRandomColor(0.5));
    var borderColors = costValues.map(() => getRandomColor(1));

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'ក្រាហ្វិកសមាសភាគថ្លៃដើម',
                data: costValues,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script>
function printModalContent() {
    var printArea = document.getElementById('printArea').cloneNode(true); // Clone the print area to manipulate
    var canvas = document.getElementById('myChart'); // Get the canvas element

    if (canvas) {
        // Convert canvas to image
        var img = new Image();
        img.onload = function() {
            // Replace the canvas with the generated image in the cloned area
            var clonedCanvasParent = printArea.querySelector('canvas').parentNode;
            clonedCanvasParent.replaceChild(img, clonedCanvasParent.querySelector('canvas'));

            // Open the print window after replacing the canvas with the image
            openPrintWindow(printArea.innerHTML);
        };
        img.src = canvas.toDataURL('image/png'); // Convert canvas to a PNG data URL
        img.style.width = '100%'; // Match the canvas size
        img.style.height = 'auto'; // Maintain aspect ratio
    } else {
        // If no canvas is found, print directly
        openPrintWindow(printArea.innerHTML);
    }
}

function openPrintWindow(content) {
    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .card { border: 1px solid #e3e3e3; margin: 20px; padding: 20px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15); }
                img { max-width: 100%; height: auto; }
                .no-print { display: none; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>