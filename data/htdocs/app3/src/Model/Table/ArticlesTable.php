<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class ArticlesTable extends Table {
    public function initialize(array $config) {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags'); 
    }

    public function validationDefault(Validator $validator) {
        $validator
            ->allowEmptyString('title', false)
            ->minLength('title', 3)
            ->maxLength('title', 255)
            ->allowEmptyString('body', false)
            ->minLength('body', 5);
        return $validator;
    }

    public function beforeSave($event, $entity, $options) {
        if ($entity['tag_string']) {
            $entity['tags'] = $this->_buildTags($entity['tag_string']);
        }
        // 他のコード
    }

    public function findTagged(Query $query, array $options) {
        $columns = [
            'Articles.id', 'Articles.user_id', 'Articles.title', 'Articles.body',
            'Articles.published', 'Articles.created', 'Articles.id'];
        $query = $query
            ->select($columns)
            ->distinct($columns);
    
        if (empty($options['tags'])) {
            // If there are no tags provided, find articles that have no tags.
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            // Find articles that have one or more of the provided tags.
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }
        return $query->group(['Articles.id']);
    }

    protected function _buildTags($tagString) {
        // タグをトリミング
        $newTags = array_map('trim', explode(',', $tagString));
        // 全てのからのタグを削除
        $newTags = array_filter($newTags);
        // 重複するタグの削減
        $newTags = array_unique($newTags);

        $out = [];
        $query = $this->Tags->find()->where(['Tags.title IN' => $newTags]);

        // 新しいタグのリストから既存のタグを削除。
        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        // 既存のタグを追加。
        foreach ($query as $tag) {
            $out[] = $tag;
        }
        // 新しいタグを追加。
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        return $out;
    }
}