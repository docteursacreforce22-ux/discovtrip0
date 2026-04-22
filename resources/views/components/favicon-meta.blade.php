<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * COMPOSANT FAVICON - META TAGS
 * ═══════════════════════════════════════════════════════════════════════════════
 * À inclure dans la section <head> de tous les layouts
 * Usage : <?php include resources_path('views/components/favicon-meta.php'); ?>
 * ═══════════════════════════════════════════════════════════════════════════════
 */
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     FAVICON - DISCOVTRIP
     ═══════════════════════════════════════════════════════════════════════════ -->

<!-- Favicon principal (multi-résolution) -->
<link rel="icon" type="image/x-icon" href="<?= url('favicon.ico') ?>">

<!-- Favicons PNG (différentes tailles) -->
<link rel="icon" type="image/png" sizes="16x16" href="<?= url('favicon-16x16.png') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= url('favicon-32x32.png') ?>">
<link rel="icon" type="image/png" sizes="48x48" href="<?= url('favicon-48x48.png') ?>">
<link rel="icon" type="image/png" sizes="64x64" href="<?= url('favicon-64x64.png') ?>">
<link rel="icon" type="image/png" sizes="128x128" href="<?= url('favicon-128x128.png') ?>">

<!-- Apple Touch Icon (iOS/Safari) -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= url('apple-touch-icon.png') ?>">

<!-- Android Chrome Icons -->
<link rel="icon" type="image/png" sizes="192x192" href="<?= url('android-chrome-192x192.png') ?>">
<link rel="icon" type="image/png" sizes="512x512" href="<?= url('android-chrome-512x512.png') ?>">

<!-- Web App Manifest (PWA) -->
<link rel="manifest" href="<?= url('manifest.json') ?>">

<!-- Microsoft Tile (Windows) -->
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-config" content="<?= url('browserconfig.xml') ?>">

<!-- Theme Color (Mobile Browsers) -->
<meta name="theme-color" content="#ffffff">

<!-- ═══════════════════════════════════════════════════════════════════════════
     FIN FAVICON META TAGS
     ═══════════════════════════════════════════════════════════════════════════ -->
