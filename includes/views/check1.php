<?php 

defined( 'ABSPATH' ) or exit;

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  
<script type="text/javascript">
	jQuery( function( $ ) {
		$("#footer-thankyou").html("If you like <strong>WooCommPlugin</strong> please leave us a <a href='#'>★★★★★</a> rating. A huge thank you in advance!");
	});
</script>

<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e( 'Taxes', 'woocommplugin' ); ?></h2>
	<h2 class="nav-tab-wrapper">
	<?php
	foreach ($settings_tabs as $tab_slug => $tab_title ) {
		$tab_link = esc_url("?page=woocommplugin_tax_sample_submenu&tab={$tab_slug}");
		printf('<a href="%1$s" class="nav-tab nav-tab-%2$s %3$s">%4$s</a>', $tab_link, $tab_slug, (($active_tab == $tab_slug) ? 'nav-tab-active' : ''), $tab_title);
	}
	?>
	</h2>
    <form id="tax_sample_form">
        <?php
			// do_action( 'woocommplugin_tax_sample_page', $active_tab, $active_section );
			// if ( has_action( 'woocommplugin_tax_sample_page_'.$active_tab )) {
				
			// 	do_action( 'woocommplugin_tax_sample_page_'.$active_tab, $active_section );
				
			// } else {
				
			// 	do_action( 'woocommplugin_tax_sample_page_'.$active_tab, $active_section );
				
			// }
		?>
        <label for="shipping_address">Choose Shipping state:</label><br>

        <select name="shipping_address" id="shipping_address">
            <option value="Uttar Pradesh">Uttar Pradesh</option>
            <option value="Karnataka">Karnataka</option>
            <option value="Gujrat">Gujrat</option>
            <option value="West Bengal">West Bengal</option>
        </select>  
        <br />
        <input type="file" id="csvFile" accept=".csv" />
        <br />
        <!-- <input type="submit" value="Submit" /> -->
        <button type="button" id="submit_data">Load</buttton>
        <div id="employee_table">
        </div>
    </form>
</div>
<script>
$(document).ready(function(){
 $('#submit_data').click(function(){
  $.ajax({
   url:"..\\wp-content\\plugins\\WooCommPlugin\\public\\HSN_codes.csv",
   dataType:"text",
   success:function(data)
   {
    var employee_data = data.split(/\r?\n|\r/);
    var table_data = '<table class="table table-bordered table-striped">';
    for(var count = 0; count<employee_data.length; count++)
    {
     var cell_data = employee_data[count].split(",");
     table_data += '<tr>';
     for(var cell_count=0; cell_count<cell_data.length; cell_count++)
     {
      if(count === 0)
      {
       table_data += '<th>'+cell_data[cell_count]+'</th>';
      }
      else
      {
       table_data += '<td>'+cell_data[cell_count]+'</td>';
      }
     }
     table_data += '</tr>';
    }
    table_data += '</table>';
    $('#employee_table').html(table_data);
   }
  });
 });
 
});
</script>
<!-- <script>
    const myForm = document.getElementById("tax_sample_form");
    // document.getElementById("csvFile").defaultValue = 
    const csvFile = document.getElementById("csvFile");
    var tax_rate = 0.0;
    var SGST = 0.0;
    var CGST = 0.0;
    var IGST = 0.0;

    function csvToArray(str, delimiter = ",") {

        // slice from start of text to the first \n index
        // use split to create an array from string by delimiter
        const headers = str.slice(0, str.indexOf("\n")).split(delimiter);

        // slice from \n index + 1 to the end of the text
        // use split to create an array of each csv value row
        const rows = str.slice(str.indexOf("\n") + 1).split("\n");

        // Map the rows
        // split values from each row into an array
        // use headers.reduce to create an object
        // object properties derived from headers:values
        // the object passed as an element of the array
        const arr = rows.map(function (row) {
            const values = row.split(delimiter);
            const el = headers.reduce(function (object, header, index) {
                object[header] = values[index];
                return object;
            }, {});
            return el;
        });

        // return the array
        return arr;
    }

    myForm.addEventListener("submit", function (e) {
        e.preventDefault();
        
        var input = new File([""], "HSN_codes.csv", {type:"csv"});
        if(csvFile.files.length>0)
        {
            input = csvFile.files[0];
        }
        
        const reader = new FileReader();
        
        var shipping_address;

        reader.onload = function (e) {
            const text = e.target.result;
            const data = csvToArray(text);
            
            shipping_address  = document.getElementById("shipping_address").value;
            for(let i=0;i<data.length;i++)
            {
                
                if(data[i]['HSNCode']==='0403')      //code value according to cart item
                {
                    tax_rate = 2*data[i]['SGSTRate'];
                    if(shipping_address == 'Uttar Pradesh')
                    {
                        SGST = tax_rate/2;
                        CGST = tax_rate/2;
                        IGST = 0;
                    }
                    else
                    {
                        SGST = 0;
                        CGST = 0;
                        IGST = tax_rate;
                    }
                    window.alert('Tax distribution : \nSGST' + SGST + '\nCGST' + CGST + '\nIGST' + IGST);
                }
            }
        };

        reader.readAsText(input);
    });

