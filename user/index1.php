
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<title>HCAS - DR CLINIC HANNANI</title>
	

	<style>
	body{
    background-image: url(../image/1.jpg);
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
    height: 100%;}
	
	html, body {
    height: 100%;
    margin: 0;}
	
	table{
    width: 100%;
    padding-top: 5px;}/*full size*/
	
.full-height {
    background: rgba(26, 26, 26, 0.548);
    background-attachment: fixed;
    max-height: 100vh;
    height: 100vh;}

.heading-text{
    color: white;
    font-size: 42px;
    font-weight: 700;
    line-height: 63px;
    margin-top: 15%;
    text-align: center;
    margin-bottom: 0;
}

.sub-text2{
    color:white;
    font-size: 17px;
    line-height: 27px;
    font-weight: 400;
    text-align: center;
    margin-top: 0;
}

.register-btn{
    background-color: rgba(240, 248, 255, 0.589);
    color: #345cc4;
}

.edoc-logo{
	color: rgba(255, 255, 255, 0.733);
    font-weight: bolder;
    font-size: 20px;
    padding-left: 20px;
    animation: transitionIn-Y-over 0.5s;
}

.edoc-logo-sub{
     color: white;
    font-size: 20px;
	
}

.nav-item{
  font-size: 20px;
  text-transform: uppercase;
  color: white;
  font-weight:bold;
}

.nav-item:hover{
    color: black;
}

.footer-hashen{
    position: absolute;
    bottom: 0;
    left: 45%;
    font-size: 13px;
    animation: transitionIn-Y-over 0.5s;
}
   .login-btn {
            border: none;
            outline: none;
            background: #ff652f;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
			text-decoration:none;
			color:white;
        }

        .login-btn:hover {
            color: black;
        }
	</style>
</head>
<body>
    <div class="full-height">
        <center>
            <table border="0">
            <tr>
                    <td width="80%">
                        <font class="edoc-logo">hCas. </font>
                        <font class="edoc-logo-sub">| CLINIC DR HANNANI</font>
                    </td>
					
                   <td>
						<br>
						<br>
							<a href="patient_register.php" class="login-btn">SignUp</a> &nbsp;&nbsp;
                    </td>
					
                    <td>
						<br>
						<br>
						   <a href="patient_login.php" class="login-btn">SignIn</a> &nbsp; &nbsp;
                    </td>

				<td>
					<br>
					<a href="../index.php" class="login-btn">
						<i style="font-size:21px" class="fa">&#xf015;</i>
					</a>
				</td>
           </tr>

             <tr>
                    <td colspan="3">
                        <p class="heading-text">Welcome to HCAS Patient Portal</p>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <p class="sub-text2">Rumah Sihat Kita Sdn Bhd (1361050-X) <br>Aims to create a new perception of a clinic as being at home. <br>
                            A doctor who listens to problems carefully and gives the best treatment wholeheartedly, Make your appointment now.</p>
                    </td>
                </tr>
                <tr>

                    <td colspan="3">
                        <center>
                            <a href="patient_login.php">
                                <input type="button" value="Make Appointment" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                            </a>
                        </center>
                    </td>

                </tr>
                <tr>
                    <td colspan="3">

                    </td>
                </tr>
            </table>
            <p class="sub-text2 footer-hashen">Rumah Sihat Kita Sdn Bhd (1361050-X)</p>
        </center>

    </div>
</body>

</html>