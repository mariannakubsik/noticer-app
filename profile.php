<?php

    include("connect.php");

    $url_name_profile = explode("/", $_SERVER['REQUEST_URI']);

    if(isset($_SESSION['id'])) {   


        $stmt = $dbh->prepare("SELECT * FROM Users WHERE login = :login");
        $stmt->execute([':login' =>$url_name_profile[2]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($user)) {
            header("Location:https://s401354.labagh.pl/main");
            exit();
        }
        else {
            $user_id = $user['idUser'];

            $stmt = $dbh->prepare("SELECT * FROM Users WHERE idUser = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $dbh->prepare("SELECT COUNT(*) FROM Posts WHERE idUser = :userID");
            $stmt->execute([':userID' => $user_id]);
            $how_many_photos = intval($stmt->fetchColumn());
            
            $my_posts = [];
            $stmt = $dbh->prepare("SELECT * FROM Posts WHERE idUser = :userID ORDER BY createdTime DESC");
            $stmt->execute([':userID' => $user_id]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $my_posts[] = $row;
            }

            $stmt = $dbh->prepare("SELECT * FROM Posts WHERE idUser = :idUser ORDER BY createdTime DESC LIMIT 1");
            $stmt->execute([':idUser' => $user_id]);
            $last_post = $stmt->fetch(PDO::FETCH_ASSOC);

            if(isset($_POST['delete']))
            {
                print_r('You clicked');
                echo $twig->render('profile.html.twig', ['post' => $_POST, 'user' =>$user, 'get' => $_GET, 'session' => $_SESSION, 'my_posts' => $my_posts, 'how_many_photos' => $how_many_photos, 'last_post' => $last_post, 'data' => $date, 'categoriesNames' => $categoriesNames]);
            }

            echo $twig->render('profile.html.twig', ['post' => $_POST, 'user' =>$user, 'get' => $_GET, 'session' => $_SESSION, 'my_posts' => $my_posts, 'how_many_photos' => $how_many_photos, 'last_post' => $last_post, 'data' => $date, 'categoriesNames' => $categoriesNames]);
        }
        
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }