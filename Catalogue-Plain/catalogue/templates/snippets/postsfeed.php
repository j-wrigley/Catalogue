<div class="postsfeed-container">
    <?php 
    // Filter options - limit is automatically ignored during HTML generation
    // (all items included for JavaScript pagination)
    $filters = [
        'status' => 'published',
        'sort' => 'created_at',
        'order' => 'asc',
        'limit' => 50
    ];
    ?>
    <?php foreach (catalogueCollection('posts', $filters) as $post): ?>
        <article class="feed-post">
            <div class="feed-post-info">
            <a href="<?= catalogue('url') ?>"><span class="feed-post-tags"><?= catalogue('tags') ?></span><p><?= catalogue('title') ?> <span class="project-status feed-status p-<?= catalogue('project-status') ?>"><?= catalogue('project-status') ?></span></p></a>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<?php echo cataloguePagination('posts', $filters); ?>