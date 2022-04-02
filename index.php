<?php

	include("connect.php");

	$userFeedback = "";
	$ile_photos = 0;
	$last_post = 0;
	$comment = "";
	$currentDate = date("Y-m-d H:i:s");

	$posts = [];
	$stmt = $dbh->prepare("SELECT p.*, u.login FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser ORDER BY createdTime DESC LIMIT 72");
	$stmt->execute();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$stmt_1 = $dbh->prepare("SELECT categoryName FROM Categories WHERE idCategory = :idCategory");
		$stmt_1->execute([':idCategory' => $row['idCategory']]);
		$row['name'] = $stmt_1->fetch(PDO::FETCH_ASSOC);
        $posts[] = $row;
    }

	if (empty($_POST)) {
    }
	elseif (isset($_POST['logout'])) {
		$userFeedback = "Wylogowałeś się!";
		unset($_SESSION['id']);
		unset($_SESSION['email']);
	}
	else {
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

	
	if (isset($_GET['message']) && isset($_SESSION['id']) && isset($_POST['message']) ) {

		$id = intval($_GET['message']);
		$message = $_POST['message'];

		$stmt_1 = $dbh->prepare("SELECT idUser FROM Posts WHERE idPost = $id");
		$stmt_1->execute();
		$idReceiver = intval($stmt_1->fetchColumn());
		
		if (mb_strlen($message) >= 2 && mb_strlen($message) <= 2000) {
			try {
				$stmt = $dbh->prepare ("INSERT INTO UserPostMessage (
				idUserPostMessage, message, idUserSender, idUserReceiver, idPost, createdTimeMessage) 
				VALUES (
					null, :message, :idUserSender, :idUserReceiver, :postID , '$currentDate') 
				");

			$stmt->execute([':message' => $message, ':idUserSender' => $_SESSION['id'], ':idUserReceiver' => $idReceiver, ':postID' => $id]);
			} 
			catch (PDOException $e) {
			}
			header('Location: /main');
			print_r("powrót");
			exit();
		}	
	}

	echo $twig->render('index.html.twig', ['data' => $date, 'session' => $_SESSION, 'test' => $userFeedback, 'posts' => $posts, 'ile_photos' => $ile_photos, 'last_post' => $last_post, 'comment'=> $comment, 'categoriesNames' => $categoriesNames]);