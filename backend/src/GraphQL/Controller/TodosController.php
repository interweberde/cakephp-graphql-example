<?php

namespace App\GraphQL\Controller;

use App\GraphQL\Filter\TodosFilter;
use App\Model\Entity\Todo;
use App\Model\Entity\User;
use App\Model\Table\TodosTable;
use GraphQL\Type\Definition\ResolveInfo;
use Interweber\GraphQL\Classes\BaseController;
use Interweber\GraphQL\Classes\CakeORMPaginationResult;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\UseInputType;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @template-extends BaseController<TodosTable, Todo>
 */
class TodosController extends BaseController {
	#[Query]
	public function getTodo(
		ResolveInfo $resolveInfo,
		#[InjectUser]
		User        $identity,
		ID          $id
	): Todo {
		return $this->_fetchEntityByPK($resolveInfo, $identity, $id);
	}

	/**
	 * @param ResolveInfo $resolveInfo
	 * @param User $identity
	 * @param TodosFilter $filter
	 * @return Todo[]|CakeORMPaginationResult
	 * @psalm-return CakeORMPaginationResult<Todo>
	 */
	#[Query]
	public function getTodos(
		ResolveInfo $resolveInfo,
		#[InjectUser]
		User        $identity,
		TodosFilter $filter
	): CakeORMPaginationResult {
		return $this->_fetchEntities($resolveInfo, $identity, $filter);
	}

	#[Mutation]
	public function createTodo(
		ResolveInfo $resolveInfo,
		#[InjectUser]
		User        $identity,
		#[UseInputType(inputType: 'CreateTodo')]
		Todo        $todo
	): Todo {
		return $this->_createEntity($resolveInfo, $identity, $todo);
	}

	#[Mutation]
	public function updateTodo(
		ResolveInfo $resolveInfo,
		#[InjectUser]
		User        $identity,
		#[UseInputType(inputType: 'UpdateTodo')]
		Todo        $todo
	): Todo {
		return $this->_updateEntity($resolveInfo, $identity, $todo);
	}

	#[Mutation]
	public function deleteTodo(
		#[InjectUser]
		User $identity,
		ID   $id
	): bool {
		return $this->_deleteEntity($identity, 'id', $id);
	}
}
