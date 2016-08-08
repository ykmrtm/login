<?	//セッション開始
	session_start();
	//ログイン(submit)ボタン押下時イベント
	if(isset($_POST['login'])){
		//入力取得時イベント
		if((!empty($_POST['username'])) && (!empty($_POST['pass']))){
			//入力情報代入と同時に取得パスワードハッシュ化
			$user=$_POST['username'];
			$pass=hash('sha256',$_POST['pass']);	//d74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1
			//データベース接続、取得
			$db=mysqli_connect('localhost','root','','login')or die('データベース接続に失敗しました');
			//セレクトクエリ代入
			$sql="SELECT * FROM list WHERE username='$user' AND pass='$pass';";
			//セレクトクエリ実行
			$result= mysqli_query($db,$sql);
			//セレクトクエリ結果行数取得
			$num=mysqli_num_rows($result);
			//セレクトクエリ結果行数別イベント
			if($num != 0){
				//ログイン日時更新クエリ代入
				$date = "UPDATE list SET date=now() WHERE username='$user' AND pass='$pass';";
				//ログイン日時更新クエリ実行
				mysqli_query($db, $date)or die('ログイン日時を更新できませんでした');
				//セレクトクエリ結果テーブル内容取得
				$table = mysqli_fetch_assoc($result);
				//セッション作成
				$_SESSION['user'] = hash("sha256",$table["username"].$table["pass"].$table["date"]);
				//セッション情報更新クエリ代入
				$query = $_SESSION['user'];
				$session = "UPDATE list SET session='$query' WHERE username='$user' AND pass='$pass';";
				//セッション情報更新クエリ実行
				mysqli_query($db, $session)or die('セッション情報を更新できませんでした');
				//ログインフラグ情報更新クエリ代入
				$flag = "UPDATE list SET flag=1 WHERE username='$user' AND pass='$pass';";
				//ログインフラグ情報更新クエリ実行
				mysqli_query($db, $flag)or die('ログイン情報を更新できませんでした');
			}else{							//セレクトクエリ結果0行時イベント
				//エラー分出力変数
				$flag['login']='miss';
				//ユーザ名入力欄に入力された文字列を出力
				$setname=$user;
			}
			//データベース切断
			mysqli_close($db);
		}else{								//入力欄空欄時イベント
			//エラー分出力変数
			$flag['login']='miss';
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>login</title>
    <style>
	    p{margin: 0;}
	    body{margin:0; background: #494949; font-family: sans-serif; font-size: 13px;}
	    #wrap{width:400px;height:320px;position: absolute;right:0;left:0;top:0;bottom:0;margin: auto;}
	    #flag{background: rgba(230, 184, 184, 0.71);border:1px solid rgba(194, 22, 22, 0.6); color: #c31616;
		    width: 400px;padding: 10px 0px;text-indent: 30px;border-radius: 5px;margin: 0 0 20px 0;}
	    form{padding: 20px 30px 25px 30px;text-align: center; background: #fff; border-radius: 3px;
		    box-shadow: 0px 0px 0px 9px rgba(255, 255, 255, 0.17); overflow: hidden;}
		p.title{text-align: left; color: rgba(0, 0, 0, 0.5); margin: 0px 0px 0px 3px; font-size: 18px;}
		hr{margin:0 0 10px 0;width: 120%; position: relative; left: -30px; border-top: 1px solid rgba(0, 0, 0, 0.08);
			border-bottom: 0;border-left: 0;border-right: 0;}
		input{margin:5px 0;width:100%; padding: 15px 0; background: #f8f8f8; border: 1px solid rgba(0, 0, 0, 0.08);
			text-indent: 15px; transition: .1s;}
		input.login{background: #24bce1; width:100%; color: #fff; border: 1px solid rgba(0, 0, 0, 0.1);}
		input:focus{background:#fcffb9;}
		input.login:hover{background: #00ccff;border: 1px solid rgba(200,200,200, 0.5);}
		input.login:active{background: #51dcff;}
    </style>
    <script></script>
  </head>
  <body>
	<div id="wrap">
	  <? if(@$flag['login']=='miss'): ?>
	  <div id="flag">
	  	<p>ユーザ名またはパスワードが間違っています</p>
	  </div>
	  <? endif; ?>
	  <form action='' method='POST'>
		<p class="title">サインイン</p>
		<hr>
	  	<p><input type="text" name="username" autofocus="" placeholder="ユーザ名を入力してください" value="<? echo @$setname; ?>"></p>
	  	<p><input type="password" name="pass" placeholder="パスワード"></p>
	  	<p><input type="submit" name="login" value="ログイン" class="login"></p>
	  </form>
	</div>
  </body>
</html>