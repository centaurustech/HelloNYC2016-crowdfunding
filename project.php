<?php
session_start();
$pid = $_GET['id'];
$_SESSION['pid']=$pid;
$db = mysqli_connect("127.0.0.1","root","yuqi00","Final");
if (!$db)
{
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM Project p, User u WHERE p.pid='$pid' AND u.uid=p.uid";
$result = mysqli_query($db,$sql);
while($row=mysqli_fetch_array($result))
{
    $name=$row[2];
    $owner=$row[13];
    $id=$row['uid'];
    $description=$row['description'];
    $moneyraised=$row['moneyraised'];
    $minimum=$row['minimum'];
    $maximum=$row['maximum'];
    $posttime=$row['posttime'];
    $endtime=$row['endtime'];
    $completetime=$row['completetime'];
    $status=$row['status'];
}
$sql = "SELECT count(*) as total FROM Pledge WHERE pid='$pid'";
$result = mysqli_query($db,$sql);
$row = mysqli_fetch_assoc($result);
$total = $row['total'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/journal/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=News+Cycle:400,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/project.css">
    <link rel="stylesheet" href="assets/css/styles.min.css">
    <!-- proile -->
    <link rel="stylesheet" href="assets/css/profile.css">

</head>

<body>
<?php
if(isset($_SESSION['login']) && $_SESSION['login']==true)
  {
    include "nav-login.php";
  }
  else
  {
    include "nav-sign.php";
  }
   ?>
<ol class="breadcrumb">
    <li class="active"><span>Project </span></li>
    <li><a href="<?php echo "update.php?id=".$pid ?>"><span>Updates </span></a ></li>
</ol>
<div class="container">
    <div class="row product">
        <div class="col-md-5 col-md-offset-0">
            <img class="img-responsive" src="assets/images/suit_jacket.jpg"></div>
        <div class="col-md-7">
            <h2><?php echo $name; ?></h2>
            <p>Post by <?php echo "<a href='home.php?id={$id}'>{$owner}</a>"; ?></h2>
            <p><?php echo $description; ?> </p >
            <!-- show donate -->
             <?php
            $sql = "SELECT status FROM Project WHERE pid='$pid'";
            $result = mysqli_query($db,$sql);
            $row = mysqli_fetch_assoc($result);
            if ($row['status'] == "ongoing") {
                echo "<a class=\"btn btn-primary\" href= \"donate.php\" role=\"button\">Donate</a >";
            }
            ?>       
            <!-- show like -->
            <?php
            if (isset($_SESSION['uid'])) {
                echo "<button class=\"btn btn-info\" onclick=\"myFunction()\">Like</button>";
            }
            ?>
            
            <script>
                function myFunction() {
                    alert("You have followed this project.");
                    <?php 
                    $follow = "INSERT INTO Likes(uid,pid) VALUES ('{$_SESSION['uid']}', '$pid')";
                    mysqli_query($db, $follow);
                    ?>
                }
            </script>
        </div>
    </div>
    <div class="page-header">
        <h3>Project Progress</h3></div>
    <div class="panel panel-default">

        <div class="panel-body">
            <h4><small class="display-inline-block pull-right"></small></h4>
            <p>Project Popularity: <strong class="text-danger">Hot </strong> </p >
            <div class="progress">
                <?php $percent=100*$moneyraised/$maximum; ?>
                <div class="progress-bar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent; ?>%"><?php echo $percent; ?>%</div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>Raised: $<?php echo $moneyraised; ?>
                        <br>
                        <small class="text-muted"><i class="fa fa-heart text-primary"></i><?php echo $total; ?> Donations</small></p >
                </div>
                <div class="col-md-6">
                    <p class="text-right">Goal: $<?php echo $maximum; ?>
                        <br>
                        <small class="text-muted"><i class="fa fa-clock-o text-primary"></i> Ends <?php echo $endtime; ?></small></p >
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <div class="page-header">
            <h3>Donation</h3></div>
        <?php
        $sql1 = "SELECT * FROM Pledge NATURAL JOIN User WHERE pid='$pid' ORDER BY time desc";
        $result1 = mysqli_query($db,$sql1);
        echo "<table class='table table-striped'><thead><tr><th>Donor</th><th>Amount</th><th>Time</th></tr></thead>";
        while($row1=mysqli_fetch_array($result1))
        {
            $username=$row1['username'];
            $amount=$row1['amount'];
            $time=$row1['time'];
            echo "<tbody>";
            echo "<tr>";
            echo "<td>{$username}</td>";
            echo "<td>{$amount}</td>";
            echo "<td>{$time}</td>";
            echo "</tr>";
            echo "</tbody>";
        }
        echo "</table>";
        ?>
    </div>

<?php
    $sql = "SELECT status FROM Project WHERE pid='$pid'";
    $result = mysqli_query($db,$sql);
    $row = mysqli_fetch_assoc($result);

    $uid = $_SESSION['uid'];
    $sql2 = "SELECT * FROM PLEDGE WHERE pid='$pid' AND uid = '$uid'";
    $result2 = mysqli_query($db,$sql2);
    if ($row['status'] == "succeeded" && mysqli_num_rows($result2) >= 1) {
        echo "<div class=\"page-header\"><h3>Rate</h3></div>";
        echo "<div class=\"page-header\">";
    }
    ?>
    <div class="media">
        <div class="media-body">
            <?php
            $sql3 = "SELECT r.stars, u.username FROM Rate r, User u NATURAL JOIN User WHERE r.uid = u.uid AND pid='$pid'";
            $result3 = mysqli_query($db,$sql3);
            while($row3=mysqli_fetch_array($result3))
            {
                $count=$row3['stars'];
                $user=$row3['username'];
                echo "<p>";
                while($count>0)
                {
                    echo "<i class='fa fa-star'></i>";
                    $count=$count-1;
                }
                echo "</p >";
                echo "<p><span class='reviewer-name'><strong>$user</strong></span></p >";
                echo "</br>";
            }
            ?>
        </div>

        <?php
        $sql = "SELECT status FROM Project WHERE pid='$pid'";
    $result = mysqli_query($db,$sql);
    $row = mysqli_fetch_assoc($result);

    $uid = $_SESSION['uid'];
    $sql2 = "SELECT * FROM PLEDGE WHERE pid='$pid' AND uid = '$uid'";
    $result2 = mysqli_query($db,$sql2);
    if ($row['status'] == "succeeded" && mysqli_num_rows($result2) >= 1){
            echo "<form action=\"postrate.php\" method=\"post\">";
            echo "<input class=\"form-control\" style=\"width: 100px\"  name=\"stars\" placeholder=\"Rate 1 to 5.\">";
            echo "<button class=\"btn btn-primary\" style=\"margin-top: 10px\" type=\"submit\">Submit</button>";
            echo "</form>";
        }
        ?>
    </div>


    <div class="page-header">
        <h3>Reviews</h3></div>
    <div class="media">
        <div class="media-body">
            <?php
            $sql2 = "SELECT * FROM Comment NATURAL JOIN User WHERE pid='$pid' ORDER BY time DESC";
            $result2 = mysqli_query($db,$sql2);
            while($row2=mysqli_fetch_array($result2))
            {
                $title=$row2['title'];
                $content=$row2['content'];
                $time=$row2['time'];
                $user=$row2['username'];
                echo "<h4 class='media-heading'>$title</h4>";
                echo "<p>$content</p >";
                echo "<p><span class='reviewer-name'><strong><a href='home.php?id={$row2['uid']}'>{$user}</a></strong></span><span class='review-date'>$time</span></p >";
                echo "</br>";
            }
            ?>
        </div>

        <br>
        <?php
        if(isset($_SESSION['login'])){
        echo "<form action='postcom.php' method='POST'>";
        echo "<textarea class='form-control' rows='1' name='title' placeholder='Title' ></textarea>";
        echo "<textarea class='form-control' rows='6' name='content' placeholder='Comment'></textarea>";
        echo "<br>";
        echo "<input class='btn btn-info' type='submit'>";
        echo "</form>";
    }
    ?>
    </div>
    <footer class="site-footer">
        <div class="container">
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <h5>Fund Raiser @ 2016</h5></div>
                <div class="col-sm-6 social-icons"><a href="#"><i class="fa fa-facebook"></i></a ><a href="#"><i class="fa fa-twitter"></i></a ><a href="#"><i class="fa fa-instagram"></i></a ></div>
            </div>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>

</html>