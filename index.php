<!doctype html>
<html lang="ru">
  <head>
    <!-- Обязательные метатеги -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>belky</title>
    <style>
      .scrolling-wrapper{
        overflow-x: auto;
      }
    </style>

  </head>
  <body>

      <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php">Belky</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto mb-2 mb-md-0">
              <li class="nav-item active">
                <a class="nav-link" aria-current="page" href="index.php">Мои последовательности</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">База патентов</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>

        <div class="starter-template text-center py-5 px-1 mt-3">
          <h1>Добавить последовательность</h1>
        </div>

        <form method="post">
          <div class="scrolling-wrapper row flex-row flex-nowrap p-3 mb-4">
            <table class="table table-bordered border-primary">
              <tbody>
                <?php
                  include_once 'comb.php';
                  // CREATE TABLE m_params ( param_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, mcol INTEGER NOT NULL);
                  // INSERT INTO m_params ( mcol ) VALUES ( 30 );
                  ini_set('display_errors', 1);
                  ini_set('display_startup_errors', 1);
                  error_reporting(E_ALL);
                  $db = new SQLite3("my.db", SQLITE3_OPEN_READWRITE);

                  function set_def($mdb)
                  {
                    $mdb->exec("UPDATE m_params SET mcol=30 WHERE param_id=1;");
                    $mdb->exec("DELETE FROM mcols WHERE col>29;");
                    for ($i = 0; $i < 30; $i++)
                      $mdb->exec("UPDATE mcols SET crow=1 WHERE col=".$i.";");
                  }                  

                  // OBR GETS
                  if(isset($_GET["cols"])){
                    if((int)$_GET["cols"] < 0)
                    {
                      $c = abs((int)$_GET["cols"]) - 2;
                      $db->exec("UPDATE m_params SET mcol=".$c." WHERE param_id=1;");
                      //$db->exec("INSERT INTO mcols (col, crow) VALUES (".($_GET["cols"]-1).", 1);");
                      $db->exec("DELETE FROM mcols WHERE col =".$c);
                    }
                    else
                    {
                      $db->exec("UPDATE m_params SET mcol=".$_GET["cols"]." WHERE param_id=1;");
                      $db->exec("INSERT INTO mcols (col, crow) VALUES (".($_GET["cols"]-1).", 1);");
                    }
                  }
                  if(isset($_GET["set_default"]))
                    set_def($db);                  
                  if(isset($_GET["acol"]))
                    $db->exec("UPDATE mcols SET crow = crow + 1 WHERE col=".$_GET["acol"].";");
                  if(isset($_GET["del_pos"])){
                    $db->exec("DELETE FROM m_posled_0 WHERE posled_id=".$_GET["del_pos"].";");
                    $db->exec("DELETE FROM m_posled_x WHERE nposled0=".$_GET["del_pos"].";");
                  }

                  // ADD POSLED
                  $res = $db->querySingle("SELECT mcol FROM m_params;");
                  $fadd = 0;
                  if(isset($_POST["0X0"]))
                  { 
                    $str_posled = "";
                    for($i=0; $i < $res; $i++)
                      if(isset($_POST["0X".$i]))
                        $str_posled .= $_POST["0X".$i];
                    $db->exec("INSERT INTO m_posled_0 (posled) VALUES (\"".$str_posled."\")");
                    $vect_p = [];
                    $max_row = $db->querySingle("SELECT MAX(crow) as max FROM mcols");
                    for($i=0; $i < $res; $i++){
                      $vect = [];
                      if(isset($_POST["0X".$i]))
                        $vect[] = $_POST["0X".$i];
                      for($nrow = 1; $nrow < $max_row; $nrow++){
                        if(isset($_POST[$nrow."X".$i]))
                          $vect[] = $_POST[$nrow."X".$i];
                      }
                      $vect_p[] = $vect;
                    }
                    $comb = Combinations($vect_p);
                    foreach ($comb as $vcomb)
                    {
                      $str_val = "";
                      foreach ($vcomb as $val)
                        $str_val .= $val;
                      $db->exec("INSERT INTO m_posled_x (nposled0, posled) VALUES (".$db->querySingle("SELECT MAX(posled_id) FROM m_posled_0").",\"".$str_val."\")");
                    }
                    set_def($db);
                    $fadd = 1;
                  }

                  // GEN FORM ADD POSLED
                  $res = $db->querySingle("SELECT mcol FROM m_params;");
                  echo "<tr><td></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td class=\"text-center\">X".($i+1)."</td>";
                  echo "<td></td></tr><tr>";
                  echo "<td><a href=\"?cols=-".($res+1)."\" class=\"btn btn-outline-primary\">-</a></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td><input type=\"text\" name=\"0X".$i."\" class=\"form-control input-sm\" required></td>";
                  echo "<td><a href=\"?cols=".($res+1)."\" class=\"btn btn-outline-primary\">+</a></td></tr>";
                  for($nrow=1; $nrow < $db->querySingle("SELECT MAX(crow) as max FROM mcols"); $nrow++){
                    echo "<tr><td></td>";
                    for ($i = 0; $i < $res; $i++) {
                      if($db->querySingle("SELECT crow FROM mcols WHERE col=".$i.";") > $nrow)
                        echo "<td><input type=\"text\" name=\"".$nrow."X".$i."\" class=\"form-control input-sm\" required></td>";
                      else
                        echo "<td></td>";
                    }
                    echo "<td></td></tr>";
                  }
                  echo "<tr><td></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td><a href=\"?acol=".$i."\" class=\"btn btn-outline-primary\">+</a></td>";
                  echo "<td></td></tr>";
                ?>            
              </tbody>
            </table>
          </div>


      <main class="container">
          <a href="?set_default=1" class="btn btn-outline-warning">Сбросить настройки формы</a>
          <button type="submit" class="btn btn-outline-primary">Добавить последовательность</button>
        </form>

        <?php
          if($fadd)
          {
            echo "<div class=\"alert alert-success d-flex align-items-center mt-3\" role=\"alert\">";
            echo "<svg class=\"bi flex-shrink-0 me-2\" width=\"24\" height=\"24\" role=\"img\" aria-label=\"Success:\"><use xlink:href=\"#check-circle-fill\"/></svg>";
            echo "<div> Последовательность успешно добавлена! </div> </div>";
            $fadd = 1;
          }
        ?>

        <div class="starter-template text-center py-3 px-2">
          <h1>Список моих последовательностей</h1>
        </div>
        <?php
          $posleds = $db->query("SELECT * FROM m_posled_0;");
          $nom0 = 1;
          while ($row = $posleds->fetchArray()) {
              echo "<table class=\"table table-bordered border-primary\">";
              echo "<thead><tr>";              
              echo "<th scope=\"col\" style=\"width:5%\"><a href=\"?del_pos={$row['posled_id']}\" class=\"btn btn-outline-danger\">Удалить</a></th>";
              echo "<th scope=\"col\">{$nom0}. {$row['posled']}</th> </tr>";
              echo "</thead>";
              echo "<tbody>";
              $posledsx = $db->query("SELECT * FROM m_posled_x WHERE nposled0 = {$row['posled_id']};");
              $nomx = 1;
              while ($rowx = $posledsx->fetchArray()) {
                echo "<tr>";
                echo "<th scope=\"row\" class=\"text-center\">{$nomx}</th>";
                echo "<td>{$rowx['posled']}</td>";
                echo "</tr>";
                $nomx++;
              }
              echo "</tbody>";
              echo "</table>";
              $nom0++;
          }
        ?>


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
