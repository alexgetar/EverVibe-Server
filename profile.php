<?php
include('classes/db.php');
include('classes/Login.php');
include('classes/Post.php');
include('classes/notify.php');
include('classes/Comment.php');
$username = "";
$isFollowing = "";
$followCheck = "";
$isVerified = False;
$posts = "";
$message = "";
if(Login::isLoggedIn()){
if(isset($_GET['username'])){
	if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){

		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		$followerid = Login::isLoggedIn();
		$followCheck = DB::query('SELECT user_id FROM followers WHERE follower_id=:followerid AND user_id=:userid', array(':userid'=>$userid, ':followerid'=>$followerid));

		if(!$followCheck){
			$isFollowing = False;
		}else{
			$isFollowing = True;
		}

		if(DB::query('SELECT * FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>'52'))){
			$isVerified = True;
		}

		if(isset($_POST['post'])){
			Post::MakePost($_POST['postbody'], Login::isLoggedIn(), $userid);
		}

		if(isset($_GET['postid'])){
			if(isset($_POST['deletepost'])){
				if(DB::query("SELECT id FROM posts WHERE id=:postid AND user_id=:user_id", array(':postid'=>$_GET['postid'], ':user_id'=>$followerid))){
					DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
					DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
					$message = "<div class='alert alert-success alert-dismissiable'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Post deleted</strong></div>";
				}
			}else{
			Post::LikePost($_GET['postid'], Login::isLoggedIn(), $userid);
			}
		}

		$posts = Post::displayPosts($userid, $username, Login::isLoggedIn());

	}else{
		die('User Not Found');
	}
}
}else{
	die('Not logged in!');
}

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Profile Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<script src='https://code.jquery.com/jquery-3.3.1.js'></script>
  <style type="text/css">
  	@import url('https://fonts.googleapis.com/css?family=ABeeZee|Questrial|Ropa+Sans');
  	.title-1{
  		font-size: 60px;
  		font-family: 'ABeeZee', sans-serif;
  	}

  	textarea {
  		resize: none;
  	}
  </style>
</head>

</body>
<section class="hero is-info is-medium is-fullwidth" style="padding-bottom: 5px;">
  <!-- Hero head: will stick at the top -->
  <div class="hero-head">
    <nav class="navbar">
      <div class="container">
        <div class="navbar-brand">
          <span class="navbar-item">
            <h1 class="title title-1"><?php if(strlen($username <= 10)){echo htmlspecialchars(substr($username, 0 ,9));}else{echo htmlspecialchars($username);} if($isVerified){echo "<i class='material-icons' style='font-size:38px;'>verified_user</i>";} ?></h1>
          </span>
          <span class="navbar-burger burger" id='NavBarToggle' data-target="navbarMenuHeroA">
            <span></span>
            <span></span>
            <span></span>
          </span>
        </div>
        <div id="navbarMenuHeroA" class="navbar-menu">
          <div class="navbar-end">
            <a href="index.php" class="navbar-item">
              Home
            </a>
            <?php if($userid == $followerid){ ?>
            <a class="navbar-item" href="change-password.php">
              Change-Password
            </a>
            <a href="notify.php" class="navbar-item">
              Notifications
            </a>
            <span class="navbar-item">
              <a href='logout.php' class="button is-primary is-inverted">
                Logout
              </a>
			</span>
			<?php }else{ ?>
			<a class="navbar-item" href="talk.php?username=<?php echo $_GET['username']; ?>">
              Chat
            </a>
            <a href="notify.php" class="navbar-item">
              Notifications
            </a>
            <span class="navbar-item">
              <a href='logout.php' class="button is-primary is-inverted">
                Logout
              </a>
			</span>
			<?php } ?>
          </div>
        </div>
      </div>
    </nav>
  </div>

				<br>
				<div class="container">
					<form id="Follow_Unfollow">
					<?php if($followerid != $userid){
					if(!$isFollowing){
					echo "<input type='button' id='followUser' name='follow' class='button is-danger is-focused is-medium' value='Follow'>";
					}else{
					echo "<input type='button' id='unfollowUser' name='follow' class='button is-danger is-focused is-medium' value='Unfollow'>";
					 } }?>
				 </form>
</section>
<br>
<?php
if(Login::isLoggedIn() == $userid){
?>
<?php
if($_GET['username'] == 'AmanSetia'){
	$connection = mysqli_connect("sql108.epizy.com", "epiz_22081076", "THEHERO", "epiz_22081076_finale");
	$row_count = mysqli_query($connection,"SELECT * FROM users");
	$rows = mysqli_num_rows($row_count);
	printf("Number of users on the site are %d.\n",$rows);
	if($rows >= 10){
		echo "Awesome nibbs";
	}
}
 ?>
	<div class="container-fluid">
<form>
	<div class="cloumns">
		<center>
		<div class="column is-one-quarter">
			<label class="label">What's on your mind?</label>
		</div>
		<div class="column is-two-thirds">
			<textarea id="postbody" name="postbody" rows="5" cols="25" class="input is-rounded is-focused is-primary" maxlength="120"></textarea>
		</div>
		<div class="column">
			<input type="button" id="Post" class="button is-dark" name="post" value="Post">
		</div>
		</form>
	</div>
	</center>
</div>
<br>
<?php } ?>
</div>
	<?php
	if($posts != ""){
	 echo "<div class='col-lg-3'></div>";
	 echo "<div class='col-lg-6'>";
	 echo "<span id='PostsAll'></span>";
	 echo "</div>";
	 echo "<div class='col-lg-3'></div>";
	}else{
	echo "<h2>No posts Made by users!";
}
?>
<script>
	function GetPostAll() { 
		$.get("functions.php", {
			thing: "Get Posts",
			userid: <?php echo $userid; ?>,
			followerid: <?php echo $followerid; ?>
		},function(data){
			$("#PostsAll").html(data);
		})
	}

	GetPostAll();

  	$('#NavBarToggle').click(function(){
		$('#navbarMenuHeroA').toggleClass('is-active');
	});

	$("#followUser").click(function(){
		$.post("functions.php", {
			thing: "Following",
			userId: <?php echo $userid; ?>,
			followerId: <?php echo $followerid; ?>
		},function(data, status){
			console.log(data);
			console.log(status);
			$("#Follow_Unfollow").html("<input type='button' id='unfollowUser' name='follow' class='button is-danger is-focused is-medium' value='Unfollow'>");
			
		})
	})

	$("#unfollowUser").click(function(){
		$.post("functions.php", {
			thing: "UnFollowing",
			userId: <?php echo $userid; ?>,
			followerId: <?php echo $followerid; ?>
		},function(status, data){
			console.log(data);
			console.log(status);
			$("#Follow_Unfollow").html("<input type='button' id='followUser' name='follow' class='button is-danger is-focused is-medium' value='Follow'>");
			
		})
	})

	$("#Post").click(function(){
		$.post("functions.php", {
			thing: "MakingPost",
			postbody: $("#postbody").val(),
			followerid: <?php echo $followerid; ?>,
			userid: <?php echo $userid; ?>
		},function(data){
			console.log(data);
			$("#postbody").val("");
			GetPostAll();
		})
	})

</script>
</html>
