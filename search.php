<?php

	include("connect.php");

    $currentDate = date("Y-m-d H:i:s");
	$userFeedback = "";
	$ile_photos = 0;
	$last_post = 0;
	$comment = "";

    if (empty($_POST)) {
    }
	elseif (isset($_POST['logout'])){
		$userFeedback = "Wylogowałeś się!";
		unset($_SESSION['id']);
		unset($_SESSION['email']);
	}
	else{
		if(isset($_POST['login']) && isset($_POST['password'])) {

			$login = $_POST['login'];
			$password = $_POST['password'];

			$stmt = $dbh->prepare("SELECT * FROM Users WHERE login = :login");
			$stmt->execute([':login' => $login]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user) {
				if(password_verify($password, $user['password'])) {
					$_SESSION['id'] = $user['idUser'];
					$_SESSION['login'] = $user['login'];
					$_SESSION['email'] = $user['email'];
					$userFeedback = "";
				}
				else {
					$userFeedback = "Nie poprawne hasło!";
				}
			}
			else {
				$userFeedback = "Nie ma takiego użytkownika!";
			}
		}
	}

	if (isset($_SESSION['id'])) {

		$stmt = $dbh->prepare("SELECT COUNT(*) FROM Posts WHERE idUser = :userID");
		$stmt->execute([':userID' => $_SESSION['id']]);
		$ile_photos = intval($stmt->fetchColumn());

		$stmt = $dbh->prepare("SELECT * FROM Posts WHERE idUser = :userid ORDER BY createdTime DESC LIMIT 1");
    	$stmt->execute([':userid' => $_SESSION['id']]);
    	$last_post = $stmt->fetch(PDO::FETCH_ASSOC);
	}
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    }

		$stmt_howmuch = $dbh->prepare("SELECT COUNT(*) FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE idCategory = 
			(SELECT idCategory FROM Categories WHERE categoryName = '$category')");
		$stmt_howmuch->execute();
		$how_many_post = intval($stmt_howmuch->fetchColumn());	

		if ($how_many_post == 0) {
			$posts = [];
		}
		else {
				$posts = [];
				$stmt = $dbh->prepare("SELECT p.*, u.login FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE idCategory = 
					(SELECT idCategory FROM Categories WHERE categoryName = '$category') ORDER BY createdTime DESC");
				$stmt->execute();

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

					$stmt_1 = $dbh->prepare("SELECT categoryName FROM Categories WHERE idCategory = :idCategory");
					$stmt_1->execute([':idCategory' => $row['idCategory']]);
					$row['name'] = $stmt_1->fetch(PDO::FETCH_ASSOC);
					$posts[] = $row;
					}
		}

    echo $twig->render('search.html.twig', ['data' => $date, 'session' => $_SESSION, 'test' => $userFeedback, 'posts' => $posts, 'ile_photos' => $ile_photos, 'last_post' => $last_post, 'comment'=> $comment, 'category' => $category, 'how_many_post' => $how_many_post, 'categoriesNames' => $categoriesNames]);
