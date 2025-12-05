<div class="navbar-container">
    <ul class="navbar-list pages">
            <?php foreach (catalogueNav(['featured' => true, 'status' => 'published']) as $page): ?>
            <li>
                    <a href="<?= catalogue('url') ?>">
                        <?= catalogue('title') ?>
                    </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>