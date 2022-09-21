<?php

namespace App\Policy;

use Cake\ORM\Query;

class TodosTablePolicy extends TablePolicy {
    public function scopeShow($user, Query $query): Query {
        return $query->matching('Users', fn (Query $q) => $q->where(['Users.id' => $user->id]));
    }

    public function scopeList($user, Query $query): Query {
        return $this->scopeShow($user, $query);
    }

    public function scopeUpdate($user, Query $query): Query {
        return $this->scopeShow($user, $query);
    }

    public function scopeDelete($user, Query $query): Query {
        return $this->scopeShow($user, $query);
    }
}
