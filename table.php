<?php

$day = date('N');

function getJohnSchedule($day) {
    if ($day == 1 || $day == 3 || $day == 5) {
        return "8:00-12:00";
    } else {
        return "Нерабочий день";
    }
}

function getJaneSchedule($day) {
    if ($day == 2 || $day == 4 || $day == 6) {
        return "12:00-16:00";
    } else {
        return "Нерабочий день";
    }
}

function getSchedule($name) 
{
    $localDay = date('N'); 

    switch ($name) 
    {
        case "John Styles":
            switch ($localDay) 
            {
                case 1:
                case 3:
                case 5:
                    return "8:00-12:00";

                default:
                    return "Нерабочий день";
            }

        case "Jane Doe":
            switch ($localDay) 
            {
                case 2:
                case 4:
                case 6:
                    return "12:00-16:00";

                default:
                    return "Нерабочий день";
            }

        default:
            return "Сотрудник не найден";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab3 table task blabla</title>
</head>
<body>

    <table border="1" cellpadding="8">
        <tr>
            <th>№</th>
            <th>Фамилия Имя</th>
            <th>График работы</th>
        </tr>
        <tr>
            <td>1</td>
            <td>John Styles</td>
            <td><?php echo getJohnSchedule($day); ?></td>
        </tr>
        <tr>
            <td>2</td>
            <td>Jane Doe</td>
            <td><?php echo getJaneSchedule($day); ?></td>
        </tr>
    </table>

    <br/>

    <table border="1" cellpadding="8">
        <tr>
            <th>№</th>
            <th>Фамилия Имя</th>
            <th>График работы</th>
        </tr>
        <tr>
            <td>1</td>
            <td>John Styles</td>
            <td><?php echo getSchedule("John Styles"); ?></td>
        </tr>
        <tr>
            <td>2</td>
            <td>Jane Doe</td>
            <td><?php echo getSchedule("Jane Doe"); ?></td>
        </tr>
    </table>

</body>
</html>