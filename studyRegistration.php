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
    if (empty($data['title'])) $errMSG = "제목";
    else if (empty($data['tag'])) $errMSG = "태그";
    else if (empty($data['content'])) $errMSG = "내용";
    else if (empty($data['period'])) $errMSG = "기간";
    else if (empty($data['people'])) $errMSG = "인원";
    else if (empty($data['limit'])) $errMSG = "제한사항";

    // 에러가 없으면 데이터베이스에 삽입
    if (!isset($errMSG)) {

        // 입력값이 비어있는지 확인하고 오류 메시지 설정
        $studyTitle = $data['title'];
        $studyTag = $data['tag'];
        $studyContent = $data['content'];
        $studyPeriod = $data['period'];
        $studyPeople = $data['people'];
        $studyLimit = $data['limit'];

        try {
            $stmt = $conn->prepare("INSERT INTO project(`title`, `tag`, `content`, `period`, `people`, `limit`) 
                        VALUES('$studyTitle', '$studyTag', '$studyContent', '$studyPeriod', '$studyPeople', '$studyLimit')");


            // 쿼리 실행에 실패하면 예외 발생
            if (!$stmt) throw new Exception('Prepare statement failed: ' . $conn->error);


            // 쿼리 실행
            if ($stmt->execute()) $successMSG = "등록 성공";
            else $errMSG = "등록 실패";

            // Statement 닫기
            $stmt->close();
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
        제목: <input type="text" name="title" /> <br>
        태그: <input type="text" name="tag" /> <br>
        내용: <input type="text" name="content" /> <br>
        기간: <input type="date" name="period" /> <br>
        인원: <input type="text" name="people" /> <br>
        제한 : <input type="text" name="limit" /> <br>
        <input type="submit" name="submit" />
    </form>
    </body>
    </html>
    <?php
}
?>