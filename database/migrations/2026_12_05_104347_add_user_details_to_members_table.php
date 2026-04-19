<?php

use App\Models\Brand;
use App\Models\DeviceModel;
use App\Models\GraphicCard;
use App\Models\Member;
use App\Models\Memory;
use App\Models\Processor;
use App\Models\Storage as HDStorage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('created_by_member_id')
                ->nullable()
                ->after('id')
                ->constrained('members')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['created_by_member_id']);
            $table->dropColumn('created_by_member_id');
        });
    }
};
