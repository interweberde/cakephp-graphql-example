<?php

namespace App\Policy;

use App\Model\Entity\User;
use Interweber\GraphQL\Policy\EntityPolicy as BaseEntityPolicy;

/**
 * @template-extends BaseEntityPolicy<User>
 */
abstract class EntityPolicy extends BaseEntityPolicy {

}
