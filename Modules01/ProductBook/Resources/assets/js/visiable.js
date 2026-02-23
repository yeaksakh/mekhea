
document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (!isViewMode) {
                return;
            }

            // Function to hide all columns except specified ones
            function showOnlySpecificColumns(tableSelector, columnsToShow) {
                try {
                    var table = $(tableSelector);
                    if (table.length === 0) {
                        console.error('Table not found with selector: ' + tableSelector);
                        return;
                    }

                    // Check if the table is a DataTable
                    if (!$.fn.DataTable || !$.fn.DataTable.isDataTable(tableSelector)) {
                        console.error(
                            'The table is not a DataTable. Ensure DataTables is initialized.');
                        return;
                    }

                    var dataTable = $(tableSelector).DataTable();

                    // Get total columns
                    var totalColumns = dataTable.columns().count();
                    console.log('Total columns detected: ' + totalColumns);

                    // Create an array of all column indexes
                    var allColumns = [];
                    for (var i = 0; i < totalColumns; i++) {
                        allColumns.push(i);
                    }

                    // Log column mapping for reference
                    dataTable.columns().every(function(index) {
                        var header = $(this.header());
                    });

                    // Filter out the columns we want to show
                    var columnsToHide = allColumns.filter(function(index) {
                        return !columnsToShow.includes(index);
                    });

           

                    // Hide columns using DataTables API
                    columnsToHide.forEach(function(index) {
                        dataTable.column(index).visible(false);
                    });

                } catch (error) {
                    console.error('Error in showOnlySpecificColumns: ' + error.message);
                }
            }

            // Define columns to show (zero-based indexes)
            var columnsToShow = visibleColumnNames; // Example: Show only these columns

            // Call the function
            showOnlySpecificColumns(tablename, columnsToShow);
        }, 500);
    });
