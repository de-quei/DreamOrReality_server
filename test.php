<?php
    // 모든 에러의 표시 설정
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('db_conn.php');

    // 사용자 정보와 프로젝트 정보를 조인하여 추출
    $sql = "SELECT project.id, project.title, project.tag, project.content, project.period, project.people, project.limit, user.username as writer
            FROM project
            INNER JOIN user ON project.writer = user.username";

    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Data</title>
</head>
<body>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Tag</th>
                <th>Content</th>
                <th>Period</th>
                <th>People</th>
                <th>Limit</th>
                <th>Writer</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['tag'] ?></td>
                    <td><?= $row['content'] ?></td>
                    <td><?= $row['period'] ?></td>
                    <td><?= $row['people'] ?></td>
                    <td><?= $row['limit'] ?></td>
                    <td><?= $row['writer'] ?></td>
                </tr>
            <?php endwhile; ?>

        </table>
    <?php else: ?>
        <p>No data found</p>
    <?php endif; ?>

</body>
</html>

<?php
    // 데이터베이스 연결 종료
    $conn->close();
?>
