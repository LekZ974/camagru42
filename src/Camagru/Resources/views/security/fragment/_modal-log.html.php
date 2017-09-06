<div id="auth" class="modal">
    <form class="form animate" action="" method="POST">
        <div class="imgcontainer">
            <span onclick="document.getElementById('auth').style.display='none'" class="close" title="Close Modal">&times;</span>
        </div>
        <div class="containerForm">
            <label><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="login" required>
            <label><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>
            <button type="submit"><span>Login</span></button>
            <input type="checkbox" checked="checked" name="rememberMe" id="remembercheckbox"><label for="remembercheckbox">Remember me
        </div>
        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('auth').style.display='none'" class="cancel-btn"><span>Cancel</span></button>
            <span class="psw">Forgot <a href="/forgot">password?</a></span>
        </div>
    </form>
</div>