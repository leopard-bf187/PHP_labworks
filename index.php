<?php

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) 
{
   $a += 10; $b += 5;
   echo "Шаг $i: a = $a, b = $b <br/>";
}

echo "Конец цикла for: a = $a, b = $b <br/>";


$a = 0;
$b = 0;
$i = 0;

while ($i <= 5) 
{
    $a += 10; $b += 5;
    echo "Шаг $i: a = $a, b = $b <br/>";
    $i++;
}

echo "Конец цикла while: a = $a, b = $b <br/>";


$a = 0;
$b = 0;
$i = 0;

do 
{
    $a += 10; $b += 5;
    echo "Шаг $i: a = $a, b = $b <br/>";
    $i++;
} 
while ($i <= 5);

echo "Конец цикла do-while: a = $a, b = $b  <br/>";

?>