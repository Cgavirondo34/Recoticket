<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tenants ──────────────────────────────────────────────────────────
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->default('America/Argentina/Buenos_Aires');
            $table->string('currency', 10)->default('ARS');
            $table->boolean('active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add tenant_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->index('tenant_id');
        });

        // ── Partners ──────────────────────────────────────────────────────────
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('gym_percentage', 5, 2)->default(50.00);
            $table->decimal('field_percentage', 5, 2)->default(50.00);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Membership Plans ─────────────────────────────────────────────────
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->boolean('auto_renew_default')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Members ──────────────────────────────────────────────────────────
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->unsignedBigInteger('current_plan_id')->nullable();
            $table->date('membership_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('current_plan_id')->references('id')->on('membership_plans')->nullOnDelete();
        });

        // ── Trainers ─────────────────────────────────────────────────────────
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('specialty')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Add trainer FK to members
        Schema::table('members', function (Blueprint $table) {
            $table->foreign('trainer_id')->references('id')->on('trainers')->nullOnDelete();
        });

        // ── Member Memberships (subscription history) ─────────────────────────
        Schema::create('member_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->constrained()->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('expires_at');
            $table->boolean('auto_renew')->default(false);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });

        // ── Payment Methods ──────────────────────────────────────────────────
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ── Gym Payments ─────────────────────────────────────────────────────
        Schema::create('gym_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_membership_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->string('reference')->nullable();
            $table->string('mercadopago_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Exercises ─────────────────────────────────────────────────────────
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('muscle_group')->nullable();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Routines ─────────────────────────────────────────────────────────
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->text('goal')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // ── Routine Exercises ─────────────────────────────────────────────────
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sets')->default(3);
            $table->string('reps')->default('10');
            $table->string('rest_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // ── Routine Assignments ────────────────────────────────────────────────
        Schema::create('routine_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('routine_id')->constrained()->cascadeOnDelete();
            $table->date('assigned_at');
            $table->date('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->text('trainer_notes')->nullable();
            $table->timestamps();
        });

        // ── Field Time Slots ──────────────────────────────────────────────────
        Schema::create('field_time_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('label');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ── Reservations ──────────────────────────────────────────────────────
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('field_time_slot_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_whatsapp')->nullable();
            $table->date('reservation_date');
            $table->enum('type', ['occasional', 'recurring'])->default('occasional');
            $table->enum('recurrence_day', ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'])->nullable();
            $table->date('recurring_until')->nullable();
            $table->enum('status', ['confirmed', 'cancelled', 'pending'])->default('confirmed');
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Reservation Payments ──────────────────────────────────────────────
        Schema::create('reservation_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Expense Categories ─────────────────────────────────────────────────
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('color', 20)->default('#6b7280');
            $table->timestamps();
        });

        // ── Expenses ──────────────────────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('expense_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->enum('business_unit', ['gym', 'field', 'shared'])->default('shared');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Financial Settlements ─────────────────────────────────────────────
        Schema::create('financial_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('month');
            $table->decimal('gym_income', 12, 2)->default(0);
            $table->decimal('field_income', 12, 2)->default(0);
            $table->decimal('total_income', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('net_income', 12, 2)->default(0);
            $table->json('partner_distributions')->nullable();
            $table->enum('status', ['draft', 'confirmed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'year', 'month']);
        });

        // ── Settlement Items ───────────────────────────────────────────────────
        Schema::create('settlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_settlement_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        // ── WhatsApp Templates ─────────────────────────────────────────────────
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('event');
            $table->string('name');
            $table->text('body');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ── Notification Log ───────────────────────────────────────────────────
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('channel')->default('whatsapp');
            $table->string('recipient');
            $table->string('event');
            $table->text('body');
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // ── Settings ───────────────────────────────────────────────────────────
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('settlement_items');
        Schema::dropIfExists('financial_settlements');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('reservation_payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('field_time_slots');
        Schema::dropIfExists('routine_assignments');
        Schema::dropIfExists('routine_exercises');
        Schema::dropIfExists('routines');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('gym_payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('member_memberships');
        Schema::dropIfExists('members');
        Schema::dropIfExists('trainers');
        Schema::dropIfExists('membership_plans');
        Schema::dropIfExists('partners');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
        Schema::dropIfExists('tenants');
    }
};
