<?php
$pdo = new PDO('mysql:host=localhost;dbname=jsk', 'root','root' );
//$pre = $pdo->prepare('UPDATE `dd_order`  SET `订单编号`=:data__111  WHERE  `编号` = :where_id  ');
//$arr = [':data__111'=>'汉子',':where_id'=>'4'];
$pdo->query ( 'set names utf8' );


$order_no = "汉子22";
$uid = "411";
$pre = $pdo->prepare('INSERT INTO dd_order(订单编号,客户ID) VALUES(:asd,:adq)');
//$pre->bindParam( ':订单编号',$order_no);
//$pre->bindParam( ':客户ID',$uid);
$arr = [':asd'=>'汉子22',':adq'=>'411'];


//$instance_id = "汉子22";
//$user_name = "411";
//$pre = $pdo->prepare('INSERT INTO sys_user(instance_id,user_name) VALUES(:instance_id,:user_name)');
////$pre->bindParam( ':instance_id',$instance_id);
////$pre->bindParam( ':user_name',$user_name);
//$arr = [':instance_id'=>'汉子22',':user_name'=>'411'];

//$pre = $pdo->prepare('INSERT INTO `dd_order` (`订单编号` , `客户ID` , `微信ID` , `订单类型` , `下单日期` , `下单时间` , `审核人`) VALUES (:订单编号 , :客户ID , :微信ID , :订单类型 , :下单日期 , :下单时间 , :审核人) ')
//$arr = [':订单编号' => '201709293',':客户ID' => 3,':微信ID' => '', ':订单类型' => '1',':下单日期' => '2017-09-29',':下单时间' => '2017-09-29 15:13:00',':审核人' => '01.C.01.05:1.00,01.C.01.02:5.33'];
$pre->execute($arr);
print_r ( $pre->errorInfo() );


?>