<?php

	require __DIR__ . '/vendor/autoload.php';

	use Twig\Environment;
	use Twig\Loader\FilesystemLoader;
	
	$loader = new FilesystemLoader(__DIR__ . '/templates');
	$twig = new Environment($loader);
	
	session_start();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
	require __DIR__ . '/vendor/autoload.php';
	define("IN_INDEX", 1);

    include("config.inc.php");
    include("functions.inc.php");

	$date = date('Y-m-d');
    date_default_timezone_set('Europe/Warsaw');

    if (isset($config) && is_array($config)) {
        try {
            $dbh = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4', $config['db_user'], $config['db_password']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Nie mozna polaczyc sie z baza danych: " . $e->getMessage();
            exit();
        }
    } else {
        exit("Nie znaleziono konfiguracji bazy danych.");
    }

    $categoriesNames = [];
    $stmt = $dbh->prepare("SELECT DISTINCT categoryName FROM Categories");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoriesNames[] = $row;
    }