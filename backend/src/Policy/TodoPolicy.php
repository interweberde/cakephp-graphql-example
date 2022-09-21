<?php

namespace App\Policy;

use Authorization\Policy\Result;
use Cake\Datasource\EntityInterface;

class TodoPolicy extends EntityPolicy {
    public function canShow($user, EntityInterface $entity): Result {
        if ($entity->get('user_id') === $user->id) {
            return new Result(true, 'Own Todo');
        }

        return new Result(false, 'Other Users Todo');
    }

    public function canCreate($user, EntityInterface $entity): Result {
        return $this->canShow($user, $entity);
    }

    public function canUpdate($user, EntityInterface $entity): Result {
        return $this->canShow($user, $entity);
    }

    public function canDelete($user, EntityInterface $entity): Result {
        return $this->canShow($user, $entity);
    }
}
