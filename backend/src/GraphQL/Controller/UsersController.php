<?php

namespace App\GraphQL\Controller;

use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use GraphQL\Type\Definition\ResolveInfo;
use Interweber\GraphQL\Classes\BaseController;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Query;

/**
 * @template-extends BaseController<UsersTable, User>
 */
class UsersController extends BaseController {
    public string $modelName = 'Users';

    #[Query]
    public function getIdentity(
        #[InjectUser]
        User $identity,
        ResolveInfo $resolveInfo
    ): User {
        return $this->_fetchEntity($resolveInfo, $identity, $identity);
    }
}
