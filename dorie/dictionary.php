    <div>
        <?php
            $client = new nusoap_client($wsdl, true);
            $proxy = $client->getProxy();
            $temp = $proxy->GetToken($username, $password);
            $token = $temp;
            //print $token; // token digunakan untuk request berikutnya

            isset($_GET['table'])?$table=$_GET['table']:$table='mahasiswa';
        
            $result = $proxy->GetDictionary($token, $table);
            $i=0;
            echo "<table class=\"table table-hover table-striped\">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Nama Kolom</td>
                            <td>Primary Key</td>
                            <td>Tipe</td>
                            <td>Not Null</td>
                            <td>Keterangan</td>
                        </tr>
                    </thead>
                    <tbody>";
                    foreach($result as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $key2 => $value2) {
                                
                                isset($value2['pk'])?$pk='Yes':$pk='';
                                isset($value2['not_null'])?$nl='not_null':$nl='null';
                               
                                echo "<tr>
                                            <td>".++$i."</td>
                                            <td>".$value2['column_name']."</td>
                                            <td>".$pk."</td>
                                            <td>".$value2['type']."</td>
                                            <td>".$nl."</td>
                                            <td>".$value2['desc']."</td>
                                        </tr>";
                            }
                        }
                    }
            echo "</tbody></table>";
        ?>
    </div>