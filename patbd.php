<!doctype html>
<html lang="ru">
  <head>
    <!-- Обязательные метатеги -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>patbd</title>
    <style>
      .scrolling-wrapper{
        overflow-x: auto;
      }
      input[type=text] {
        border: 2px solid black;
        border-radius: 4px;
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
                <a class="nav-link" href="patbd.php">База патентов</a>
              </li>
            </ul>
          </div>
          <div class="pull-right">
            <ul class="nav navbar-nav">
                <li><form method="post"><input type="hidden" name="logout" value="1"><button type="submit" class="btn navbar-btn btn-danger" name="logout" id="logout"  value="Log Out">Выход</button></form></li>
            </ul>
          </div>
        </div>
      </nav>

      <?php
        session_start();
        
        if(isset($_POST["logout"]))
          $_SESSION['auth'] = null;

        if (!empty($_POST['pass'])) {
          
          if ($_POST['pass'] == "popilich") {
            $_SESSION['auth'] = true;
          } else {
            echo "<div class=\"alert alert-danger text-center py-5 px-1 mt-3\" role=\"alert\">Не верный пароль пароль администратора</div>";
          }
        }
      ?>
      <?php 
        if (empty($_SESSION['auth']))
        {
          echo "<form method=\"post\" class=\"text-center py-5 px-1 mt-3\"> <div class=\"mb-3\">";
          echo "<label for=\"exampleInputPassword1\" class=\"form-label\">Пароль администратора</label>";
          echo "<input type=\"password\" class=\"form-control\" id=\"exampleInputPassword1\" name=\"pass\"></div>";
          echo "<button type=\"submit\" class=\"btn btn-primary\">Войти</button> </form>";
        }
      ?> 
<?php if (!empty($_SESSION['auth'])): ?>
        <div class="starter-template text-center py-5 px-1 mt-3">
          <h1>Добавить последовательность в базу патентов</h1>
        </div>
        <div class="btn-group row d-flex justify-content-center flex-nowrap mt-1" role="group" aria-label="...">
<?php endif; ?>
            <?php
              if (empty($_SESSION['auth'])) exit();
              include_once 'comb.php';
              // CREATE TABLE m_params_pat ( param_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, mcol INTEGER NOT NULL);
              // INSERT INTO m_params_pat ( mcol ) VALUES ( 30 );
              ini_set('display_errors', 1);
              ini_set('display_startup_errors', 1);
              error_reporting(E_ALL);
              $db = new SQLite3("my.db", SQLITE3_OPEN_READWRITE);              

              if(isset($_GET["reg"]))
                  $db->exec("UPDATE m_params_pat SET reg=".$_GET["reg"]." WHERE param_id=1;");

              $reg = $db->querySingle("SELECT reg FROM m_params_pat;");

              if($reg)
              {
                echo "<button type=\"button\" onclick=\"location.href='?reg=0';\" class=\"btn btn-outline-primary\">Ввести последовательность полностью</button>";
                echo "<button type=\"button\" onclick=\"location.href='?reg=1';\" class=\"btn btn-primary\" disabled>Комбинировать</button>";                
              }
              else
              {
                echo "<button type=\"button\" onclick=\"location.href='?reg=0';\" class=\"btn btn-primary\" disabled>Ввести последовательность полностью</button>";
                echo "<button type=\"button\" onclick=\"location.href='?reg=1';\" class=\"btn btn-outline-primary\">Комбинировать</button>";
              }

            ?>
        </div>

        <form method="post">
          <div class="scrolling-wrapper row flex-row flex-nowrap p-3 mb-4">
            <table class="table-bordered border-primary">
              <tbody>
                <?php

                  function set_def($mdb)
                  {
                    $mdb->exec("UPDATE m_params_pat SET mcol=30 WHERE param_id=1;");
                    $mdb->exec("DELETE FROM mcols_pat;");
                    for ($i = 0; $i < 30; $i++)
                      $mdb->exec("INSERT INTO mcols_pat (col, crow) VALUES (".$i.", 1);");
                  }

                  // OBR GETS
                  if(isset($_GET["cols"])){
                    if((int)$_GET["cols"] < 0)
                    {
                      $c = abs((int)$_GET["cols"]) - 2;
                      $db->exec("UPDATE m_params_pat SET mcol=".$c." WHERE param_id=1;");
                      $db->exec("DELETE FROM mcols_pat WHERE col =".$c);
                    }
                    else
                    {
                      $db->exec("UPDATE m_params_pat SET mcol=".$_GET["cols"]." WHERE param_id=1;");
                      $db->exec("INSERT INTO mcols_pat (col, crow) VALUES (".($_GET["cols"]-1).", 1);");
                    }
                  }
                  if(isset($_GET["set_default"]))
                    set_def($db);
                  if(isset($_GET["del_pos"])){
                    $db->exec("DELETE FROM m_posled_pat WHERE posled_id=".$_GET["del_pos"].";");
                  }
                  if(isset($_GET["acol"]))
                    $db->exec("UPDATE mcols_pat SET crow = crow + 1 WHERE col=".$_GET["acol"].";");
                  if(isset($_GET["dcol"]))
                  {
                    if($db->querySingle("SELECT crow FROM mcols_pat WHERE col=".$_GET["dcol"].";") > 1)
                      $db->exec("UPDATE mcols_pat SET crow = crow - 1 WHERE col=".$_GET["dcol"].";");
                  }

                  // ADD POSLED
                  $fadd = 0;
                  if($reg)
                  {
                    $res = $db->querySingle("SELECT mcol FROM m_params_pat;");
                    if(isset($_POST["0X0"]))
                    { 
                      $str_posled = "";
                      $vect_p = [];
                      $max_row = $db->querySingle("SELECT MAX(crow) as max FROM mcols_pat");
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
                        {
                          if (substr_count($val, 'Ø') > 0)
                            break;
                          if (substr_count($val, 'ø') > 0)
                            break;
                          $str_val .= $val;
                        }
                        if (substr_count($str_val, 'K*') > 1)
                          continue;
                        else
                          $db->exec("INSERT INTO m_posled_pat (posled) VALUES (\"".$str_val."\")");
                      }
                      set_def($db);
                      $fadd = 1;
                    }
                  }
                  else
                  {
                    if(isset($_POST["X"]))
                    {
                      $db->exec("INSERT INTO m_posled_pat (posled) VALUES (\"".$_POST["X"]."\")");
                      $fadd = 1;
                      set_def($db);
                    }
                  }

                  // GEN FORM ADD POSLED
                  if($reg)
                  {
                    $res = $db->querySingle("SELECT mcol FROM m_params_pat;");
                  echo "<tr><td></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td class=\"text-center\"><a href=\"?dcol=".$i."\" class=\"btn btn-outline-primary\">-</a></td>";
                  echo "<td></td></tr>";
                  echo "<tr bgcolor=\"#cfe2ff\"><td bgcolor=\"white\"></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td class=\"text-center\">X".($i+1)."</td>";
                  echo "<td bgcolor=\"white\"></td></tr><tr bgcolor=\"#cbccce\">";
                  echo "<td bgcolor=\"white\"><a href=\"?cols=-".($res+1)."\" class=\"btn btn-outline-primary\">-</a></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td><input type=\"text\" name=\"0X".$i."\" class=\"form-control input-sm\" required></td>";
                  echo "<td bgcolor=\"white\"><a href=\"?cols=".($res+1)."\" class=\"btn btn-outline-primary\">+</a></td></tr>";
                  for($nrow=1; $nrow < $db->querySingle("SELECT MAX(crow) as max FROM mcols_pat"); $nrow++){
                    echo "<tr bgcolor=\"#cbccce\"><td bgcolor=\"white\"></td>";
                    for ($i = 0; $i < $res; $i++) {
                      if($db->querySingle("SELECT crow FROM mcols_pat WHERE col=".$i.";") > $nrow)
                        echo "<td><input type=\"text\" name=\"".$nrow."X".$i."\" class=\"form-control input-sm\" required></td>";
                      else
                        echo "<td></td>";
                    }
                    echo "<td bgcolor=\"white\"></td></tr>";
                  }
                  echo "<tr><td></td>";
                  for ($i = 0; $i < $res; $i++)
                      echo "<td class=\"text-center\"><a href=\"?acol=".$i."\" class=\"btn btn-outline-primary\">+</a></td>";
                  echo "<td></td></tr>";
                  }
                  else
                    echo "<tr><td><input type=\"text\" name=\"X\" class=\"form-control input-sm\" required></td></tr>";
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
          <h1>Запатентованные последовательности</h1>
        </div>
        <div class="row justify-content-center">
        <?php
          $posleds = $db->query("SELECT * FROM m_posled_pat;");
          echo "<table class=\"table-bordered border-primary\">";
          echo "<thead><tr>";
          echo "<th scope=\"col\" style=\"width:5%\"></th>";
          echo "<th scope=\"col\" style=\"width:5%\">№</th>";
          echo "<th scope=\"col\"> Последовательность </th> </tr>";
          echo "</thead>";
          echo "<tbody>";
          $nom = 1;
          while ($row = $posleds->fetchArray()) {
            echo "<tr>";
            echo "<th scope=\"row\" class=\"text-center\"><a href=\"?del_pos={$row['posled_id']}\" class=\"btn btn-outline-danger\">х</a></th>";
            echo "<th scope=\"row\" class=\"text-center\">{$nom}</th>";
            echo "<td>{$row['posled']}</td>";
            echo "</tr>";
            $nom++;
          }
          echo "</tbody>";
          echo "</table>";

          $db->close();
          unset($db);
        ?>
        </div>
      </main>
      <!-- /.container --><script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-179173139-1');
      </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>
