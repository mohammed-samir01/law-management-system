<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('signing_status')->default('none')->after('status'); // none|pending|signed|rejected
            $table->string('signing_token')->nullable()->unique()->after('signing_status');
            $table->timestamp('signing_expires_at')->nullable()->after('signing_token');
            $table->foreignId('signing_client_id')->nullable()->after('signing_expires_at')
                ->constrained('clients')->nullOnDelete();
            $table->timestamp('signed_at')->nullable()->after('signing_client_id');
            $table->string('signer_ip')->nullable()->after('signed_at');
            $table->longText('signature_data')->nullable()->after('signer_ip'); // base64 PNG
            $table->string('signed_pdf_path')->nullable()->after('signature_data');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('signing_client_id');
            $table->dropColumn([
                'signing_status', 'signing_token', 'signing_expires_at',
                'signed_at', 'signer_ip', 'signature_data', 'signed_pdf_path',
            ]);
        });
    }
};
