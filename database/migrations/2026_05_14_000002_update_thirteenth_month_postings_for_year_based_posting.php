<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class UpdateThirteenthMonthPostingsForYearBasedPosting extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('thirteenth_month_postings')) {
            return;
        }

        $indexColumns = $this->indexColumns();
        if (in_array('posting_cut_off', $indexColumns)) {
            DB::statement('ALTER TABLE thirteenth_month_postings DROP INDEX thirteenth_month_unique_posting');
        }

        DB::statement('ALTER TABLE thirteenth_month_postings MODIFY posting_cut_off DATE NULL');

        if (in_array('posting_cut_off', $indexColumns)) {
            DB::statement('ALTER TABLE thirteenth_month_postings ADD UNIQUE thirteenth_month_unique_posting (employee_no, half, year)');
        }
    }

    public function down()
    {
        if (!Schema::hasTable('thirteenth_month_postings')) {
            return;
        }

        DB::statement('ALTER TABLE thirteenth_month_postings DROP INDEX thirteenth_month_unique_posting');
        DB::statement('ALTER TABLE thirteenth_month_postings MODIFY posting_cut_off DATE NOT NULL');
        DB::statement('ALTER TABLE thirteenth_month_postings ADD UNIQUE thirteenth_month_unique_posting (employee_no, half, year, posting_cut_off)');
    }

    private function indexColumns()
    {
        return collect(DB::select("SHOW INDEX FROM thirteenth_month_postings WHERE Key_name = 'thirteenth_month_unique_posting'"))
            ->sortBy('Seq_in_index')
            ->pluck('Column_name')
            ->toArray();
    }
}
