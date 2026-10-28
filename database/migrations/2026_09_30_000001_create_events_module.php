<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 12)->default('tourist')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('origin_city')->nullable()->after('phone');
            $table->boolean('marketing_consent')->default(false)->after('origin_city');
            $table->string('avatar')->nullable()->after('marketing_consent');
        });
        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        // Las paradas de un tour son las filas de factory_tour (una fábrica dentro de un tour).
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_stop_id')->constrained('factory_tour')->cascadeOnDelete();
            $table->string('name_es');
            $table->string('name_en');
            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();
            $table->string('type', 12); // checkpoint | photo | quiz
            $table->json('config')->nullable();
            $table->unsignedSmallInteger('max_points')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->restrictOnDelete();
            $table->foreignId('guide_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->unsignedSmallInteger('capacity');
            $table->string('status', 12)->default('scheduled'); // scheduled | in_progress | finished | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'date']);
        });

        Schema::create('event_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_stop_id')->constrained('factory_tour')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->time('estimated_time')->nullable();
            $table->unique(['event_id', 'tour_stop_id']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 12)->default('confirmed'); // confirmed | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedSmallInteger('attempts')->default(1);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'activity_id', 'user_id']);
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_stop_id')->nullable()->constrained('factory_tour')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->string('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('memories', function (Blueprint $table) {
            $table->id();
            $table->string('token', 40)->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memories');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('participations');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('event_stops');
        Schema::dropIfExists('events');
        Schema::dropIfExists('activities');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'phone', 'origin_city', 'marketing_consent', 'avatar']));
    }
};
