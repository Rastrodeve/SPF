<?php
$link = mssql_connect('172.20.134.7', 'yupak', 'yupak');

if (!$link || !mssql_select_db('YP_PRUEBA', $link)) {
    die('No se puede conectar o seleccionar una base de datos!');
}
// $sql = mssql_query('SELECT * FROM YP_FAC_SERVICE');
// while($row = mssql_fetch_array($sql)){
//   echo utf8_encode($row["Descripcion"])."<br>";
// }
//
// $fecha = date("m/d/Y H:i:s");
// $sql3 = mssql_query("INSERT INTO
// YP_EGDATA_FAC(Codigo_Empresa,Localidad,Tipo,Documento,Caja,Num_Documento, Codigo_Id,Codigo_Id2,Codigo_Id3,Detalle1,Detalle2,Detalle3,Fecha,Valor1, Valor2, Valor3,Valor4,Estado)
// VALUES (8,1,'C',0,1020,1004,2,1,'1726215757','Jairo Castilllo','Prueba3','Correo | Telefono','$fecha',10,1,10,1,0)");
// // $new_Num_Documento=0;
// $sql2 = mssql_query('SELECT TOP 1 Num_Documento FROM YP_EGDATA_FAC ORDER BY Fecha DESC');
// if($row = mssql_fetch_array($sql2)){
//   $new_Num_Documento = $row["Num_Documento"] + 1;
// }else{
//   echo "Tabla Vacia<br>";
//   $new_Num_Documento = $new_Num_Documento + 1;
// }
// echo "$new_Num_Documento";
// try {
//   $fecha = date("m/d/Y H:i:s");
//   $sql3 = mssql_query("INSERT INTO
//   YP_EGDATA_FAC(Codigo_Empresa,Localidad,Tipo,Documento,Caja,Num_Documento, Codigo_Id,Codigo_Id2,Codigo_Id3,Detalle1,Detalle2,Detalle3,Fecha,Valor1, Valor2, Valor3,Valor4,Estado)
//   VALUES (8,1,'C',0,1020,$new_Num_Documento,2,7,'1726215757','Jairo Castilllo','Prueba3','Correo | Telefono','$fecha',10,1,10,1,0)");
//   echo "<br>$sql3 ";
// } catch (Exception $e) {
//   echo "$e";
// }
//truncar tabla 
// $sql2 = mssql_query('TRUNCATE TABLE YP_EGDATA_FAC ');
//
  echo "<table border='1' ><thead>
  <th>Codigo_Empresa</th>
  <th>  Localidad</th>
  <th>  Tipo</th>
  <th>  Documento</th>
  <th>  Caja</th>
  <th>  Num_Documento</th>
  <th>  Codigo_Id</th>
  <th>  Codigo_Id2</th>
  <th>  Codigo_Id3</th>
  <th>  Detalle1</th>
  <th>  Detalle2</th>
  <th>  Detalle3</th>
  <th>  Fecha</th>
  <th>  Valor1</th>
  <th>  Valor2</th>
  <th>  Valor3</th>
  <th>  Valor4</th>
  <th>  Estado</th>
  </thead><tbody>";
  $sql2 = mssql_query('SELECT * FROM YP_EGDATA_FAC ORDER BY Num_Documento DESC');
  while($row = mssql_fetch_array($sql2)){
    echo "<tr>
    <td>".$row["Codigo_Empresa"]."</td>
    <td>".$row["Localidad"]."</td>
    <td>".$row["Tipo"]."</td>
    <td>".$row["Documento"]."</td>
    <td>".$row["Caja"]."</td>
    <td>".$row["Num_Documento"]."</td>
    <td>".$row["Codigo_Id"]."</td>
    <td>".$row["Codigo_Id2"]."</td>
    <td>".$row["Codigo_Id3"]."</td>
    <td>".$row["Detalle1"]."</td>
    <td>".$row["Detalle2"]."</td>
    <td>".$row["Detalle3"]."</td>
    <td>".$row["Fecha"]."</td>
    <td>".$row["Valor1"]."</td>
    <td>".$row["Valor2"]."</td>
    <td>".$row["Valor3"]."</td>
    <td>".$row["Valor4"]."</td>
      <td>".$row["Estado"]."</td>
    </tr>";
  }
  echo "</tbody></table>";


?>
