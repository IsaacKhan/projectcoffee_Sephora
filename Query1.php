<?php
$mysqli = new mysqli("coffee-gave-me-gas.cgzqmhf3sjbn.us-east-2.rds.amazonaws.com", "root", "csc4112018", "projectcoffee");
$result = $mysqli->query("SELECT @cur_rank, productName as Product, units as 'Units Sold', storeName as Store, store_ID
from (select product_ID as upc, amountSold as units_sold, store_ID, storeName,
            -- Leveraged mysql session variables to track ranking 
            -- If the store_ID is = to the previous one than increase the rank, otherwise start back at 1
              @cur_rank := IF(@store = store_ID, @cur_rank + 1, 1) as Rank, 
              @store := store_ID,
              @units := amountSold as units
                -- In order to get the correct ranking order a Subquery of each stores products sales in descending order is needed
                from  (    select product_ID, amountSold, store_ID, storeName
                            from sales inner join store on store.ID = sales.store_ID
                            order by store_ID, amountSold desc) as top_items
) as rankings inner join product on upc = product.ID
-- Cap the ranking at the top 20 items
where @cur_rank <= 20;");

$prev_store = "";
$prev_prod = "";
$rank = 1;
?>

<!doctype html>
<html>
<body bgcolor="#E6E6FA">
<h1 align="center">What are the 20 top-selling products at each store?</h1>
<table border="1" align="center" style="line-height:25px;">
<tr>
<th>Rank</th>
<th>Product</th>
<th>Units Sold</th>
<th>Store</th>
</tr>
<?php
//Fetch Data form database
if($result->num_rows > 0)
{
  while($row = $result->fetch_assoc())
  {
    if ($prev_store == $row["store_ID"])
      $rank++;
    else 
    {
      echo "<tr><td>" . "Top 20 for ". $row["Store"]. " ". $row["store_ID"] . "</td></tr>";

      $rank = 1;
    }

    #echo $rank . ".) " . $row["Product"] . " " . $row["Units Sold"]  . " " . $row["Store"] ." <br>"; 
    echo "<tr><td>" . $rank . "</td><td>" . $row['Product'] . "</td><td>" . $row['Units Sold'] . "</td><td>" . $row['Store'] . "</td></tr>";
    
    $prev_store = $row["store_ID"];
 
  }
}
else
{
 ?>
 <tr>
 <th colspan="2">There's No data found!!!</th>
 </tr>
 <?php
}
?>
</table>
</body>
</html>

