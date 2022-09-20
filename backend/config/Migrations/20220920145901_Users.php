<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Table\Column;

class Users extends AbstractMigration
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
        $this->table('users')
            ->addColumn('first_name', Column::STRING)
            ->addColumn('middle_name', Column::STRING, ['null' => true])
            ->addColumn('last_name', Column::STRING)
            ->addColumn('email', Column::STRING)
            ->addColumn('password', Column::STRING)
            ->addColumn('created', Column::DATETIME)
            ->addColumn('modified', Column::DATETIME)
            ->create();
    }
}
