<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->string('visit_hours_es')->nullable()->after('address');
            $table->string('visit_hours_en')->nullable()->after('visit_hours_es');
            $table->string('phone')->nullable()->after('visit_hours_en');
            $table->string('website')->nullable()->after('phone');
            $table->string('social_url')->nullable()->after('website');
            $table->boolean('has_museum')->default(false)->after('social_url');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('variety')->nullable()->after('cacao');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->string('meeting_point_es')->nullable()->after('capacity');
            $table->string('meeting_point_en')->nullable()->after('meeting_point_es');
            $table->decimal('meeting_lat', 10, 7)->nullable()->after('meeting_point_en');
            $table->decimal('meeting_lng', 10, 7)->nullable()->after('meeting_lat');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('tour_id')->constrained()->nullOnDelete();
        });

        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('distance_m')->nullable();
            $table->boolean('gps_verified')->default(false);
            $table->unsignedSmallInteger('points')->default(10);
            $table->timestamps();
            $table->unique(['user_id', 'factory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropConstrainedForeignId('user_id');
        });
        Schema::table('tours', fn (Blueprint $t) => $t->dropColumn(['meeting_point_es', 'meeting_point_en', 'meeting_lat', 'meeting_lng']));
        Schema::table('products', fn (Blueprint $t) => $t->dropColumn('variety'));
        Schema::table('factories', fn (Blueprint $t) => $t->dropColumn(['visit_hours_es', 'visit_hours_en', 'phone', 'website', 'social_url', 'has_museum']));
    }
};
