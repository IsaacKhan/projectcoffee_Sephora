<?php
    $mysqli = new mysqli("coffee-gave-me-gas.cgzqmhf3sjbn.us-east-2.rds.amazonaws.com", "root", "csc4112018", "projectcoffee");
    $result = $mysqli->query("SELECT @cur_rank, productName as Product, units as 'Units Sold', storeState as State, store_ID as Store
    from (SELECT product_ID as upc, amountSold as units_sold, store_ID, storeState,  @cur_rank := IF(@cur_state = storeState, @cur_rank + 1, 1) , 
          @cur_state := storeState,@units := amountSold as units
          from  (    select product_ID, amountSold, store_ID, storeState
                        from sales inner join store on store.ID = sales.store_ID
                        order by storeState, amountSold desc) as top_items) as rankings inner join product on upc = product.ID  where @cur_rank <= 20;");
    
        //-- Cap the ranking at the top 20 items

        $prev_state = "";
        $prev_prod = "";
        $rank = 1;

?>

<!-- QUERIES WILL BE OUTPUT -->
<!doctype html>
<html>
<body bgcolor="#E6E6FA">
<h1 align="center">What are the 20 top-selling products in each state?</h1>
<table border="1" align="center" style="line-height:25px;">
<tr>
<th>Rank</th>
<th>Product</th>
<th>Units Sold</th>
<th>State</th>
</tr>
<?php
//Fetch Data form database
if($result->num_rows > 0){
 while($row = $result->fetch_assoc())
 {
    if ($prev_state == $row["State"])
    {
        $rank++;

    }
else 
    {
        echo "<tr><td>" . "Top 20 for ". $row["State"] . "</td></tr>";
        $rank = 1;
    }

if ($rank <= 20)

# INSERT YOUR ECHO HERE
echo "<tr><td>" . $rank . "</td><td>" . $row['Product'] . "</td><td>" . $row['Units Sold'] . "</td><td>" . $row['State'] . "</td></tr>";
$prev_state = $row["State"];
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