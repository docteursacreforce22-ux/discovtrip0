<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            
            // File Info
            $table->string('filename');
            $table->string('path');
            $table->enum('type', ['image', 'video', 'document']);
            $table->string('mime_type')->nullable();
            $table->bigInteger('size_bytes');
            
            // Image Dimensions
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // Metadata
            $table->string('alt')->nullable();
            $table->text('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('uploaded_by');
        });
    }

    public function down(): void {
        Schema::dropIfExists('media');
    }
};
