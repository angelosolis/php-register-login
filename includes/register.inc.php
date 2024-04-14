<?php
include_once "dbc.inc.php";

try {
    if (isset($_POST['firstName'], $_POST['middleName'], $_POST['lastName'], $_POST['suffix'], $_POST['birthday'], $_POST['address'], $_POST['contactNumber'], $_FILES['profilePicture'], $_POST['emailAddressReg'], $_POST['passwordReg'])) 
    {
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $suffix = $_POST['suffix'];
        $birthday = $_POST['birthday'];
        $address = $_POST['address'];
        $contactNumber = $_POST['contactNumber'];
        $emailAddress = $_POST['emailAddressReg'];
        $password = $_POST['passwordReg'];

        $profilePicture = ''; // Initialize profile picture variable
        if (isset($_FILES['profilePicture']['name']) && !empty($_FILES['profilePicture']['name'])) {
            $img_name = $_FILES['profilePicture']['name'];
            $tmp_name = $_FILES['profilePicture']['tmp_name'];
            $error = $_FILES['profilePicture']['error'];

            if ($error === 0) {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_to_lc = strtolower($img_ex);

                $allowed_exs = array('jpg', 'jpeg', 'png');
                if (in_array($img_ex_to_lc, $allowed_exs)) 
                {
                    $new_img_name = uniqid('profile', true) . '.' . $img_ex_to_lc;
                    $img_upload_path = '../upload/' . $new_img_name; // Directory where profile pictures will be stored

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($tmp_name, $img_upload_path)) 
                    {
                        // File uploaded successfully
                        $profilePicture = $img_upload_path;
                    } 
                    else 
                    {
                        throw new Exception("Error uploading profile picture.");
                    }
                } 
                else 
                {
                    throw new Exception("Invalid file format. Allowed formats: JPG, JPEG, PNG.");
                }
            } 
            else 
            {
                throw new Exception("Error uploading profile picture: $error");
            }
        } 
        else 
        {
            throw new Exception("Profile picture is required.");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (firstName, middleName, lastName, suffix, birthday, address, contactNumber, profilePicture, emailAddress, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $stmt->execute([$firstName, $middleName, $lastName, $suffix, $birthday, $address, $contactNumber, $profilePicture, $emailAddress, $hashedPassword]);

        if ($stmt->rowCount() > 0) {
            session_start();
            $_SESSION['registration_success'] = true; 
            header("Location: ../login.php"); 
            exit;
        } else {
            throw new Exception("Registration failed. Please try again.");
        }
    } else {
        throw new Exception("Missing required fields.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
