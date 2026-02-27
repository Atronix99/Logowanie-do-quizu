<?php
ini_set('session.cookie_lifetime', 0);
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "loginy";
$conn = mysqli_connect($host, $user, $pass, $db);

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_me']);
    $sql = "SELECT id, login FROM uzytkownicy WHERE token_logowania = '$token'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: quiz.php");
    exit();
}

if (isset($_POST['register_action'])) {
    $login = mysqli_real_escape_string($conn, $_POST['reg_login']);
    $dog_name = mysqli_real_escape_string($conn, $_POST['reg_dogname']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $haslo = $_POST['reg_password'];
    $haslo2 = $_POST['reg_password_confirm'];

    if ($haslo !== $haslo2) {
        $msg = "Hasła nie są identyczne!";
        $msg_type = "error";
    } else {
        $hashed_pass = password_hash($haslo, PASSWORD_DEFAULT);
        $sql = "INSERT INTO uzytkownicy (login, dog_name, email, haslo) VALUES ('$login', '$dog_name', '$email', '$hashed_pass')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Hau! Konto utworzone!";
            $msg_type = "success";
        } else {
            $msg = "Błąd rejestracji.";
            $msg_type = "error";
        }
    }
}

if (isset($_POST['login_action'])) {
    $login = mysqli_real_escape_string($conn, $_POST['log_login']);
    $haslo = $_POST['log_password'];
    $remember = isset($_POST['remember_me']);

    $sql = "SELECT id, login, haslo FROM uzytkownicy WHERE login = '$login'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($haslo, $user['haslo'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_me', $token, time() + (86400 * 30), "/");
                mysqli_query($conn, "UPDATE uzytkownicy SET token_logowania = '$token' WHERE id = " . $user['id']);
            }
            header("Location: quiz.php");
            exit();
        } else {
            $msg = "Błędne hasło!";
            $msg_type = "error";
        }
    } else {
        $msg = "Właściciel nieznany!";
        $msg_type = "error";
    }
}

$dog_data_login = json_decode(file_get_contents("https://dog.ceo/api/breeds/image/random"), true);
$dog_url_login = $dog_data_login['message'];

$dog_data_signup = json_decode(file_get_contents("https://dog.ceo/api/breeds/image/random"), true);
$dog_url_signup = $dog_data_signup['message'];
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz z pieskami - Logowanie</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>

<body>
    <?php if (isset($msg)): ?>
        <div id="popup-msg" class="system-msg <?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="container">
        <div class="side-panel"></div>

        <div class="form-box login">
            <h2 class="animation">Zaloguj się <i class="fa-solid fa-bone"></i></h2>
            <form action="index.php" method="POST">
                <div class="input-box animation">
                    <input type="text" name="log_login" required>
                    <label>Login</label>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box animation">
                    <input type="password" name="log_password" id="log_pass" required>
                    <label>Hasło</label>
                    <i class="fa-solid fa-eye toggle-password" data-target="log_pass"></i>
                </div>
                <div class="remember-forgot animation">
                    <label><input type="checkbox" name="remember_me"> Zapamiętaj mnie</label>
                </div>
                <button class="btn animation" type="submit" name="login_action">Zaloguj się <i
                        class="fa-solid fa-paw"></i></button>
                <div class="regi-link animation">
                    <p>Pierwszy raz? <a href="#" class="SignUpLink">Zarejestruj się</a></p>
                </div>
            </form>
        </div>

        <div class="info-content login">
            <h2 class="animation">Witaj użytkowniku! <i class="fa-solid fa-dog"></i></h2>
            <p class="animation">Cieszymy się, że jesteś z nami!</p>
            <img src="<?php echo $dog_url_login; ?>" alt="Pies do logowania" class="info-dog-img animation">
        </div>

        <div class="form-box Signup">
            <h2 class="animation">Dołącz do nas!</h2>
            <form action="index.php" method="POST">
                <div class="input-box animation">
                    <input type="text" name="reg_login" required>
                    <label>Login</label>
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div class="input-box animation">
                    <input type="text" name="reg_dogname" required>
                    <label>Imię Twojego psa</label>
                    <i class="fa-solid fa-dog"></i>
                </div>
                <div class="input-box animation">
                    <input type="email" name="reg_email" required>
                    <label>E-mail właściciela</label>
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box animation">
                    <input type="password" name="reg_password" id="reg_pass" required>
                    <label>Hasło</label>
                    <i class="fa-solid fa-eye toggle-password" data-target="reg_pass"></i>
                </div>
                <div class="input-box animation">
                    <input type="password" name="reg_password_confirm" id="reg_pass_conf" required>
                    <label>Potwierdź hasło</label>
                    <i class="fa-solid fa-eye toggle-password" data-target="reg_pass_conf"></i>
                </div>
                <button class="btn animation" type="submit" name="register_action">Załóż konto <i
                        class="fa-solid fa-check"></i></button>
                <div class="regi-link animation">
                    <p>Masz już konto? <a href="#" class="SignInLink">Zaloguj się</a></p>
                </div>
            </form>
        </div>

        <div class="info-content Signup">
            <h2 class="animation">Nie masz konta?<i class="fa-solid fa-dog"></i></h2>
            <p class="animation">Dołącz do społeczności kochającej psy!</p>
            <img src="<?php echo $dog_url_signup; ?>" alt="Pies do rejestracji" class="info-dog-img animation">
        </div>
    </div>
</body>

</html>