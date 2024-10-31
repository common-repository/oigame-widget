<?php
/*
Plugin Name: oiga.me widget
Plugin URI: http://wordpress.org/extend/plugins/oigame-widget/
Description: Muestra una campaña de oiga.me como widget
Author: Asociacion aLabs
Version: 0.2
Author URI: https://alabs.org
License: AGPLv3
 */


class OigameWidget extends WP_Widget
{
	function OigameWidget()
	{
		$widget_ops = array('classname' => 'OigameWidget', 'description' => 'Muestra una campaña de oiga.me como widget' );
		$this->WP_Widget('OigameWidget', 'oiga.me widget', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$instance = wp_parse_args( (array) $instance, array( 'height' => '' ) );
		$title = $instance['title'];
		$height = $instance['height'];

		//$oigame_campaigns = file_get_contents("https://oiga.me/es/campaigns.xml", "r");

		$oigame_campaigns_cache = wp_cache_get( 'oigame_campaigns' ); 
		if ($oigame_campaigns_cache == false) { // if no data, then
			$oigame_campaigns_cache = file_get_contents("https://oiga.me/es/campaigns/list.xml");
			//$oigame_campaigns_cache = file_get_contents("https://beta.oiga.me/es/campaigns/list.xml", "r");
			wp_cache_set( 'oigame_campaigns', $oigame_campaigns_cache ); 
		}

		$campaigns = new SimpleXMLElement($oigame_campaigns_cache);
?>
		<a href="https://oiga.me" title="oiga.me"><img src="https://oiga.me/assets/logo-small.png" alt="oiga.me" style="margin-left: auto; margin-right: auto; display: block; margin-bottom: 1em;"></a>
<p>
		<select class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>">
<?php
		foreach ($campaigns as $campaign) {
			if (strlen($campaign->name) > 40){
				$name = substr($campaign->name, 0, 40) . "...";
			} else {
				$name = $campaign->name;
			}
?>
				<option value="<?php echo $campaign->slug; ?>" <?php if ( $campaign->slug == $instance['title'] ) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
<?php
		}
?>
		</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('height'); ?>">Altura: </label>
	<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo attribute_escape($height); ?>" />
	<small> En caso de no ver la altura correctamente con el diseño del Wordpress, es posible cambiarlo. Tamaño en pixeles.</small> 
</p>

<?php
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['height'] = $new_instance['height'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$height = empty($instance['height']) ? ' ' : apply_filters('widget_height', $instance['height']);

		echo "<script type=\"text/javascript\" src=\"https://oiga.me/es/campaigns/$title/widget.js?height=$height\"></script>";
		//echo "<script type=\"text/javascript\" src=\"https://beta.oiga.me/en/campaigns/$title/widget.js\"></script>";

		echo $after_widget;
	}

}

add_action( 'widgets_init', create_function('', 'return register_widget("OigameWidget");') );?>
