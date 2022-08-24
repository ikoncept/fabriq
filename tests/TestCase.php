<?php

namespace Ikoncept\Fabriq\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        // Pass::routes(function ($registrar) { $registrar->forAuthorization(); });
        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->setUpDatabase($this->app);
    }

    public function setUpDatabase($app)
    {
        // $app['config']->set('translatable-revisions.revisions_table_name', 'pages');
        // $app['config']->set('translatable-revisions.revision_templates_table_name', 'revision_templates');
        // $app['config']->set('translatable-revisions.revision_meta_table_name', 'revision_meta');
        // $app['config']->set('translatable-revisions.revision_template_fields_table_name', 'revision_template_fields');

        // include_once(__DIR__  . '/../database/migrations/create_translatable_revisions_table.php.stub');
        // (new \CreateTranslatableRevisionsTable())->up();

        // include_once(__DIR__  . '/../database/migrations/create_translatable_revision_templates_table.php.stub');
        // (new \CreateTranslatableRevisionTemplatesTable())->up();

        // include_once(__DIR__  . '/../database/migrations/create_translatable_revision_meta_table.php.stub');
        // (new \CreateTranslatableRevisionMetaTable())->up();

        // include_once(__DIR__  . '/../database/migrations/create_translatable_revision_template_fields_table.php.stub');
        // (new \CreateTranslatableRevisionTemplateFieldsTable())->up();

        // include_once(__DIR__  . '/../database/migrations/create_i18n_tables.php.stub');
        // (new \CreateI18nTables())->up();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
