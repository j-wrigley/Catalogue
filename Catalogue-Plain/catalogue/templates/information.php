<?= snippet('header') ?>
<!-- Information Template -->
<div class="home-container">
    <section class="navbar">
        <?= snippet('navbar') ?>
    </section>

    <section class="information-content">
        <p class="information-description"><?= catalogue('description') ?></p>
        <div class="information-content-inner">
            <?= catalogue('content') ?>
        </div>
    </section>

</div>

<?= snippet('footer') ?>

