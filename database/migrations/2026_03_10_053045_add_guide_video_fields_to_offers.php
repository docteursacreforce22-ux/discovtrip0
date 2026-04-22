<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $cols = Schema::getColumnListing('offers');

            if (! in_array('guide_type', $cols))
                $table->string('guide_type')->default('agency')->after('status');

            if (! in_array('video_url', $cols))
                $table->string('video_url')->nullable()->after('gallery');

            if (! in_array('languages', $cols))
                $table->json('languages')->nullable()->after('video_url');

            if (! in_array('meeting_point', $cols))
                $table->string('meeting_point')->nullable()->after('languages');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $cols = Schema::getColumnListing('offers');

            if (in_array('guide_type', $cols))   $table->dropColumn('guide_type');
            if (in_array('video_url', $cols))    $table->dropColumn('video_url');
            if (in_array('languages', $cols))    $table->dropColumn('languages');
            if (in_array('meeting_point', $cols)) $table->dropColumn('meeting_point');
        });
    }
};
