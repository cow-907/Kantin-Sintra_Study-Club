<?php
/**
 * Bootstrap tunggal: session, koneksi database, dan fungsi bantu.
 * Cukup include file ini di baris paling atas setiap halaman:
 *   require 'includes/init.php';   (untuk halaman di folder root)
 *   require '../includes/init.php'; (untuk halaman di folder admin/)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/fungsi.php';
