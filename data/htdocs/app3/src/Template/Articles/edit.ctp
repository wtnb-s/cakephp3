<h1>Edit Article</h1>
<?= $this->Form->create($article); ?>
<?= $this->Form->control('user_id', ['type' => 'hidden']); ?>
<?= $this->Form->control('title'); ?>
<?= $this->Form->control('tags._ids', ['options' => $tags]); ?>
<?= $this->Form->control('tag_string', ['type' => 'text']); ?>
<?= $this->Form->control('body', ['rows' => '3']); ?>
<?= $this->Form->button(__('Save Article')); ?>
<?= $this->Form->end(); ?>