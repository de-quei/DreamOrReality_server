<?php
    // 모든 에러의 표시 설정
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('db_conn.php');

    // 사용자 에이전트에서 안드로이드를 찾는다.
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

    // POST 요청이거나 안드로이드에서 요청일 경우
    if (($_SERVER['REQUEST_METHOD'] == 'POST') || $android) {

        // POST 요청으로 전달된 JSON 데이터 수집
        $data = json_decode(file_get_contents("php://input"), true);

        // 입력값이 비어있는지 확인하고 오류 메시지 설정
        if (empty($data['userid']) || empty($data['userpw'])) {
            $errMSG = "아이디와 비밀번호를 모두 입력하세요.";
            echo json_encode(array('error' => true, 'message' => $errMSG));
            exit;
        }

        // 에러가 없으면 데이터베이스에서 사용자 정보 확인
        if (!isset($errMSG)) {
            $userId = $data['userid'];
            $userPw = $data['userpw'];

            try {
                $stmt = $conn->prepare("SELECT * FROM user WHERE userid = ? AND userpw = ?");
                $stmt->bind_param('ss', $userId, $userPw);

                if ($stmt->execute()) {
                    $stmt->store_result();
                    $rowCount = $stmt->num_rows;

                    if ($rowCount > 0) {
                        $successMSG = "로그인 성공";

                        $stmt->bind_result($dbid, $dbUsername, $dbUserId, $dbUserpw, $dbUsergender, $dbUserclass, $dbUseremail, $dbUsergraduation, $dbUserdream); 
                        
                        $stmt->fetch();

                        session_start(); //세션 시작

                        $_SESSION['username'] = $dbUsername;

                        $userData = array(
                            'id' => $dbid,
                            'username' => $dbUsername,
                            'userid' => $dbUserId,
                            'userpw' => $dbUserpw,
                            'usergender' => $dbUsergender,
                            'userclass' => $dbUserclass,
                            'useremail' => $dbUseremail,
                            'usergraduation' => $dbUsergraduation,
                            'userdream' => $dbUserdream,
                        );

                        echo json_encode(array('error' => false, 'message' => $successMSG, 'userData' => $userData));
                    } else {
                        $errMSG = "일치하는 사용자 정보가 없습니다.";
                        echo json_encode(array('error' => true, 'message' => $errMSG));
                    }
                } else {
                    $errMSG = "로그인 실패";
                    echo json_encode(array('error' => true, 'message' => $errMSG));
                }
            } catch (Exception $e) {
                die("데이터베이스 오류: " . $e->getMessage());
            }
        } else {
            echo json_encode(array('error' => true, 'message' => $errMSG));
        }
    }
?>
