    <div>
		<?php
			$client = new nusoap_client($wsdl, true);
			$proxy = $client->getProxy();
			$temp = $proxy->GetToken($username, $password);
			$token = $temp;
			//print $result; // token digunakan untuk request berikutnya
		
			$result = $proxy->ListTable($token);
			$i=0;
			echo "<table class=\"table table-hover table-striped\">
					<thead>
    					<tr>
    						<td>#</td>
    						<td>Nama Table</td>
    						<td>Jenis</td>
    						<td>Keterangan</td>
    						<td width=\"30%\">Service</td>
    					</tr>
					</thead>
					<tbody>";
					foreach($result as $key => $value) {
						if (is_array($value)) {
							foreach ($value as $key2 => $value2) {
								echo "<tr>
											<td>".++$i."</td>
											<td>".$value2['table']."</td>
											<td>".$value2['jenis']."</td>
											<td>".$value2['keterangan']."</td>
											<td>
											     <a href=\"getdictionary.php?table=".$value2['table']."\">Get Dictionary</a> - 
											     <a href=\"getrecord.php?table=".$value2['table']."\">Get Record</a> - 
											     <a href=\"getrecordset.php?table=".$value2['table']."\">Get Recordset</a>
											</td>
										</tr>";
							}
						}
					}
			echo "   </tbody></table>";
		
		?>
    </div>


