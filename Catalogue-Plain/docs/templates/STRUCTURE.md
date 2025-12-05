# Structure Fields

Displaying structure field values in templates.

## Field Type

- `structure` - Repeatable table items (array of objects)

## Basic Usage

Use `catalogueStructure()` to iterate through structure items:

```php
<?php foreach (catalogueStructure('settings') as $item): ?>
    <div>
        <?= catalogue('title') ?>
        <?= catalogue('value') ?>
    </div>
<?php endforeach; ?>
```

## How It Works

Inside a `catalogueStructure()` loop, `catalogue()` automatically uses the current item:

```php
<?php foreach (catalogueStructure('settings') as $item): ?>
    <?= catalogue('title') ?>  <!-- Gets title from current $item -->
    <?= catalogue('value') ?>  <!-- Gets value from current $item -->
<?php endforeach; ?>
```

## Examples

### Simple List

```php
<?php foreach (catalogueStructure('list_items') as $item): ?>
    <div class="list-item">
        <?= catalogue('text') ?>
    </div>
<?php endforeach; ?>
```

### Table Display

```php
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (catalogueStructure('settings') as $item): ?>
            <tr>
                <td><?= catalogue('title') ?></td>
                <td><?= catalogue('value') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

### Feature Cards

```php
<div class="features-grid">
    <?php foreach (catalogueStructure('features') as $feature): ?>
        <div class="feature-card">
            <?php if (catalogue('icon')): ?>
                <img src="<?= catalogue('icon') ?>" alt="">
            <?php endif; ?>
            <h3><?= catalogue('title') ?></h3>
            <p><?= catalogue('description') ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

### Pricing Table

```php
<table class="pricing-table">
    <thead>
        <tr>
            <th>Plan</th>
            <th>Price</th>
            <th>Features</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (catalogueStructure('pricing_tiers') as $tier): ?>
            <tr>
                <td><?= catalogue('plan') ?></td>
                <td><?= catalogue('price') ?></td>
                <td><?= catalogue('features') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

### Timeline

```php
<div class="timeline">
    <?php foreach (catalogueStructure('timeline') as $event): ?>
        <div class="timeline-item">
            <div class="timeline-date">
                <?= catalogue('date') ?>
            </div>
            <div class="timeline-content">
                <h3><?= catalogue('title') ?></h3>
                <p><?= catalogue('description') ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

### Team Members

```php
<div class="team">
    <?php foreach (catalogueStructure('team_members') as $member): ?>
        <div class="team-member">
            <?php if (catalogue('image')): ?>
                <img src="<?= catalogue('image') ?>" alt="<?= catalogue('name') ?>">
            <?php endif; ?>
            <h3><?= catalogue('name') ?></h3>
            <p class="role"><?= catalogue('role') ?></p>
            <?php if (catalogue('bio')): ?>
                <p><?= catalogue('bio') ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
```

### Empty State

```php
<?php if (catalogueStructure('items')): ?>
    <?php foreach (catalogueStructure('items') as $item): ?>
        <!-- Display items -->
    <?php endforeach; ?>
<?php else: ?>
    <p>No items available.</p>
<?php endif; ?>
```

## Available Fields

Inside the loop, access any field from the structure item:

```php
<?php foreach (catalogueStructure('settings') as $item): ?>
    <div>
        <?= catalogue('title') ?>
        <?= catalogue('value') ?>
        <?= catalogue('description') ?>
        <?php if (catalogue('icon')): ?>
            <img src="<?= catalogue('icon') ?>" alt="">
        <?php endif; ?>
    </div>
<?php endforeach; ?>
```

## Context

Inside a `catalogueStructure()` loop, `catalogue()` automatically uses the current item:

```php
<?php foreach (catalogueStructure('settings') as $item): ?>
    <?= catalogue('title') ?>  <!-- Gets title from current $item -->
<?php endforeach; ?>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Conditionals](./CONDITIONALS.md)
