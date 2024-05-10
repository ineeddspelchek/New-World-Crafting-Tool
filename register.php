<?php 
include("connect-db.php");

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <title>Register</title>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet/less" type="text/css" href="main.less?ts=\<\?=filemtime('style.css')?"/>
        <script src="less.js" type="text/javascript"></script>
        <script src="jquery.js" type="text/javascript"></script>

        <script>
            if(localStorage.getItem("darkmode") == null)
                localStorage.setItem("darkmode", "");

            function load() {
                $("#darkmode").on("click", function() {
                    if(localStorage.getItem("darkmode")) {
                        $(this).html("🌙");
                        $(document.querySelector("html")).attr("data-bs-theme", "light");
                        localStorage.setItem("darkmode", "");
                        $("#darkmode").val("false");
                    }
                    else {
                        $(this).html("☀️");
                        $(document.querySelector("html")).attr("data-bs-theme", "dark");
                        localStorage.setItem("darkmode", "true");
                        $("#darkmode").val("true");
                    }
                });
                
                if(!localStorage.getItem("darkmode")) {
                    $("#darkmode").html("🌙");
                    $(document.querySelector("html")).attr("data-bs-theme", "light");
                    localStorage.setItem("darkmode", "");
                }
                else {
                    $("#darkmode").html("☀️");
                    $(document.querySelector("html")).attr("data-bs-theme", "dark");
                    localStorage.setItem("darkmode", "true");
                }
            }

            function save() {

            }
            
        
        </script>

    <body onload="load();" onunload="save();">
        <?php include("header.php");?>
        
        <div class="m-2 w-50 d-flex flex-column align-content-start">
            <h2><u>Register</u></h2>
            <form action="index.php" method="post">
                <div class="d-flex flex-column">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" required></input>

                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" required></input>
                </div>
                <div class="mt-2 d-flex flex-row">
                    <button class="btn btn-primary" type="submit">Register</button>
                    <a class="ms-2 btn btn-secondary" href="login.php">Go to Login</a>
                </div>
                <input type="hidden" name="register"></input>
                <input type="hidden" id="darkmode" name="darkmode" value="false"></input>
            </form>
            <?php if(isset($_GET["status"])) { ?>
                <div class="alert alert-danger">
                    Email already used.
                </div>
            <?php } ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>