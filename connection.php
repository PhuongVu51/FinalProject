<?php
//creating a database connection - $link is a variable use for just connection class
$link=mysqli_connect("127.0.0.1","root","") or die(mysqli_error($link)); //goi ham SQL de ket noi den CSDL
mysqli_select_db($link,"teacher_bee_db") or die(mysqli_error($link)); 