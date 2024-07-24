<?php

class SSP {
    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data    Data from the SQL get
     * @param bool  $isJoin  Determine the JOIN/complex query or simple one
     *
     * @return array Formatted data in a row based format
     */
    public static function data_output($columns, $data, $isJoin = false)
    {
        $out = [];

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = [];

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $isJoin ? 
                        $column['formatter']($data[$i][$column['field']], $data[$i]) : 
                        $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $isJoin ? 
                        $data[$i][$columns[$j]['field']] : 
                        $data[$i][$columns[$j]['db']];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @return string SQL limit clause
     */
    public static function limit($request, $columns)
    {
        $limit = '';

        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @param  bool  $isJoin  Determine the JOIN/complex query or simple one
     *
     * @return string SQL order by clause
     */
    public static function order($request, $columns, $isJoin = false)
    {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = [];
            $dtColumns = self::pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] == 'true' && $columnIdx != 0 && $columnIdx != 1 && $columnIdx != 2 && $columnIdx != 3) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
                    $orderBy[] = ($isJoin ? $column['db'] : '`' . $column['db'] . '`') . ' ' . $dir;
                }
            }

            if (count($orderBy)) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }

        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @param  array $bindings Array of values for PDO bindings, used in the sql_exec() function
     * @param  bool  $isJoin  Determine the JOIN/complex query or simple one
     *
     * @return string SQL where clause
     */
    public static function filter($request, $columns, &$bindings, $isJoin = false)
    {
        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $globalSearch[] = ($isJoin ? $column['db'] : "`" . $column['db'] . "`") . " LIKE " . $binding;
                }
            }
        }

        // Individual column filtering
        for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];

            $str = $requestColumn['search']['value'];

            if ($requestColumn['searchable'] == 'true' && $str != '') {
                $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                $columnSearch[] = ($isJoin ? $column['db'] : "`" . $column['db'] . "`") . " LIKE " . $binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilizing the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $sql_details SQL connection details - see sql_connect()
     * @param  string $table SQL table to query
     * @param  string $primaryKey Primary key of the table
     * @param  array $columns Column information array
     * @param  string $joinQuery Join query String
     * @param  string $extraWhere Where query String
     * @param  string $groupBy groupBy by any field will apply
     * @param  string $having HAVING by any condition will apply
     *
     * @return array  Server-side processing response array
     */
    public static function simple($request, $sql_details, $table, $primaryKey, $columns, $joinQuery = null, $extraWhere = '', $groupBy = '', $having = '')
    {
        $bindings = [];
        $db = self::sql_connect($sql_details);

        // Menyesuaikan order berdasarkan konfigurasi
        $order = self::order($request, $columns, $joinQuery);
        if (empty($order)) {
            $order = 'ORDER BY ' . $columns[4]['db'] . ' ASC';  // Default sort by 5th column ascending
        }

        $limit = self::limit($request, $columns);
        $where = self::filter($request, $columns, $bindings, $joinQuery);

        if ($extraWhere) {
            $extraWhere = ($where) ? ' AND ' . $extraWhere : ' WHERE ' . $extraWhere;
        }

        $groupBy = ($groupBy) ? ' GROUP BY ' . $groupBy . ' ' : '';
        $having = ($having) ? ' HAVING ' . $having . ' ' : '';

        // Main query
        if ($joinQuery) {
            $col = self::pluck($columns, 'db', $joinQuery);
            $query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $col) . "
                 $joinQuery
                 $where
                 $extraWhere
                 $groupBy
                 $having
                 $order
                 $limit";
        } else {
            $query = "SELECT SQL_CALC_FOUND_ROWS `" . implode("`, `", self::pluck($columns, 'db')) . "`
                 FROM `$table`
                 $where
                 $extraWhere
                 $groupBy
                 $having
                 $order
                 $limit";
        }

        $data = self::sql_exec($db, $bindings, $query);

        // Data set length after filtering
        $resFilterLength = self::sql_exec($db, "SELECT FOUND_ROWS()");
        $recordsFiltered = $resFilterLength[0]['FOUND_ROWS()'];

        // Total data set length
        $resTotalLength = self::sql_exec($db, "SELECT COUNT(`{$primaryKey}`) AS total FROM `$table`");
        $recordsTotal = $resTotalLength[0]['total'];

        return [
            "draw"            => isset($request['draw']) ? intval($request['draw']) : 0,
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => self::data_output($columns, $data, $joinQuery)
        ];
    }

    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return PDO Database connection handle
     */
    public static function sql_connect($sql_details)
    {
        try {
            $db = new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset=utf8mb4",
                $sql_details['user'],
                $sql_details['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            self::fatal(
                "An error occurred while connecting to the database. " .
                "The error reported by the server was: " . $e->getMessage()
            );
        }

        return $db;
    }

    /**
     * Execute an SQL query on the database
     *
     * @param  PDO   $db  Database handler
     * @param  array $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string $sql SQL query to execute.
     * @return array       Result from the query (all rows)
     */
    public static function sql_exec($db, $bindings, $sql = null)
    {
        // Argument shifting
        if ($sql === null) {
            $sql = $bindings;
        }

        $stmt = $db->prepare($sql);

        // Bind parameters
        if (is_array($bindings)) {
            foreach ($bindings as $key => $value) {
                $stmt->bindValue($value['key'], $value['val'], $value['type']);
            }
        }

        // Execute
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            self::fatal("An SQL error occurred: " . $e->getMessage());
        }

        // Return all
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    public static function fatal($msg)
    {
        echo json_encode(["error" => $msg]);
        exit(1);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    public static function bind(&$a, $val, $type)
    {
        $key = ':binding_' . count($a);

        $a[] = [
            'key' => $key,
            'val' => $val,
            'type' => $type
        ];

        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param  array  $a    Array to get data from
     * @param  string $prop Property to read
     * @param  bool   $isJoin  Determine the JOIN/complex query or simple one
     * @return array        Array of property values
     */
    public static function pluck($a, $prop, $isJoin = false)
    {
        $out = [];

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = ($isJoin && isset($a[$i]['as'])) ? $a[$i][$prop] . ' AS ' . $a[$i]['as'] : $a[$i][$prop];
        }

        return $out;
    }
}