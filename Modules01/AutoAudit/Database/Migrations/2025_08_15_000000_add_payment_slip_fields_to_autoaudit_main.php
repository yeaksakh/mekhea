<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentSlipFieldsToAutoauditMain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('autoaudit_main')) {
            Schema::table('autoaudit_main', function (Blueprint $table) {
                if (!Schema::hasColumn('autoaudit_main', 'extracted_text')) {
                    $table->text('extracted_text')->nullable()->after('audit_status_2');
                }
                if (!Schema::hasColumn('autoaudit_main', 'comparison_details')) {
                    $table->text('comparison_details')->nullable()->after('extracted_text');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autoaudit_main', function (Blueprint $table) {
            if (Schema::hasColumn('autoaudit_main', 'extracted_text')) {
                $table->dropColumn('extracted_text');
            }
            if (Schema::hasColumn('autoaudit_main', 'comparison_details')) {
                $table->dropColumn('comparison_details');
            }
        });

        Schema::dropIfExists('autoaudit_main'); // Fixed table name

    }
} 