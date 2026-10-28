<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline_es')->nullable();
            $table->string('tagline_en')->nullable();
            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();
            $table->string('address');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('color', 7)->default('#6b3e26');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('route_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('address');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('hours')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_factory_outlet')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_es');
            $table->string('name_en');
            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->decimal('price', 8, 2);
            $table->unsignedSmallInteger('capacity')->default(15);
            $table->json('departure_times');
            $table->json('weekdays');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('factory_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->foreignId('tour_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->string('time', 5);
            $table->unsignedSmallInteger('people');
            $table->decimal('total', 10, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('country')->nullable();
            $table->string('language', 2)->default('es');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['tour_id', 'date', 'time']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name_es');
            $table->string('name_en');
            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('weight')->nullable();
            $table->string('cacao')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->date('pickup_date');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('factory_tour');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('factories');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
