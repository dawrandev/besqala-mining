<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->string('cadastre_number')->nullable()->after('location_address');
            $table->string('property_right')->nullable()->after('cadastre_number');
            $table->string('bank_name')->nullable()->after('bank_account');
            $table->string('btc_address')->nullable()->after('bank_name');
        });

        Schema::table('residents', function (Blueprint $table): void {
            $table->string('cadastre_number')->nullable()->after('location_address');
            $table->string('btc_address')->nullable()->after('energy_source');
        });

        Schema::table('news_articles', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->dropColumn(['cadastre_number', 'property_right', 'bank_name', 'btc_address']);
        });

        Schema::table('residents', function (Blueprint $table): void {
            $table->dropColumn(['cadastre_number', 'btc_address']);
        });

        Schema::table('news_articles', function (Blueprint $table): void {
            $table->dropColumn('category');
        });
    }
};
