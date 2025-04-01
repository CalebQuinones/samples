<?php
session_start();
include 'config.php';

// Signup
if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];

    // Check if exists
    $select = "SELECT * FROM login WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('User already exists!');</script>";
    } else {
        // Check if passwords match
        if ($password === $rpassword) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $insert = "INSERT INTO login (Fname, Lname, email, password) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($stmt, "ssss", $Fname, $Lname, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Passwords do not match!');</script>";
        }
    }
}

// Signin
if (isset($_POST['signin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $select = "SELECT * FROM login WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_email'] = $email; // Store in session
            echo "<script>alert('Login successful!'); window.location.href='account.php';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            height: 100vh;
            position: relative;
            overflow: hidden;
            background-color: white;
        }

        .form-container {
            position: absolute;
            width: 50%;
            height: 100%;
            top: 0;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
        }

        .form-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 40px;
            background-color: white;
        }

        .image-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: all 0.6s ease-in-out;
            z-index: 100;
            background-color: #d47a87;
        }

        .container.right-panel-active .image-container {
            transform: translateX(-100%);
        }

        .image-inner {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.6s ease-in-out;
            background-size: cover;
            position: relative;
        }

        .image-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.6s ease-in-out;
        }

        .signin-image {
            opacity: 1;
        }

        .signup-image {
            opacity: 0;
        }

        .container.right-panel-active .signin-image {
            opacity: 0;
        }

        .container.right-panel-active .signup-image {
            opacity: 1;
        }

        .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
            text-align: center;
            font-weight: 600;
        }

        p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Common form styles */
        .form {
            width: 100%;
            max-width: 320px;
        }

        .input-field {
            margin-bottom: 10px;
        }

        .input-field label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
            font-weight: 500;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .input-field input::placeholder {
            color: #bbb;
            font-size: 13px;
        }

        .input-field input:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 5px rgba(255,105,180,0.5);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #ffc0cb;
            color: #333;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #ffb6c1;
        }

        .social-text {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }

        .social-text::before,
        .social-text::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: #e0e0e0;
        }

        .social-text::before {
            left: 0;
        }

        .social-text::after {
            right: 0;
        }

        .social-media {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }

        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #e0e0e0;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .social-icon:hover {
            background-color: #f0f0f0;
        }

        .social-icon svg {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }

        .social-icon.google {
            color: #4285F4;
        }

        .social-icon.facebook {
            color: #3F60A0;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 5px;
        }

        .forgot-password a {
            color: #ff69b4;
            font-size: 14px;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .toggle-container {
            margin-top: 20px;
            text-align: center;
        }

        .toggle-text {
            font-size: 14px;
            color: #666;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #ff69b4;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .form-container, .image-container {
                width: 100%;
                position: absolute;
            }

            .form-container {
                height: 100%;
            }

            .sign-up-container {
                opacity: 0;
                z-index: 1;
            }

            .sign-in-container {
                z-index: 2;
            }

            .container.right-panel-active .sign-in-container {
                transform: translateY(-100%);
                opacity: 0;
                z-index: 1;
            }

            .container.right-panel-active .sign-up-container {
                transform: translateY(0);
                opacity: 1;
                z-index: 5;
                top: 0;
            }

            .image-container {
                display: none;
            }

            .form-wrap {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <div class="form-wrap">
                <img src="Logo.png" alt="Logo" class="logo">
                <h2>Create an Account</h2>
                <p>Let's Get Baked! Sign up to get started.</p>
                
                <form action=" " method= "post" id="signup-form" class="form">
                    <div class="input-field">
                        <label for="name">First Name</label>
                        <input type="text" name="Fname" id="name" placeholder="Enter First Name" required>
                    </div>
                    <div class="input-field">
                        <label for="name">Last Name</label>
                        <input type="text" name="Lname" id="name" placeholder="Enter Last Name" required>
                    </div>
                    <div class="input-field">
                        <label for="email">Email</label>
                        <input type="email" name= "email" id="email" placeholder="Example@email.com" required>
                    </div>
                    <div class="input-field">
                        <label for="password">Create Password</label>
                        <input type="password" name="password" id="password" placeholder="At least 8 characters" required>
                    </div>
                    <div class="input-field">
                        <label for="confirm-password">Re-enter Password</label>
                        <input type="password" name="rpassword" id="confirm-password" placeholder="At least 8 characters" required>
                    </div>
                    <button type="submit" name="signup" class="btn">Sign up</button>
                    
                    <div class="social-text">Or</div>
                    
                    <div class="social-media">
                        <button class="social-icon google">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/>
                                <path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.32-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>
                                <path fill="#FBBC05" d="M11.68 28.18c-1.11-3.3-1.11-6.88 0-10.18V12.3H4.34A20.04 20.04 0 0 0 2 24c0 3.23.77 6.27 2.34 9.03l7.34-5.85z"/>
                                <path fill="#EA4335" d="M24 9.75c3.23 0 6.13 1.15 8.41 3.35l6.31-6.31C34.91 3.29 29.62 1 24 1 15.4 1 7.96 5.93 4.34 14.97l7.34 5.85c1.74-5.2 6.59-9.07 12.32-9.07z"/>
                            </svg>
                            Sign up with Google
                        </button>
                        <button class="social-icon facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#3F60A0" d="M24 4C12.954 4 4 12.954 4 24s8.954 20 20 20 20-8.954 20-20S35.046 4 24 4z"/>
                                <path fill="#FFFFFF" d="M26.707 29.301h5.176l.813-5.258h-5.989v-2.874c0-2.184.714-4.121 2.757-4.121h3.283V12.46c-.532-.073-1.834-.232-4.186-.232-4.455 0-7.061 2.345-7.061 7.683v3.131h-4.565v5.258h4.565v14.311h5.601V29.301z"/>
                            </svg>
                            Sign up with Facebook
                        </button>
                    </div>
                </form>
                
                <div class="toggle-container">
                    <span class="toggle-text">Already have an account?</span>
                    <button class="toggle-btn" id="signInBtn">Sign in</button>
                </div>
            </div>
        </div>
        
        <div class="form-container sign-in-container">
            <div class="form-wrap">
                <img src="Logo.png" alt="Logo" class="logo">
                <h2>Log in to Your Account</h2>
                <p>Today is a new day. It's your day. You shape it. Log in to get started.</p>
                
                <form action=" " method="post" id="signin-form" class="form">
                    <div class="input-field">
                        <label for="login-email">Email</label>
                        <input type="email" name="email" id="login-email" placeholder="Example@email.com" required>
                    </div>
                    <div class="input-field">
                        <label for="login-password">Password</label>
                        <input type="password" name="password" id="login-password" placeholder="At least 8 characters" required>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>
                    <button type="submit" name="signin" class="btn">Sign in</button>
                    
                    <div class="social-text">Or</div>
                    
                    <div class="social-media">
                        <button class="social-icon google">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/>
                                <path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.32-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>
                                <path fill="#FBBC05" d="M11.68 28.18c-1.11-3.3-1.11-6.88 0-10.18V12.3H4.34A20.04 20.04 0 0 0 2 24c0 3.23.77 6.27 2.34 9.03l7.34-5.85z"/>
                                <path fill="#EA4335" d="M24 9.75c3.23 0 6.13 1.15 8.41 3.35l6.31-6.31C34.91 3.29 29.62 1 24 1 15.4 1 7.96 5.93 4.34 14.97l7.34 5.85c1.74-5.2 6.59-9.07 12.32-9.07z"/>
                            </svg>
                            Sign in with Google
                        </button>
                        <button class="social-icon facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#3F60A0" d="M24 4C12.954 4 4 12.954 4 24s8.954 20 20 20 20-8.954 20-20S35.046 4 24 4z"/>
                                <path fill="#FFFFFF" d="M26.707 29.301h5.176l.813-5.258h-5.989v-2.874c0-2.184.714-4.121 2.757-4.121h3.283V12.46c-.532-.073-1.834-.232-4.186-.232-4.455 0-7.061 2.345-7.061 7.683v3.131h-4.565v5.258h4.565v14.311h5.601V29.301z"/>
                            </svg>
                            Sign in with Facebook
                        </button>
                    </div>
                </form>
                
                <div class="toggle-container">
                    <span class="toggle-text">Don't have an account?</span>
                    <button class="toggle-btn" id="signUpBtn">Sign up</button>
                </div>
            </div>
        </div>
        
        <div class="image-container">
            <div class="image-inner">
                <img src="image 3.png" alt="Sign in image" class="signin-image">
                <img src="image sign-up.png" alt="Sign up image" class="signup-image">
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        
        // Toggle between sign in and sign up
        signUpBtn.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });
        
        signInBtn.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });

        // Prevent form submission when clicking social buttons
        const socialButtons = document.querySelectorAll('.social-icon');
        socialButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
            });
        });
    </script>
</body>
</html>