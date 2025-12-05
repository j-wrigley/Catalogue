# Template Examples

Complete template examples for common use cases.

## Blog Post Template

```php
<?= snippet('header') ?>

<article class="post">
    <?php if (catalogue('featured_image')): ?>
        <div class="post-image">
            <?= catalogue('featured_image') ?>
        </div>
    <?php endif; ?>
    
    <header class="post-header">
        <h1><?= catalogue('title') ?></h1>
        <?php if (catalogue('description')): ?>
            <p class="post-description"><?= catalogue('description') ?></p>
        <?php endif; ?>
    </header>
    
    <div class="post-content">
        <?= catalogue('content') ?>
    </div>
    
    <?php if (catalogue('tags')): ?>
        <div class="post-tags">
            <?= catalogue('tags') ?>
        </div>
    <?php endif; ?>
    
    <?php if (catalogueFiles('files')): ?>
        <div class="post-files">
            <?php foreach (catalogueFiles('files') as $file): ?>
                <figure class="post-file">
                    <?= catalogue('image') ?>
                    <?php if (catalogue('caption')): ?>
                        <figcaption><?= catalogue('caption') ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>

<?= snippet('footer') ?>
```

## Archive/List Template

```php
<?= snippet('header') ?>

<main class="archive">
    <h1>Blog Posts</h1>
    
    <ul class="post-list">
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li class="post-item">
                <a href="<?= catalogue('url') ?>" class="post-link">
                    <h2><?= catalogue('title') ?></h2>
                    <?php if (catalogue('description')): ?>
                        <p><?= catalogue('description') ?></p>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?= snippet('footer') ?>
```

## Page Template

```php
<?= snippet('header') ?>

<main>
    <h1><?= catalogue('title') ?></h1>
    
    <?php if (catalogue('description')): ?>
        <p class="lead"><?= catalogue('description') ?></p>
    <?php endif; ?>
    
    <div class="content">
        <?= catalogue('content') ?>
    </div>
    
    <?php if (catalogueFiles('files')): ?>
        <div class="page-files">
            <?php foreach (catalogueFiles('files') as $file): ?>
                <figure>
                    <?= catalogue('image') ?>
                    <?php if (catalogue('description')): ?>
                        <p><?= catalogue('description') ?></p>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?= snippet('footer') ?>
```

## Portfolio/Project Template

```php
<?= snippet('header') ?>

<article class="project">
    <header>
        <h1><?= catalogue('title') ?></h1>
        <?php if (catalogue('description')): ?>
            <p><?= catalogue('description') ?></p>
        <?php endif; ?>
    </header>
    
    <?php if (catalogue('featured_image')): ?>
        <div class="project-image">
            <?= catalogue('featured_image') ?>
        </div>
    <?php endif; ?>
    
    <div class="project-content">
        <?= catalogue('content') ?>
    </div>
    
    <?php if (catalogueFiles('gallery')): ?>
        <div class="project-gallery">
            <?php foreach (catalogueFiles('gallery') as $file): ?>
                <figure>
                    <?= catalogue('image') ?>
                    <?php if (catalogue('caption')): ?>
                        <figcaption><?= catalogue('caption') ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (catalogue('tags')): ?>
        <div class="project-tags">
            <?= catalogue('tags') ?>
        </div>
    <?php endif; ?>
</article>

<?= snippet('footer') ?>
```

## Home Page Template

```php
<?= snippet('header') ?>

<main class="home">
    <section class="hero">
        <h1><?= catalogue('title', 'Welcome') ?></h1>
        <?php if (catalogue('description')): ?>
            <p class="lead"><?= catalogue('description') ?></p>
        <?php endif; ?>
    </section>
    
    <?php if (catalogue('content')): ?>
        <section class="intro">
            <?= catalogue('content') ?>
        </section>
    <?php endif; ?>
    
    <?php if (catalogueCollection('posts', ['status' => 'published', 'featured' => true])): ?>
        <section class="featured-posts">
            <h2>Featured Posts</h2>
            <div class="posts-grid">
                <?php foreach (catalogueCollection('posts', ['status' => 'published', 'featured' => true]) as $post): ?>
                    <article class="post-card">
                        <?php if (catalogue('featured_image')): ?>
                            <?= catalogue('featured_image') ?>
                        <?php endif; ?>
                        <h3><a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a></h3>
                        <?php if (catalogue('description')): ?>
                            <p><?= catalogue('description') ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?= snippet('footer') ?>
```

## Collection Archive Template

```php
<?= snippet('header') ?>

<main class="archive">
    <h1>Projects</h1>
    
    <div class="projects-grid">
        <?php foreach (catalogueCollection('projects', ['status' => 'published']) as $project): ?>
            <article class="project-card">
                <a href="<?= catalogue('url') ?>">
                    <?php if (catalogue('featured_image')): ?>
                        <div class="project-image">
                            <?= catalogue('featured_image') ?>
                        </div>
                    <?php endif; ?>
                    <div class="project-info">
                        <h2><?= catalogue('title') ?></h2>
                        <?php if (catalogue('description')): ?>
                            <p><?= catalogue('description') ?></p>
                        <?php endif; ?>
                        <?php if (catalogue('tags')): ?>
                            <div class="project-tags"><?= catalogue('tags') ?></div>
                        <?php endif; ?>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?= snippet('footer') ?>
```

## Meta Tags Example

**`snippets/meta.php`:**
```php
<meta name="description" content="<?= catalogue('meta_description') ?>">
<meta name="keywords" content="<?= catalogue('meta_keywords', '', 'content', ', ') ?>">
<meta name="author" content="<?= catalogue('site_name', 'Site', 'site') ?>">
<meta property="og:title" content="<?= catalogue('meta_title') ?>">
<meta property="og:description" content="<?= catalogue('meta_description') ?>">
<meta property="og:image" content="<?= catalogue('og_image') ?>">
```

**Usage in template:**
```php
<?= snippet('header') ?>
<!-- header.php includes: <?= snippet('meta') ?> -->
```

**Output:**
```html
<meta name="keywords" content="Design, Development, Marketing">
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Files & Images](./FILES.md)
- [Collections](./COLLECTIONS.md)
- [Conditionals](./CONDITIONALS.md)
- [Tags](./TAGS.md)

