<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatableRevisionSnapshotsTable extends Migration
{
    public function up()
    {
        $tableName = config('translatable-revisions.revision_snapshots_table_name');

        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('model', 'snapshot_model');
            $table->integer('model_version');
            $table->string('locale');
            $table->json('content');
            $table->timestamps();

            $table->unique(['model_type', 'model_id', 'model_version', 'locale'], 'revision_snapshot_unique');
            $table->index(['model_type', 'model_id', 'model_version'], 'revision_snapshot_lookup_idx');
        });
    }

    public function down()
    {
        $tableName = config('translatable-revisions.revision_snapshots_table_name');

        Schema::dropIfExists($tableName);
    }
}
