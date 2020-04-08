<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->char('guid', '36');
            $table->bigInteger('folder_id');
            $table->char('name', 255);
            $table->char('description', 255)->nullable();
            $table->char('extension', 255);
            $table->char('mimetype', 255);
            $table->integer('size');

            // File storage info
            $table->char('storage_method', 255);
            $table->char('storage_region', 255);
            $table->text('storage_path');

            $table->text('file_hash')->nullable();

            // Audit columns.
            $table->bigInteger('last_accessed_by')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->bigInteger('last_downloaded_by')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();

            // Audit columns.
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('guid');
            $table->index('name');
            $table->index('description');
            $table->index('extension');
            $table->index('type');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
