    <div>
        <?php
            $client = new nusoap_client($wsdl, true);
            $proxy = $client->getProxy();
            $temp = $proxy->GetToken($username, $password);
            $token = $temp;
            //print $result; // token digunakan untuk request berikutnya
        
            isset($_GET['table'])?$table=$_GET['table']:$table='mahasiswa';
            
            $temp_dir = $proxy->GetDictionary($token, $table);
            $result = $proxy->GetRecord($token, $table, $filter);
            
        
            $i=0;
            echo "<table class=\"table table-hover table-striped\">
                    <thead>
                        <tr>
                            <td>#</td>";
                            foreach ($temp_dir as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $key2 => $value2) {
                                        echo "<td>".$value2['column_name']."</td>";
                                    }
                                }
                            }
                    echo "</tr>
                    </thead>
                    <tbody>";
                    foreach($result as $key => $value) {
                        if (is_array($value)) {
                            echo "<tr>
                                    <td>".++$i."</td>";
                                    foreach ($temp_dir as $temp_key => $temp_value) {
                                        if (is_array($temp_value)) {
                                            foreach ($temp_value as $temp_key2 => $temp_value2) {
                                                if (isset($value[$temp_value2['column_name']])) {
                                                    $temp_isi = $value[$temp_value2['column_name']];
                                                } else {
                                                    $temp_isi = "";
                                                }
                                                    echo "<td>".$temp_isi."</td>";
                                                }
                                            }
                                        }
                                        echo "</tr>";
                                    }
                        }
            echo "</tbody></table>";
        
        ?>
    </div>

