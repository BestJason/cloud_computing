<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>

    <?php
    if (!empty($_GET['number'])) {
      $n = intval($_GET['number']);
      $numbers = getFibonacci($n);
      // write numbers into gcloud
      if (!empty($numbers)) {
        $numbers_str = join(",", $numbers);
      } else {
        $numbers_str = "";
      }
      writeFile($n, $numbers_str);
    }

    // write file
    function writeFile($number, $str) {
      $handle = fopen('gs://s3620273-ass1-task2-storage/fibonacci_'. $number. '.txt','w');
      fwrite($handle, $str);
      fclose($handle);
    }

      // get fibonacci numbers
    function getFibonacci($number) {
      $numbers = [];
      for ($i = 0; $i < $number; $i++) {
        if ($i == 0 || $i == 1) {
          $numbers[$i] = 1;
        } else {
          $numbers[$i] = $numbers[$i - 1] + $numbers[$i - 2];
        }
      }
      return $numbers;
    }

  ?>

    <form method="post">
    <input type="hidden" name="number" value="<?php echo intval($_GET['number']); ?>">
    A:<input type="number" name="A"> <br>
    B:<input type="number" name="B"> <br>
    C:<input type="number" name="C"> <br>
    <input type="submit" value="Submit" style="background-color:blue;color:white">
  </form>

  <?php

    // read file from gcloud
    function readGC($number) {
      $filename = 'gs://s3620273-ass1-task2-storage/fibonacci_'. $number. '.txt';
      $handle = fopen($filename,'r');
      $str = fread($handle, filesize($filename));
      fclose($handle);
      return $str;
    }

    // calculate total
    function calculateTotal($numbers, $a, $b, $c) {
      $s = $a + $b;
      $m = $s * $c;
      $all = array_sum($numbers);
      return $m + $all;
    }

    // calculate average
    function calculateAverage($numbers, $a, $b, $c) {
      $all = array_sum($numbers);
      $t = count($numbers);
      return round(($all + $a + $b + $c) / ($t + 3), 2);
    }

    // write average
    function writeAverage($average) {
      $handle = fopen('gs://s3620273-ass1-task2-storage/result.txt','w');
      fwrite($handle, $average);
      fclose($handle);
    }


    // handle A,B,C
    $number = $_POST['number'];
    $a = $_POST['A'];
    $b = $_POST['B'];
    $c = $_POST['C'];
    if (!empty($a) && !empty($b) && !empty($c) && !empty($number)) {
      $allNum = readGC($number);
      $all = explode(",", $allNum);
      $total = calculateTotal($all, $a, $b, $c);
      $average = calculateAverage($all, $a, $b, $c);
      writeAverage($average);
      echo "<br>Total Sum: ". $total."<br>";
      echo "Average: ". $average;
    }

  ?>

</body>
</html>
