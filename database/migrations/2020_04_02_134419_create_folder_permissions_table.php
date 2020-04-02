<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('folder_id');
            $table->bigInteger('user_id');

            // Specific permissions for a user for a folder.
            $table->boolean('read');
            $table->boolean('write');
            $table->boolean('download');

            // Audit columns.
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes.
            $table->index('folder_id');
            $table->index('user_id');
            $table->index('read');
            $table->index('write');
            $table->index('download');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_permissions');
    }
}
