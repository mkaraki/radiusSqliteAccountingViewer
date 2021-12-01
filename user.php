<?php
require_once('init.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Per User Accounting Data</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end m-2">
        <a href="javascript:void(0)" class="btn btn-primary" role="button" onclick="download_table_as_csv('user');">Download</a>
    </div>
    <table class="table table-striped table-hover" id="user">
        <thead>
            <tr>
                <th>Session time</th>
                <th>User</th>
                <th>Download</th>
                <th>Upload</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $wheres = [];
                if (isset($_GET['before']) && is_numeric($_GET['before'])) $wheres[] = "acctstarttime < {$v($_GET['before'])}";
                if (isset($_GET['after']) && is_numeric($_GET['after'])) $wheres[] = "acctstarttime > {$v($_GET['after'])}" ;
                if (isset($_GET['user']) && !empty($_GET['user'])) $wheres[] = "username = '{$v(SQLite3::escapeString($_GET['user']))}'";
                if (isset($_GET['client']) && !empty($_GET['client'])) $wheres[] = "callingstationid = '{$v(SQLite3::escapeString($_GET['client']))}'";
                if (isset($_GET['reporter']) && !empty($_GET['reporter'])) $wheres[] = "nasipaddress = '{$v(SQLite3::escapeString($_GET['reporter']))}'";

                $sql_add = '';
                if (count($wheres) > 0) $sql_add .= ' WHERE ' . implode(' AND ', $wheres);
                $sql_add .= ' GROUP BY username';

                $res = $sqlite->query("SELECT SUM(acctsessiontime), username, SUM(acctoutputoctets), SUM(acctinputoctets) FROM radacct" . $sql_add);

                while ($row = $res -> fetchArray())
                {
                    print '<td>' . uptime($row['SUM(acctsessiontime)']) . '</td>';
                    if (!$hideuser) print '<td>' . $row['username'] . '</td>'; else print $hide_td;
                    print '<td>' . human_bytes($row['SUM(acctoutputoctets)'], true) . '</td>';
                    print '<td>' . human_bytes($row['SUM(acctinputoctets)'], true) . '</td>';
                    //foreach ($row as $k => $v)
                    //{
                    //    print '<td>' . $k . ' => ' . $v . '</td>';
                    //}
                    print '</tr>';
                }
            ?>
        </tbody>
    </table>
    <script src="script.js"></script>
</body>

</html>

<?php closesession(); ?>