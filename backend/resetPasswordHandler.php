<?php 
    if(isset($_POST['reset-pw-btn'])){
        // connecting to db
        require __DIR__ . "/../db/conn.php";

        // grabbing user credentials
        $selector = $_POST['selector'] ?? ''; // using for running a select stmt in the db
        $validator = $_POST['validator'] ?? ''; // using for validating the user
        $password = $_POST['pw'] ?? '';
        $passwordRepeat = $_POST['pw-repeat'] ?? '';

        // Both tokens must be hexadecimal before they are used anywhere
        if(!ctype_xdigit($selector) || !ctype_xdigit($validator)){
            echo "Could not validate your request!";
            exit();
        }

        if(empty($password) || empty($passwordRepeat)){
            // sending back to create new password page with tokens
            header("Location: ../forgotpwd/create-new-password.php?newpwd=empty&selector=" . $selector . "&validator=" . $validator);
            exit();
        }else if($password != $passwordRepeat){
            header("Location: ../forgotpwd/create-new-password.php?newpwd=pwdnotsame&selector=" . $selector . "&validator=" . $validator);
            exit();
        }

        // checking the token date if it has expired or not
        $currentDate = date("U");

        $sql= $conn->prepare("SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >=?");
          if(!$sql){
            echo "There was an error";
            exit();
        }else{
            $sql->bind_param("ss", $selector, $currentDate);
            $sql->execute();
            $result= $sql->get_result();
            // checking if there are no rows
            if(!$row= $result->fetch_assoc()){
                echo "You need to resubmit your reset request.";
                exit();
            }
            // matching token from db and form in matching format
            else{
                // The token is now stored hashed as a hex string so it is verified as-is
                $tokenCheck = password_verify($validator, $row['pwdResetToken']);

                // checking if token is valid
                if($tokenCheck === false){
                    echo "You need to re-submit your reset request.";
                    exit();
                }
                // password will be updated to db if valid
                else if($tokenCheck === true){
                    $tokenEmail = $row['pwdResetEmail'];

                    $sql= $conn->prepare("SELECT * FROM users WHERE email_address=?");
                    if(!$sql){
                        echo "There was an error";
                        exit();
                    }
                    else{
                        // binding tokenEmail to email_address placeholder
                        $sql->bind_param("s", $tokenEmail);
                        $sql->execute();

                        $result= $sql->get_result();
                        // checking if there are no rows
                        if(!$row= $result->fetch_assoc()){
                            echo "There was an error!";
                            exit();
                        }
                        else{
                            // updating the user's password inside the user table
                            $sql= $conn->prepare("UPDATE users SET password_hash=? WHERE email_address=?");
                            if(!$sql){
                                echo "There was an error.";
                                exit();
                            }
                            else{
                                // hashing password before inserting into db
                                $newPwdHash = password_hash($password, PASSWORD_DEFAULT);

                                $sql->bind_param("ss", $newPwdHash, $tokenEmail);
                                $sql->execute(); // update password

                                // deleting the existing token after new password has been set
                                $sql = $conn->prepare("DELETE FROM pwdReset WHERE pwdResetEmail=?");
                                if(!$sql){
                                    echo "There was an error.";
                                    exit();
                                }
                                else{
                                    $sql->bind_param('s', $tokenEmail);
                                    $sql->execute();
                                    header("Location: ../pages/login.php?newpwd=passwordupdated");
                                    exit();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else{
        header("Location: ../pages/index.php");
        exit();
    }
?>