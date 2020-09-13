<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>

<?php
   // DB接続設定
    $dsn = 'データベース名';// ・データベース名
    $user = 'ユーザー名';// ・ユーザー名
    $password = 'パスワード';// ・パスワード
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
   //PHPとデータベースサーバーの間の接続を行う 
   //「 array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING) 」とは、データベース操作でエラーが発生した場合に警告
   //（Worning: ）として表示

   //テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"//自動で登録されていうナンバリング。
    . "name char(32),"//名前を入れる。文字列、半角英数で32文字。
    . "comment TEXT,"//コメントを入れる。文字列、長めの文章も入る。
    . "date TEXT,"//日時を入れる。文字列、長めの文章も入る。
    . "password char(10)"//パスワードを入れる。文字列、半角英数で32文字。
    .");";
    $stmt = $pdo->query($sql);//$sqlの実行

    //削除ボタンを押したとき
    if(isset($_POST["delete"])){
        $id=$_POST["dnum"];
        $d_pass=$_POST["d_pass"];
        $sql = 'SELECT * FROM mission5 WHERE id=:id';//テーブルに登録されたデータを取得
        
        $stmt = $pdo->prepare($sql);//PDOStatement::execute() メソッドによって実行される SQL ステートメントを準備
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//$idをidの変数に指定する
        $stmt->execute();//実行
        $results = $stmt->fetchAll();//結果を配列で取得
        //抽出されたデータのパスワードを$passwordに代入
    	foreach ($results as $row){
    		$password = $row['password'];
        }
        //$passwordと削除フォームに書き込まれたパスワードを比べて同じであったら削除する
        if($d_pass==$password){
        	$sql = 'delete from mission5 where id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->execute();
        }

    //編集ボタンを押したとき
    }elseif(isset($_POST["edit"])){
        $id=$_POST["enum"];
        $e_pass=$_POST["e_pass"];
        $sql = 'SELECT * FROM mission5 WHERE id=:id';//テーブルに登録されたデータを取得
        $stmt = $pdo->prepare($sql);//$sqlの実行
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//$idをidの変数に指定する
        $stmt->execute();//実行
        $results = $stmt->fetchAll();//結果を配列で取得
        //抽出されたデータのパスワードを$passwordに代入
    	foreach ($results as $row){
            //編集番号と投稿番号が一致したとき
            if($e_pass==$row['password'] && $id=$row['id']){
                $editnumber=$row['id'];
        		$ename = $row['name'];
        		$ecom = $row['comment'];
            }
        }

    //送信ボタンが押されたとき    
    }elseif(isset($_POST["submit"])){
        //編集したい番号が入力されていた時＝編集処理
        if($_POST["editpost"]){
            $id=$_POST["editpost"];
            $name=$_POST["name"];
            $str=$_POST["comment"];
            $date=date("Y/m/d H:i:s");
            $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date WHERE id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt->execute();
        //編集したい番号が入力されていなかったとき＝データベースに追加
        }else{
            $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $str, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $name = $_POST["name"];
            $str =$_POST["comment"]; //好きな名前、好きな言葉は自分で決めること
            $date=date("Y/m/d H:i:s");
            $password=$_POST["pass"];
            $sql -> execute();
        }
    }
    //データーベース内のデータをすべて表示表示させる。
    $sql='SELECT * FROM mission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	    echo "<hr>";
    }
?>
<form method="POST" action="">
    　　　　　　　　　　　投稿
    <input type="hidden" name="editpost" value="<?php if(!empty($editnumber)){echo $editnumber;}?>"><br>
    　　　名前:<input type="text" name="name" value="<?php if(!empty($ename)){echo $ename;}?>"><br>
    　コメント:<input type="text" name="comment" value="<?php if(!empty($ecom)){echo $ecom;}?>"><br>
    パスワード:<input type="text" name=pass>
    <input type="submit" name="submit" value="送信"><br>
    　　　　　　　　　　　削除<br>
    　削除番号:<input type="number" name="dnum"><br>
    パスワード:<input type="text" name="d_pass">
    <input type="submit" name="delete" value="削除"><br>
    　　　　　　　　　　　編集<br>
    　編集番号:<input type="number" name="enum"><br>
    パスワード:<input type="text" name="e_pass">
    <input type="submit" name="edit" value="編集"><br><br>
</form>

</body>
</html>