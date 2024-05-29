# posse3-hackathon-202309-team3B

◾️環境構築  
以下の順番でコンテナを立ち上げる  
①docker compose build —no-cache  
②docker compose up -d  

◾️ログイン・ユーザー登録  
①http://localhost:8080/auth/signup.php　にアクセスし、ユーザー登録する  
②http://localhost:8025/　にアクセスし、届いたメールからパスワード登録をする  

◾️入退室  
①入室ボタンをクリックし、バーで何時までHarborSにいるかを選ぶ  
②登録ボタンを押すと現在HarborSにいる人リストに追加される（初期設定が必要だが、line notifyによりlineへ通知を送るように実装している）  
③退室ボタンを押すとHarborSにいる人リストから外される  
