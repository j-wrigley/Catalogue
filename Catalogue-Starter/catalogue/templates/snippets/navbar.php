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

    <ul class="navbar-list collection">
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li>
                <a href="<?= catalogue('url') ?>">
                    <?= catalogue('title') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <ul class="navbar-list socials">
        <li>
            <a href="<?= catalogue('github_url', '', 'site') ?>">
                GitHub
            </a>
        </li>
        <li>
            <a href="<?= catalogue('threads_url', '', 'site') ?>">
                Threads
            </a>
        </li>
        <li>
            <a href="<?= catalogue('instagram_url', '', 'site') ?>">
                Instagram
            </a>
        </li>
        <li>
            <a href="<?= catalogue('linkedin_url', '', 'site') ?>">
                LinkedIn
            </a>
        </li>
    </ul>
</div>