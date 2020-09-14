<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>
<body>

<?php
   // DB接続設定
   $dsn = 'mysql:dbname=データベース名;host=localhost';// ・データベース名：tb220364db
   $user = 'ユーザー名';// ・ユーザー名：tb-220364
   $password = 'パスワード';
   $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
   //PHPとデータベースサーバーの間の接続を行う 
   //「 array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING) 」とは、データベース操作でエラーが発生した場合に警告
   //（Worning: ）として表示

   //テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS mission51"
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
        $sql = 'SELECT * FROM mission51 WHERE id=:id';//テーブルに登録されたデータを取得
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
        	$sql = 'delete from mission51 where id=:id';//対象の行を削除する
        	$stmt = $pdo->prepare($sql);//PDOStatement::execute() メソッドによって実行される SQL ステートメントを準備
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);//$idをidの変数に指定する
        	$stmt->execute();//実行
        }

    //編集ボタンを押したとき
    }elseif(isset($_POST["edit"])){
        $id=$_POST["enum"];
        $e_pass=$_POST["e_pass"];
        $sql = 'SELECT * FROM mission51 WHERE id=:id';//テーブルに登録されたデータを取得
        $stmt = $pdo->prepare($sql);//PDOStatement::execute() メソッドによって実行される SQL ステートメントを準備
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//$idをidの変数に指定する
        $stmt->execute();//実行
        $results = $stmt->fetchAll();//結果を配列で取得
        //抽出されたデータのパスワードを$passwordに代入
    	foreach ($results as $row){
            //編集番号と投稿番号が一致したとき
            if($e_pass==$row['password'] && $id=$row['id']){
                $editnumber=$row['id'];//編集対象の投稿番号をフォームに見えるようにする（実際は見えていない）
        		$ename = $row['name'];//編集対象の名前をフォームに見えるようにする
        		$ecom = $row['comment'];//編集対象のコメントをフォームに見えるようにする
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
            //対象の名前、コメント、日時を編集
            $sql = 'UPDATE mission51 SET name=:name,comment=:comment,date=:date WHERE id=:id';//
	        $stmt = $pdo->prepare($sql);//PDOStatement::execute() メソッドによって実行される SQL ステートメントを準備
	        $stmt->bindParam(':name', $name, PDO::PARAM_STR);//$nameをnameの変数に指定する
            $stmt->bindParam(':comment', $str, PDO::PARAM_STR);//$strをcommentの変数に指定する
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);//$dateをdateの変数に指定する
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);//$idをidの変数に指定する
            $stmt->execute();//実行
            
        //編集したい番号が入力されていなかったとき＝データベースに追加
        }else{
            //テーブルの名前、コメント、日時、パスワードのカラムにそれぞれデータを入力する
            $sql = $pdo -> prepare("INSERT INTO mission51 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);//$nameをnameの変数に指定する
            $sql -> bindParam(':comment', $str, PDO::PARAM_STR);//$strをcommentの変数に指定する
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);//$dateをdateの変数に指定する
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);//$idをidの変数に指定する
            $name = $_POST["name"];
            $str =$_POST["comment"]; 
            $date=date("Y/m/d H:i:s");
            $password=$_POST["pass"];
            $sql -> execute();//実行
        }
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
<?php
//データーベース内のデータをすべて表示表示させる。
    $sql='SELECT * FROM mission51';
    $stmt = $pdo->query($sql);//prepareメソッドでSQLをセット
    $results = $stmt->fetchAll();//結果を配列で取得
    foreach($results as $row){
        echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';//投稿番号、名前、コメント、日時の順でブラウザに表示
	    echo "<hr>";//横線で区切る
    }
    
?>
</body>
</html>
