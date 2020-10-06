<?php
namespace App\Controller;
use App\Controller\AppController;

class ArticlesController extends AppController {
    public function initialize() {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
    }

    public function index() {
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($articleId) {
        $article = $this->Articles->findById($articleId)->firstOrFail();
        $this->set(compact('article'));
    }

    public function add() {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            $article['user_id'] = 1;
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('記事を追加できませんでいた'));
        }
        $tags = $this->Articles->Tags->find('list');
        $this->set(compact('article', 'tags'));
    }

    public function edit($articleId) {
        $article = $this->Articles->findById($articleId)->firstOrFail();
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('記事を更新できませんでした'));
        }
        $this->set(compact('article'));
    }

    public function delete($articleId) {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->findById($articleId)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('{0} の記事を削除できませんでいた.', $article['title']));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags() {
        $tags = $this->request->getParam('pass');
        $articles = $this->Articles->find('tagged', ['tags' => $tags]);
        $this->set(compact('articles', 'tags'));
    }

}