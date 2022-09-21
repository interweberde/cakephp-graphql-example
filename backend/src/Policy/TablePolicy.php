<?php

namespace App\Policy;

use App\Model\Entity\User;
use Interweber\GraphQL\Policy\TablePolicy as BaseTablePolicy;

/**
 * @template-extends BaseTablePolicy<User>
 */
abstract class TablePolicy extends BaseTablePolicy {

}
