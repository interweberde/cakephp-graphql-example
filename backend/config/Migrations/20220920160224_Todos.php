<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Todos extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('todos')
            ->addColumn('user_id', \Phinx\Db\Table\Column::INTEGER)
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('sort_by', \Phinx\Db\Table\Column::INTEGER)
            ->addIndex(['user_id', 'sort_by'])
            ->addColumn('title', \Phinx\Db\Table\Column::STRING)
            ->addColumn('content', \Phinx\Db\Table\Column::TEXT)
            ->addColumn('done', \Phinx\Db\Table\Column::DATETIME, ['null' => true])
            ->addColumn('created', \Phinx\Db\Table\Column::DATETIME)
            ->addColumn('modified', \Phinx\Db\Table\Column::DATETIME)
            ->create();
    }
}
