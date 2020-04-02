<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('file_id');
            $table->bigInteger('user_id');

            // Specific permissions for a user for a file.
            $table->boolean('read');
            $table->boolean('write');
            $table->boolean('download');

            // Audit columns.
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes.
            $table->index('file_id');
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
        Schema::dropIfExists('file_permissions');
    }
}
