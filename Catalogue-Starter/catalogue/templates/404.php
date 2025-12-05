<?= snippet('header') ?>

<div style="text-align: center; padding: 4rem 2rem; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <h1 style="font-size: 4rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-text, #1a1a1a);">
        <?= catalogue('title', '404 - Page Not Found') ?>
    </h1>
    
    <?php if (catalogue('message')): ?>
        <p style="font-size: 1.25rem; color: var(--color-text-muted, #666); margin-bottom: 2rem; max-width: 600px;">
            <?= catalogue('message') ?>
        </p>
    <?php endif; ?>
    
    <?php if (catalogue('show_navigation', false)): ?>
        <nav style="margin-top: 2rem; margin-bottom: 2rem;">
            <p style="margin-bottom: 1rem; color: var(--color-text-muted, #666);">
                <?= catalogue('navigation_text', 'You might want to visit:') ?>
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
                <?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
                    <?= navLink($page, null, ['style' => 'display: inline-block; padding: 0.5rem 1rem; margin: 0.25rem;']) ?>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>
    
    <div style="margin-top: 2rem;">
        <a href="<?= catalogueNav('home', 'url') ?: '/' ?>" style="display: inline-block; padding: 0.75rem 1.5rem; background: var(--color-accent, #e11d48); color: var(--color-accent-text, #ffffff); text-decoration: none; border-radius: 0.375rem; font-weight: 500; transition: opacity 0.2s;">
            Go to Homepage
        </a>
    </div>
</div>

<?= snippet('footer') ?>

