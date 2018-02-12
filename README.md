# php_bbs
phpとmysqlによる掲示板

## データベースのデータ構造
こちらのコードは下記のデータ構造のデータベースを前提に動作します

| Field    | Type         | Null | Key | Default | Extra          |  
|---       |---           |---   |---  |---      |---             |
| id       | int(11)      | NO   | PRI | NULL    | auto_increment |  
| name     | varchar(255) | YES  |     | NULL    |                |  
| message  | varchar(255) | YES  |     | NULL    |                |  
| parentID | int(11)      | YES  |     | NULL    |                |  
