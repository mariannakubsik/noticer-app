<?php

    include("connect.php");
    $currentDate = date("Y-m-d H:i:s");

    if (isset($_SESSION['id'])) {

        if (isset($_GET['post']) && isset($_GET['person'])) {

            $idPost = $_GET['post'];
            $idOtherUser = $_GET['person'];
    
            $stmt_1 = $dbh->prepare("SELECT login FROM Users WHERE idUser = $idOtherUser");
            $stmt_1->execute();
            $otherUserLogin = $stmt_1->fetchColumn();
    
            $messages = [];
            $stmt = $dbh->prepare("SELECT um.message, u.login, um.createdTimeMessage, um.idUserSender
                FROM UserPostMessage um INNER JOIN Users u ON um.idUserSender = u.idUser 
                    WHERE um.idPost = :idPost AND ((um.idUserSender = :idOtherUser AND um.idUserReceiver = :idMeUser) 
                        OR (um.idUserSender = :idMeUser AND um.idUserReceiver = :idOtherUser)) ORDER BY createdTimeMessage");
            $stmt->execute([':idPost' => $idPost, ':idOtherUser' => $idOtherUser, ':idMeUser' => $_SESSION['id']]);
        
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messages[] = $row;
            }
        } else {

            if (isset($_GET['message']) && isset($_GET['post']) && isset($_POST['message'])) {

                $idPost =intval($_GET['post']);
                $idReceiver = intval($_GET['message']);
                $message = $_POST['message'];
                
                if (mb_strlen($message) >= 2 && mb_strlen($message) <= 200) {
                    
                    try {
                        $stmt = $dbh->prepare ("INSERT INTO UserPostMessage (
                        idUserPostMessage, message, idUserSender, idUserReceiver, idPost, createdTimeMessage) 
                        VALUES (
                            null, :message, :idUserSender, :idUserReceiver, :postID , '$currentDate') 
                        ");
        
                    $stmt->execute([':message' => $message, ':idUserSender' => $_SESSION['id'], ':idUserReceiver' => $idReceiver, ':postID' => $idPost]);
                    } 
                    catch (PDOException $e) {
                    }

                }
        
                $stmt_1 = $dbh->prepare("SELECT login FROM Users WHERE idUser = $idReceiver");
                $stmt_1->execute();
                $otherUserLogin = $stmt_1->fetchColumn();
        
                $messages = [];
                $stmt_2 = $dbh->prepare("SELECT um.message, u.login, um.createdTimeMessage, um.idUserSender
                    FROM UserPostMessage um INNER JOIN Users u ON um.idUserSender = u.idUser 
                        WHERE um.idPost = :idPost AND ((um.idUserSender = :idOtherUser AND um.idUserReceiver = :idMeUser) 
                            OR (um.idUserSender = :idMeUser AND um.idUserReceiver = :idOtherUser)) ORDER BY createdTimeMessage");
                $stmt_2->execute([':idPost' => $idPost, ':idOtherUser' => $idReceiver, ':idMeUser' => $_SESSION['id']]);
            
                while ($row = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
                    $messages[] = $row;
                }

                $idOtherUser = $idReceiver;

            }
        }
        echo $twig->render('conversation.html.twig', ['data' => $date, 'session' => $_SESSION, 'messages' => $messages, 'otherUserLogin' => $otherUserLogin, 'idPost' => $idPost, 'idOtherUser' => $idOtherUser, 'categoriesNames' => $categoriesNames]);

    } else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }