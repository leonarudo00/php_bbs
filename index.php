<?php

// 定数宣言
define('DB_DATABASE', 'bbs_db');	// DB名
define('DB_USERNAME', 'dbuser');	// ユーザ名
define('DB_PASSWORD', 'dbuser');	// パスワード
define('PDO_DSN', 'mysql:host=localhost;dbname=' . DB_DATABASE);	//データソース名

$db;

try{
	// PDOオブジェクトの作成
	$db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
	// 例外を投げるようにエラーモードを設定
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	// 例外のメッセージを出力
	echo $e->getMessage();
	// 終了
	exit;
}

// 指定したparendIDの投稿を取得する関数
function getPosts($id){
	$q = $GLOBALS['db']->query("select * from comments where parentID = ". $id);
	// 投稿一つ分ごとに分割して配列に代入
	$posts = $q->fetchAll(PDO::FETCH_ASSOC);

	return $posts;
}

function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// 投稿内容をデータベースに登録
if(	$_SERVER['REQUEST_METHOD']=='POST' &&
	isset($_POST['message']) &&
	isset($_POST['user'])
	){

	// 本文
	$message = $_POST['message'];
	// 投稿者名
	$user = $_POST['user'];
	// スレッドのID
	$parentID = $_POST['id'];

	if($message !== ''){
		$user = ($user === '') ? 'ななしさん' : $user;

		$message = str_replace("\t", ' ', $message);
		$user = str_replace("\t", ' ', $user);

		// 名前と本文をデータベースに挿入
		$db->exec("insert into comments (name, message, parentID) values ('$user', '$message', '$parentID')" );
	}
} else{
}

// 投稿の削除
if( $_SERVER['REQUEST_METHOD'] == 'POST' &&
	$_POST['request'] == "delete" ){
	$db->exec("delete from comments where id =". $_POST['id'] );
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<title>掲示板</title>
</head>

<body>
	<h1>掲示板</h1>

	<!--投稿フォーム-->
	<form action="" method="post">
		message: <input type="text" name="message">
		user: <input type="text" name="user">
		<input type="submit" value="投稿">
		<input type="hidden" name="id" value=0>
	</form>

	<!--各スレッドの先頭の投稿を取得-->
	<?php $comments = getPosts(0);?>
	
	<h2>スレッド一覧（<?php echo count($comments); ?>件）</h2>
	<ul>
		<!--スレッドの先頭の投稿毎の処理-->
		<?php foreach( $comments as $comment ){?>
			<li><?php echo h($comment['message']); ?>(<?php echo h($comment['name']); ?>)</li>
			<!--スレッド削除ボタン-->
			<form action="" method="post">
				<input type="submit" value="スレッド削除">
				<input type="hidden" name="id" value="<?php echo h($comment['id']);?>">
				<input type="hidden" name="request" value="delete">
			</form>

			<!--スレッド毎に返信されたコメントを取得して出力-->
			<?php $childPosts = getPosts($comment['id']); ?>
			<ul>
				<?php foreach( $childPosts as $childPost){?>
					<li><?php echo h($childPost['message']); ?>(<?php echo h($childPost['name']); ?>)</li>
					<form action="" method="post">
						<input type="submit" value="コメント削除">
						<input type="hidden" name="id" value="<?php echo h($childPost['id']);?>">
						<input type="hidden" name="request" value="delete">
					</form>
				<?php } //foreach( $childPosts as $childPost)?>
			</ul>

			<!--返信ボタン-->
			<form action="" method="post">
				message: <input type="text" name="message">
				user: <input type="text" name="user">
				<input type="submit" value="返信">
				<input type="hidden" name="id" value="<?php echo h($comment['id']);?>">
			</form>
		<?php } //foreach( $comments as $comment )?>
	</ul>
</body>
</html>
