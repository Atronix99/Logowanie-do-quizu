<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "loginy";
$conn = mysqli_connect($host, $user, $pass, $db);

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_me']);
    $res = mysqli_query($conn, "SELECT id, login FROM uzytkownicy WHERE token_logowania = '$token'");
    if ($res && mysqli_num_rows($res) > 0) {
        $u = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_login'] = $u['login'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$feedback = "";

if (isset($_POST['guess'])) {
    if ($_POST['guess'] === $_POST['correct_breed']) {
        mysqli_query($conn, "UPDATE uzytkownicy SET punkty_quizu = punkty_quizu + 1 WHERE id = $user_id");

        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT punkty_quizu, max_punkty FROM uzytkownicy WHERE id = $user_id"));
        if ($check['punkty_quizu'] > $check['max_punkty']) {
            mysqli_query($conn, "UPDATE uzytkownicy SET max_punkty = " . $check['punkty_quizu'] . " WHERE id = $user_id");
        }
        $feedback = "success";
    } else {
        mysqli_query($conn, "UPDATE uzytkownicy SET punkty_quizu = 0 WHERE id = $user_id");
        $feedback = "error";
    }
}
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dog_name, punkty_quizu, max_punkty FROM uzytkownicy WHERE id = $user_id"));

$dog_data = json_decode(file_get_contents("https://dog.ceo/api/breeds/image/random"), true);
$image_url = $dog_data['message'];
$correct_breed = explode('/', $image_url)[4];

$breeds_data = json_decode(file_get_contents("https://dog.ceo/api/breeds/list/all"), true);
$breeds_list = array_keys($breeds_data['message']);

$options = [$correct_breed];
while (count($options) < 4) {
    $rb = $breeds_list[array_rand($breeds_list)];
    if (!in_array($rb, $options))
        $options[] = $rb;
}
shuffle($options);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Quiz z psami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style_quiz.css">
</head>

<body>
    <div id="loader"><i class="fa-solid fa-paw fa-spin loader-icon"></i></div>
    <div class="dashboard-container">
        <div class="stats-bar">
            <div class="badges-wrapper">
                <span class="badge badge-current"><i class="fa-solid fa-star"></i> Punkty:
                    <?php echo $user_data['punkty_quizu']; ?></span>
                <span class="badge badge-best"><i class="fa-solid fa-trophy"></i> Rekord:
                    <?php echo $user_data['max_punkty']; ?></span>
            </div>
            <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>

        <h1>Jaka to rasa?</h1>
        <p>Masz problem ze zgadnięciem rasy psa? Może twój piesek
            <strong><?php echo htmlspecialchars($user_data['dog_name']); ?></strong> ci pomoże.
        </p>

        <img src="<?php echo $image_url; ?>" class="quiz-img">

        <form method="POST" class="options-grid" onsubmit="document.getElementById('loader').style.display='flex'">
            <input type="hidden" name="correct_breed" value="<?php echo $correct_breed; ?>">
            <?php foreach ($options as $o): ?>
                <button type="submit" name="guess" value="<?php echo $o; ?>" class="btn-quiz">
                    <?php echo ucfirst(str_replace('-', ' ', $o)); ?>
                </button>
            <?php endforeach; ?>
        </form>

        <?php if ($feedback === "success"): ?>
            <div class="feedback success">Hau! Świetnie ci idzie!</div>
        <?php elseif ($feedback === "error"): ?>
            <div class="feedback error">Pudło! To był:
                <?php echo ucfirst(str_replace('-', ' ', $_POST['correct_breed'])); ?>.<br>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>