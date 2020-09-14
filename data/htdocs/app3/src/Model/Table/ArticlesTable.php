<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ArticlesTable extends Table {
    public function initialize(array $config) {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator) {
    $validator
        ->allowEmptyString('title', false)
        ->minLength('title', 10)
        ->maxLength('title', 255)
        ->allowEmptyString('body', false)
        ->minLength('body', 10);
        return $validator;
    }
}
