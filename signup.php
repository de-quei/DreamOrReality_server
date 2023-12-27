<?php
    //모든 에러의 표시 설정
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
        if (empty($data['username'])) $errMSG = "이름";
        else if (empty($data['userid'])) $errMSG = "아이디";
        else if (empty($data['userpw'])) $errMSG = "비밀번호";
        else if (empty($data['usergender'])) $errMSG = "성별";
        else if (empty($data['userclass'])) $errMSG = "반";
        else if (empty($data['useremail'])) $errMSG = "이메일";
        else if (empty($data['usergraduation'])) $errMSG = "졸업일";
        else if (empty($data['userdream'])) $errMSG = "꿈";

        // 에러가 없으면 데이터베이스에 삽입
        if (!isset($errMSG)) {
            //json 에러로그
            error_log(file_get_contents("php://input"));

            // 입력값이 비어있는지 확인하고 오류 메시지 설정
            $userName = $data['username'];
            $userId = $data['userid'];
            $userPw = $data['userpw'];
            $userGender = $data['usergender'];
            $userClass = $data['userclass'];
            $userEmail = $data['useremail'];
            $userGraduation = $data['usergraduation'];
            $userDream = $data['userdream'];
            
            try {
                $stmt = $conn->prepare("INSERT INTO user(username, userid, userpw, usergender, userclass, useremail, usergraduation, userdream) 
                                    VALUES('$userName', '$userId', '$userPw', '$userGender', '$userClass', '$userEmail', '$userGraduation', '$userDream')");

                // 쿼리 실행에 실패하면 예외 발생
                if (!$stmt) {
                    throw new Exception('Prepare statement failed: ' . $conn->error);
                }

                // 쿼리 실행
                if ($stmt->execute()) {
                    $successMSG = "가입 성공";
                } else {
                    $errMSG = "가입 실패";
                }
            } catch (PDOException $e) {
                // 데이터베이스 오류 시 예외 처리
                die("데이터베이스 오류: " . $e->getMessage());
            } catch (Exception $e) {
                // 일반적인 오류 예외 처리
                die("오류: " . $e->getMessage());
            }
        }
    }
?>

<?php
    // 에러 메시지 및 성공 메시지 출력
    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;
 
    // 안드로이드에서 요청이 아니면 HTML 폼 출력
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
 
    if (!$android) {
?>
    <html>
        <body>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"> <br>
                이름: <input type="text" name="username" /> <br>
                아이디: <input type="text" name="userid" /> <br>
                비밀번호: <input type="text" name="userpw" /> <br>
                성별: <input type="text" name="usergender" /> <br>
                반: <input type="text" name="userclass" /> <br>
                이메일: <input type="text" name="useremail" /> <br>
                졸업일: <input type="text" name="usergraduation" /> <br>
                꿈: <input type="text" name="userdream" /> <br>
                <input type="submit" name="submit" />
            </form>
        </body>
    </html>
<?php
    }
?>
