<!doctype html>
<html lang="ru">
  <head>
    <!-- Обязательные метатеги -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>belky</title>
      </head>
      <body>

      <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">Belky</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto mb-2 mb-md-0">
              <li class="nav-item active">
                <a class="nav-link" aria-current="page" href="#">Мои последовательности</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">База патентов</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <main class="container">

        <div class="starter-template text-center py-5 px-1 mt-3">
          <h1>Добавить последовательность</h1>
        </div>

        <form method="get">
          <div class="row justify-content-center">
          <div class="col-sm-2 text-center">
            <table class="table table-bordered border-primary">
              <tbody>
                <?php
                  // CREATE TABLE m_params ( param_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, mcol INTEGER NOT NULL);
                  // INSERT INTO m_params ( mcol ) VALUES ( 30 );
                  ini_set('display_errors', 1);
                  ini_set('display_startup_errors', 1);
                  error_reporting(E_ALL);

                  $db = new SQLite3("my.db", SQLITE3_OPEN_READWRITE);                

                  if(isset($_GET["cols"])){
                    $db->exec("UPDATE m_params SET mcol=".$_GET["cols"]." WHERE param_id=1;");
                  }

                  if(isset($_GET["set_default"])){
                    $db->exec("UPDATE m_params SET mcol=30 WHERE param_id=1;");
                  }

                  $res = $db->querySingle("SELECT mcol FROM m_params;");

                  
                  for ($i = 0; $i < $res; $i++) {
                      echo "<tr>";
                      echo "<td>X".($i+1)."</td>";
                      echo "<td><input type=\"text\" class=\"form-control input-sm\"></td>";
                      echo "<td><button type=\"submit\" class=\"btn btn-outline-primary\">+</button></td>";
                      echo "</tr>";
                  }                
                  echo "<tr>";
                  echo "<td></td>";
                  echo "<td class=\"text-center\"><a href=\"?cols=".($res+1)."\" class=\"btn btn-outline-primary\">+</a></td>";
                  echo "<td></td></tr>";
                ?>            
              </tbody>
            </table>
          </div>
          </div>
          <a href="?set_default=1" class="btn btn-outline-warning">Сбросить настройки формы</a>
          <button type="submit" class="btn btn-outline-primary">Добавить последовательность</button>
        </form>

        <div class="starter-template text-center py-3 px-2">
          <h1>Список моих последовательностей</h1>
        </div>

        <table class="table table-bordered border-primary">
          <thead>
            <tr>
              <th scope="col"><a href="#" class="btn btn-outline-danger">Удалить</a></th>
              <th scope="col">1. XXXXXXXXXXX</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">1</th>
              <td>Mark</td>
            </tr>
            <tr>
              <th scope="row">2</th>
              <td>Jacob</td>
            </tr>
            <tr>
              <th scope="row">3</th>
              <td>Jacob</td>
            </tr>
          </tbody>
        </table>



      <?php
        $db->close();
        unset($db);
      ?> 


      </main><!-- /.container --><script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
      <!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-179173139-1"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-179173139-1');
      </script>

    <!-- Вариант 1: Bootstrap в связке с Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Вариант 2: Bootstrap JS отдельно от Popper
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>
