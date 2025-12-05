<?= snippet('header') ?>
<!-- Posts Template -->
<div class="posts-page-container">
    <section class="navbar">
        <?= snippet('navbar') ?>
    </section>
    <div class="posts-page-content">
    <section class="post-info">
        <div class="post-info-left">
            <p>Status: <?= catalogue('status') ?></p>
            <p>Created: <?= catalogue('created_at', '', 'content', 'F j, Y') ?></p>
            <p>Updated: <?= catalogue('updated_at', '', 'content', 'F j, Y') ?></p>
            <div class="post-tags"><?= catalogue('tags') ?></div>

            <div class="project-status p-<?= catalogue('project-status') ?>">
                <?= catalogue('project-status') ?>
            </div>

            <div class="post-links">
            <?php foreach (catalogueStructure('project-links') as $item): ?>
                <div>
                    <p><a href="<?= catalogue('url') ?>" target="_blank"><?= catalogue('name') ?></a></p>
                </div>
            <?php endforeach; ?>
            </div>


        </div>
        <div class="post-info-right">
            <p><?= catalogue('title') ?></p>
            <p><?= catalogue('description') ?></p>
            <p><?= catalogue('content') ?></p>
        </div>
    </section>
</div>

 
 </div>


<?= snippet('footer') ?>