</script> -->
<?php

// echo get_option( 'woocommerce_store_address', '' );
// echo get_option( 'woocommerce_store_address_2', '' );
$state = wc_get_base_location();
// echo $state['state'];
// echo $state['country'];

?>
<?php  
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        // echo '<br /><a href="'.get_permalink().'">' . woocommerce_get_product_thumbnail().' '.get_the_title().'</a>';
        // echo $product['title'];
        $hsn_code = $product->get_meta('hsn_prod_id');
    endwhile;

    // echo 'hi';
    global $woocommerce, $post;

$order = new WC_Order('19');

//to escape # from order id 

// $order_id = trim(str_replace('#', '', $order->get_order_number()));
// echo $order;
// echo $order_id;

    wp_reset_query();
?>
<?php
// add_filter( 'woocommerce_cart_taxes_total', function($total, $compound, $display, $mycart){
    
// 		echo '<br>'.$total.' '.$compound.' '.$display.' '.$mycart.'<br>';

// 		return;
// }, 10, 4 );

// if(has_action( 'woocommerce_cart_taxes_total'))
//     echo "yesssss";
// add_filter( 'woocommerce_calculated_totals', 'change_calculated_total', 10, 2 );
// function change_calculated_total( $total, $cart ) {
//     echo $total;
//     return $total + 300;
// }

// add_action( 'woocommerce_cart_calculate_fees', 'add_custom_fee', 10, 1 );
// function add_custom_fee ( $cart ) {
//     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
//         return;

//     $fee = 300;

//     $cart->add_fee( __( 'Fee', 'woocommerce' ) , $fee, false );
// }
// add_action( 'woocommerce_calculate_totals', 'action_cart_calculate_totals', 10, 1 );
// function action_cart_calculate_totals( $cart_object ) {

//     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
//     {
//         return;
//     }

//     if ( !WC()->cart->is_empty() ):
//         ## Displayed subtotal (+10%)
//         // $cart_object->subtotal *= 1.1;

//         ## Displayed TOTAL (+10%)
//         // $cart_object->total *= 1.1;

//         ## Displayed TOTAL CART CONTENT (+10%)
//         $cart_object->cart_contents_total *= 1.1;

//     endif;

//     if(WC()->cart->is_empty())
//         echo $cart_object
// }

// $file = fopen(".\\tax_rates.csv", "r");
// $myFileContents = fread($file, filesize(".\\tax_rates.csv"));
// echo $myFileContents;
// // fclose($file);
// $myFile = plugin_dir_path( __FILE__ ) ."tax_rates.csv";
// $myFileLink = fopen($myFile, 'r');
// $myFileContents = fread($myFileLink, filesize($myFile));
// fclose($myFileLink);
// // print_r($myFileContents);

// $store_location = wc_get_base_location();
// // // $store_location =  wc_get_base_location();

// if(!strpos($myFileContents,"CGST")&&!strpos($myFileContents,"SGST")):
// $state_pos = strpos($myFileContents,$store_location['state']);
// // echo $state_pos;
// // echo $myFileContents[157];
// $len=0;
// for(;substr($myFileContents,$state_pos+$len,2) != "IN"; $len++);
// $igst_row = substr($myFileContents,$state_pos,$len);
// $I = strpos($igst_row,"IGST");
// // $igst_row[$I] = "S";
// $sgst_row = substr_replace($igst_row,"S", $I , 1);
// // $igst_row[$I] = "C";
// $cgst_row = substr_replace($igst_row,"C", $I , 1);
// $priority = strpos($cgst_row,"7");
// $cgst_row = substr_replace($cgst_row,"6", $priority , 1);
// $cgst_row = "IN,".$cgst_row;
// $new_row = $sgst_row.$cgst_row;
// $first = substr($myFileContents,0,$state_pos);
// $last = substr($myFileContents,$state_pos+$len);
// // $newFileContent = substr_replace($myFileContents,$new_row, $state_pos , strlen($new_row));
// $newFileContent = $first.$new_row.$last;


// // // $myFile2 = "testFolder/sampleFile2.txt";
// $myFileLink2 = fopen($myFile, 'w+');
// // $newContent = substr_replace($myFileContents,"",0,strlen(""));
// fwrite($myFileLink2, $newFileContent);
// fclose($myFileLink2);
// endif;

if(wc_prices_include_tax())
    echo "include tax";