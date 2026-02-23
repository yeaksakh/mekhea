<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('productcostingb11::lang.add_ProductCostingB11')</h4>
        </div>
        <div class="modal-body">
            <form id="add_ProductCostingB11_form" method="POST" action="{{ route('ProductCostingB11.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="productcostingb11_category_id">@lang('productcostingb11::lang.category'):</label>
                            <select class="form-control select2" id="productcostingb11_category_id" name="productcostingb11_category_id" style="width: 100%;">
                                <option value="">@lang('messages.select')</option>
                                @foreach ($productcostingb11_categories as $id => $category)
                                <option value="{{ $id }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_1">@lang('productcostingb11::lang.product_1'):</label>
                            <input type="text" class="form-control" id="product_1" name="product_1">
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_1">@lang('productcostingb11::lang.product_1'):</label>
                            <input type="text" class="form-control" id="product_1" name="product_1">
                            <!-- <select class="form-control select2" id="product_1" name="product_1">
                                <option value="">@lang('messages.please_select')</option>
                                @foreach($products as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select> -->
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="cost_2">@lang('productcostingb11::lang.cost_2'):</label>
                            <input type="number" class="form-control" id="cost_2" name="cost_2">
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                            <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('productcostingb11::lang.cost')</h3>
                            <div class="card-body">
                                <table class="table table-hover" id="cost-table" style="width: 100%;">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>#</th>
                                            <th style="width: 50%;">@lang('productcostingb11::lang.name')</th>
                                            <th style="width: 50%;">@lang('productcostingb11::lang.value')</th>
                                            <th style="width: 50%;">@lang('productcostingb11::lang.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cost-body">
                                        @foreach ([
                                        ['name' => __('productcostingb11::lang.enter_name'), 'index' => 0],
                                        ] as $key => $competency)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><input type="text" name="cost[{{$competency['index']}}][name]" class="form-control" placeholder="@lang('productcostingb11::lang.enter_name')"></td>
                                            <td><input type="text" name="cost[{{$competency['index']}}][value]" class="form-control" placeholder="@lang('productcostingb11::lang.enter_value')"></td> <!-- Placeholder added -->
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row">@lang('productcostingb11::lang.remove')</button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm" id="add-cost-row">@lang('productcostingb11::lang.add_cost')</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                            <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('productcostingb11::lang.qty')</h3>
                            <div class="card-body">
                                <table class="table table-hover" id="quantity-table" style="width: 100%;">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>#</th>
                                            <th style="width: 50%;">@lang('productcostingb11::lang.value')</th>
                                            <th style="width: 50%;">@lang('productcostingb11::lang.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quantity-body">
                                        @foreach ([
                                        ['name' => __('productcostingb11::lang.customer_experience_management'), 'index' => 0],
                                        ] as $key => $competency)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><input type="text" name="quantity[{{$competency['index']}}][value]" class="form-control" placeholder="@lang('productcostingb11::lang.enter_value')"></td> <!-- Placeholder added -->
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row">@lang('productcostingb11::lang.remove')</button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm" id="add-quantity-row">@lang('productcostingb11::lang.add_cost')</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="qty_3">@lang('productcostingb11::lang.qty_3'):</label>
                            <input type="number" class="form-control" id="qty_3" name="qty_3">
                        </div>
                    </div> -->

                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-cost-row').addEventListener('click', function() {
    addCompetencyRow('cost-body', 'cost');
});

document.getElementById('add-quantity-row').addEventListener('click', function() {
    addCompetencyRow('quantity-body', 'quantity');
});

function addCompetencyRow(tableBodyId, namePrefix) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) {
        console.error(`Table body with id ${tableBodyId} not found`);
        return;
    }

    const rowCount = tableBody.getElementsByTagName('tr').length;
    let newRow = '';

    if (tableBodyId === 'quantity-body') {
        newRow = `
        <tr>
            <td>${rowCount + 1}</td>
            <td>
                <input type="text" 
                       name="${namePrefix}[${rowCount}][value]" 
                       class="form-control" 
                       placeholder="Enter Value"
                       required>
            </td>
            <td>
                <button type="button" 
                        class="btn btn-danger btn-sm remove-row">
                    Remove
                </button>
            </td>
        </tr>`;
    } else {
        newRow = `
        <tr>
            <td>${rowCount + 1}</td>
            <td>
                <input type="text" 
                       name="${namePrefix}[${rowCount}][name]" 
                       class="form-control" 
                       placeholder="New Competency"
                       required>
            </td>
            <td>
                <input type="text" 
                       name="${namePrefix}[${rowCount}][value]" 
                       class="form-control" 
                       placeholder="Enter Value"
                       required>
            </td>
            <td>
                <button type="button" 
                        class="btn btn-danger btn-sm remove-row">
                    Remove
                </button>
            </td>
        </tr>`;
    }

    try {
        tableBody.insertAdjacentHTML('beforeend', newRow);
        addRemoveRowFunctionality(tableBodyId);
    } catch (error) {
        console.error('Error adding new row:', error);
    }
}

function addRemoveRowFunctionality(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    tableBody.querySelectorAll('.remove-row').forEach(function(button) {
        button.addEventListener('click', function() {
            this.closest('tr').remove();
            updateRowNumbers(tableBodyId);
        });
    });
}

function updateRowNumbers(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    const rows = tableBody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const firstCell = rows[i].getElementsByTagName('td')[0];
        if (firstCell) {
            firstCell.textContent = i + 1;
        }
    }
}

    addRemoveRowFunctionality();
</script>