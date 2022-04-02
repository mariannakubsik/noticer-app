<?php

    include("connect.php");

    $currentDate = date("Y-m-d H:i:s");
    $postid = explode("/", $_SERVER['REQUEST_URI']);


    if(isset($_SESSION['id'])){
        if (isset($_GET['show']) && intval($_GET['show']) > 0) {
    
            $id = intval($_GET['show']);
            $owner = false;

            $stmt = $dbh->prepare("SELECT p.*, categoryName FROM Posts p INNER JOIN Categories c ON c.idCategory = p.idCategory WHERE idPost= :id");
            $stmt->execute([':id' => $id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $stmt = $dbh->prepare("SELECT u.* FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE p.idPost = :postid");
            $stmt->execute([':postid' => $postid[3]]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $dbh->prepare("SELECT COUNT(*) FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE u.idUser = :userid");
            $stmt->execute([':userid' => $user['idUser']]);
            $how_many_photos = intval($stmt->fetchColumn());
    
            $stmt = $dbh->prepare("SELECT * FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE u.idUser = :userid ORDER BY createdTime DESC LIMIT 1");
            $stmt->execute([':userid' => $user['idUser']]);
            $last_post = $stmt->fetch(PDO::FETCH_ASSOC);

            if (($_SESSION['id']) == $user['idUser']) {
                $owner = 'true';
            }           

        }
        else {       
    
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
                    header('Location: post/show/'.$id.'');
                    print_r("powrót");
                    exit();
                }	
            }

            if(isset($_GET['delete']) && isset($_POST['delete']))
            {
                $id = intval($_GET['delete']);
                print_r($id);
                try {
                    $stmt = $dbh->prepare ("DELETE FROM Posts WHERE idPost = :idPost");
    
                    $stmt->execute([':idPost' => $id]);

                    print_r("Ad was deleted");
                } 
                catch (PDOException $e) {
                    print_r("Ad wasn't deleted");
                }
                
                $idUser = $_SESSION['id'];

                $stmt = $dbh->prepare("SELECT login FROM  Users u WHERE idUser = :userid");
                $stmt->execute([':userid' => $idUser]);
                $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

                $login = $currentUser['login'];
                header('Location: profile/'.$login.'');
                exit();
            }

        }
            echo $twig->render('post.html.twig', ['data' => $date, 'post' => $_POST, 'get' => $_GET, 'session' => $_SESSION, 'post' => $post, 'user' => $user, 'how_many_photos' => $how_many_photos, 'last_post' => $last_post, 'owner' => $owner, 'categoriesNames' => $categoriesNames]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }