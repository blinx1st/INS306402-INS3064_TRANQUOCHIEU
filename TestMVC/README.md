# PHP MVC Practice

Mini project MVC PHP thuan de luyen tap voi XAMPP va MySQL.

## Cau truc

```text
TestMVC/
  app/
    Controllers/
    Models/
    Views/
  config/
  core/
  database/
  public/
  index.php
```

## Cach chay

1. Mo XAMPP va start `Apache`, `MySQL`.
2. Vao `http://localhost/phpmyadmin`.
3. Import file `database/testmvc.sql`.
4. Neu MySQL cua ban co password, sua file `config/database.php`.
5. Mo trinh duyet:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/TestMVC/
```

Neu rewrite chua bat, dung URL dang:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/TestMVC/index.php?url=students
```

