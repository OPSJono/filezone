<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_folder_id')->nullable();
            $table->char('name', 255);
            $table->char('description', 255)->nullable();

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
            $table->index('name');
            $table->index('description');
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
        Schema::dropIfExists('folders');
    }
}
