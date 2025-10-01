<?php

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Comment;
use Ikoncept\Fabriq\Models\SearchTerm;
use Ikoncept\Fabriq\Models\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Models\I18nTerm;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $media = Fabriq::getModelClass('media')->where('model_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->model_type;
            $morphName = $model->getMorphClass();
            $item->model_type = $morphName;
            $item->save();
        });
        $searchTerms = SearchTerm::where('model_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->model_type;
            $morphName = $model->getMorphClass();
            $item->model_type = $morphName;
            $item->save();
        });

        $taggables = DB::table('taggables')->where('taggable_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->taggable_type;
            $morphName = $model->getMorphClass();
            DB::table('taggables')
                ->where('tag_id', $item->tag_id)
                ->where('taggable_id', $item->taggable_id)
                ->update(['taggable_type' => $morphName]);
        });

        $comments = Comment::where('commentable_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->commentable_type;
            $morphName = $model->getMorphClass();
            $item->commentable_type = $morphName;
            $item->save();
        });
        $modelHasRoles = DB::table('model_has_roles')->where('model_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->model_type;
            $morphName = $model->getMorphClass();
            DB::table('model_has_roles')
                ->where('role_id', $item->role_id)
                ->where('model_id', $item->model_id)
                ->update(['model_type' => $morphName]);
        });

        $slugs = Slug::where('model_type', 'LIKE', 'Ikoncept%')->get()->each(function ($item) {
            $model = new $item->model_type;
            $morphName = $model->getMorphClass();
            $item->model_type = $morphName;
            $item->save();
        });

        $terms = I18nTerm::where('model_type', 'LIKE', 'Ikoncept%')->get()->each(function ($term) {
            $model = new $term->model_type;
            $morphName = $model->getMorphClass();
            $term->model_type = $morphName;
            $term->save();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
