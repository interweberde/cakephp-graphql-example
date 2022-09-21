<?php

namespace App\GraphQL\Factory;

use App\Model\Entity\Todo;
use App\Model\Entity\User;
use App\Model\Table\TodosTable;
use Interweber\GraphQL\Factory\BaseFactory;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @template-extends BaseFactory<TodosTable>
 */
class TodoFactory extends BaseFactory {
    protected $defaultTable = TodosTable::class;

    #[Factory(name: 'CreateTodo', default: true)]
    public function create(
        #[InjectUser]
        User $user,
        string $title,
        string $content,
        ?int $sort_by
    ): Todo {
        return $this->model->newEntity([
            'user_id' => $user->id,
            'title' => $title,
            'content' => $content,
            'sort_by' => $sort_by,
        ]);
    }

    #[Factory(name: 'UpdateTodo', default: false)]
    public function update(
        #[InjectUser]
        User $user,
        ID $id,
        ?string $title,
        ?string $content,
        ?int $sort_by,
    ): Todo {
        $entity = $this->model->get((string) $id);

        return $this->model->patchEntity($entity, [
            'title' => $title === null ? $entity->title : $title,
            'content' => $content === null ? $entity->content : $content,
            'sort_by' => $sort_by === null ? $entity->sort_by : $sort_by
        ]);
    }
}
