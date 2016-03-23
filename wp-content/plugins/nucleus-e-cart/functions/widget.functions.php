<?php
# PRODUCT SPECIAL WIDGET
class ProductSpecialWidget extends WP_Widget
{
  function ProductSpecialWidget()
  {
    $widget_ops = array('classname' => 'ProductSpecialWidget', 'description' => 'Displays product special' );
    $this->WP_Widget('ProductSpecialWidget', 'E-Cart Product Special', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number_product' => '', 'image_title' => '' ) );
    $title = $instance['title'];
	$number_product = $instance['number_product'];
	$image_title = $instance['image_title'];

	if($image_title == "no")
	{
		$select_yes = "";
		$select_no = "checked";
	}
	else
	{
		$select_yes = "checked";
		$select_no = "";
	}
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
	<p><label for="<?php echo $this->get_field_id('number_product'); ?>">Number Show Product: <input class="widefat" id="<?php echo $this->get_field_id('number_product'); ?>" name="<?php echo $this->get_field_name('number_product'); ?>" type="text" value="<?php echo attribute_escape($number_product); ?>" /></label></p>
	<p><label for="<?php echo $this->get_field_id('image_title'); ?>">Show Image Widget Title:

	<input name="<?php echo $this->get_field_name('image_title'); ?>" type="radio" value="yes" <?php echo $select_yes; ?> /> Yes
    <input name="<?php echo $this->get_field_name('image_title'); ?>" type="radio" value="no" <?php echo $select_no; ?> /> No

	</label></p>

<?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['number_product'] = $new_instance['number_product'];
	$instance['image_title'] = $new_instance['image_title'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$number_product = empty($instance['number_product']) ? ' ' : $instance['number_product'];

	$title = empty($instance['title']) ? ' ' : $instance['title'];
	$image_title = empty($instance['image_title']) ? ' ' : $instance['image_title'];

    //if (!empty($title))
    //  echo $before_title . $title . $after_title;;

	if (class_exists('ecart')) {
		$ecart = new ecart;
	}
	else
	{
		include_once(ECART_DIR."frontend/ecart.class.php");
		$ecart = new ecart;

		$ecart->js();
	}

    // WIDGET CODE GOES HERE
    //echo "<h1>Product Special!</h1>";
	//echo "<img src='".ECART_URL."images/specials.png' style='position:absolute; margin-left:-15px; width:272px'>";
        
	if($image_title != "no")
		echo "<div class='head_product_special_widget'></div>";

	echo "<div class='title_product_special_widget'>$title</div>";

	//echo $number_product;
	echo "<br>&nbsp;";
	#$feed_url = "http://rss.detik.com/index.php/detikcom";
	#$this->getFeed($feed_url);

	if(is_numeric($number_product))
	{
		if($number_product > 0)
			$limit_product = "limit $number_product";
	}

	echo "<div align='center'>";
	echo "<table>";
	$qry = mysql_query("select * from  wp_ecart_product where product_on_special='1' AND inactive <> 'Y' order by rand() ".$limit_product);
	while($data = mysql_fetch_array($qry))
	{
		$product_id = $data['product_id'];
		$product_name = $data['product_name'];
		$plink = urlencode($product_name);
		$product_image = $data['product_image'];
		$selling_price_inc_tax = "$".number_format($data['selling_price_inc_tax'], 2, '.', ',');
		$selling_price_exc_tax = $data['selling_price_exc_tax'];
		$special_selling_price_inc_tax = "$".number_format($data['special_selling_price_inc_tax'], 2, '.', ',');
		$product_amount = $data['product_amount'];

		//$product_image_link = "<a href='$product_image' class='MagicThumb' rel='image-size: fit-screen; buttons-display:close' onclick='return false;'>";
		$product_image_link = "<a href='$product_image' title='".$product_name."' rel='lightbox' onclick='return false;'>";
		$product_image_link .= "<img src='$product_image' class='image_product_special_widget'>";
		$product_image_link .= "</a>";

		echo "<tr>";
		echo "<td>";
		echo $product_image_link;
		echo "</td>";
		echo "<td>";

		echo "<b><a href='".ECART_LINK."ecart=product_detail&product_id=$product_id&product_name=$plink' class='ecart_link'>".$product_name."</a></b>";
		echo "<br>&nbsp;";
		echo "<del>$selling_price_inc_tax</del> $special_selling_price_inc_tax ($product_amount)";
		echo "<br>&nbsp;";

		echo "</td>";
		echo "</tr>";

		/*echo "<tr>";
		echo "<td colspan='2'>";
		echo "<hr style='size:1;'>";
		echo "</td>";
		echo "</tr>";*/
	}
	echo "</table>";
	echo "</div>";


    echo $after_widget;
  }

  function getFeed($feed_url) {
		$content = file_get_contents($feed_url);

		$x = new SimpleXmlElement($content);
		echo "<ul>";
		foreach($x->channel->item as $entry) {
			echo "<li><a href='$entry->link' title='$entry->title' class='ecart_link'>" . $entry->title . "</a></li>";
		}
		echo "</ul>";
	}

}
add_action( 'widgets_init', create_function('', 'return register_widget("ProductSpecialWidget");') );


# SHOPPING CART WIDGET
class ShoppingCartWidget extends WP_Widget
{
  function ShoppingCartWidget()
  {
    $widget_ops = array('classname' => 'ShoppingCartWidget', 'description' => 'Displays shopping cart' );
    $this->WP_Widget('ShoppingCartWidget', 'E-Cart Shopping Cart', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'image_title' => '' ) );
    $title = $instance['title'];
	$image_title = $instance['image_title'];

	if($image_title == "no")
	{
		$select_yes = "";
		$select_no = "checked";
	}
	else
	{
		$select_yes = "checked";
		$select_no = "";
	}
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
	<p><label for="<?php echo $this->get_field_id('image_title'); ?>">Show Image Widget Title: <input name="<?php echo $this->get_field_name('image_title'); ?>" type="radio" value="yes" <?php echo $select_yes; ?> /> Yes &nbsp;&nbsp;&nbsp;<input name="<?php echo $this->get_field_name('image_title'); ?>" type="radio" value="no" <?php echo $select_no; ?> /> No</label></p>

<?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['image_title'] = $new_instance['image_title'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

	$title = empty($instance['title']) ? ' ' : $instance['title'];
	$image_title = empty($instance['image_title']) ? ' ' : $instance['image_title'];
    //if (!empty($title))
    //  echo $before_title . $title . $after_title;

	if (class_exists('ecart')) {
		$ecart = new ecart;
	}
	else
	{
		include_once(ECART_DIR."frontend/ecart.class.php");
		$ecart = new ecart;

		$ecart->js();
	}

    // WIDGET CODE GOES HERE
    //echo "<h1>Shopping Cart!</h1>";
	#echo "<img src='".ECART_URL."images/shopping_cart.png' style='position:absolute; margin-left:-15px; width:272px'>";
	if($image_title != "no")
		echo "<div class='head_shopping_basket_widget'></div>";

	echo "<div class='title_shopping_basket_widget'>$title</div>";
	echo "&nbsp;";

	$ecart->shopping_cart_widget();
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("ShoppingCartWidget");') );

?>