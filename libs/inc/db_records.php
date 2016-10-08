<?php 

class DB extends PDO {

	public static $dbc;

	public function __construct($DRIVER,$HOST,$USERNAME,$PASSWORD,$NAME) {

       	try {
       		parent::__construct("$DRIVER:host=$HOST;dbname=$NAME;charset=utf8", $USERNAME, $PASSWORD);
       		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
       		$dbc = $this;
       	} catch (exception $e) {
       		die("<p style='font-size: 2.5em;'>Error Establishing Database Connection <b>:/</b> </p>");
       	}
    }
	
	public function tablesNames() {
	
		###	Returns: The names of all the tables in it.
		
		# querying for data.
		$result = $this->query("SHOW TABLES");
		
		$tables = [];

		# Getting all the tables with a loop and appending them to [array: table].
		while($row = $result->fetch(PDO::FETCH_NUM)){
			# row[0] is the name of the table.
			$name = $this->strip_str($row[0]);
			
			array_push($tables, $name);
		}

		return($tables);
	}

	public function strip_str($str) {
		### Takes a string.
		### Returns the same string with dashes and underscores stripped out.
		
		# strrpos returns the position of the underscore.
		# of False if it doesn't exist, 
		# so if it was a number then it evaluates to True in the IF statement
		if (strrpos($str, "-")) {
			$str = implode(" ",explode("-", $str));
		}
		if (strrpos($str, "_")) {
			$str = implode(" ",explode("_", $str));
		}
		return $str;
	}

	function lookupRowId($table, $columnToSearchIn,$searchString) {
		$query = $this->prepare("SELECT id FROM `table` WHERE $columnToSearchIn LIKE '%$searchString%' LIMIT 1");

		$query->execute();

		return $query->fetch();
	}

	function getColumnNames($TABLE_NAME) {
		### Takes: query result like -> "SELECT * FROM table_name."
		### Returns: - names of columns in that table in an array.
		###          - additionaly: it also gets the length of that column.
		###							for <input> use if wanted.
		try {
			$result = $this->query("SELECT * FROM $TABLE_NAME");

		} catch (exception $e) {
			echo "{$e->getMessage()}";
		} 
		
		$columns = [];

		$row = $result->fetch(PDO::FETCH_ASSOC);
		
		foreach ($row as $key => $value) {
			$columns[count($columns)] = $key;
		}	

		return $columns;
	}

	function INSERT($table, $values) {  
		### Takes: - str: table name.
		### 	   - str: the variables .

		$placeholders = [];
		foreach ($values as $value) {
			array_push($placeholders, '?');
		}
		$placeholders = implode(',', $placeholders);

		# Using arrays to append the column names and the values.
		$cols = [];
		$values = [];
		
		foreach ($formVars as $key => $value) {
			# because you can only get the TIMESTAMP when 'date' column is not passed.
			# so we only work on the columns that are not 'date'. 
			if($key != 'date') {
				$cols[sizeof($cols)] = $key;
				$values[sizeof($values)] = "'" . $value . "'" ;
			}
		}

		# Then the columns and values are imploded with ',' to match the sql syntax
		$cols = implode(",",$cols);
		$values = implode(",",$values);

		# Using the created column names and values to insert data in the database. 
		$sql = "INSERT INTO $table ($cols) VALUES ($values)";
		mysqli_query($dbc, $sql);
		
		# Only echoes something out when there is an error.
		echo mysqli_error($dbc);
	}

	function echoTableOfRecords($sqlQuery, $columnsWanted,$tableIdInDOM, $tableNameInDB = "") {
	## $ColumnsToDisplay is an array 
	## of (column_name => column_name_in_the_DB)

	$query = $this->query($sqlQuery);

	$data = $query->fetchAll();
	?>
	<table class="records" id="<?php echo $tableIdInDOM; ?>">
	<input type="hidden" name="table" value="<?php echo $tableNameInDB ?>">
		<thead>
			<tr>
				<?php 
				foreach ($columnsWanted as $displayCol) {
					echo "<th>$displayCol</th>";
				}
				 ?>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($data as $row) {
				echo "<tr>";
				
				foreach ($columnsWanted as $DbCol => $displayCol) {
					$DbCol = (is_numeric($DbCol)) ? $displayCol : $DbCol;
					echo "<td>".$row->$DbCol."</td>";
				}

				foreach ($row as $column => $value) {
					echo "<input type='hidden' name='$column' value='$value'>";
				}
				echo "</tr>\n";
			}
			 ?>
		</tbody>
	</table>
	<?php
	}
}

?>